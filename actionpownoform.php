<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\Challenge;
use AltchaOrg\Altcha\ChallengeParameters;
use AltchaOrg\Altcha\Payload;
use AltchaOrg\Altcha\Solution;
use AltchaOrg\Altcha\VerifySolutionOptions;
use Shady\Altcha\Enum\Pbkdf2Difficulty;

const HMAC_KEY_POWNOFORM = 'altcha-pow-pbkdf2-demo-key-averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodipartenza';

header('Content-Type: application/json; charset=UTF-8');

if ('POST' !== filter_input(INPUT_SERVER, 'REQUEST_METHOD')) {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metodo non consentito.']);
    exit;
}

$altchaRaw = filter_input(INPUT_POST, 'altcha');

$verified = false;
$errorMsg = null;

if (empty($altchaRaw)) {
    $errorMsg = 'Payload ALTCHA mancante.';
} else {
    $json = base64_decode($altchaRaw, true);
    $data = is_string($json) ? json_decode($json, true) : null;

    if (!is_array($data)) {
        $errorMsg = 'Payload ALTCHA non valido.';
    } else {
        try {
            $params    = ChallengeParameters::fromArray($data['challenge']['parameters'] ?? []);
            $challenge = new Challenge($params, $data['challenge']['signature'] ?? null);
            $solution  = new Solution(
                (int) ($data['solution']['counter'] ?? 0),
                (string) ($data['solution']['derivedKey'] ?? ''),
            );
            $payload    = new Payload($challenge, $solution);
            $difficulty = Pbkdf2Difficulty::LOW;

            $altcha = new Altcha(hmacSignatureSecret: HMAC_KEY_POWNOFORM);
            $result = $altcha->verifySolution(new VerifySolutionOptions(
                payload: $payload,
                algorithm: new Pbkdf2($difficulty->hmacAlgorithm()),
            ));

            $verified = $result->verified;
            if (!$verified) {
                $errorMsg = $result->expired
                    ? 'Challenge scaduta. Ricarica la pagina e riprova.'
                    : ($result->invalidSignature
                        ? 'Firma del challenge non valida.'
                        : 'Soluzione PoW PBKDF2/' . $difficulty->hmacAlgorithm()->value . ' non corretta.');
            }
        } catch (Throwable $e) {
            $errorMsg = 'Errore durante la verifica PBKDF2.';
        }
    }
}

if (!$verified) {
    http_response_code(400);
}

echo json_encode(['ok' => $verified, 'error' => $errorMsg]);
