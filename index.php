<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ALTCHA Demo — Proof of Work & Widget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .25);
            max-width: 860px;
            width: 100%;
            margin: 0 auto;
        }
        .card-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.5rem 2rem;
        }
        .card-body {
            padding: 2rem;
        }
        .demo-card {
            border-radius: .75rem;
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            transition: transform .2s, box-shadow .2s;
            display: block;
            height: 100%;
        }
        .demo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
            color: inherit;
        }
        .demo-card h5 {
            font-weight: 700;
        }
        .demo-card p {
            font-size: .9rem;
            margin-bottom: 0;
            opacity: .85;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-white text-center">
                <h4 class="mb-0 fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-grid-fill me-2 mb-1" viewBox="0 0 16 16">
                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5z"/>
                    </svg>
                    ALTCHA Demo
                </h4>
                <small class="opacity-75">Seleziona una modalità di verifica</small>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <a href="indexpow.php" class="demo-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
                            <h5><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-cpu-fill me-1" viewBox="0 0 16 16"><path d="M6.5 6a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5z"/><path d="M5.5.5a.5.5 0 0 0-1 0V2A2.5 2.5 0 0 0 2 4.5H.5a.5.5 0 0 0 0 1H2v1H.5a.5.5 0 0 0 0 1H2v1H.5a.5.5 0 0 0 0 1H2A2.5 2.5 0 0 0 4.5 12v1.5a.5.5 0 0 0 1 0V12h1v1.5a.5.5 0 0 0 1 0V12h1v1.5a.5.5 0 0 0 1 0V12a2.5 2.5 0 0 0 2.5-2.5h1.5a.5.5 0 0 0 0-1H13v-1h1.5a.5.5 0 0 0 0-1H13v-1h1.5a.5.5 0 0 0 0-1H13A2.5 2.5 0 0 0 10.5 2V.5a.5.5 0 0 0-1 0V2h-1V.5a.5.5 0 0 0-1 0V2h-1zm1 4.5h3A1.5 1.5 0 0 1 11 6.5v3A1.5 1.5 0 0 1 9.5 11h-3A1.5 1.5 0 0 1 5 9.5v-3A1.5 1.5 0 0 1 6.5 5"/></svg>
                            PoW Argon2id</h5>
                            <p>Proof of Work con Argon2id via ALTCHA 3.x — worker cross-origin</p>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="indexpow2.php" class="demo-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff;">
                            <h5><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-key-fill me-1" viewBox="0 0 16 16"><path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1.037 1.004A3.5 3.5 0 0 1 3.5 11.5m2.5 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0"/></svg>
                            PoW PBKDF2</h5>
                            <p>Proof of Work con PBKDF2/SHA-256 via ALTCHA 3.x — WebCrypto nativo</p>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="indexv3.php" class="demo-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff;">
                            <h5><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-shield-lock-fill me-1" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.8 11.8 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7 7 0 0 0 1.048-.625 11.8 11.8 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 62.5 62.5 0 0 0-2.887-.87C9.843.266 8.69 0 8 0m0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5"/></svg>
                            Widget v3</h5>
                            <p>ALTCHA Widget v3 classico — checkbox "Non sono un robot"</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
