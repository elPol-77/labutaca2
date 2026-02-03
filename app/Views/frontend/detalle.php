<div class="detail-container" style="position:relative; min-height:100vh; padding-top:80px; overflow-x:hidden;">

    <div class="hero-bg" style="
        position:absolute; top:0; left:0; width:100%; height:80vh; 
        background-image: url('<?= $peli['imagen_bg'] ?>'); 
        background-size: cover; background-position: center top; 
        mask-image: linear-gradient(to bottom, black 10%, transparent 100%);
        -webkit-mask-image: linear-gradient(to bottom, black 10%, transparent 100%);
        z-index:-1; opacity:0.5; filter: saturate(1.2) contrast(1.1);">
    </div>

    <div class="container" style="max-width:1200px; margin:0 auto; padding:40px 20px; display:flex; gap:50px; align-items:flex-start; position:relative; z-index:2;">

        <div class="detail-poster" style="flex-shrink:0; width:320px; border-radius:12px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.6); border:1px solid rgba(255,255,255,0.1);">
            <?php $img = str_starts_with($peli['imagen'], 'http') ? $peli['imagen'] : base_url('assets/img/' . $peli['imagen']); ?>
            <img src="<?= $img ?>" style="width:100%; display:block;">
        </div>

        <div class="detail-info" style="flex:1; padding-top:10px;">

            <h1 style="font-size:3.5rem; margin:0 0 10px 0; font-family:'Playfair Display'; line-height:1.1;">
                <?= esc($peli['titulo']) ?>
            </h1>

            <div class="meta-data" style="margin-bottom:25px; display:flex; flex-wrap:wrap; gap:15px; font-size:1.1rem; color:#ccc; align-items:center;">
                
                <span style="background:rgba(255,255,255,0.1); padding:2px 8px; border-radius:4px; font-weight:bold; color:white;">
                    <?= $peli['anio'] ?>
                </span>

                <?php if($peli['duracion'] > 0): ?>
                    <span><?= $peli['duracion'] ?> min</span> •
                <?php endif; ?>

                <?php if (isset($peli['rating']) && $peli['rating'] > 0): ?>
                    <span style="color:#46d369; font-weight:bold; display:flex; align-items:center; gap:5px;">
                        <i class="fa fa-star"></i> <?= $peli['rating'] ?>/10
                    </span> •
                <?php endif; ?>

                <?php foreach ($peli['generos'] as $g): ?>
                    <span style="border:1px solid #555; padding:2px 8px; border-radius:4px; font-size:0.9rem;">
                        <?= $g['nombre'] ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <div class="actions" style="display:flex; gap:20px; margin-bottom:30px;">
                
                <?php 
                    $btnStyle = "background: linear-gradient(90deg, #00d2ff 0%, #3a7bd5 100%); color: white; padding: 12px 40px; text-decoration:none; border-radius:30px; font-weight:bold; font-size:1.2rem; display:flex; align-items:center; gap:10px; box-shadow: 0 0 20px rgba(0, 210, 255, 0.4); transition: transform 0.2s;";
                ?>

                <?php if ($puede_ver || (isset($es_externo) && $es_externo)): ?>
                    <a href="<?= base_url('ver/' . $peli['id']) ?>" style="<?= $btnStyle ?>" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="fa fa-play"></i> REPRODUCIR
                    </a>

                    <?php if (!isset($es_externo) || !$es_externo): ?>
                        <button onclick="toggleMiLista(<?= $peli['id'] ?>)" id="btn-milista" class="<?= $en_lista ? 'en-lista' : '' ?>" style="background:rgba(255,255,255,0.1); color:white; border:none; width:50px; height:50px; border-radius:50%; font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                            <i class="fa <?= $en_lista ? 'fa-check' : 'fa-plus' ?>" style="<?= $en_lista ? 'color:#00d2ff' : '' ?>"></i>
                        </button>
                    <?php endif; ?>

                <?php else: ?>
                    <button disabled style="background:#333; color:#777; padding:12px 40px; border-radius:30px; border:none; font-weight:bold;">
                        <i class="fa fa-lock"></i> BLOQUEADO
                    </button>
                <?php endif; ?>
            </div>

            <p style="font-size:1.1rem; line-height:1.6; color:#ddd; margin-bottom:30px; max-width:800px;">
                <?= esc($peli['descripcion']) ?>
            </p>

            <?php 
                // Unificamos lógica: Puede venir de TMDB (dentro de $peli) o Local ($director)
                $dir = $peli['director_externo'] ?? $director ?? null;
            ?>
            <?php if (!empty($dir)): ?>
                <div style="margin-bottom:30px;">
                    <span style="color:#aaa;">Dirección:</span> 
                    <?php 
                        // Si tiene ID (tmdb_person_XXX), creamos enlace. Si no, solo texto.
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
                <h3 style="color:#eee; font-size:1.1rem; margin-bottom:15px; border-left:3px solid #00d2ff; padding-left:10px;">Reparto</h3>
                <div style="display:flex; gap:15px; overflow-x:auto; padding-bottom:10px; scrollbar-width: thin;">
                    <?php foreach ($peli['actores'] as $actor): ?>
                        
                        <a href="<?= isset($actor['id']) ? base_url('persona/' . $actor['id']) : '#' ?>" 
                           style="text-decoration:none; min-width:90px; text-align:center; transition:transform 0.2s; display:block;"
                           onmouseover="this.style.transform='scale(1.05)'" 
                           onmouseout="this.style.transform='scale(1)'">
                            
                            <div style="width:80px; height:80px; border-radius:50%; overflow:hidden; margin:0 auto 5px auto; border:2px solid #444;">
                                <img src="<?= $actor['foto'] ?? 'https://ui-avatars.com/api/?name='.urlencode($actor['nombre']) ?>" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <div style="font-size:0.8rem; color:#fff; font-weight:bold; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:90px;"><?= $actor['nombre'] ?></div>
                            <?php if(!empty($actor['personaje'])): ?>
                                <div style="font-size:0.7rem; color:#aaa; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:90px;"><?= $actor['personaje'] ?></div>
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