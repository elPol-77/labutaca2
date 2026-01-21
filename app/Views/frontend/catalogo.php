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
            <div class="hero-card">
                <div class="hero-info">
                    <div class="hero-badges">
                        <span class="badge badge-premium"><i class="fa fa-crown"></i> Premium</span>
                        <span class="badge badge-hd">4K Ultra HD</span>
                    </div>
                    <h1>Dune: Parte Dos</h1>
                    <p style="max-width: 500px; color: #ddd; margin-bottom: 2rem;">Paul Atreides se une a Chani y a los
                        Fremen mientras busca venganza.</p>

                    <button class="btn-action btn-primary" onclick="playCinematic('<?= base_url('detalle/1') ?>')">
                        <i class="fa fa-play"></i> Ver Ahora
                    </button>
                </div>
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