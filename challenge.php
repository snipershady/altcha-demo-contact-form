<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Algorithm\Sha;
use AltchaOrg\Altcha\Algorithm\ShaAlgorithm;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\CreateChallengeOptions;

header('Content-Type: application/json');

$hmacKey = 'averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodipartenza';

$altcha = new Altcha(hmacSignatureSecret: $hmacKey);
$options = new CreateChallengeOptions(
    algorithm: new Sha(ShaAlgorithm::SHA256),
    cost: 1,
    keyPrefixLength: 2,   // prefisso random 2 byte → ~65 536 tentativi medi in browser
    expiresAt: new DateTimeImmutable()->add(new DateInterval('PT2M')),
);

$challenge = $altcha->createChallenge($options);

echo $challenge->toJson();
