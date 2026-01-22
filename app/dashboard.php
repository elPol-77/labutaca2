<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_web ?></title>

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <style>
        /* --- PEGA AQUÍ TU CSS COMPLETO QUE ME PASASTE ANTES --- */
        /* (Por brevedad, asumo que copias aquí todo el bloque <style> de tu diseño anterior) */
        :root {
            --glass-bg: rgba(255, 255, 255, 0.08);
            --accent: #00d2ff;
            --premium: #ffd700;
            --text-main: #ffffff;
            --text-muted: #a0a0a0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background: #0f0c29;
            color: white;
            height: 100vh;
            overflow: hidden;
        }

        /* ... Copia el resto de tu CSS aquí ... */

        /* Asegúrate de incluir los estilos de .movie-grid, .movie-poster, etc. */
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 2rem;
            padding-bottom: 4rem;
        }

        .movie-poster {
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            aspect-ratio: 2/3;
            cursor: pointer;
            transition: 0.4s;
        }

        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .movie-poster:hover {
            transform: scale(1.1) translateY(-10px);
            z-index: 10;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            border: 2px solid var(--accent);
        }

        .badge-premium {
            background: var(--premium);
            color: black;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.7rem;
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
        }
    </style>
</head>

<body>

    <div id="dynamic-bg"
        style="position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1; transition: 0.5s; opacity: 0.4;">
    </div>

    <section id="view-splash"
        style="position:absolute; width:100%; height:100%; background:black; z-index:9999; display:flex; justify-content:center; align-items:center; flex-direction:column;">
        <h1 style="font-family:'Playfair Display'; font-size:4rem; color:white;">LA BUTACA</h1>
        <div style="width:200px; height:2px; background:#333; margin-top:20px;">
            <div id="loader" style="width:0%; height:100%; background:#00d2ff; transition: width 1s;"></div>
        </div>
    </section>

    <section id="view-home" style="display:none; width:100%; height:100%; display:flex;">

        <nav
            style="width:250px; background:rgba(0,0,0,0.5); backdrop-filter:blur(10px); padding:20px; display:flex; flex-direction:column;">
            <h2 style="font-family:'Playfair Display'; margin-bottom:40px;">LB</h2>
            <a href="#"
                style="color:white; text-decoration:none; margin-bottom:20px; font-weight:bold; color:#00d2ff;">Explorar</a>
            <a href="#" style="color:#aaa; text-decoration:none; margin-bottom:20px;">Favoritos</a>
        </nav>

        <main style="flex:1; padding:30px; overflow-y:auto;">
            <h1>Tendencias</h1>
            <div class="movie-grid" id="grid-container">
            </div>
        </main>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        // --- AQUÍ ESTÁ LA CLAVE DEL MVC ---
        // PHP inyecta los datos de la BD en una variable JavaScript
        const dbData = <?php echo json_encode($peliculas); ?>;

        // Transformamos los datos de la BD (español) al formato que espera tu diseño (inglés)
        const movies = dbData.map(item => {
            return {
                id: item.id,
                title: item.titulo,
                // Si la imagen es un enlace web (http) úsalo, si no, busca en local
                img: item.imagen.startsWith('http') ? item.imagen : '<?= base_url("assets/img/") ?>' + item.imagen,
                bg: item.imagen_bg,
                premium: item.nivel_acceso == '2' // 2 es Premium
            };
        });

        $(document).ready(function () {
            // 1. Animación Splash
            $('#loader').css('width', '100%');
            setTimeout(() => {
                $('#view-splash').fadeOut(500, function () {
                    $('#view-home').fadeIn().css('display', 'flex');
                });
            }, 1000);

            // 2. Renderizar Grid
            const grid = $('#grid-container');
            movies.forEach(m => {
                const badge = m.premium ? '<span class="badge-premium">PRO</span>' : '';
                const card = `
                    <div class="movie-poster" data-bg="${m.bg}">
                        ${badge}
                        <img src="${m.img}" alt="${m.title}">
                        <div style="position:absolute; bottom:0; left:0; width:100%; padding:15px; background:linear-gradient(to top, black, transparent);">
                            <h4 style="margin:0; text-shadow:0 2px 4px black;">${m.title}</h4>
                        </div>
                    </div>
                `;
                grid.append(card);
            });

            // 3. Efecto Fondo
            $(document).on('mouseenter', '.movie-poster', function () {
                const bg = $(this).data('bg');
                if (bg) $('#dynamic-bg').css('background-image', `url(${bg})`);
            });
        });
    </script>
</body>

</html>