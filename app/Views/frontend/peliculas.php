<div id="view-splash" class="active">

    <div class="camera-loader">
        <div class="reels-container">
            <div class="reel"></div>
            <div class="reel"></div>
        </div>

        <div class="camera-body">
            <div class="lens"></div>
            <div class="matte-box"></div>
        </div>
    </div>

    <p class="loading-text">CARGANDO...</p>

    <div class="loader-line-container"
        style="width: 150px; background: #333; height: 3px; border-radius: 2px; margin-top: 10px;">
        <div class="loader-line" style="width: 0%; height: 100%; background: var(--accent); transition: width 1s;">
        </div>
    </div>

</div>
<section id="view-movies-full" class="view-section active" style="padding-top: 100px;">
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div id="loading-initial" class="active"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: #0f0c29; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <p class="loading-text"
            style="color: #fff; font-family: 'Outfit', sans-serif; letter-spacing: 3px; font-weight: bold; margin-bottom: 15px;">
            CARGANDO...</p>
        <div class="loader-line-container"
            style="width: 200px; background: #333; height: 4px; border-radius: 2px; overflow: hidden;">
            <div class="loader-line"
                style="width: 0%; height: 100%; background: #e50914; transition: width 0.5s ease-out; box-shadow: 0 0 10px #e50914;">
            </div>
        </div>
    </div>

    <main class="content" style="display:none; opacity:0;">

        <?php if (!empty($destacada)): ?>
            <div class="hero-static">
                <div class="hero-item" style="background-image: url('<?= $destacada['backdrop'] ?>');">
                    <div class="hero-content-wrapper">
                        <div class="hero-info" style="padding-left: 5%;">
                            <h1><?= esc($destacada['titulo']) ?></h1>

                            <div class="hero-badges">
                                <span class="badge badge-premium">PELÍCULA</span>
                                <span class="badge badge-hd">HD</span>
                            </div>

                            <p
                                style="color:#ddd; margin-bottom:2rem; font-size:1.1rem; line-height:1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= esc($destacada['descripcion']) ?>
                            </p>

                            <div style="display: flex; gap: 15px;">
                                <button class="btn-primary" onclick="playCinematic('<?= $destacada['link_ver'] ?>')">
                                    <i class="fa fa-play"></i> Reproducir
                                </button>

                                <a href="<?= $destacada['link_detalle'] ?>" class="btn-secondary"
                                    style="background:rgba(255,255,255,0.2); border:none; color:white; padding:12px 25px; border-radius:50px; cursor:pointer; font-weight:bold; display:flex; align-items:center; gap:8px; text-decoration:none;">
                                    <i class="fa fa-info-circle"></i> Más Info
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div id="rows-container" class="netflix-container" style="position: relative; z-index: 10; min-height: 500px;">
        </div>

        <div id="infinite-loader" style="text-align:center; padding: 30px; height: 100px;">
            <div class="spinner-border text-danger" role="status" style="display:none;" id="spinner-scroll">
                <span class="visually-hidden">Cargando más...</span>
            </div>
        </div>

    </main>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
    $(document).ready(function () {

        let bloqueActual = 0;
        let cargandoBloque = false;
        let hayMasBloques = true;

        // CONFIGURACIÓN SLICK (Igual que series)
        const slickSettings = {
            dots: false,
            infinite: false, // Importante false para detectar final
            speed: 500,
            slidesToShow: 6,
            slidesToScroll: 3,
            arrows: true,
            prevArrow: '<button type="button" class="slick-prev custom-arrow left-arrow"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next custom-arrow right-arrow"><i class="fa fa-chevron-right"></i></button>',
            responsive: [
                { breakpoint: 1600, settings: { slidesToShow: 5, slidesToScroll: 2 } },
                { breakpoint: 1200, settings: { slidesToShow: 4, slidesToScroll: 2 } },
                { breakpoint: 800, settings: { slidesToShow: 3, slidesToScroll: 1 } },
                { breakpoint: 500, settings: { slidesToShow: 2, slidesToScroll: 1 } }
            ]
        };

        // --- INICIALIZADOR DE CARRUSELES ---
        function inicializarCarruseles() {
            $('.slick-carousel-ajax:not(.slick-initialized)').each(function () {
                let $carousel = $(this);
                $carousel.slick(slickSettings);

                // Evento para cargar más en horizontal
                $carousel.on('afterChange', function (event, slick, currentSlide) {
                    if (slick.currentSlide + slick.options.slidesToShow >= slick.slideCount) {
                        if ($carousel.data('loading-more') === true) return;

                        let endpoint = $carousel.attr('data-endpoint');
                        if (endpoint === 'tmdb') {
                            cargarMasPelisEnHorizontal($carousel); // <--- OJO: Llama a función de PELIS
                        }
                    }
                });
            });
        }

        // --- FUNCIÓN HORIZONTAL (PELIS) ---
        function cargarMasPelisEnHorizontal($carousel) {
            let params = $carousel.attr('data-params');
            let page = parseInt($carousel.attr('data-page'));
            let tipo = $carousel.attr('data-endpoint');

            $carousel.data('loading-more', true);

            // CAMBIO CLAVE: URL apunta a 'peliculas/ajax-expandir-fila'
            $.ajax({
                url: '<?= base_url("peliculas/ajax-expandir-fila") ?>',
                method: 'POST',
                data: {
                    params: params,
                    page: page,
                    tipo: tipo,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function (newHtml) {
                    if (newHtml.trim() !== "") {
                        var $nuevosElementos = $(newHtml);
                        $nuevosElementos.each(function () {
                            $carousel.slick('slickAdd', this);
                        });
                        $carousel.attr('data-page', page + 1); // TMDB API pagina de 1 en 1
                    }
                    $carousel.data('loading-more', false);
                },
                error: function () {
                    $carousel.data('loading-more', false);
                }
            });
        }

        // --- CARGA INICIAL ---
        setTimeout(function () {
            $('#loading-initial').fadeOut(500, function () {
                $('.content').css('display', 'block').animate({ opacity: 1 }, 500);
                cargarSiguienteBloque();
            });
        }, 600);

        // --- SCROLL INFINITO VERTICAL ---
        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 400) {
                if (!cargandoBloque && hayMasBloques) cargarSiguienteBloque();
            }
        });

        function cargarSiguienteBloque() {
            if (cargandoBloque || !hayMasBloques) return;
            cargandoBloque = true;
            $('#spinner-scroll').show();

            $.ajax({
                url: '<?= base_url("peliculas/ajax-fila") ?>',
                method: 'POST',
                dataType: 'json', // <--- FUNDAMENTAL: Le decimos que esperamos JSON
                data: { bloque: bloqueActual, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                success: function (res) {
                    cargandoBloque = false;
                    $('#spinner-scroll').hide();

                    // Comprobamos si nos devolvió un html vacío (fin de resultados)
                    if (!res.html || res.html.trim() === "") {
                        hayMasBloques = false;
                        return;
                    }

                    // Insertamos las 4 filas juntas en la web
                    $('#rows-container').append(res.html);

                    // Inicializamos los 4 carruseles a la vez
                    inicializarCarruseles();

                    // Damos el efecto fade-in a las 4 filas a la vez
                    setTimeout(() => {
                        $('.category-row').css('opacity', 1);
                    }, 50);

                    // ACTUALIZAMOS EL CONTADOR CON EL DEL SERVIDOR
                    bloqueActual = res.next_bloque;

                    // Si la pantalla es muy grande, carga el siguiente bloque de 4.
                    if ($(document).height() <= $(window).height() + 100) {
                        cargarSiguienteBloque();
                    }
                },
                error: function () {
                    cargandoBloque = false;
                    $('#spinner-scroll').hide();
                    console.error("Error al cargar filas.");
                }
            });
        }
    });
</script>

<style>
    .hero-static {
        margin: 2rem 4% 3rem 4%;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        height: 65vh;
        display: block;
        position: relative;
    }

    .hero-static .hero-item {
        height: 100% !important;
        width: 100% !important;
        background-size: cover;
        background-position: center;
        display: flex !important;
        align-items: center;
        position: relative;
    }

    @media (max-width: 768px) {
        .hero-static {
            margin: 1rem 2%;
            height: 55vh;
        }

        .hero-info h1 {
            font-size: 2rem;
        }
    }

    <style>
    /* ... Tus estilos anteriores para .hero-static y media queries ... */

    /* 1. Fondo temporal animado (Efecto Skeleton) para el contenedor de la película */
    .movie-poster {
        background-color: #222;
        animation: skeletonPulse 1.5s infinite ease-in-out;
        /* Asegúrate de mantener tus estilos anteriores (border-radius, aspect-ratio, etc.)
           Si los tienes definidos en front.css, no es necesario repetirlos aquí,
           pero la estructura básica suele ser: */
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 2/3;
    }

    @keyframes skeletonPulse {
        0% {
            background-color: #222;
        }

        50% {
            background-color: #333;
        }

        100% {
            background-color: #222;
        }
    }

    /* 2. La imagen empieza invisible (opacidad 0) */
    .movie-poster img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        /* Inicia invisible */
        transition: opacity 0.5s ease-in-out;
        /* Transición suave */
    }

    /* 3. Clase que se añade mediante JS cuando la imagen termina de cargar */
    .movie-poster img.loaded {
        opacity: 1;
        /* Se hace visible y oculta el esqueleto */
    }
</style>