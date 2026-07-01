<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\Challenge;
use AltchaOrg\Altcha\ChallengeParameters;
use AltchaOrg\Altcha\HmacAlgorithm;
use AltchaOrg\Altcha\SolveChallengeOptions;

/**
 * Client di esempio (nessuna pagina, nessun browser, nessun widget).
 * Dimostra il protocollo completo verso challengebe.php + indexbe.php:
 *
 *   1. GET  challengebe.php -> riceve la challenge (parameters + signature)
 *   2. risolve la PoW in locale, con la stessa libreria usata dal server
 *      (il client NON conosce la chiave HMAC: quella resta solo sul server)
 *   3. POST indexbe.php     -> invia challenge+soluzione, ottiene "Completed"
 *
 * Uso:
 *   php do_challenge.php [base_url]
 *   php do_challenge.php https://localaltcha.spinfo.it
 *
 * Se base_url non è passato, viene letta da ALTCHA_BASE_URL oppure usato un
 * default. Un client non-PHP deve solo riprodurre lo stesso algoritmo PBKDF2
 * (nonce+counter come password, salt dalla challenge, cost iterazioni, e
 * confronto del prefisso esadecimale) — è uno standard disponibile in
 * qualunque libreria crypto (es. hashlib.pbkdf2_hmac in Python, crypto in
 * Node, PBKDF2 in OpenSSL/Go).
 */

$baseUrl = $argv[1] ?? (getenv('ALTCHA_BASE_URL') ?: 'https://localaltcha.spinfo.it');
$baseUrl = rtrim($baseUrl, '/');

// Solo per ambienti di test con certificato self-signed/non pubblico:
// ALTCHA_INSECURE_SSL=1 php do_challenge.php ...
// In produzione, con un certificato valido, non va mai impostata.
$insecureSsl = true; // '1' === getenv('ALTCHA_INSECURE_SSL');

/**
 * @return array{status: int, body: string}
 */
function httpRequest(string $url, ?array $postFields = null, bool $insecureSsl = false): array
{
    $ch = curl_init($url);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ];
    if ($insecureSsl) {
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = 0;
    }
    if (null !== $postFields) {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = http_build_query($postFields);
    }
    curl_setopt_array($ch, $options);

    $body = curl_exec($ch);
    if (false === $body) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException("Richiesta a {$url} fallita: {$error}");
    }

    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['status' => $status, 'body' => (string) $body];
}

// ─── 1. Richiedi una nuova challenge a challengebe.php ──────────────────────

echo "1) GET {$baseUrl}/challengebe.php\n";

$challengeResponse = httpRequest($baseUrl.'/challengebe.php', null, $insecureSsl);
if (200 !== $challengeResponse['status']) {
    fwrite(STDERR, "   Errore: HTTP {$challengeResponse['status']} - {$challengeResponse['body']}\n");
    exit(1);
}

$challengeJson = json_decode($challengeResponse['body'], true);
if (!is_array($challengeJson) || !isset($challengeJson['parameters'], $challengeJson['signature'])) {
    fwrite(STDERR, "   Risposta non valida: {$challengeResponse['body']}\n");
    exit(1);
}

$params = ChallengeParameters::fromArray($challengeJson['parameters']);
$challenge = new Challenge($params, $challengeJson['signature']);

echo "   algoritmo={$params->algorithm} cost={$params->cost} keyPrefix={$params->keyPrefix}\n";

// ─── 2. Risolvi la PoW in locale ────────────────────────────────────────────

echo "2) Risoluzione PoW in corso...\n";

// "PBKDF2/SHA-256" -> HmacAlgorithm::SHA256. Nessuna chiave HMAC richiesta
// per risolvere: solveChallenge() non firma né verifica nulla.
$hmacAlgorithm = HmacAlgorithm::from(str_replace('PBKDF2/', '', $params->algorithm));

$solveStart = microtime(true);
$solver = new Altcha();
$solution = $solver->solveChallenge(new SolveChallengeOptions(
    algorithm: new Pbkdf2($hmacAlgorithm),
    challenge: $challenge,
    timeout: 30.0,
));

if (null === $solution) {
    fwrite(STDERR, "   Nessuna soluzione trovata entro il timeout.\n");
    exit(1);
}

printf("   risolta in %.3fs (counter=%d)\n", microtime(true) - $solveStart, $solution->counter);

// ─── 3. Invia challenge + soluzione a indexbe.php ───────────────────────────

echo "3) POST {$baseUrl}/indexbe.php\n";

$altchaPayload = base64_encode(json_encode([
    'challenge' => $challengeJson,
    'solution' => [
        'counter' => $solution->counter,
        'derivedKey' => $solution->derivedKey,
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$result = httpRequest($baseUrl.'/indexbe.php', ['altcha' => $altchaPayload], $insecureSsl);

printf("   HTTP %d: %s\n", $result['status'], $result['body']);

exit(200 === $result['status'] ? 0 : 1);
