<section id="view-movies-full" class="view-section active" style="padding-top: 100px;">
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div id="loading-initial" class="active"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: #0f0c29; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <div class="camera-loader" style="transform: scale(1.5); margin-bottom: 20px;">
            <div class="reels-container"
                style="display: flex; gap: 15px; justify-content: center; margin-bottom: -15px; position: relative; z-index: 2;">
                <div class="reel"
                    style="width: 40px; height: 40px; border: 4px solid #333; border-radius: 50%; position: relative; animation: spinReel 2s linear infinite; background: #1a1a1a;">
                </div>
                <div class="reel"
                    style="width: 40px; height: 40px; border: 4px solid #333; border-radius: 50%; position: relative; animation: spinReel 2s linear infinite; background: #1a1a1a;">
                </div>
            </div>
            <div class="camera-body"
                style="width: 100px; height: 60px; background: #222; border-radius: 8px; position: relative; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                <div class="lens"
                    style="width: 50px; height: 50px; background: #111; border-radius: 50%; border: 3px solid #444; position: relative; overflow: hidden; box-shadow: inset 0 0 10px rgba(0,0,0,0.8);">
                    <div class="lens-reflection"
                        style="width: 20px; height: 20px; background: rgba(255,255,255,0.1); border-radius: 50%; position: absolute; top: 5px; left: 5px;">
                    </div>
                </div>
            </div>
        </div>
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
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, #141414 0%, transparent 60%); z-index: 1;"></div>
                    
                    <div class="hero-content-wrapper" style="position: relative; z-index: 2;">
                        <div class="hero-info" style="padding-left: 5%;">
                            <h1 style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);"><?= esc($destacada['titulo']) ?></h1>

                            <div class="hero-badges">
                                <span class="badge badge-premium">PELÍCULA</span>
                                <span class="badge badge-hd">HD</span>
                            </div>

                            <p style="color:#ddd; margin-bottom:2rem; font-size:1.1rem; line-height:1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); max-width: 600px;">
                                <?= esc($destacada['descripcion']) ?>
                            </p>

                            <div style="display: flex; gap: 15px;">
                                <button class="btn-primary" onclick="playCinematic('<?= $destacada['link_ver'] ?>')">
                                    <i class="fa fa-play"></i> Reproducir
                                </button>

                                <a href="<?= $destacada['link_detalle'] ?>" class="btn-secondary"
                                    style="background:rgba(255,255,255,0.2); border:none; color:white; padding:12px 25px; border-radius:50px; cursor:pointer; font-weight:bold; display:flex; align-items:center; gap:8px; text-decoration:none; backdrop-filter: blur(5px);">
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
            lazyLoad: 'ondemand',
            arrows: true,
            prevArrow: '<button class="slick-prev custom-arrow left-arrow">❮</button>',
            nextArrow: '<button class="slick-next custom-arrow right-arrow">❯</button>',
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

            // CAMBIO CLAVE: URL apunta a 'peliculas/ajax-fila'
            $.ajax({
                url: '<?= base_url("peliculas/ajax-fila") ?>',
                method: 'POST',
                data: { bloque: bloqueActual, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                success: function (html) {
                    cargandoBloque = false;
                    $('#spinner-scroll').hide();

                    if (html.trim() === "" && !html.includes("sync-data")) {
                        hayMasBloques = false; return;
                    }

                    $('#rows-container').append(html);
                    $('.category-row').css('opacity', 1);
                    
                    inicializarCarruseles();

                    bloqueActual++;

                    // Auto-relleno si la pantalla es muy grande
                    if ($(document).height() <= $(window).height() + 100) {
                        cargarSiguienteBloque();
                    }
                },
                error: function () {
                    cargandoBloque = false;
                    $('#spinner-scroll').hide();
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
</style>