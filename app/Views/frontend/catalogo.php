
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

<section id="view-home-full" class="view-section active" style="padding-top: 100px;">
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    <?php if (session()->getFlashdata('error_general')): ?>
        <div id="alerta-flotante"
            style="position: fixed; top: 500px; left: 50%; transform: translateX(-50%); z-index: 99999; background: #e50914; color: white; padding: 15px 30px; border-radius: 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); font-family: 'Outfit', sans-serif; font-weight: bold; text-align: center; min-width: 300px;">
            <i class="fa fa-exclamation-circle" style="margin-right: 10px;"></i>
            <?= session()->getFlashdata('error_general') ?>
        </div>

        <script>
            // Usamos Javascript nativo para que funcione aunque jQuery cargue después
            setTimeout(function() {
                var alerta = document.getElementById('alerta-flotante');
                if (alerta) {
                    alerta.style.transition = "opacity 0.5s ease";
                    alerta.style.opacity = "0";
                    setTimeout(function() { alerta.style.display = "none"; }, 500);
                }
            }, 4000);
        </script>
    <?php endif; ?>

    <div id="loading-initial" class="active"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: #0f0c29; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <div class="spinner-border text-danger" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <main class="content" style="display:none; opacity:0;">

        <?php if (!empty($destacada)): ?>
            <div class="hero-static">
                <div class="hero-item">
                    <img src="<?= $destacada['backdrop'] ?>" alt="<?= esc($destacada['titulo']) ?>"
                        class="hero-img-absoluta" fetchpriority="high" decoding="async">

                    <div class="hero-content-wrapper">

                        <div class="hero-info" style="padding-left: 5%;">
                            <h1><?= esc($destacada['titulo']) ?></h1>

                            <div class="hero-badges">
                                <?php if (isset($destacada['tipo_id']) && $destacada['tipo_id'] == 1): ?>
                                    <span class="badge badge-premium">PELÍCULA</span>

                                <?php elseif (isset($destacada['tipo_id']) && $destacada['tipo_id'] == 2): ?>
                                    <span class="badge badge-premium">SERIE</span>

                                <?php else: ?>
                                    <span class="badge badge-premium">DESTACADO</span>
                                <?php endif; ?>

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
            <div class="spinner-border text-danger" role="status" style="display:none;" id="spinner-scroll"></div>
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

        const slickSettings = {
            dots: false, infinite: false, speed: 500, slidesToShow: 6, slidesToScroll: 3, lazyLoad: 'ondemand', arrows: true,
            prevArrow: '<button type="button" class="slick-prev custom-arrow left-arrow"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next custom-arrow right-arrow"><i class="fa fa-chevron-right"></i></button>',
            responsive: [
                { breakpoint: 1600, settings: { slidesToShow: 5, slidesToScroll: 2 } },
                { breakpoint: 1200, settings: { slidesToShow: 4, slidesToScroll: 2 } },
                { breakpoint: 800, settings: { slidesToShow: 3, slidesToScroll: 1 } },
                { breakpoint: 500, settings: { slidesToShow: 2, slidesToScroll: 1 } }
            ]
        };

        function inicializarCarruseles() {
            $('.slick-carousel-ajax:not(.slick-initialized)').each(function () {
                let $carousel = $(this);
                $carousel.slick(slickSettings);
                $carousel.on('afterChange', function (event, slick, currentSlide) {
                    if (slick.currentSlide + slick.options.slidesToShow >= slick.slideCount) {
                        if ($carousel.data('loading-more') === true) return;

                        // Detectamos dinámicamente si esta fila es 'movie' o 'tv'
                        let endpoint = $carousel.attr('data-endpoint');
                        if (endpoint === 'movie' || endpoint === 'tv') {
                            cargarMasHorizontal($carousel);
                        }
                    }
                });
            });
        }

        function cargarMasHorizontal($carousel) {
            let params = $carousel.attr('data-params');
            let page = parseInt($carousel.attr('data-page'));
            let tipo = $carousel.attr('data-endpoint'); // 'movie' o 'tv'
            $carousel.data('loading-more', true);

            $.ajax({
                url: '<?= base_url("home/ajax-expandir-fila") ?>', // <-- RUTA HOME
                method: 'POST',
                data: { params: params, page: page, tipo: tipo, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                success: function (newHtml) {
                    if (newHtml.trim() !== "") {
                        $(newHtml).each(function () { $carousel.slick('slickAdd', this); });
                        $carousel.attr('data-page', page + 1);
                    }
                    $carousel.data('loading-more', false);
                },
                error: function () { $carousel.data('loading-more', false); }
            });
        }

        // Init
        setTimeout(function () {
            $('#loading-initial').fadeOut(500, function () {
                $('.content').css('display', 'block').animate({ opacity: 1 }, 500);
                cargarSiguienteBloque();
            });
        }, 600);

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
                url: '<?= base_url("home/ajax-fila") ?>', // <-- RUTA HOME
                method: 'POST',
                data: { bloque: bloqueActual, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                success: function (html) {
                    cargandoBloque = false;
                    $('#spinner-scroll').hide();
                    if (html.trim() === "" && !html.includes("sync-data")) { hayMasBloques = false; return; }
                    $('#rows-container').append(html);
                    $('.category-row').css('opacity', 1);
                    inicializarCarruseles();
                    bloqueActual++;
                    if ($(document).height() <= $(window).height() + 100) cargarSiguienteBloque();
                },
                error: function () { cargandoBloque = false; $('#spinner-scroll').hide(); }
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

    .desc-clamp {
        font-size: 0.75rem;
        color: #ccc;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .hero-static {
            margin: 1rem 2%;
            height: 55vh;
        }
    }
</style>