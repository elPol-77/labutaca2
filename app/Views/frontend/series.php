<section id="view-series-full" class="view-section active">
    
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

<div id="loading-initial" class="active" style="position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: #0f0c29; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        
        <div class="camera-loader" style="transform: scale(1.5); margin-bottom: 20px;">
            <div class="reels-container" style="display: flex; gap: 15px; justify-content: center; margin-bottom: -15px; position: relative; z-index: 2;">
                <div class="reel" style="width: 40px; height: 40px; border: 4px solid #333; border-radius: 50%; position: relative; animation: spinReel 2s linear infinite; background: #1a1a1a;"></div>
                <div class="reel" style="width: 40px; height: 40px; border: 4px solid #333; border-radius: 50%; position: relative; animation: spinReel 2s linear infinite; background: #1a1a1a;"></div>
            </div>

            <div class="camera-body" style="width: 100px; height: 60px; background: #222; border-radius: 8px; position: relative; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                <div class="lens" style="width: 50px; height: 50px; background: #111; border-radius: 50%; border: 3px solid #444; position: relative; overflow: hidden; box-shadow: inset 0 0 10px rgba(0,0,0,0.8);">
                    <div class="lens-reflection" style="width: 20px; height: 20px; background: rgba(255,255,255,0.1); border-radius: 50%; position: absolute; top: 5px; left: 5px;"></div>
                </div>
            </div>
        </div>

        <p class="loading-text" style="color: #fff; font-family: 'Outfit', sans-serif; letter-spacing: 3px; font-weight: bold; margin-bottom: 15px;">CARGANDO...</p>
        
        <div class="loader-line-container" style="width: 200px; background: #333; height: 4px; border-radius: 2px; overflow: hidden;">
            <div class="loader-line" style="width: 0%; height: 100%; background: #e50914; transition: width 0.5s ease-out; box-shadow: 0 0 10px #e50914;"></div>
        </div>

    </div>

    <main class="content" style="display:none; opacity:0; transition:opacity 0.5s;">

        <div id="hero-wrapper">
            <?php if (!empty($destacada)): ?>
                <div class="hero-carousel">
                    <div class="hero-item" style="background-image: url('<?= $destacada['imagen_bg'] ?>');">
                        <div class="hero-content-wrapper">
                            <div class="hero-info">
                                <h1><?= esc($destacada['titulo']) ?></h1>
                                <div class="hero-badges">
                                    <span class="badge badge-premium">SERIE</span>
                                    <span class="badge badge-hd">HD</span>
                                    <span class="badge badge-age">+<?= $destacada['edad_recomendada'] ?? '12' ?></span>
                                </div>
                                <p style="color:#ddd; margin-bottom:20px; font-size:1.1rem; line-height:1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= esc($destacada['descripcion']) ?>
                                </p>
                                <div style="display:flex; gap:15px;">
                                    <button class="btn-primary" onclick="playCinematic('<?= base_url('ver/' . $destacada['id']) ?>')"><i class="fa fa-play"></i> Ver</button>
                                    
                                    <?php $enLista = $destacada['en_mi_lista'] ?? false; ?>
                                    <button class="btn-secondary btn-lista-<?= $destacada['id'] ?>" onclick="toggleMiLista('<?= $destacada['id'] ?>')" 
                                        style="background:rgba(255,255,255,0.2); border:none; color:white; padding:12px 25px; border-radius:50px; cursor:pointer; font-weight:bold; display:flex; align-items:center; gap:8px;">
                                        <i class="fa <?= $enLista ? 'fa-check' : 'fa-plus' ?>"></i> Mi Lista
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div id="rows-container" class="netflix-container" style="padding-bottom: 50px; margin-top: -100px; position:relative; z-index:10;"></div>

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
    $(document).ready(function(){
        
        let bloqueActual = 0;
        let cargandoBloque = false; // Semáforo para filas verticales
        let hayMasBloques = true;

        // CONFIGURACIÓN SLICK
        // IMPORTANTE: 'infinite: false' es necesario para detectar el final y cargar más
        const slickSettings = {
            dots: false, 
            infinite: false, 
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

        // --- FUNCIÓN MAESTRA: INICIALIZA LOS CARRUSELES Y LOS "VIGILA" ---
        function inicializarCarruseles() {
            // Seleccionamos solo los que NO se han iniciado aún
            $('.slick-carousel-ajax:not(.slick-initialized)').each(function() {
                let $carousel = $(this);
                
                // 1. Iniciamos Slick
                $carousel.slick(slickSettings);

                // 2. Añadimos el "Vigilante" (Evento afterChange)
                $carousel.on('afterChange', function(event, slick, currentSlide) {
                    
                    // Comprobamos si el usuario ha llegado al final derecho del carrusel
                    // (Slide actual + slides visibles >= total slides)
                    if (slick.currentSlide + slick.options.slidesToShow >= slick.slideCount) {
                        
                        // Evitar doble carga si ya está pidiendo
                        if ($carousel.data('loading-more') === true) return;

                        let endpoint = $carousel.attr('data-endpoint');
                        
                        // Solo expandimos si es TMDB (Local ya carga 50 de golpe)
                        if (endpoint === 'tmdb') {
                            cargarMasSeriesEnHorizontal($carousel);
                        }
                    }
                });
            });
        }

        // --- FUNCIÓN PARA PEDIR MÁS SERIES (FLECHA DERECHA) ---
        function cargarMasSeriesEnHorizontal($carousel) {
            let params = $carousel.attr('data-params');
            let page = parseInt($carousel.attr('data-page'));
            let tipo = $carousel.attr('data-endpoint');

            console.log("➡️ Pidiendo más series... Página actual: " + page);
            $carousel.data('loading-more', true); // Bloqueamos para no repetir

            $.ajax({
                url: '<?= base_url("serie/ajax-expandir-fila") ?>',
                method: 'POST',
                data: { 
                    params: params, 
                    page: page, 
                    tipo: tipo,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>' 
                },
                success: function(newHtml) {
                    if (newHtml.trim() !== "") {
                        
                        // --- CORRECCIÓN CLAVE ---
                        // Convertimos el texto HTML en elementos reales de jQuery
                        var $nuevosElementos = $(newHtml);
                        
                        // Añadimos cada tarjeta COMO UNA DIAPOSITIVA INDIVIDUAL
                        $nuevosElementos.each(function() {
                            $carousel.slick('slickAdd', this);
                        });
                        
                        // Actualizamos el contador de página (+2 porque cargamos 2 de golpe)
                        $carousel.attr('data-page', page + 2);
                        console.log("✅ Cargadas 40 series nuevas. Próxima página: " + (page + 2));
                        
                    } else {
                        console.log("⛔ Se acabó. No hay más contenido en esta categoría.");
                    }
                    $carousel.data('loading-more', false); 
                }, // Desbloqueamos

     
                error: function() {
                    console.log("❌ Error al cargar más series.");
                    $carousel.data('loading-more', false);
                }
            });
        }

        // --- CARGA INICIAL (SCROLL VERTICAL) ---
        setTimeout(function() {
            $('#loading-initial').fadeOut(500, function(){
                $('.content').css('display', 'block').animate({opacity: 1}, 500);
                cargarSiguienteBloque();
            });
        }, 600);

        // Detector de Scroll Vertical
        $(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() >= $(document).height() - 400) {
                if(!cargandoBloque && hayMasBloques) cargarSiguienteBloque();
            }
        });

        function cargarSiguienteBloque() {
            if(cargandoBloque || !hayMasBloques) return;
            cargandoBloque = true;
            $('#spinner-scroll').show();

            $.ajax({
                url: '<?= base_url("serie/ajax-fila") ?>', 
                method: 'POST',
                data: { bloque: bloqueActual, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                success: function(html) {
                    cargandoBloque = false;
                    $('#spinner-scroll').hide();
                    
                    // Detectar fin
                    if(html.trim() === "" && !html.includes("sync-data")) {
                        hayMasBloques = false; return;
                    }

                    // Sincronizar saltos del servidor
                    let tempDiv = $('<div>').html(html);
                    let sync = tempDiv.find('.sync-data');
                    if (sync.length) bloqueActual += parseInt(sync.data('jump'));

                    // Pegar HTML
                    $('#rows-container').append(html);
                    
                    // EFECTO VISUAL + INICIALIZACIÓN
                    $('.category-row').css('opacity', 1);
                    
                    // ¡¡AQUÍ ESTÁ LA CLAVE!! Inicializamos los nuevos carruseles
                    inicializarCarruseles();

                    bloqueActual++;

                    // Auto-relleno si la pantalla es gigante
                    if ($(document).height() <= $(window).height() + 100) {
                        cargarSiguienteBloque();
                    }
                },
                error: function() { 
                    cargandoBloque = false; 
                    $('#spinner-scroll').hide();
                }
            });
        }
    });
</script>

<style>
    /* Estilos V3 */
    .slick-list { overflow: visible !important; padding-top: 40px; padding-bottom: 40px; }
    .slick-slide { z-index: 1; transition: z-index 0.2s; }
    .slick-slide:hover { z-index: 1000; }
    
    .custom-arrow {
        width: 40px; height: 100%; z-index: 50; background: rgba(0,0,0,0.5); border:none; color:white;
        position:absolute; top:0; display:flex; align-items:center; justify-content:center; opacity:0; transition:0.3s;
        cursor: pointer; font-size: 1.5rem;
    }
    .category-row:hover .custom-arrow { opacity: 1; }
    .left-arrow { left: 0; border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
    .right-arrow { right: 0; border-top-left-radius: 4px; border-bottom-left-radius: 4px; }
    .custom-arrow:hover { background: rgba(0,0,0,0.8); color: #00d2ff; }
<style>
    /* --- ESTILOS DEL LOADER DE CINE --- */
    #loading-initial {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #0f0c29;
    }

    .camera-loader {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        transform: scale(1.2); /* Hacemos la cámara un poco más grande */
        margin-bottom: 20px;
    }

    .reels-container {
        display: flex;
        gap: 12px;
        margin-bottom: -12px; /* Para que se unan al cuerpo */
        z-index: 2;
        position: relative;
    }

    .reel {
        width: 40px;
        height: 40px;
        border: 4px solid #333;
        border-radius: 50%;
        background: #1a1a1a;
        position: relative;
        animation: spinReel 3s linear infinite;
    }

    /* Rayos internos del carrete para que se vea girar */
    .reel::after, .reel::before {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        background: #333;
    }
    .reel::after { width: 100%; height: 4px; transform: translate(-50%, -50%); }
    .reel::before { height: 100%; width: 4px; transform: translate(-50%, -50%); }

    .camera-body {
        width: 110px;
        height: 65px;
        background: #222;
        border-radius: 12px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        z-index: 10;
    }

    /* Visor pequeño arriba a la izquierda */
    .camera-body::before {
        content: '';
        position: absolute;
        top: -8px; left: 15px;
        width: 20px; height: 8px;
        background: #222;
        border-radius: 4px 4px 0 0;
    }

    .lens {
        width: 55px;
        height: 55px;
        background: #111;
        border-radius: 50%;
        border: 4px solid #444;
        position: relative;
        box-shadow: inset 0 0 15px #000;
        overflow: hidden;
    }

    /* Brillo de la lente */
    .lens::after {
        content: '';
        position: absolute;
        top: 10px; right: 10px;
        width: 15px; height: 15px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .matte-box {
        /* Opcional: si quieres simular las aletas laterales, pero con lente queda bien */
        display: none; 
    }

    .loading-text {
        font-family: 'Outfit', sans-serif;
        color: white;
        letter-spacing: 4px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-top: 20px;
        opacity: 0.8;
    }

    @keyframes spinReel {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>