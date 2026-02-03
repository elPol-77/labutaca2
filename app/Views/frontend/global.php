<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>La Butaca Global</title>
    <base href="/labutaca2/global/">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">

    <style>
        /* Estilos básicos para evitar el parpadeo blanco */
        body {
            margin: 0;
            background-color: #141414;
            color: white;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-left-color: #00d2ff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>

    <app-root>
        <div class="loading-container">
            <div class="spinner"></div>
            <p style="letter-spacing: 2px; font-size: 0.9rem; color: #aaa;">INICIANDO INTERFAZ GLOBAL</p>
        </div>
    </app-root>

    <script src="<?= base_url('assets/angular-global/browser/polyfills-5CFQRCPP.js') ?>" type="module"></script>
    <script src="<?= base_url('assets/angular-global/browser/main-ZQBSHZN2.js') ?>" type="module"></script>

</body>

</html>