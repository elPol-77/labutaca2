<section id="view-splash" class="view-section active">
    <div class="splash-logo">LA BUTACA</div>
    <div style="width: 200px;">
        <div class="loader-line"></div>
    </div>
</section>

<section id="view-home" class="view-section">

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background: rgba(229, 9, 20, 0.9); color: white; padding: 15px; border-radius: 10px; margin: 20px 4%; display: flex; align-items: center; gap: 10px;">
            <i class="fa fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <main class="content">

        <?php if (!empty($carrusel) && (!isset($mostrarHero) || $mostrarHero === true)): ?>
            <div class="hero-carousel">
                <?php foreach ($carrusel as $c): ?>
                    <div class="hero-item" style="background-image: url('<?= $c['imagen_bg'] ?>');">
                        <div class="hero-content-wrapper">
                            <div class="hero-info">
                                <h1><?= esc($c['titulo']) ?></h1>
                                <div class="hero-badges">
                                    <span class="badge badge-hd">4K UHD</span>
                                    <span class="badge badge-age">+<?= $c['edad_recomendada'] ?></span>
                                </div>
                                <p style="color:#ddd; margin-bottom:20px; font-size:1.1rem; max-width:500px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= esc($c['descripcion']) ?>
                                </p>
                                <div style="display:flex; gap:15px;">
                                    <button class="btn-primary" onclick="playCinematic('<?= base_url('ver/' . $c['id']) ?>')">
                                        <i class="fa fa-play"></i> Ver Ahora
                                    </button>
                                    <button class="btn-secondary" onclick="toggleMiLista(<?= $c['id'] ?>)" 
                                            style="background:rgba(255,255,255,0.2); border:none; color:white; padding:12px 25px; border-radius:50px; cursor:pointer; font-weight:bold; display:flex; align-items:center; gap:8px;">
                                        <i class="fa <?= $c['en_mi_lista'] ? 'fa-check' : 'fa-plus' ?>"></i> Mi Lista
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($categoria) && $categoria != 'Tendencias' && $categoria != ''): ?>
            <h3 class="section-title" style="margin-top: 30px;">
                <?php if ($categoria == 'Mi Lista'): ?>
                    <i class="fa fa-heart" style="color:#ff4757; margin-right:10px;"></i> Mi Lista Personal
                <?php else: ?>
                    <?= esc($categoria) ?>
                <?php endif; ?>
            </h3>
        <?php endif; ?>

        <?php if (isset($secciones) && !empty($secciones)): ?>
            
            <div class="netflix-container" style="padding-bottom: 50px; margin-top: -30px; position: relative; z-index: 10;">
                <?php foreach ($secciones as $index => $seccion): ?>
                    <?php if(!empty($seccion['data'])): ?>
                        
                        <div class="category-row" style="margin-bottom: 40px; padding-left: 4%;">
                            <h3 class="row-title" style="color:white; font-size:1.4rem; margin-bottom:15px; font-weight:600;">
                                <?= esc($seccion['titulo']) ?>
                            </h3>

                            <div class="slick-row" id="row-<?= $index ?>">
                                <?php foreach ($seccion['data'] as $m): ?>
                                    <div class="slick-slide-item" style="padding: 0 5px;">
                                        <div class="movie-card">
                                            <div class="poster-visible">
                                                <img src="<?= $m['imagen'] ?>" alt="<?= esc($m['titulo']) ?>">
                                            </div>
                                            
                                            <div class="hover-details-card">
                                                <div class="hover-backdrop" style="background-image: url('<?= $m['imagen_bg'] ?>'); cursor: pointer;" 
                                                     onclick="window.location.href='<?= base_url('detalle/' . $m['id']) ?>'">
                                                </div>
                                                <div class="hover-info">
                                                    <div class="hover-buttons">
                                                        <button class="btn-mini-play" onclick="playCinematic('<?= base_url('ver/' . $m['id']) ?>')"><i class="fa fa-play"></i></button>
                                                        <button class="btn-mini-icon btn-lista-<?= $m['id'] ?>" 
                                                                onclick="toggleMiLista(<?= $m['id'] ?>)" 
                                                                style="<?= $m['en_mi_lista'] ? 'border-color: var(--accent); color: var(--accent);' : '' ?>">
                                                            <i class="fa <?= $m['en_mi_lista'] ? 'fa-check' : 'fa-heart' ?>"></i>
                                                        </button>
                                                    </div>
                                                    <h4 style="cursor:pointer;" onclick="window.location.href='<?= base_url('detalle/' . $m['id']) ?>'"><?= esc($m['titulo']) ?></h4>
                                                    <div class="hover-meta">
                                                        <span style="color:#46d369; font-weight:bold;">98% para ti</span>
                                                        <span class="badge badge-hd">+<?= $m['edad_recomendada'] ?></span>
                                                    </div>
                                                    <p style="font-size:0.75rem; color:#ccc; margin:0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                        <?= esc($m['descripcion']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    $('.slick-row').slick({
                        dots: false, infinite: true, speed: 500,
                        slidesToShow: 6, slidesToScroll: 3,
                        responsive: [
                            { breakpoint: 1600, settings: { slidesToShow: 5, slidesToScroll: 2 } },
                            { breakpoint: 1100, settings: { slidesToShow: 4, slidesToScroll: 2 } },
                            { breakpoint: 800, settings: { slidesToShow: 3, slidesToScroll: 1 } },
                            { breakpoint: 500, settings: { slidesToShow: 2, slidesToScroll: 1 } }
                        ],
                        prevArrow: '<button type="button" class="slick-prev custom-arrow"><i class="fa fa-chevron-left"></i></button>',
                        nextArrow: '<button type="button" class="slick-next custom-arrow"><i class="fa fa-chevron-right"></i></button>'
                    });
                });
            </script>

        <?php else: ?>
            <div class="movie-grid" id="grid-container"></div>
        <?php endif; ?>

    </main>
</section>