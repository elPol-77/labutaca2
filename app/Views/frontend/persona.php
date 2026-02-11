<div class="person-container" style="background-color: #141414; min-height: 100vh; padding-top: 100px; padding-bottom: 50px; color: white;">
    
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        
        <div class="person-layout" style="display: flex; gap: 50px; flex-wrap: wrap;">
            
            <div class="person-sidebar" style="flex: 0 0 300px;">
                <div style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); margin-bottom: 25px;">
                    <img src="<?= $persona['foto'] ?>" alt="<?= esc($persona['nombre']) ?>" style="width: 100%; display: block;">
                </div>

                <h3 style="color: white; border-bottom: 1px solid #333; padding-bottom: 10px; margin-bottom: 15px;">Información</h3>
                
                <div style="margin-bottom: 15px;">
                    <strong style="display: block; color: #aaa; font-size: 0.9rem;">Conocido por</strong>
                    <span><?= $persona['conocido_por'] == 'Acting' ? 'Interpretación' : ($persona['conocido_por'] == 'Directing' ? 'Dirección' : $persona['conocido_por']) ?></span>
                </div>

                <div style="margin-bottom: 15px;">
                    <strong style="display: block; color: #aaa; font-size: 0.9rem;">Fecha de nacimiento</strong>
                    <span><?= $persona['fecha_nacimiento'] ?></span>
                </div>

                <?php if(!empty($persona['lugar_nacimiento'])): ?>
                <div style="margin-bottom: 15px;">
                    <strong style="display: block; color: #aaa; font-size: 0.9rem;">Lugar de nacimiento</strong>
                    <span><?= $persona['lugar_nacimiento'] ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="person-content" style="flex: 1; min-width: 0;"> <h1 style="font-size: 3rem; margin: 0 0 20px 0; font-family: 'Playfair Display';"><?= esc($persona['nombre']) ?></h1>
                
                <div class="bio-section" style="margin-bottom: 40px;">
                    <h3 style="color: #e50914; margin-bottom: 10px;">Biografía</h3>
                    <p style="color: #ddd; line-height: 1.6; font-size: 1.1rem;">
                        <?= nl2br(esc($persona['biografia'])) ?>
                    </p>
                </div>

                <?php if (!empty($acting)): ?>
                    <h3 style="border-left: 4px solid #e50914; padding-left: 15px; margin-bottom: 20px; font-size: 1.5rem;">
                        Interpretación <span style="font-size: 1rem; color: #777;">(<?= count($acting) ?> títulos)</span>
                    </h3>
                    
                    <div class="film-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 20px; margin-bottom: 50px;">
                        <?php foreach ($acting as $credito): ?>
                            <?php if($credito['poster']): // Solo mostrar si tiene poster para que quede bonito ?>
                            <a href="<?= base_url('detalle/' . $credito['id']) ?>" class="film-card" style="text-decoration: none; color: white; transition: transform 0.2s; display: block;">
                                <div style="aspect-ratio: 2/3; border-radius: 8px; overflow: hidden; margin-bottom: 10px; position: relative; border: 1px solid #333;">
                                    <img src="<?= $credito['poster'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php if($credito['media_type'] == 'tv'): ?>
                                        <span style="position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.8); color: #e50914; font-size: 0.7rem; font-weight: bold; padding: 2px 4px; border-radius: 3px;">SERIE</span>
                                    <?php endif; ?>
                                </div>
                                <h4 style="margin: 0; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($credito['titulo']) ?></h4>
                                <div style="font-size: 0.85rem; color: #aaa; margin-top: 2px;">
                                    <?= $credito['anio'] ?> 
                                    <?php if(!empty($credito['personaje'])): ?>
                                        <span style="color: #666;">como <?= $credito['personaje'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($directing)): ?>
                    <h3 style="border-left: 4px solid #00d2ff; padding-left: 15px; margin-bottom: 20px; font-size: 1.5rem;">
                        Dirección <span style="font-size: 1rem; color: #777;">(<?= count($directing) ?> títulos)</span>
                    </h3>
                    
                    <div class="film-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 20px;">
                        <?php foreach ($directing as $credito): ?>
                            <?php if($credito['poster']): ?>
                            <a href="<?= base_url('detalle/' . $credito['id']) ?>" class="film-card" style="text-decoration: none; color: white; transition: transform 0.2s; display: block;">
                                <div style="aspect-ratio: 2/3; border-radius: 8px; overflow: hidden; margin-bottom: 10px; position: relative; border: 1px solid #333;">
                                    <img src="<?= $credito['poster'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <h4 style="margin: 0; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($credito['titulo']) ?></h4>
                                <div style="font-size: 0.85rem; color: #aaa; margin-top: 2px;">
                                    <?= $credito['anio'] ?>
                                </div>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<script>
    // Efecto Hover simple
    document.querySelectorAll('.film-card').forEach(card => {
        card.addEventListener('mouseenter', () => card.style.transform = 'scale(1.05)');
        card.addEventListener('mouseleave', () => card.style.transform = 'scale(1)');
    });
</script>

<style>
    @media (max-width: 768px) {
        .person-layout { flex-direction: column; }
        .person-sidebar { flex: auto; width: 100%; text-align: center; }
        .person-sidebar img { max-width: 200px; margin: 0 auto; }
    }
</style>