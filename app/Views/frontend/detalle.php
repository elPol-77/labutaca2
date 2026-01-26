<div class="detail-container" style="position:relative; min-height:100vh; padding-top:80px;">

    <div class="hero-bg" style="
        position:absolute; top:0; left:0; width:100%; height:70vh; 
        background-image: url('<?= $peli['imagen_bg'] ?>'); 
        background-size: cover; background-position: center; 
        mask-image: linear-gradient(to bottom, black 20%, transparent 100%);
        -webkit-mask-image: linear-gradient(to bottom, black 20%, transparent 100%);
        z-index:-1; opacity:0.6;">
    </div>

    <div class="container"
        style="max-width:1200px; margin:0 auto; padding:0 20px; display:flex; gap:40px; align-items:flex-start;">

        <div class="detail-poster"
            style="flex-shrink:0; width:300px; border-radius:10px; overflow:hidden; box-shadow:0 20px 50px rgba(0,0,0,0.5);">
            <?php 
                // Detectar si la imagen es local o URL externa
                $img = str_starts_with($peli['imagen'], 'http') ? $peli['imagen'] : base_url('assets/img/' . $peli['imagen']); 
            ?>
            <img src="<?= $img ?>" style="width:100%; display:block;">
        </div>

        <div class="detail-info" style="flex:1; padding-top:20px;">

            <h1 style="font-size:3.5rem; margin:0; font-family:'Playfair Display'; line-height:1;">
                <?= esc($peli['titulo']) ?>
            </h1>

            <div class="meta-data" style="margin:20px 0; display:flex; flex-wrap:wrap; gap:15px; font-size:1.1rem; color:#ccc; align-items:center;">
                
                <span><?= $peli['anio'] ?></span>
                <span>•</span>
                <span><?= $peli['duracion'] ?> min</span>
                <span>•</span>

                <?php if (isset($peli['imdb_rating']) && $peli['imdb_rating'] > 0): ?>
                    <div title="Nota Global (IMDb)" style="display:flex; align-items:center; gap:5px; border:1px solid #ffd700; padding:2px 10px; border-radius:15px;">
                        <i class="fa fa-imdb" style="color:#ffd700; font-size:1.2rem;"></i>
                        <span style="color:#ffd700; font-weight:bold;"><?= $peli['imdb_rating'] ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($peli['nota_usuarios']) && $peli['nota_usuarios'] > 0): ?>
                    <div title="Nota de la Comunidad" style="display:flex; align-items:center; gap:5px; border:1px solid #46d369; background:rgba(70, 211, 105, 0.1); padding:2px 10px; border-radius:15px;">
                        <i class="fa fa-users" style="color:#46d369;"></i>
                        <span style="color:#46d369; font-weight:bold;"><?= $peli['nota_usuarios'] ?></span>
                    </div>
                <?php endif; ?>

                <span>•</span>

                <?php foreach ($peli['generos'] as $g): ?>
                    <span style="border:1px solid #555; padding:2px 8px; border-radius:4px; font-size:0.8rem;">
                        <?= $g['nombre'] ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <p style="font-size:1.2rem; line-height:1.6; color:#ddd; margin-bottom:30px;">
                <?= esc($peli['descripcion']) ?>
            </p>

            <div class="actions" style="display:flex; gap:20px;">

                <?php if (isset($es_externo) && $es_externo): ?>

                    <button onclick="abrirTrailer('<?= esc($peli['titulo']) ?>', '<?= $peli['anio'] ?>')"
                        class="btn-primary" style="background: linear-gradient(90deg, #ff416c 0%, #ff4b2b 100%); 
                        color: white; padding: 15px 40px; border:none; cursor:pointer;
                        border-radius:30px; font-weight:bold; font-size:1.2rem; display:flex; align-items:center; gap:10px;
                        box-shadow: 0 0 20px rgba(255, 75, 43, 0.4);">
                        <i class="fa fa-youtube-play"></i> VER TRAILER
                    </button>

                    <button disabled
                        style="background: rgba(255,255,255,0.1); color: #aaa; padding: 15px 25px; border: 1px solid rgba(255,255,255,0.2);
                        border-radius:30px; font-weight:bold; font-size:1rem; display:flex; align-items:center; gap:10px; cursor:default;">
                        <i class="fa fa-globe"></i> GLOBAL
                    </button>

                <?php elseif ($puede_ver): ?>

                    <a href="<?= base_url('ver/' . $peli['id']) ?>" class="btn-primary" style="
                        background: linear-gradient(90deg, #00d2ff 0%, #3a7bd5 100%); 
                        color: white; padding: 15px 40px; text-decoration:none; 
                        border-radius:30px; font-weight:bold; font-size:1.2rem; display:flex; align-items:center; gap:10px;
                        box-shadow: 0 0 20px rgba(0, 210, 255, 0.4);">
                        <i class="fa fa-play"></i> REPRODUCIR
                    </a>

                    <button onclick="toggleMiLista(<?= $peli['id'] ?>)" id="btn-milista"
                        class="<?= $en_lista ? 'en-lista' : '' ?>"
                        style="background:rgba(255,255,255,0.1); color:white; border:none; width:50px; height:50px; border-radius:50%; font-size:1.2rem; cursor:pointer; transition:0.3s;">
                        <i class="fa <?= $en_lista ? 'fa-check' : 'fa-plus' ?>"
                            style="<?= $en_lista ? 'color:#00d2ff' : '' ?>"></i>
                    </button>

                <?php else: ?>

                    <button disabled
                        style="background: #333; color: #777; padding: 15px 40px; border:none;
                        border-radius:30px; font-weight:bold; font-size:1.2rem; display:flex; align-items:center; gap:10px; cursor:not-allowed;">
                        <i class="fa fa-lock"></i> NECESITAS PREMIUM
                    </button>

                <?php endif; ?>

            </div>

            <?php if (!empty($director)): ?>
                <div style="margin-top: 30px; margin-bottom: 20px;">
                    <span style="color: #a0a0a0; font-weight: 600; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px;">
                        Director
                    </span>
                    <br>
                    <a href="<?= base_url('director/' . $director['id']) ?>"
                        style="color: white; text-decoration: none; font-size: 1.1rem; font-weight: 700; border-bottom: 2px solid var(--accent); padding-bottom: 2px; transition: 0.3s;"
                        onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='white'">
                        <?= esc($director['nombre']) ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="cast" style="margin-top:40px;">
                <h3 style="border-bottom:1px solid #333; padding-bottom:10px; color:#eee;">Reparto Principal</h3>
                <div style="display:flex; gap:20px; overflow-x:auto; padding-bottom:20px; padding-top:10px;">
                    <?php if (empty($peli['actores'])): ?>
                        <p style="color:#666;">No hay información de actores disponible.</p>
                    <?php else: ?>
                        <?php foreach ($peli['actores'] as $actor): ?>
                            <div style="text-align:center; min-width:90px;">
                                <div style="width:90px; height:90px; border-radius:50%; background:#222; margin-bottom:10px; overflow:hidden; border:2px solid #333;">
                                    <?php
                                    // Si no tiene foto, usamos avatar generado
                                    $fotoActor = $actor['foto'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($actor['nombre']) . '&background=random';
                                    ?>
                                    <img src="<?= $fotoActor ?>" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div style="font-size:0.85rem; font-weight:bold; color:#fff;"><?= $actor['nombre'] ?></div>
                                <div style="font-size:0.75rem; color:#888;"><?= $actor['personaje'] ?? '' ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function abrirTrailer(titulo, anio) {
        // Abre una búsqueda directa en YouTube
        const query = encodeURIComponent(titulo + " trailer español " + anio);
        window.open('https://www.youtube.com/results?search_query=' + query, '_blank');
    }
</script>