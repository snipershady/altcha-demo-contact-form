<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Algorithm\Argon2id;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\CreateChallengeOptions;

const HMAC_KEY_POW = 'altcha-pow-argon2id-demo-key-2024';

$altcha = new Altcha(hmacSignatureSecret: HMAC_KEY_POW);
$challenge = $altcha->createChallenge(new CreateChallengeOptions(
    algorithm: new Argon2id(),
    cost: 1,
    memoryCost: 16384,   // 16 MB — lower memory cost for browser compatibility
    keyPrefixLength: 1,  // 1-byte prefix → ~256 average solver attempts
    expiresAt: new DateTimeImmutable('+10 minutes'),
));
$challengeJsonRaw = $challenge->toJson();

?><!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Demo — ALTCHA PoW Argon2id</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

        <!--
            Step 1 — regular script (runs synchronously, before any module).
            We intercept the first assignment to globalThis.$altcha via a setter trap:
            the altcha module does  globalThis.$altcha = globalThis.$altcha || {...}
            which triggers our setter exactly once, before customElements.define() is
            called.  We inject ARGON2ID into the algorithms Map right there, so the
            algorithm is present when connectedCallback eventually reads it.

            Step 2 — the worker script is fetched as a Blob to sidestep the
            cross-origin Worker restriction.
        -->
        <script>
            (function () {
                var blobUrlReady = fetch('https://cdn.jsdelivr.net/npm/altcha@3.0.10/dist/workers/argon2id.js')
                    .then(function (r) { return r.text(); })
                    .then(function (t) { return URL.createObjectURL(new Blob([t], { type: 'application/javascript' })); });

                Object.defineProperty(window, '$altcha', {
                    configurable: true,
                    enumerable: true,
                    get: function () { return undefined; },
                    set: function (val) {
                        Object.defineProperty(window, '$altcha', { value: val, writable: true, configurable: true, enumerable: true });
                        if (val && val.algorithms) {
                            val.algorithms.set('ARGON2ID', function () {
                                return blobUrlReady.then(function (url) { return new Worker(url); });
                            });
                        }
                    }
                });
            }());
        </script>
        <script type="module" src="https://cdn.jsdelivr.net/npm/altcha@3.0.10/dist/main/altcha.min.js"></script>

        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                padding: 2rem 0;
            }
            .card {
                border: none;
                border-radius: 1rem;
                box-shadow: 0 10px 40px rgba(0, 0, 0, .25);
                max-width: 460px;
                width: 100%;
                margin: 0 auto;
            }
            .card-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 1rem 1rem 0 0 !important;
                padding: 1.5rem 2rem;
            }
            .card-body {
                padding: 2rem;
            }
            .btn-submit {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                color: #fff;
                font-weight: 600;
                transition: transform .15s, box-shadow .15s, opacity .15s;
            }
            .btn-submit:hover:not(:disabled) {
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 6px 18px rgba(102, 126, 234, .5);
            }
            .btn-submit:disabled {
                opacity: .65;
                cursor: not-allowed;
            }
            .pow-status {
                font-size: .8rem;
                color: #6c757d;
                text-align: center;
                min-height: 1.2em;
            }
            .pow-status.text-success {
                color: #198754 !important;
            }
            .pow-status.text-danger {
                color: #dc3545 !important;
            }
        </style>
    </head>
    <body>

        <div class="container">
            <div class="card">
                <div class="card-header text-white text-center">
                    <h4 class="mb-0 fw-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-cpu-fill me-2 mb-1" viewBox="0 0 16 16">
                            <path d="M6.5 6a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5z"/>
                            <path d="M5.5.5a.5.5 0 0 0-1 0V2A2.5 2.5 0 0 0 2 4.5H.5a.5.5 0 0 0 0 1H2v1H.5a.5.5 0 0 0 0 1H2v1H.5a.5.5 0 0 0 0 1H2A2.5 2.5 0 0 0 4.5 12v1.5a.5.5 0 0 0 1 0V12h1v1.5a.5.5 0 0 0 1 0V12h1v1.5a.5.5 0 0 0 1 0V12a2.5 2.5 0 0 0 2.5-2.5h1.5a.5.5 0 0 0 0-1H13v-1h1.5a.5.5 0 0 0 0-1H13v-1h1.5a.5.5 0 0 0 0-1H13A2.5 2.5 0 0 0 10.5 2V.5a.5.5 0 0 0-1 0V2h-1V.5a.5.5 0 0 0-1 0V2h-1zm1 4.5h3A1.5 1.5 0 0 1 11 6.5v3A1.5 1.5 0 0 1 9.5 11h-3A1.5 1.5 0 0 1 5 9.5v-3A1.5 1.5 0 0 1 6.5 5"/>
                        </svg>
                        Login Demo
                    </h4>
                    <small class="opacity-75">Protetto da ALTCHA PoW invisibile — Argon2id</small>
                </div>

                <div class="card-body">
                    <form id="loginForm" action="actionpow.php" method="POST" novalidate>

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

                        <div class="mb-4">
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

                        <altcha-widget
                            id="altchaWidget"
                            display="invisible"
                            auto="onload"
                            challenge="<?php echo htmlspecialchars($challengeJsonRaw, ENT_QUOTES, 'UTF-8'); ?>"
                            name="altcha">
                        </altcha-widget>

                        <div class="mb-3 pow-status" id="powStatus">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Verifica di sicurezza Argon2id in corso…
                        </div>

                        <div class="d-grid">
                            <button
                                type="submit"
                                class="btn btn-primary btn-submit btn-lg"
                                id="submitBtn"
                                disabled>
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
        <script>
            (function () {
                const widget = document.getElementById('altchaWidget');
                const submitBtn = document.getElementById('submitBtn');
                const powStatus = document.getElementById('powStatus');

                widget.addEventListener('verified', function () {
                    submitBtn.disabled = false;
                    powStatus.textContent = '✓ Verifica di sicurezza completata.';
                    powStatus.className = 'mb-3 pow-status text-success';
                });

                widget.addEventListener('error', function () {
                    powStatus.textContent = '✗ Verifica di sicurezza fallita. Ricarica la pagina.';
                    powStatus.className = 'mb-3 pow-status text-danger';
                });
            })();
        </script>
    </body>
</html>
