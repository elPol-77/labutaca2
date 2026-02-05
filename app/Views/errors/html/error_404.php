<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>404 - Corte de Cinta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --bg-color: #141414;
            --text-color: #e5e5e5;
            --accent: #e50914; /* Rojo tipo Netflix o usa tu azul #00d2ff */
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            position: relative;
        }

        /* Efecto de ruido de fondo (TV antigua) */
        .noise {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAMAAAAp4XiDAAAAUVBMVEWFhYWDg4N3d3dtbW17e3t1dXVNbW1bW1tSbW1NbW1tbW1ubm5wcHBxcXFycHJzc3N0dHR1dXV2dnZ3d3d4eHh5eXl6enp7e3t8fHx9fX1+fn5/f3+Dn5sAAAAAXRSTlMAQObYZgAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxMAAAsTAQCanBgAAAAHdElNRQfnAg0IDx2NvozdAAAAHElEQVRIx2NgGAWjYBSMglEwCkbBSMHYoUiP8gAAgRwAskzsPHcAAAAASUVORK5CYII=');
            opacity: 0.05;
            z-index: 1;
            pointer-events: none;
        }

        .container {
            position: relative;
            z-index: 2;
            padding: 20px;
            max-width: 600px;
        }

        h1 {
            font-size: 10rem;
            margin: 0;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -5px;
            background: -webkit-linear-gradient(#fff, #666);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 20px rgba(0,0,0,0.5);
        }

        h2 {
            font-size: 2rem;
            margin-top: -20px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        p {
            font-size: 1.2rem;
            color: #999;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .btn-home {
            background-color: white;
            color: black;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: bold;
            text-decoration: none;
            border-radius: 4px;
            transition: transform 0.2s, opacity 0.2s;
            display: inline-block;
        }

        .btn-home:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }

        /* Animación de Glitch simple */
        .glitch-wrapper {
            position: relative;
        }
    </style>
</head>
<body>

    <div class="noise"></div>

    <div class="container">
        <div class="glitch-wrapper">
            <h1>404</h1>
        </div>
        
        
        <p>
            Parece que la película que buscas no está en cartelera, 
            el enlace está roto o no tienes los permisos necesarios para ver este rollo.
        </p>

        <?php 
            $homeUrl = function_exists('base_url') ? base_url('/') : '/';
        ?>
        <a href="<?= $homeUrl ?>" 
           onclick="if(history.length > 1) { history.back(); return false; }" 
           class="btn-home">
            ← Volver atrás
        </a>

        <div style="margin-top: 15px;">
            <a href="<?= $homeUrl ?>" style="color: #666; text-decoration: none; font-size: 0.9rem;">
                O ir al Inicio
            </a>
        </div>

</body>
</html>