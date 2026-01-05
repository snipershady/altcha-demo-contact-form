<?php

require_once 'vendor/autoload.php';

use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\ChallengeOptions;

// Imposta l'header per JSON e CORS
header('Content-Type: application/json');

// La tua chiave segreta (deve essere la stessa di action.php)
$hmac_key = 'averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodimartenza';

// Inizializza la libreria
$altcha = new Altcha($hmac_key);

// Configura le opzioni della sfida
$options = new ChallengeOptions(
        maxNumber: 50000,
        expires: (new \DateTimeImmutable())->add(new \DateInterval('PT15M')) // Scade tra 15 min
);

// Crea la sfida
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

// Restituisci la challenge come JSON
echo json_encode($challengeData);
