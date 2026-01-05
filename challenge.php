<?php

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\ChallengeOptions;

// Imposta l'header per JSON
header('Content-Type: application/json');

// La tua chiave segreta (deve essere la stessa di action.php)
$hmacKey = 'averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodimartenza';

$altcha = new Altcha($hmacKey);
$options = new ChallengeOptions(
    maxNumber: 50000,
    expires: new DateTimeImmutable()->add(new DateInterval('PT2M')) // Scade tra 2 min
);

$challenge = $altcha->createChallenge($options);

// Converti in formato compatibile con il widget JavaScript
// Il widget si aspetta "maxnumber" (minuscolo) non "maxNumber"
$challengeData = [
    'algorithm' => $challenge->algorithm,
    'challenge' => $challenge->challenge,
    'maxnumber' => $challenge->maxNumber, // Converti da camelCase a lowercase
    'salt' => $challenge->salt,
    'signature' => $challenge->signature,
];

echo json_encode($challengeData);
