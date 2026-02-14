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
<section id="view-series-full" class="view-section active" style="padding-top: 100px;">
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

    <main class="content">

        <?php if (!empty($destacada)): ?>
            <div class="hero-static">
                <div class="hero-item" style="background-image: url('<?= $destacada['backdrop'] ?>');">
                    <div class="hero-content-wrapper">
                        <div class="hero-info" style="padding-left: 5%;">
                            <h1><?= esc($destacada['titulo']) ?></h1>

                            <div class="hero-badges">
                                <span class="badge badge-premium">SERIE</span>
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


        const slickSettings = {
            dots: false,
            infinite: false,
            speed: 500,
            slidesToShow: 6,
            slidesToScroll: 3,
            lazyLoad: 'ondemand',
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

        function inicializarCarruseles() {
            $('.slick-carousel-ajax:not(.slick-initialized)').each(function () {
                let $carousel = $(this);

                $carousel.slick(slickSettings);

                $carousel.on('afterChange', function (event, slick, currentSlide) {


                    if (slick.currentSlide + slick.options.slidesToShow >= slick.slideCount) {

                        if ($carousel.data('loading-more') === true) return;

                        let endpoint = $carousel.attr('data-endpoint');

                        if (endpoint === 'tmdb') {
                            cargarMasSeriesEnHorizontal($carousel);
                        }
                    }
                });
            });
        }

        function cargarMasSeriesEnHorizontal($carousel) {
            let params = $carousel.attr('data-params');
            let page = parseInt($carousel.attr('data-page'));
            let tipo = $carousel.attr('data-endpoint');

            console.log("➡️ Pidiendo más series... Página actual: " + page);
            $carousel.data('loading-more', true); 

            $.ajax({
                url: '<?= base_url("serie/ajax-expandir-fila") ?>',
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

                        // Actualizamos el contador de página (+2 porque cargamos 2 de golpe)
                        $carousel.attr('data-page', page + 2);
                        console.log("Cargadas 40 series nuevas. Próxima página: " + (page + 2));

                    } else {
                        console.log("No hay más contenido en esta categoría.");
                    }
                    $carousel.data('loading-more', false);
                }, 


                error: function () {
                    console.log("Error al cargar más series.");
                    $carousel.data('loading-more', false);
                }
            });
        }

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
                url: '<?= base_url("serie/ajax-fila") ?>',
                method: 'POST',
                data: { bloque: bloqueActual, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                success: function (html) {
                    cargandoBloque = false;
                    $('#spinner-scroll').hide();

                    // Detectar fin
                    if (html.trim() === "" && !html.includes("sync-data")) {
                        hayMasBloques = false; return;
                    }

                    let tempDiv = $('<div>').html(html);
                    let sync = tempDiv.find('.sync-data');
                    if (sync.length) bloqueActual += parseInt(sync.data('jump'));

                    $('#rows-container').append(html);

                    $('.category-row').css('opacity', 1);

                    inicializarCarruseles();

                    bloqueActual++;

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
