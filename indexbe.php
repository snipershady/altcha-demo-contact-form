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

// Deve combaciare con la chiave usata in challengebe.php per generare la firma.
const HMAC_KEY_BE = 'altcha-be-demo-secret-key-averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodipartenza';

// Deve combaciare con il tier usato in challengebe.php per generare la challenge.
$difficulty = Pbkdf2Difficulty::LOW;

// Chiedi al client di risolvere una PoW ottenuta da GET challengebe.php e di
// inviarla qui come campo POST "altcha" (base64 di {"challenge":...,"solution":...}).
// $powcompletata diventa true SOLO dopo una verifica server-side riuscita:
// nessun valore hardcoded, nessuna scorciatoia.
$powcompletata = false;

$altchaRaw = filter_input(INPUT_POST, 'altcha');

if (!empty($altchaRaw)) {
    $json = base64_decode($altchaRaw, true);
    $data = is_string($json) ? json_decode($json, true) : null;

    if (is_array($data)) {
        try {
            $params    = ChallengeParameters::fromArray($data['challenge']['parameters'] ?? []);
            $challenge = new Challenge($params, $data['challenge']['signature'] ?? null);
            $solution  = new Solution(
                (int) ($data['solution']['counter'] ?? 0),
                (string) ($data['solution']['derivedKey'] ?? ''),
            );
            $payload = new Payload($challenge, $solution);

            $altcha = new Altcha(hmacSignatureSecret: HMAC_KEY_BE);
            $result = $altcha->verifySolution(new VerifySolutionOptions(
                payload: $payload,
                algorithm: new Pbkdf2($difficulty->hmacAlgorithm()),
            ));

            $powcompletata = $result->verified;
        } catch (Throwable) {
            $powcompletata = false;
        }
    }
}

if (!$powcompletata) {
    http_response_code(403);
}

if($powcompletata) {
    echo "<br />";
    echo "<br />";
    echo PHP_EOL . "Completed. Il codice qui viene eseguito solo se la challenge è stata completata" . PHP_EOL;
}
