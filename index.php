
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Form Contatti - Protetto da Altcha</title>

        <!-- Bootstrap 5.3.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <!-- Altcha Widget -->
        <script async defer src="https://cdn.jsdelivr.net/npm/altcha@latest/dist/altcha.min.js" type="module"></script>

        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                color: #667eea;
                font-weight: 700;
                margin-bottom: 10px;
            }
            .form-header p {
                color: #6c757d;
            }
            .btn-submit {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                padding: 12px 40px;
                font-weight: 600;
                transition: transform 0.2s;
            }
            .btn-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            altcha-widget {
                margin: 20px 0;
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
                    </h1>
                    <p class="text-muted">Compila il form per inviarci un messaggio</p>
                </div>

                <form action="action.php" method="POST">
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            <i class="bi bi-envelope"></i> Indirizzo Email
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
                            <i class="bi bi-chat-dots"></i> Messaggio
                        </label>
                        <textarea
                            class="form-control form-control-lg"
                            id="message"
                            name="message"
                            rows="5"
                            placeholder="Scrivi qui il tuo messaggio..."
                            required></textarea>
                    </div>

                    <div class="mb-4 d-flex justify-content-center">
                        <altcha-widget
                            challengeurl="challenge.php"
                            strings='{"error":"Errore di verifica.","expired":"Verifica scaduta. Ricarica la pagina.","footer":"Protetto da <a href=\"https://altcha.org/\" target=\"_blank\">ALTCHA</a>","label":"Sono un essere umano","verified":"Verificato","verifying":"Verifica in corso...","waitAlert":"Verifica in corso. Attendi..."}'>
                        </altcha-widget>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg btn-submit">
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