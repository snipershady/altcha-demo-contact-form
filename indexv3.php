<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\V1\Altcha as AltchaV1;
use AltchaOrg\Altcha\V1\ChallengeOptions;

const HMAC_KEY = 'altcha-v3-demo-secret-key-2024';

$altcha = new AltchaV1(hmacKey: HMAC_KEY);
$challenge = $altcha->createChallenge(new ChallengeOptions(
    maxNumber: 100000,
    expires: new DateTimeImmutable('+5 minutes'),
));

$challengeJson = htmlspecialchars(json_encode([
    'algorithm' => $challenge->algorithm,
    'challenge'  => $challenge->challenge,
    'maxnumber'  => $challenge->maxNumber,
    'salt'       => $challenge->salt,
    'signature'  => $challenge->signature,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');

?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Demo — ALTCHA Widget v3</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <script async defer src="https://cdn.jsdelivr.net/gh/altcha-org/altcha/dist/altcha.min.js" type="module"></script>

        <style>
            body {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                padding: 2rem 0;
            }
            .card {
                border: none;
                border-radius: 1rem;
                box-shadow: 0 10px 40px rgba(0, 0, 0, .2);
                max-width: 460px;
                width: 100%;
                margin: 0 auto;
            }
            .card-header {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                border-radius: 1rem 1rem 0 0 !important;
                padding: 1.5rem 2rem;
            }
            .card-body {
                padding: 2rem;
            }
            .btn-submit {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                border: none;
                color: #fff;
                font-weight: 600;
                transition: transform .15s, box-shadow .15s;
            }
            .btn-submit:hover {
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 6px 18px rgba(79, 172, 254, .45);
            }
        </style>
    </head>
    <body>

        <div class="container">
            <div class="card">
                <div class="card-header text-white text-center">
                    <h4 class="mb-0 fw-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-shield-lock-fill me-2 mb-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.8 11.8 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7 7 0 0 0 1.048-.625 11.8 11.8 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 62.5 62.5 0 0 0-2.887-.87C9.843.266 8.69 0 8 0m0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5"/>
                        </svg>
                        Login Demo
                    </h4>
                    <small class="opacity-75">Protetto da ALTCHA Widget v3</small>
                </div>

                <div class="card-body">
                    <form action="actionv3.php" method="POST" novalidate>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input
                                type="text"
                                class="form-control"
                                id="username"
                                name="username"
                                placeholder="Inserisci lo username"
                                autocomplete="username"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input
                                type="text"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Inserisci la password"
                                autocomplete="current-password"
                                required>
                        </div>

                        <div class="mb-4 d-flex justify-content-center">
                            <altcha-widget
                                challengejson="<?php echo $challengeJson ?>"
                                strings='{"error":"Errore di verifica.","expired":"Verifica scaduta, ricarica la pagina.","footer":"Protetto da <a href=\"https://altcha.org\" target=\"_blank\">ALTCHA</a>","label":"Non sono un robot","verified":"Verificato","verifying":"Verifica in corso...","waitAlert":"Attendi..."}'>
                            </altcha-widget>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-submit btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-in-right me-2 mb-1" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                </svg>
                                Accedi
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
