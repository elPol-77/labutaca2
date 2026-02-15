<style>
    /* --- ESTILOS GENERALES (ESCRITORIO) --- */
    .detail-container {
        position: relative;
        min-height: 100vh;
        padding-top: 80px;
        overflow-x: hidden;
        color: #fff;
    }

    .hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 85vh;
        background-image: url('<?= $peli['imagen_bg'] ?>');
        background-size: cover;
        background-position: center top;
        mask-image: linear-gradient(to bottom, black 10%, transparent 100%);
        -webkit-mask-image: linear-gradient(to bottom, black 10%, transparent 100%);
        z-index: -1;
        opacity: 0.5;
        filter: saturate(1.2) contrast(1.1);
    }

    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        display: flex;
        gap: 50px;
        align-items: flex-start;
        position: relative;
        z-index: 2;
    }

    /* PÓSTER */
    .detail-poster {
        flex-shrink: 0;
        width: 300px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .detail-poster img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* INFO */
    .detail-info {
        flex: 1;
        padding-top: 10px;
    }

    .movie-title {
        font-size: 3.5rem;
        margin: 0 0 10px 0;
        line-height: 1.1;
        font-weight: 800;
    }

    .meta-data {
        margin-bottom: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 1.1rem;
        color: #ccc;
        align-items: center;
    }

    .meta-year {
        background: rgba(255, 255, 255, 0.1);
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: bold;
        color: white;
    }

    .meta-rating {
        color: #46d369;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .meta-genre {
        border: 1px solid #555;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .description {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #ddd;
        margin-bottom: 30px;
        max-width: 800px;
    }

    /* BOTONES */
    .actions {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .btn-play {
        background: linear-gradient(90deg, #00d2ff 0%, #3a7bd5 100%);
        color: white;
        padding: 12px 40px;
        text-decoration: none;
        border-radius: 30px;
        font-weight: bold;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 0 20px rgba(0, 210, 255, 0.4);
        transition: transform 0.2s;
        border: none;
    }

    .btn-play:hover {
        transform: scale(1.05);
        color: white;
    }

    .btn-locked {
        background: #333;
        color: #777;
        padding: 12px 40px;
        border-radius: 30px;
        border: none;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: not-allowed;
    }

    .btn-list {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: 0.3s;
    }

    .btn-list:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* ACTORES */
    .cast-scroll {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding-bottom: 15px;
        scrollbar-width: thin;
    }
    
    .cast-scroll::-webkit-scrollbar {
        height: 8px;
    }
    .cast-scroll::-webkit-scrollbar-thumb {
        background: #444;
        border-radius: 4px;
    }

    .actor-card {
        text-decoration: none;
        min-width: 90px;
        text-align: center;
        transition: transform 0.2s;
        display: block;
    }

    .actor-card:hover {
        transform: scale(1.05);
    }

    .actor-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 5px auto;
        border: 2px solid #444;
    }

    .actor-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* --- RESPONSIVE (MÓVIL Y TABLET) --- */
    @media (max-width: 768px) {
        .content-wrapper {
            flex-direction: column; 
            align-items: center;
            padding-top: 20px;
            gap: 30px;
        }

        .detail-poster {
            width: 200px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .detail-info {
            text-align: center; 
            width: 100%;
        }

        .movie-title {
            font-size: 2rem; 
        }

        .meta-data {
            justify-content: center; 
            gap: 10px;
            font-size: 0.95rem;
        }

        .actions {
            justify-content: center; 
            width: 100%;
        }
        
        .btn-play {
            padding: 12px 30px; 
            font-size: 1rem;
            width: auto;
        }

        .description {
            font-size: 1rem;
            text-align: center; 
            margin-left: auto;
            margin-right: auto;
        }

        .cast-section h3 {
            text-align: left;
            margin-top: 20px;
        }
        
        .hero-bg {
            height: 60vh;
            opacity: 0.3; 
        }
    }
</style>

<div class="detail-container">

    <div class="hero-bg"></div>

    <div class="content-wrapper">

        <div class="detail-poster">
            <?php $img = str_starts_with($peli['imagen'], 'http') ? $peli['imagen'] : base_url('assets/img/' . $peli['imagen']); ?>
            <img src="<?= $img ?>" alt="<?= esc($peli['titulo']) ?>">
        </div>

        <div class="detail-info">

            <h1 class="movie-title">
                <?= esc($peli['titulo']) ?>
            </h1>

            <div class="meta-data">
                <span class="meta-year"><?= $peli['anio'] ?></span>

                <?php if ($peli['duracion'] > 0): ?>
                    <span><?= $peli['duracion'] ?> min</span> •
                <?php endif; ?>

                <?php if (isset($peli['rating']) && $peli['rating'] > 0): ?>
                    <span class="meta-rating">
                        <i class="fa fa-star"></i> <?= $peli['rating'] ?>/10
                    </span> •
                <?php endif; ?>

                <?php foreach ($peli['generos'] as $g): ?>
                    <span class="meta-genre"><?= $g['nombre'] ?></span>
                <?php endforeach; ?>
            </div>

            <div class="actions">
                <?php if ($puede_ver || (isset($es_externo) && $es_externo)): ?>
                    <a href="<?= base_url('ver/' . $peli['id']) ?>" class="btn-play">
                        <i class="fa fa-play"></i> REPRODUCIR
                    </a>

                    <button onclick="toggleMiLista('<?= $peli['id'] ?>')" id="btn-milista" class="btn-list <?= $en_lista ? 'en-lista' : '' ?>">
                        <i class="fa <?= $en_lista ? 'fa-check' : 'fa-plus' ?>" style="<?= $en_lista ? 'color:#00d2ff' : '' ?>"></i>
                    </button>

                <?php else: ?>
                    <button disabled class="btn-locked">
                        <i class="fa fa-lock"></i> BLOQUEADO
                    </button>
                <?php endif; ?>
            </div>

            <p class="description">
                <?= esc($peli['descripcion']) ?>
            </p>

            <?php $dir = $peli['director_externo'] ?? $director ?? null; ?>
            <?php if (!empty($dir)): ?>
                <div style="margin-bottom:30px; <?=  '' ?>">
                    <span style="color:#aaa;">Dirección:</span>
                    <?php
                    $linkDir = isset($dir['id']) ? base_url('persona/' . $dir['id']) : '#';
                    $styleDir = isset($dir['id']) ? "color:white; font-weight:bold; text-decoration:none; border-bottom:1px solid #00d2ff; transition:0.3s;" : "color:white; font-weight:bold;";
                    ?>
                    <a href="<?= $linkDir ?>" style="<?= $styleDir ?>">
                        <?= esc($dir['nombre']) ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!empty($peli['actores'])): ?>
                <div class="cast-section">
                    <h3 style="color:#eee; font-size:1.1rem; margin-bottom:15px; border-left:3px solid #00d2ff; padding-left:10px;">
                        Reparto
                    </h3>
                    <div class="cast-scroll">
                        <?php foreach ($peli['actores'] as $actor): ?>
                            <a href="<?= isset($actor['id']) ? base_url('persona/' . $actor['id']) : '#' ?>" class="actor-card">
                                <div class="actor-img">
                                    <img src="<?= $actor['foto'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($actor['nombre']) ?>" alt="<?= $actor['nombre'] ?>">
                                </div>
                                <div style="font-size:0.8rem; color:#fff; font-weight:bold; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:90px;">
                                    <?= $actor['nombre'] ?>
                                </div>
                                <?php if (!empty($actor['personaje'])): ?>
                                    <div style="font-size:0.7rem; color:#aaa; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:90px;">
                                        <?= $actor['personaje'] ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    // Toggle Mi Lista (Solo local)
    function toggleMiLista(id) {
        const btn = document.getElementById('btn-milista');
        const icon = btn.querySelector('i');

        if (btn.classList.contains('en-lista')) {
            btn.classList.remove('en-lista');
            icon.classList.remove('fa-check');
            icon.classList.add('fa-plus');
            icon.style.color = 'white';
        } else {
            btn.classList.add('en-lista');
            icon.classList.remove('fa-plus');
            icon.classList.add('fa-check');
            icon.style.color = '#00d2ff';
        }

        fetch('<?= base_url("api/usuario/toggle-lista") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            body: 'contenido_id=' + id
        });
    }
</script>