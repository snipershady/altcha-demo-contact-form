<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\CreateChallengeOptions;
use Shady\Altcha\Enum\Pbkdf2Difficulty;

const HMAC_KEY_POWNOFORM = 'altcha-pow-pbkdf2-demo-key-averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodipartenza';

$difficulty       = Pbkdf2Difficulty::LOW;
$altcha           = new Altcha(hmacSignatureSecret: HMAC_KEY_POWNOFORM);
$challenge        = $altcha->createChallenge(new CreateChallengeOptions(
    algorithm:       new Pbkdf2($difficulty->hmacAlgorithm()),
    cost:            $difficulty->cost(),
    keyPrefixLength: $difficulty->keyPrefixLength(),
    expiresAt:       new DateTimeImmutable('+10 minutes'),
));
$challengeJsonRaw = $challenge->toJson();
?><!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CTA Demo — ALTCHA PoW PBKDF2 (no form)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/altcha@3.0.10/dist/main/altcha.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .4);
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
        }
        .card-header {
            background: linear-gradient(135deg, #e94560 0%, #f5a623 100%);
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.5rem 2rem;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-cta {
            background: linear-gradient(135deg, #e94560 0%, #f5a623 100%);
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: .03em;
            transition: transform .15s, box-shadow .15s, opacity .15s;
        }
        .btn-cta:hover:not(:disabled) {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(233, 69, 96, .45);
        }
        .btn-cta:disabled {
            opacity: .55;
            cursor: not-allowed;
        }
        .pow-status {
            font-size: .85rem;
            text-align: center;
            color: #6c757d;
            min-height: 1.4em;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">

        <div class="card-header text-white text-center">
            <h4 class="mb-0 fw-bold">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-unlock-fill me-2 mb-1" viewBox="0 0 16 16">
                    <path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2"/>
                </svg>
                Accesso Area Riservata
            </h4>
            <small class="opacity-75">
                ALTCHA PoW invisibile — PBKDF2/<?php echo htmlspecialchars($difficulty->hmacAlgorithm()->value, ENT_QUOTES, 'UTF-8'); ?>
            </small>
        </div>

        <div class="card-body">

            <p class="text-muted text-center mb-4">
                La verifica antibot avviene in background.<br>
                <small>La CTA si attiva solo dopo la conferma lato server.</small>
            </p>

            <!--
                Widget invisibile con challenge pre-generata lato server.
                auto="onload" → il PoW parte appena il widget è montato.
                Il payload risolto viene letto da event.detail.payload nell'evento "verified".
            -->
            <altcha-widget
                id="altchaWidget"
                display="invisible"
                auto="onload"
                challenge="<?php echo htmlspecialchars($challengeJsonRaw, ENT_QUOTES, 'UTF-8'); ?>">
            </altcha-widget>

            <!-- Stato della verifica (aggiornato via JS) -->
            <p class="pow-status mb-4" id="powStatus">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                PoW PBKDF2 in corso…
            </p>

            <!-- CTA: disabilitata finché il server non conferma la challenge -->
            <div class="d-grid">
                <button type="button" class="btn btn-cta btn-lg" id="ctaBtn" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right-circle-fill me-2 mb-1" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/>
                    </svg>
                    Accedi ai contenuti
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const widget    = document.getElementById('altchaWidget');
    const powStatus = document.getElementById('powStatus');
    const ctaBtn    = document.getElementById('ctaBtn');

    // ─── Helpers ────────────────────────────────────────────────────────────

    function setStatus(text, type = 'neutral') {
        const spinner = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';
        powStatus.innerHTML  = (type === 'loading' ? spinner : '') + text;
        powStatus.className  = 'pow-status mb-4'
            + (type === 'success' ? ' text-success' : '')
            + (type === 'error'   ? ' text-danger'  : '');
    }

    async function verifyWithServer(payload) {
        const body = new FormData();
        body.append('altcha', payload);

        const response = await fetch('actionpownoform.php', { method: 'POST', body });
        return response.json(); // { ok: bool, error: string|null }
    }

    // ─── Step 1: widget completa il PoW → verifica lato server via XHR ─────

    widget.addEventListener('verified', async function (event) {
        const payload = event.detail?.payload;

        setStatus('Verifica server in corso…', 'loading');

        try {
            const result = await verifyWithServer(payload);

            if (result.ok) {
                setStatus('✓ Challenge superata — puoi procedere.', 'success');
                ctaBtn.disabled = false;
            } else {
                setStatus('✗ ' + (result.error ?? 'Verifica fallita.'), 'error');
            }
        } catch (err) {
            setStatus('✗ Errore di rete. Ricarica la pagina.', 'error');
        }
    });

    widget.addEventListener('error', function () {
        setStatus('✗ PoW fallito. Ricarica la pagina.', 'error');
    });

    // ─── Step 2: CTA cliccata → SweetAlert2 ─────────────────────────────────

    ctaBtn.addEventListener('click', function () {
        Swal.fire({
            title: 'Challenge superata e CTA cliccata',
            icon: 'success',
        });
    });
</script>

</body>
</html>
