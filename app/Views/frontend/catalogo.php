<section id="view-splash" class="view-section active">
    <div class="splash-logo">LA BUTACA</div>
    <div style="width: 200px;">
        <div class="loader-line"></div>
    </div>
</section>

<section id="view-home" class="view-section">

    <?php if (session()->getFlashdata('error')): ?>
        <div
            style="background: rgba(229, 9, 20, 0.9); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <main class="content">

<?php if (!isset($mostrarHero) || $mostrarHero === true): ?>
            <div class="hero-carousel">
                <?php 
                // 1. Cogemos las 3 primeras películas del array que nos manda el controlador
                $destacadas = (isset($peliculas) && is_array($peliculas)) ? array_slice($peliculas, 0, 3) : [];

                foreach ($destacadas as $p): 
                ?>
                    <div class="hero-item" style="background-image: url('<?= $p['imagen_bg'] ?>');">
                        
                        <div class="hero-content-wrapper">
                            <div class="hero-info">
                                <div class="hero-badges">
                                    <?php if($p['nivel_acceso'] == 2): ?>
                                        <span class="badge badge-premium"><i class="fa fa-crown"></i> Premium</span>
                                    <?php endif; ?>
                                    
                                    <span class="badge badge-age">+<?= $p['edad_recomendada'] ?></span>
                                    
                                    <span class="badge badge-hd"><?= $p['anio'] ?></span>
                                    <span class="badge badge-hd">4K UHD</span>
                                </div>
                                
                                <h1><?= esc($p['titulo']) ?></h1>
                                <p><?= esc($p['descripcion']) ?></p>

                                <button class="btn-action btn-primary" onclick="playCinematic('<?= base_url('ver/'.$p['id']) ?>')">
                                    <i class="fa fa-play"></i> Ver Ahora
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        <h3 class="section-title">
            <?php if (isset($categoria) && $categoria == 'Mi Lista'): ?>
                <i class="fa fa-heart" style="color:#ff4757; margin-right:10px;"></i> Mi Lista Personal

            <?php elseif (isset($categoria)): ?>
                <?= esc($categoria) ?>

            <?php else: ?>
                Tendencias Ahora
            <?php endif; ?>
        </h3>

        <div class="movie-grid" id="grid-container"></div>

    </main>
</section>