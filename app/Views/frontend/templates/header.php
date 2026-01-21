<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'LaButaca TV') ?></title>

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
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

        <div style="display:flex; align-items:center;">

            <div class="search-wrapper">
                <i class="fa fa-search search-icon"></i>
                <input type="text" id="global-search" class="search-input-neon" placeholder="Buscar...">
            </div>

            <a href="<?= base_url('mi-lista') ?>"
                class="header-icon-btn <?= (strpos($uri, 'mi-lista') !== false) ? 'active' : '' ?>" title="Mi Lista">
                <i class="fa fa-heart"></i>
            </a>

            <?php if (session()->get('is_logged_in')): ?>
                <div onclick="logout()" style="cursor:pointer;" title="Salir (<?= session()->get('username') ?>)">
                    <img src="<?= base_url('assets/img/avatars/' . session()->get('avatar')) ?>"
                        style="width:40px; height:40px; border-radius:50%; border:2px solid var(--accent); object-fit:cover;">
                </div>
            <?php else: ?>
                <a href="#" onclick="openLogin(null, 'Usuario')"
                    style="color:white; font-weight:bold; text-decoration:none;">Entrar</a>
            <?php endif; ?>
        </div>
    </header>