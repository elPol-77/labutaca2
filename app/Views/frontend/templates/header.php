<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($titulo ?? 'LaButaca TV') ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />

    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">

    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

    <script>
        const BASE_URL = "<?= base_url() ?>";
    </script>

    <style>
        /* Botón móvil oculto en escritorio */
        .mobile-menu-toggle { display: none; background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; padding: 10px; }

        /* Estilo base del buscador para que la transición funcione */
        .search-input-neon {
            transition: width 0.3s ease, opacity 0.3s ease;
        }

        /* --- MEDIA QUERIES PARA MÓVIL/TABLET --- */
        @media (max-width: 992px) {
            .neon-header {
                flex-wrap: wrap; 
                padding: 10px 15px;
                justify-content: space-between;
                position: relative;
            }

            /* 1. Botón hamburguesa */
            .mobile-menu-toggle { display: block; order: 1; z-index: 1001; }

            /* 2. Logo */
            .logo-text { order: 2; font-size: 1.2rem; z-index: 1001; }
            
            /* 3. Iconos derecha (Buscador, Perfil, Grid) */
            .header-right-icons { 
                order: 3; 
                gap: 15px !important; /* Espacio entre iconos */
                position: relative; /* Necesario para posicionar el mega menú */
            }

            /* 4. Navegación Central (Menú desplegable) */
            .nav-center {
                display: none;
                flex-direction: column;
                width: 100%;
                order: 4;
                background: #080808; /* Fondo muy oscuro */
                padding: 20px 0;
                border-top: 1px solid #333;
                position: absolute;
                top: 100%; 
                left: 0;
                z-index: 999;
                box-shadow: 0 10px 20px rgba(0,0,0,0.8);
            }

            .nav-center.active { display: flex; }

            .nav-link {
                padding: 15px 0;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                text-align: center;
                width: 100%;
                font-size: 1.1rem;
            }

            /* --- CORRECCIÓN GLOBAL ICON CENTRADO --- */
            /* Seleccionamos el enlace que contiene el icono global dentro del menú móvil */
            .nav-center a[href*="global"] {
                margin: 20px auto 10px auto !important; /* Auto márgenes laterales lo centran */
                display: flex;
                align-items: center;
                justify-content: center;
                width: 50px;
                height: 50px;
                background: rgba(255,255,255,0.1);
                border-radius: 50%;
            }

            /* --- CORRECCIÓN BUSCADOR RESPONSIVE --- */
            .search-wrapper {
                position: relative; 
            }
            /* En móvil, el input está oculto hasta que se activa */
            .search-input-neon {
                display: none; 
                position: absolute;
                top: 45px; /* Debajo del icono */
                right: 0; /* Alineado a la derecha para no salirse */
                width: 220px;
                background: #1a1a1a;
                border: 1px solid #444;
                z-index: 1050;
                padding: 10px;
            }
            /* Al activar (clase active añadida con JS inline), se muestra */
            .search-wrapper.active .search-input-neon {
                display: block;
            }

            /* --- CORRECCIÓN MEGA MENU (GÉNEROS) --- */
            .category-menu-wrapper {
                position: static; /* Permite que el mega menú use el ancho del header si es necesario */
            }

            .mega-menu {
                position: absolute;
                top: 50px;
                right: -60px; /* Ajuste manual para que no se salga por la derecha */
                width: 300px; /* Ancho fijo seguro para móvil */
                max-width: 90vw;
                background: rgba(20, 20, 20, 0.95);
                backdrop-filter: blur(10px);
                flex-direction: column; /* Columnas una debajo de otra */
                border: 1px solid #333;
                border-radius: 8px;
                overflow: hidden;
            }

            /* Quitar borde lateral y añadir inferior en móvil */
            .mega-column.border-left {
                border-left: none;
                border-top: 1px solid #333;
            }

            .mega-column {
                width: 100%;
                padding: 15px;
            }

            .genre-grid {
                grid-template-columns: repeat(2, 1fr); /* 2 columnas de géneros en móvil */
                gap: 8px;
            }
            
            .genre-link {
                font-size: 0.9rem;
                padding: 5px;
            }
        }
    </style>
</head>

<body>
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    <div id="dynamic-bg"></div>

    <header class="neon-header">
        
        <button class="mobile-menu-toggle" onclick="toggleMenu()">
            <i class="fa fa-bars"></i>
        </button>

        <a href="<?= base_url() ?>" class="logo-text">
            <img src="<?= base_url('labutaca2_logo.ico') ?>" alt="LaButaca Logo"
                style="width:30px; height:30px; vertical-align:middle; margin-right:8px;">
            LA BUTACA
        </a>

        <nav class="nav-center" id="mainNav">
            <?php $uri = uri_string(); ?>
            <a href="<?= base_url() ?>" class="nav-link <?= ($uri == '' || $uri == '/') ? 'active' : '' ?>">Inicio</a>
            <a href="<?= base_url('peliculas') ?>" class="nav-link <?= ($uri == 'peliculas') ? 'active' : '' ?>">Películas</a>
            <a href="<?= base_url('series') ?>" class="nav-link <?= ($uri == 'series') ? 'active' : '' ?>">Series</a>
            
            <?php if (session()->get('plan_id') == 2): ?>
                <a href="<?= base_url('global') ?>" class="header-icon-btn" title="Zona Global" style="margin-right: 0;">
                    <i class="bi bi-globe"></i>
                </a>
            <?php endif; ?>
        </nav>

        <div class="header-right-icons" style="display:flex; align-items:center; gap: 10px;">

            <div class="category-menu-wrapper">
                <button class="btn-grid-menu" onclick="this.nextElementSibling.style.display = (this.nextElementSibling.style.display === 'flex' ? 'none' : 'flex')">
                    <i class="fa fa-th-large"></i>
                </button>

                <div class="mega-menu">
                    <div class="mega-column">
                        <h4>GÉNEROS</h4>
                       <div class="genre-grid">
                            <?php if (!empty($generos)): ?>
                                <?php foreach ($generos as $g): ?>
                                    <a href="<?= base_url('genero/' . $g['id']) ?>" class="genre-link"> 
                                        <?= esc($g['nombre']) ?> 
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="padding: 10px; color: #aaa;">Cargando géneros...</p>
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

            <div class="search-wrapper" onclick="toggleSearch(this)">
                <i class="fa fa-search search-icon"></i>
                <input type="text" id="global-search" class="search-input-neon" placeholder="Buscar..." onclick="event.stopPropagation()">
            </div>

            <a href="<?= base_url('mi-lista') ?>"
                class="header-icon-btn <?= (strpos($uri, 'mi-lista') !== false) ? 'active' : '' ?>" title="Mi Lista">
                <i class="fa fa-heart"></i>
            </a>

            <?php if (session()->get('is_logged_in')): ?>
                <?php
                $avatarSesion = session()->get('avatar');
                if (empty($avatarSesion)) {
                    $avatarUrl = 'https://upload.wikimedia.org/wikipedia/commons/0/0b/Netflix-avatar.png';
                } elseif (str_starts_with($avatarSesion, 'http')) {
                    $avatarUrl = $avatarSesion;
                } else {
                    $avatarUrl = base_url('assets/img/avatars/' . $avatarSesion);
                }
                ?>

                <div class="profile-menu-wrapper">
                    <div class="avatar-trigger">
                        <img src="<?= $avatarUrl ?>" alt="Avatar"
                            style="width:40px; height:40px; border-radius:4px; border:1px solid rgba(255,255,255,0.2); object-fit:cover;">
                    </div>
                    <div class="profile-dropdown">

                        <div class="profile-column border-right">
                            <h4>MI CUENTA</h4>
                            <ul class="profile-links">
                                <li><a href="<?= base_url('perfil') ?>">Cuenta y configuración</a></li>
                                <li><a href="<?= base_url('ayuda') ?>">Ayuda</a></li>
                                <li><a href="<?= base_url('logout') ?>" style="color: #ff4757;">Cerrar sesión</a></li>
                            </ul>
                        </div>

                        <div class="profile-column">
                            <h4>PERFILES</h4>

                            <div class="profiles-list">
                                <?php if (!empty($otrosPerfiles)): ?>
                                    <?php foreach ($otrosPerfiles as $p): ?>

                                        <?php
                                        // Lógica de foto
                                        $fotoPerfil = $p['avatar'];
                                        if (empty($fotoPerfil)) {
                                            $fotoPerfil = 'https://upload.wikimedia.org/wikipedia/commons/0/0b/Netflix-avatar.png';
                                        } elseif (!str_starts_with($fotoPerfil, 'http')) {
                                            $fotoPerfil = base_url('assets/img/avatars/' . $fotoPerfil);
                                        }
                                        ?>
                                        
                                        <div class="mini-profile-item" onclick="headerAttemptLogin(<?= $p['id'] ?>, '<?= esc($p['username']) ?>', <?= $p['plan_id'] ?>)">
                                            <img src="<?= $fotoPerfil ?>" alt="<?= esc($p['username']) ?>"
                                                style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                                            <span><?= esc($p['username']) ?></span>
                                        </div>

                                    <?php endforeach; ?>
                                <?php endif; ?>


                                <div class="mini-profile-item">
                                    <div class="edit-profile-icon"><i class="fa fa-pencil"></i></div>
                                    <a href="<?= base_url('perfil') ?>" style="text-decoration: none; color: inherit;">Editar Perfil</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <a href="<?= base_url('auth') ?>"
                   style="color:white; font-weight:bold; text-decoration:none; padding: 10px;">Entrar</a>
            <?php endif; ?>
        </div>
    </header>

    <div id="modalHeaderAuth" class="password-modal">
        <div class="modal-content">
            <h3 id="headerModalUser" style="margin-top:0; color: white;">Usuario</h3>
            <p style="color:#aaa; margin-bottom:20px;">Introduce tu PIN para cambiar</p>

            <input type="hidden" id="headerSelectedUserId">
            <input type="password" id="headerPasswordInput" class="pin-input" placeholder="••••" autocomplete="off">
            
            <p class="error-msg" id="headerErrorMsg" style="display:none; color: #ff4757; margin-top: 10px;">Contraseña incorrecta</p>

            <div style="display:flex; gap:10px; justify-content:center; margin-top:20px;">
                <button class="btn-cancel" onclick="closeHeaderModal()">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        // SCRIPT PARA MENÚ MÓVIL Y BUSCADOR
        function toggleMenu() {
            document.getElementById('mainNav').classList.toggle('active');
        }

        // Función específica para el buscador en móvil
        function toggleSearch(element) {
            // Solo activar toggle si estamos en móvil/pantalla pequeña
            if (window.innerWidth <= 992) {
                element.classList.toggle('active');
                if(element.classList.contains('active')){
                    setTimeout(() => document.getElementById('global-search').focus(), 100);
                }
            }
        }

        // --- Resto de tu lógica JS original ---
        let isSubmitting = false;

        function headerAttemptLogin(id, username, planId) {
            if(planId == 3) {
                doHeaderLogin(id, '');
            } else {
                document.getElementById('headerSelectedUserId').value = id;
                document.getElementById('headerModalUser').innerText = 'Hola ' + username;
                document.getElementById('headerPasswordInput').value = '';
                document.getElementById('headerErrorMsg').style.display = 'none';
                
                var modal = document.getElementById('modalHeaderAuth');
                modal.style.display = 'flex';
                
                setTimeout(function() {
                    document.getElementById('headerPasswordInput').focus();
                }, 100);
            }
        }

        function closeHeaderModal() {
            document.getElementById('modalHeaderAuth').style.display = 'none';
        }

        function submitHeaderLogin() {
            if (isSubmitting) return;
            let id = document.getElementById('headerSelectedUserId').value;
            let pass = document.getElementById('headerPasswordInput').value;
            doHeaderLogin(id, pass);
        }

        document.getElementById('headerPasswordInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault(); 
                submitHeaderLogin();
            }
        });

        function doHeaderLogin(id, pass) {
            isSubmitting = true;
            let csrfInput = document.querySelector('.txt_csrftoken');
            let csrfName = csrfInput.getAttribute('name');
            let csrfHash = csrfInput.value;

            let params = new URLSearchParams();
            params.append('id', id);
            params.append('password', pass);
            params.append(csrfName, csrfHash);

            fetch('<?= base_url("auth/ajax_login_perfil") ?>', {
                method: 'POST',
                body: params,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.status === 403) {
                    window.location.reload();
                    throw new Error("Token Caducado");
                }
                return response.json();
            })
            .then(data => {
                isSubmitting = false;
                if(data.token) {
                    let tokenInputs = document.querySelectorAll('.txt_csrftoken');
                    tokenInputs.forEach(input => input.value = data.token);
                }

                if(data.status === 'success') {
                    window.location.reload();
                } else {
                    let errorMsg = document.getElementById('headerErrorMsg');
                    errorMsg.innerText = data.msg || 'Contraseña incorrecta';
                    errorMsg.style.display = 'block';
                    document.getElementById('headerPasswordInput').value = '';
                    document.getElementById('headerPasswordInput').focus();
                }
            })
            .catch(error => {
                isSubmitting = false;
                if (error.message !== "Token Caducado") {
                    let errorMsg = document.getElementById('headerErrorMsg');
                    errorMsg.innerText = 'Error de conexión.';
                    errorMsg.style.display = 'block';
                }
            });
        }
    </script>
</body>
</html>