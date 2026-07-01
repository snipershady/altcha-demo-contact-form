<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\CreateChallengeOptions;
use Shady\Altcha\Enum\Pbkdf2Difficulty;

// Deve combaciare con la chiave usata in indexbe.php per verificare la firma.
const HMAC_KEY_BE = 'altcha-be-demo-secret-key-averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodipartenza';

header('Content-Type: application/json; charset=UTF-8');

if ('GET' !== filter_input(INPUT_SERVER, 'REQUEST_METHOD')) {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito. Usa GET.']);
    exit;
}

// LOW: solving headless (nessuna UI ad attendere), ma senza spendere CPU
// inutile lato client. Deve combaciare con il tier usato in indexbe.php.
$difficulty = Pbkdf2Difficulty::LOW;

$altcha = new Altcha(hmacSignatureSecret: HMAC_KEY_BE);
$challenge = $altcha->createChallenge(new CreateChallengeOptions(
    algorithm: new Pbkdf2($difficulty->hmacAlgorithm()),
    cost: $difficulty->cost(),
    keyPrefixLength: $difficulty->keyPrefixLength(),
    expiresAt: new DateTimeImmutable('+2 minutes'),
));

// Il client (headless, nessuna pagina) deve:
// 1. risolvere questa challenge (Altcha::solveChallenge lato PHP, o l'equivalente
//    in qualsiasi altro linguaggio/libreria compatibile con l'algoritmo PBKDF2);
// 2. inviare a indexbe.php una POST con campo "altcha" contenente
//    base64(json_encode(["challenge" => <questo json>, "solution" => {...}])).
echo $challenge->toJson();
