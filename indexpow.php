<?php

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\CreateChallengeOptions;
use Shady\Altcha\Config\PowConfig;
use Shady\Altcha\Enum\HashAlgorithm;

$hmacKey = 'averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodipartenza';

// ── Configurazione PoW ────────────────────────────────────────────────────────
// Cambia algoritmo, cost, memoryCost e keyPrefix per variare difficoltà e sicurezza.
// Argon2id: cost = time_cost (iterazioni), memoryCost = KB di RAM per tentativo.
// Prefisso '00' = ~256 tentativi medi; '0000' = ~65 536; aumentare = più difficile.
$config = new PowConfig(
    algorithm: HashAlgorithm::ARGON2ID,
    cost: 3,
    keyLength: 32,
    keyPrefix: '00',
    memoryCost: 1024,   // 1 MB per tentativo
    parallelism: null,   // default lib (1)
    expirySeconds: 120,
);
// ─────────────────────────────────────────────────────────────────────────────

$altcha = new Altcha(hmacSignatureSecret: $hmacKey);
$options = new CreateChallengeOptions(
    algorithm: $config->algorithm->toDeriveKeyInstance(),
    cost: $config->cost,
    keyLength: $config->keyLength,
    keyPrefix: $config->keyPrefix,
    memoryCost: $config->memoryCost,
    parallelism: $config->parallelism,
    expiresAt: new DateTimeImmutable()->add($config->expiryInterval()),
);
$challenge = $altcha->createChallenge($options);

// Il widget v3 accetta il formato v2 direttamente tramite challengejson
$challengeJson = $challenge->toJson();

?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Form Contatti - Altcha PoW</title>

        <!-- Bootstrap 5.3.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <!-- Altcha Widget -->
        <script async defer src="https://cdn.jsdelivr.net/npm/altcha@latest/dist/altcha.min.js" type="module"></script>

        <style>
            body {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                padding: 20px 0;
            }
            .form-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                padding: 40px;
                max-width: 600px;
                margin: 0 auto;
            }
            .form-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .form-header h1 {
                color: #1b7a4e;
                font-weight: 700;
                margin-bottom: 10px;
            }
            .badge-pow {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                color: #1b7a4e;
                font-size: 0.7rem;
                padding: 3px 8px;
                border-radius: 10px;
                vertical-align: middle;
                margin-left: 6px;
                font-weight: 700;
            }
            .btn-submit {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                border: none;
                padding: 12px 40px;
                font-weight: 600;
                color: #1b7a4e;
                transition: transform 0.2s;
            }
            .btn-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(67, 233, 123, 0.4);
                color: #1b7a4e;
            }
            altcha-widget {
                margin: 20px 0;
            }
            .info-box {
                background: #e8f5e9;
                border-left: 4px solid #43e97b;
                border-radius: 5px;
                padding: 12px 15px;
                margin-bottom: 20px;
                font-size: 0.875rem;
                color: #1b7a4e;
            }
        </style>
    </head>
    <body>

        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h1>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-envelope-heart mb-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l3.235 1.94a2.8 2.8 0 0 0-.233 1.027L1 5.384v5.721l3.453-2.124c.146.277.329.556.55.835l-3.97 2.443A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741l-3.968-2.442c.22-.28.403-.558.55-.835L15 11.105V5.383l-3.002 1.801a2.8 2.8 0 0 0-.233-1.026L15 4.217V4a1 1 0 0 0-1-1zm6 2.993c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132"/>
                        </svg>
                        Contattaci
                        <span class="badge-pow">PoW</span>
                    </h1>
                    <p class="text-muted">Compila il form per inviarci un messaggio</p>
                </div>

                <div class="info-box">
                    <strong>Solo PoW</strong> — Il challenge è generato server-side al caricamento
                    della pagina e incorporato direttamente nel widget tramite
                    <code>challengejson</code>. Nessuna chiamata AJAX aggiuntiva per il challenge.
                </div>

                <form action="actionpow.php" method="POST">
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            Indirizzo Email
                        </label>
                        <input
                            type="email"
                            class="form-control form-control-lg"
                            id="email"
                            name="email"
                            placeholder="nome@esempio.it"
                            required>
                        <div class="form-text">Non condivideremo mai la tua email con terzi.</div>
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold">
                            Messaggio
                        </label>
                        <textarea
                            class="form-control form-control-lg"
                            id="message"
                            name="message"
                            rows="5"
                            placeholder="Scrivi qui il tuo messaggio..."
                            required></textarea>
                    </div>

                    <altcha-widget
                        challengejson="<?php echo htmlspecialchars($challengeJson, ENT_QUOTES, 'UTF-8'); ?>"
                        auto="onload"
                        hidefooter
                        hidelogo
                        style="display:none">
                    </altcha-widget>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-lg btn-submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-send-fill me-2" viewBox="0 0 16 16">
                                <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"/>
                            </svg>
                            Invia Messaggio
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bootstrap 5.3.3 JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
