<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'LaButaca TV') ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>"> 
    
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">
</head>

<body>
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    <div id="dynamic-bg"></div>

    <header class="neon-header">
        <a href="<?= base_url() ?>" class="logo-text">
            <img src="<?= base_url('labutaca2_logo.ico') ?>" alt="LaButaca Logo"
                style="width:30px; height:30px; vertical-align:middle; margin-right:8px;">
            LA BUTACA
        </a>

        <nav class="nav-center">
            <?php $uri = uri_string(); ?>
            <a href="<?= base_url() ?>" class="nav-link <?= ($uri == '' || $uri == '/') ? 'active' : '' ?>">Inicio</a>
            <a href="#" class="nav-link">Películas</a>
            <a href="#" class="nav-link">Series</a>
        </nav>

        <div style="display:flex; align-items:center; gap: 10px;">
            
            <div class="category-menu-wrapper">
                <button class="btn-grid-menu">
                    <i class="fa fa-th-large"></i>
                </button>

                <div class="mega-menu">
                    <div class="mega-column">
                        <h4>GÉNEROS</h4>
                        <div class="genre-grid">
                            <?php if (!empty($generos)): ?>
                                <?php foreach ($generos as $g): ?>
                                    <a href="<?= base_url('?genero=' . $g['id']) ?>" class="genre-link">
                                        <?= esc($g['nombre']) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Sin géneros</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mega-column border-left">
                        <h4>EXPLORAR</h4>
                        <ul class="featured-list">
                            <li><a href="<?= base_url('?sort=novedades') ?>">Novedades</a></li>
                            <li><a href="<?= base_url('?calidad=4k') ?>">Cine 4K UHD</a></li>
                            <li><a href="<?= base_url('?sort=vistas') ?>">Más Vistas</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="search-wrapper">
                <i class="fa fa-search search-icon"></i>
                <input type="text" id="global-search" class="search-input-neon" placeholder="Buscar...">
            </div>

            <a href="<?= base_url('mi-lista') ?>"
                class="header-icon-btn <?= (strpos($uri, 'mi-lista') !== false) ? 'active' : '' ?>" title="Mi Lista">
                <i class="fa fa-heart"></i>
            </a>

            <?php if (session()->get('is_logged_in')): ?>
                
                <div class="profile-menu-wrapper">
                    <div class="avatar-trigger">
                        <img src="<?= base_url('assets/img/avatars/' . session()->get('avatar')) ?>"
                             style="width:40px; height:40px; border-radius:50%; border:2px solid var(--accent); object-fit:cover;">
                    </div>

                    <div class="profile-dropdown">
                        
                        <div class="profile-column border-right">
                            <h4>MI CUENTA</h4>
                            <ul class="profile-links">
                                <li><a href="#">Cuenta y configuración</a></li>
                                <li><a href="#">Ayuda</a></li>
                                <li><a href="#" onclick="logout()" style="color: #ff4757;">Cerrar sesión</a></li>
                            </ul>
                        </div>

                        <div class="profile-column">
                            <h4>PERFILES</h4>
                            
                            <div class="profiles-list">
                                <?php if (!empty($otrosPerfiles)): ?>
                                    <?php foreach ($otrosPerfiles as $p): ?>
                                        <div class="mini-profile-item" onclick="attemptLogin(<?= $p['id'] ?>, '<?= $p['username'] ?>', <?= $p['plan_id'] ?>)">
                                            <img src="https://ui-avatars.com/api/?name=<?= $p['username'] ?>&background=random&color=fff" alt="<?= esc($p['username']) ?>">
                                            <span><?= esc($p['username']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <div class="mini-profile-item" onclick="alert('Funcionalidad próximamente...')">
                                    <div class="add-profile-icon"><i class="fa fa-plus"></i></div>
                                    <span>Añadir perfil</span>
                                </div>
                                
                                <div class="mini-profile-item" onclick="alert('Editar perfiles')">
                                    <div class="edit-profile-icon"><i class="fa fa-pencil"></i></div>
                                    <span>Editar perfiles</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            <?php else: ?>
                <a href="#" onclick="openLogin(null, 'Usuario')" style="color:white; font-weight:bold; text-decoration:none;">Entrar</a>
            <?php endif; ?>
        </div>
    </header>