<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>¿Quién eres? - La Butaca</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

    <style>
        .profile-avatar {
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #333;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ESTILOS DEL NUEVO MODAL (Solo para el botón +) */
        .general-modal {
            display: none;
            position: fixed;
            z-index: 3000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }

        .general-modal-content {
            background: rgba(20, 20, 20, 0.95);
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            position: relative;
            animation: fadeIn 0.3s;
        }

        .general-input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background: #333;
            border: none;
            border-radius: 4px;
            color: white;
            font-family: 'Outfit', sans-serif;
        }

        .general-input:focus {
            outline: 2px solid #e50914;
            background: #444;
        }

        .btn-general {
            width: 100%;
            padding: 12px;
            background: #e50914;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            font-family: 'Outfit', sans-serif;
        }

        .btn-general:hover {
            background: #f40612;
        }

        .close-general {
            position: absolute;
            top: 15px;
            right: 20px;
            color: #aaa;
            font-size: 24px;
            cursor: pointer;
        }

        .toggle-link {
            color: #ccc;
            font-size: 0.9rem;
            margin-top: 15px;
            cursor: pointer;
            text-decoration: underline;
        }

        .alert-error {
            background: #e87c03;
            color: white;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: none;
            text-align: left;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>

    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <section id="view-profiles" class="view-section active" style="display: flex;">
        <div class="glass-panel">
            <h1 style="font-family: 'Outfit'; margin-bottom: 10px;">¿Quién eres?</h1>

            <div class="profile-container">

                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $user): ?>
                        <?php
                        $avatar = $user['avatar'];
                        if (empty($avatar))
                            $avatar = 'https://upload.wikimedia.org/wikipedia/commons/0/0b/Netflix-avatar.png';
                        elseif (!str_starts_with($avatar, 'http'))
                            $avatar = base_url('assets/img/avatars/' . $avatar);
                        ?>

                        <div class="profile-item"
                            onclick="attemptLogin(<?= $user['id'] ?>, '<?= esc($user['username']) ?>', <?= $user['plan_id'] ?>)">
                            <div class="profile-avatar"><img src="<?= $avatar ?>" alt="<?= esc($user['username']) ?>"></div>
                            <span><?= esc($user['username']) ?></span>
                            <small
                                style="color:#aaa; display:block; text-transform:uppercase; font-size:0.7rem; margin-top:5px;">
                                <?= match ($user['plan_id'] ?? '1') { '2' => 'Premium', '3' => 'Kids', default => 'Free'} ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="profile-item" onclick="openGeneralModal()">
                    <div class="profile-avatar"
                        style="border: 2px dashed #666; overflow: hidden; background: rgba(0,0,0,0.5);">
                        <i class="fa fa-plus" style="font-size: 3rem; color: #888;"></i>
                    </div>
                    <span>Añadir Perfil</span>
                </div>
            </div>

            <div
                style="margin-top: 30px; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                <a href="<?= base_url('admin/login') ?>"
                    style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.85rem; font-family: 'Outfit'; transition: 0.3s;">
                    <i class="fa fa-lock"></i> Acceso Administración
                </a>
            </div>
        </div>
    </section>


    <div class="password-modal" id="modalAuth">
        <div class="modal-content">
            <h3 id="modalUser" style="margin-top:0; color: white;">Usuario</h3>
            <p style="color:#aaa; margin-bottom:20px;">Introduce tu PIN</p>

            <input type="hidden" id="selectedUserId">
            <input type="password" id="passwordInput" class="pin-input" placeholder="••••" autocomplete="off">
            <p class="error-msg" id="errorMsg" style="display:none; color: #ff4757; margin-top: 10px;">Contraseña
                incorrecta</p>

            <div style="display:flex; gap:10px; justify-content:center; margin-top:20px;">
                <button class="btn-cancel" onclick="closeModal()">Cancelar</button>
            </div>
        </div>
    </div>


    <div id="generalModal" class="general-modal">
        <div class="general-modal-content">
            <span class="close-general" onclick="closeGeneralModal()">&times;</span>

            <div id="loginFormContainer">
                <h2 style="font-family:'Outfit'; margin-bottom:20px;">Iniciar Sesión</h2>
                <?php if (session('error_general')): ?>
                    <div style="color:#e84118; margin-bottom:10px;"><?= session('error_general') ?></div>
                <?php endif; ?>
                <form action="<?= base_url('auth/login_general') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="text" name="email" class="general-input" placeholder="Email o Usuario" required>
                    <input type="password" name="password" class="general-input" placeholder="Contraseña" required>
                    <button type="submit" class="btn-general">Entrar</button>
                </form>
                <div class="toggle-link">
                    ¿Nuevo aquí? <a href="<?= base_url('registro') ?>" style="color:white; font-weight:bold;">Suscríbete
                        ahora.</a>
                </div>
            </div>

            <div id="registerFormContainer" style="display:none;">
                <h2 style="font-family:'Outfit'; margin-bottom:20px;">Crear Cuenta</h2>
                <?php if (session('errors')): ?>
                    <div class="alert-error" style="display:block;">
                        <ul style="margin:0; padding-left:20px;">
                            <?php foreach (session('errors') as $e): ?>
                                <li><?= esc($e) ?></li><?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="<?= base_url('auth/register') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="text" name="username" class="general-input" placeholder="Usuario"
                        value="<?= old('username') ?>">
                    <input type="email" name="email" class="general-input" placeholder="Email"
                        value="<?= old('email') ?>">
                    <input type="password" name="password" class="general-input" placeholder="Contraseña">
                    <button type="submit" class="btn-general">Registrarse</button>
                </form>
                <div class="toggle-link" onclick="toggleForms('login')">¿Ya tienes cuenta? Inicia sesión.</div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // --- 1. LÓGICA PARA MODAL ORIGINAL (Caritas) ---

        function attemptLogin(id, username, planId) {
            // SI ES KIDS (Plan ID 3), ENTRA DIRECTO
            if (planId == 3) {
                performAjaxLogin(id, ''); // Llamada directa sin password
            }
            // SI NO, ABRE MODAL
            else {
                $('#selectedUserId').val(id);
                $('#modalUser').text(username);
                $('#modalAuth').css('display', 'flex');
                $('#passwordInput').val('').focus();
                $('#errorMsg').hide();
            }
        }

        function closeModal() {
            $('#modalAuth').hide();
        }

        function submitPin() {
            let id = $('#selectedUserId').val();
            let pass = $('#passwordInput').val();
            performAjaxLogin(id, pass);
        }

        // Nueva función separada para hacer la llamada AJAX
        function performAjaxLogin(id, pass) {
            let csrfName = $('.txt_csrftoken').attr('name');
            let csrfHash = $('.txt_csrftoken').val();

            $.ajax({
                url: '<?= base_url("auth/ajax_login_perfil") ?>', // Esta ruta llama a Auth::login (tu antiguo código)
                type: 'POST',
                data: {
                    id: id,
                    password: pass,
                    [csrfName]: csrfHash
                },
                success: function (resp) {
                    $('.txt_csrftoken').val(resp.token);
                    if (resp.status === 'success') {
                        window.location.href = '<?= base_url("/") ?>';
                    } else {
                        $('#errorMsg').text(resp.msg).show();
                        $('#passwordInput').val('').focus();
                    }
                },
                error: function () {
                    $('#errorMsg').text('Error de conexión').show();
                }
            });
        }

        // Detectar tecla ENTER en el modal
        $('#passwordInput').keypress(function (e) {
            if (e.which == 13) submitPin();
        });


        // --- 2. LÓGICA PARA EL NUEVO MODAL (Botón +) ---
        function openGeneralModal() {
            $('#generalModal').css('display', 'flex');
        }

        function closeGeneralModal() {
            $('#generalModal').hide();
        }

        function toggleForms(mode) {
            if (mode === 'register') {
                $('#loginFormContainer').hide();
                $('#registerFormContainer').fadeIn();
            } else {
                $('#registerFormContainer').hide();
                $('#loginFormContainer').fadeIn();
            }
        }

        <?php if (session('show_register')): ?>
            openGeneralModal();
            toggleForms('register');
        <?php endif; ?>
        <?php if (session('error_general')): ?>
            openGeneralModal();
        <?php endif; ?>

    </script>
</body>

</html>