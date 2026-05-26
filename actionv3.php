<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AltchaOrg\Altcha\V1\Altcha as AltchaV1;

if (!defined('HMAC_KEY')) {
    define('HMAC_KEY', 'altcha-v3-demo-secret-key-2024');
}

if ('POST' !== filter_input(INPUT_SERVER, 'REQUEST_METHOD')) {
    header('Location: indexv3.php');
    exit;
}

$username = htmlspecialchars((string) filter_input(INPUT_POST, 'username', FILTER_DEFAULT), ENT_QUOTES, 'UTF-8');
$password = htmlspecialchars((string) filter_input(INPUT_POST, 'password', FILTER_DEFAULT), ENT_QUOTES, 'UTF-8');
$altchaRaw = filter_input(INPUT_POST, 'altcha');

$verified = false;
$errorMsg = null;

if (empty($altchaRaw)) {
    $errorMsg = 'Verifica ALTCHA mancante. Completa il widget prima di inviare.';
} else {
    try {
        $altcha = new AltchaV1(hmacKey: HMAC_KEY);
        $verified = $altcha->verifySolution($altchaRaw);
        if (!$verified) {
            $errorMsg = 'Verifica ALTCHA non riuscita o challenge scaduta.';
        }
    } catch (Throwable) {
        $errorMsg = 'Errore durante la verifica ALTCHA.';
    }
}

if (!$verified) {
    http_response_code(400);
}
?><!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $verified ? 'Accesso riuscito' : 'Accesso negato'; ?> — ALTCHA Widget v3</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

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
                max-width: 500px;
                width: 100%;
                margin: 0 auto;
            }
            .icon-circle {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.25rem;
            }
            .icon-circle.success {
                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            }
            .icon-circle.error {
                background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            }
            .btn-back {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                border: none;
                color: #fff;
                font-weight: 600;
                transition: transform .15s, box-shadow .15s;
            }
            .btn-back:hover {
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 6px 18px rgba(79, 172, 254, .45);
            }
        </style>
    </head>
    <body>

        <div class="container">
            <div class="card">
                <div class="card-body p-4 p-md-5 text-center">

                    <div class="icon-circle <?php echo $verified ? 'success' : 'error'; ?>">
<?php if ($verified) { ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="white" class="bi bi-check-lg" viewBox="0 0 16 16">
                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                            </svg>
<?php } else { ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="white" class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                            </svg>
<?php } ?>
                    </div>

<?php if ($verified) { ?>
                        <h3 class="fw-bold text-success mb-2">Accesso riuscito</h3>
                        <p class="text-muted mb-4">La verifica ALTCHA è andata a buon fine.</p>

                        <div class="text-start bg-light rounded p-3 mb-4 small">
                            <div class="mb-1">
                                <span class="fw-semibold">Username:</span>
    <?php echo '' !== $username ? $username : '<em class="text-muted">non fornito</em>'; ?>
                            </div>
                            <div>
                                <span class="fw-semibold">Password:</span>
    <?php echo '' !== $password ? str_repeat('•', max(6, mb_strlen($password))) : '<em class="text-muted">non fornita</em>'; ?>
                            </div>
                        </div>

                        <div class="alert alert-success text-start small py-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-check me-1" viewBox="0 0 16 16">
                            <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56l.01.003c1.117.315 2.218.667 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
                            <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                            </svg>
                            Verifica ALTCHA v3 completata con successo.
                        </div>

<?php } else { ?>
                        <h3 class="fw-bold text-danger mb-2">Accesso negato</h3>
                        <p class="text-muted mb-4"><?php echo $errorMsg ?? 'Verifica ALTCHA non riuscita.'; ?></p>

                        <div class="alert alert-danger text-start small py-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle me-1" viewBox="0 0 16 16">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                            Completa il widget ALTCHA e riprova.
                        </div>
<?php } ?>

                    <a href="indexv3.php" class="btn btn-primary btn-lg btn-back">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-left me-2 mb-1" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                        </svg>
                        Torna al form
                    </a>

                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
