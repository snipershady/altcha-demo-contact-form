<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\HmacAlgorithm;
use AltchaOrg\Altcha\VerifySolutionOptions;
use Shady\Altcha\Factory\AlgorithmFactory;
use Shady\Altcha\Factory\PayloadFactory;

header('Content-Type: application/json');

$hmacKey = 'averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodipartenza';

$requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
if ('POST' !== $requestMethod) {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$body = file_get_contents('php://input');
if (empty($body)) {
    http_response_code(400);
    echo json_encode(['error' => 'Payload vuoto']);
    exit;
}

// Il widget invia il payload come stringa base64 nel body
$altchaPayload = trim($body);

try {
    $payload = PayloadFactory::fromBase64($altchaPayload);
    $algorithm = AlgorithmFactory::fromName($payload->challenge->parameters->algorithm);
    $altcha = new Altcha(hmacSignatureSecret: $hmacKey);
    $result = $altcha->verifySolution(new VerifySolutionOptions($payload, $algorithm));

    if (!$result->verified) {
        http_response_code(400);
        echo json_encode(['error' => 'Soluzione non valida']);
        exit;
    }

    // Costruiamo la Server Signature firmata
    $hmacAlgo = HmacAlgorithm::SHA256;

    $verificationData = http_build_query([
        'verified' => 'true',
        'classification' => 'GOOD',
        'score' => '0.9',
        'expire' => (string) (time() + 3600),
        'time' => (string) time(),
    ]);

    $hash = hash($hmacAlgo->hashAlgo(), $verificationData, true);
    $signature = bin2hex(hash_hmac($hmacAlgo->hashAlgo(), $hash, $hmacKey, true));

    echo json_encode([
        'algorithm' => $hmacAlgo->value,
        'verificationData' => $verificationData,
        'signature' => $signature,
        'verified' => true,
    ]);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore interno del server']);
}
