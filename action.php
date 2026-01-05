<?php

require_once 'vendor/autoload.php';

use AltchaOrg\Altcha\Altcha;

$hmacKey = 'averelaquintaelementarenonèuntraguardomaunpiccoloebanalepuntodimartenza';

$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD");
if ($requestMethod === 'GET') {
    header('Location: index.php');
    exit;
}

if ($requestMethod === 'POST') {
    $altchaPayload = filter_input(INPUT_POST, "altcha");
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, "message", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Verifica che il campo altcha sia presente
    if (empty($altchaPayload)) {
        showResponse(false, 'Verifica di sicurezza non completata', 'Il widget Altcha non ha inviato la soluzione. Per favore riprova.');
        exit;
    }

    $altcha = new Altcha($hmacKey);

    try {
        // Verifica la soluzione
        $isValid = $altcha->verifySolution($altchaPayload);

        if ($isValid) {
            // SUCCESSO - Qui puoi inviare email, salvare nel database, ecc.
            showResponse(true, 'Messaggio Inviato!', 'Grazie per averci contattato. Abbiamo ricevuto il tuo messaggio e ti risponderemo al più presto.', $email, $message);
        } else {
            // FALLIMENTO
            http_response_code(400);
            showResponse(false, 'Verifica Fallita', 'La verifica di sicurezza non è andata a buon fine. Per favore riprova.');
        }
    } catch (Exception $e) {
        http_response_code(500);
        showResponse(false, 'Errore del Server', 'Si è verificato un errore durante l\'elaborazione della richiesta.');
    }
}

function showResponse(bool $success, string $title, string $message, ?string $email = null, ?string $userMessage = null) {
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo htmlspecialchars($title); ?></title>

        <!-- Bootstrap 5.3.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                padding: 20px 0;
            }
            .response-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                padding: 40px;
                max-width: 600px;
                margin: 0 auto;
                text-align: center;
            }
            .icon-circle {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                font-size: 40px;
            }
            .icon-circle.success {
                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                color: white;
            }
            .icon-circle.error {
                background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
                color: white;
            }
            .btn-back {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                padding: 12px 40px;
                font-weight: 600;
                transition: transform 0.2s;
            }
            .btn-back:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            .message-preview {
                background: #f8f9fa;
                border-left: 4px solid #667eea;
                padding: 15px;
                margin: 20px 0;
                text-align: left;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="response-container">
                <div class="icon-circle <?php echo $success ? 'success' : 'error'; ?>">
                    <?php if ($success): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                        </svg>
                    <?php endif; ?>
                </div>

                <h1 class="mb-3 <?php echo $success ? 'text-success' : 'text-danger'; ?>">
                    <?php echo htmlspecialchars($title); ?>
                </h1>

                <p class="lead mb-4">
                    <?php echo htmlspecialchars($message); ?>
                </p>

                <?php if ($success && $email && $userMessage): ?>
                    <div class="message-preview">
                        <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                        <p class="mb-0"><strong>Messaggio:</strong></p>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($userMessage)); ?></p>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg btn-back">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                        </svg>
                        Torna al Form
                    </a>
                </div>
            </div>
        </div>

        <!-- Bootstrap 5.3.3 JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
    </html>
    <?php
}