<div class="person-wrapper">
    <div class="person-ambient-bg"></div>

    <div class="person-container">
        
        <div class="person-layout">
            
            <div class="person-sidebar">
                <div class="person-avatar-container">
                    <img src="<?= $persona['foto'] ?>" alt="<?= esc($persona['nombre']) ?>" class="person-avatar">
                </div>

                <div class="person-info-card">
                    <h3 class="info-title">Información Personal</h3>
                    
                    <div class="info-item">
                        <span class="info-label">Conocido por</span>
                        <span class="info-value">
                            <?= $persona['conocido_por'] == 'Acting' ? 'Interpretación' : ($persona['conocido_por'] == 'Directing' ? 'Dirección' : $persona['conocido_por']) ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Nacimiento</span>
                        <span class="info-value"><?= $persona['fecha_nacimiento'] ?></span>
                    </div>

                    <?php if(!empty($persona['lugar_nacimiento'])): ?>
                    <div class="info-item">
                        <span class="info-label">Lugar de nacimiento</span>
                        <span class="info-value"><?= $persona['lugar_nacimiento'] ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="person-content"> 
                <h1 class="person-name"><?= esc($persona['nombre']) ?></h1>
                
                <div class="bio-section">
                    <h3 class="section-heading">Biografía</h3>
                    <div class="bio-text">
                        <?= nl2br(esc($persona['biografia'])) ?>
                    </div>
                </div>

                <?php if (!empty($acting)): ?>
                    <div class="credits-section">
                        <h3 class="section-heading">
                            Interpretación <span class="credits-count">(<?= count($acting) ?> títulos)</span>
                        </h3>
                        
                        <div class="film-grid">
                            <?php foreach ($acting as $credito): ?>
                                <?php if($credito['poster']): ?>
                                <a href="<?= base_url('detalle/' . $credito['id']) ?>" class="film-card">
                                    <div class="film-poster-container">
                                        <div class="skeleton-bg"></div>
                                        <img src="<?= $credito['poster'] ?>" alt="<?= esc($credito['titulo']) ?>" class="film-poster" loading="lazy" onload="this.classList.add('loaded')">
                                        
                                        <?php if($credito['media_type'] == 'tv'): ?>
                                            <span class="badge-tv">SERIE</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="film-details">
                                        <h4 class="film-title"><?= esc($credito['titulo']) ?></h4>
                                        <div class="film-meta">
                                            <span class="film-year"><?= $credito['anio'] ?></span>
                                            <?php if(!empty($credito['personaje'])): ?>
                                                <span class="film-character">como <?= $credito['personaje'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($directing)): ?>
                    <div class="credits-section" style="margin-top: 50px;">
                        <h3 class="section-heading" style="border-left-color: #b721ff;">
                            Dirección <span class="credits-count">(<?= count($directing) ?> títulos)</span>
                        </h3>
                        
                        <div class="film-grid">
                            <?php foreach ($directing as $credito): ?>
                                <?php if($credito['poster']): ?>
                                <a href="<?= base_url('detalle/' . $credito['id']) ?>" class="film-card">
                                    <div class="film-poster-container">
                                        <div class="skeleton-bg"></div>
                                        <img src="<?= $credito['poster'] ?>" alt="<?= esc($credito['titulo']) ?>" class="film-poster" loading="lazy" onload="this.classList.add('loaded')">
                                    </div>
                                    <div class="film-details">
                                        <h4 class="film-title"><?= esc($credito['titulo']) ?></h4>
                                        <div class="film-meta">
                                            <span class="film-year"><?= $credito['anio'] ?></span>
                                        </div>
                                    </div>
                                </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>