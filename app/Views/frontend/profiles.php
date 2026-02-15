<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>¿Quién eres? - La Butaca</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

</head>

<body>

    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <section id="view-profiles" class="view-section active">
        <div class="glass-panel">
            <h1 style="font-family: 'Outfit'; margin-bottom: 20px; font-weight: 400;">¿Quién eres?</h1>

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
                            <small style="color:#aaa; display:block; text-transform:uppercase; font-size:0.65rem; margin-top:4px; letter-spacing: 1px;">
                                <?= match ($user['plan_id'] ?? '1') { '2' => 'Premium', '3' => 'Kids', default => 'Free'} ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="profile-item" onclick="openGeneralModal()">
                    <div class="profile-avatar"
                        style="border: 2px dashed #666; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-plus" style="font-size: 2.5rem; color: #888;"></i>
                    </div>
                    <span>Añadir Perfil</span>
                </div>
            </div>

            <div style="margin-top: 40px; text-align: center;">
                <a href="<?= base_url('admin/login') ?>"
                    style="display: inline-block; border: 1px solid rgba(255,255,255,0.3); padding: 8px 20px; border-radius: 4px; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.85rem; font-family: 'Outfit'; transition: 0.3s;">
                    <i class="fa fa-lock"></i> ADMINISTRAR
                </a>
            </div>
        </div>
    </section>


    <div class="password-modal" id="modalAuth">
        <div class="modal-content">
            <h3 id="modalUser" style="margin-top:0; color: white; font-size: 1.5rem;">Usuario</h3>
            <p style="color:#aaa; margin-bottom:20px;">Introduce tu PIN para acceder</p>

            <input type="hidden" id="selectedUserId">
            <input type="password" id="passwordInput" class="pin-input" placeholder="••••" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
            <p class="error-msg" id="errorMsg" style="display:none; color: #ff4757; margin-top: 10px;">Contraseña incorrecta</p>

            <div style="display:flex; gap:10px; justify-content:center; margin-top:20px; width: 100%;">
                <button class="btn-cancel" onclick="closeModal()">CANCELAR</button>
            </div>
        </div>
    </div>


    <div id="generalModal" class="general-modal">
        <div class="general-modal-content">
            <span class="close-general" onclick="closeGeneralModal()">&times;</span>

            <div id="loginFormContainer">
                <h2 style="font-family:'Outfit'; margin-bottom:20px;">Iniciar Sesión</h2>

                <?php if (session('error_general')): ?>
                    <div style="color:#e84118; margin-bottom:10px; background: rgba(232, 65, 24, 0.1); padding: 5px; border-radius: 4px;"><?= session('error_general') ?></div>
                <?php endif; ?>

                <div id="login-js-error" class="js-error-msg"></div>

                <form id="form-login-general" action="<?= base_url('auth/login_general') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="text" name="email" class="general-input" placeholder="Email o Usuario">
                    <input type="password" name="password" class="general-input" placeholder="Contraseña">

                    <div style="text-align: right; margin-bottom: 20px;">
                        <a href="#" onclick="cambiarAModalRecuperar(); return false;"
                            style="color: #b3b3b3; font-size: 0.85rem; text-decoration: none; font-family: 'Outfit';">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                    <button type="submit" class="btn-general">Entrar</button>
                </form>
                <div class="toggle-link">
                    ¿Nuevo en La Butaca? <a href="<?= base_url('registro') ?>" style="color:white; font-weight:bold;">Suscríbete ahora.</a>
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

                <div id="register-js-error" class="js-error-msg"></div>

                <form id="form-register-general" action="<?= base_url('auth/register') ?>" method="post">
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

    <div id="view-recovery" class="general-modal" style="display: none;">
        <div class="general-modal-content">
            <span class="close-general" onclick="cerrarModalRecuperar()">&times;</span>

            <h2 style="font-family:'Outfit'; margin-bottom:10px;">Recuperar Cuenta</h2>
            <p style="color:#aaa; font-size:0.9rem; margin-bottom:20px;">
                Introduce tu email y te enviaremos una nueva contraseña.
            </p>

            <form id="form-recovery">
                <?= csrf_field() ?>
                <input type="email" name="email" class="general-input" placeholder="Tu correo electrónico">
                <div id="msg-recovery" style="display:none; margin: 10px 0; padding: 10px; border-radius: 4px;"></div>
                <button type="submit" class="btn-general">Restablecer Contraseña</button>
            </form>

            <div class="toggle-link" onclick="volverALogin()">
                <i class="fa fa-arrow-left"></i> Volver a Iniciar Sesión
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function attemptLogin(id, username, planId) {
            if (planId == 3) {
                performAjaxLogin(id, ''); 
            } else {
                $('#selectedUserId').val(id);
                $('#modalUser').text(username);
                $('#modalAuth').css('display', 'flex'); // Flex es importante para centrar
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

        function performAjaxLogin(id, pass) {
            let csrfName = $('.txt_csrftoken').attr('name');
            let csrfHash = $('.txt_csrftoken').val();

            $.ajax({
                url: '<?= base_url("auth/ajax_login_perfil") ?>',
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

        $('#passwordInput').keypress(function (e) {
            if (e.which == 13) submitPin();
        });

        function openGeneralModal() {
            $('#generalModal').css('display', 'flex');
        }

        function closeGeneralModal() {
            $('#generalModal').hide();
        }

        function toggleForms(mode) {
            $('#login-js-error').hide();
            $('#register-js-error').hide();

            if (mode === 'register') {
                $('#loginFormContainer').hide();
                $('#registerFormContainer').fadeIn();
            } else {
                $('#registerFormContainer').hide();
                $('#loginFormContainer').fadeIn();
            }
        }

        function cambiarAModalRecuperar() {
            closeGeneralModal();
            document.getElementById('view-recovery').style.display = 'flex';
        }

        function cerrarModalRecuperar() {
            document.getElementById('view-recovery').style.display = 'none';
        }

        function volverALogin() {
            cerrarModalRecuperar();
            openGeneralModal();
            toggleForms('login');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('form-login-general');
            if (loginForm) {
                loginForm.addEventListener('submit', function (e) {
                    const email = this.querySelector('input[name="email"]').value.trim();
                    const pass = this.querySelector('input[name="password"]').value.trim();
                    const errorDiv = document.getElementById('login-js-error');

                    if (!email || !pass) {
                        e.preventDefault();
                        errorDiv.style.display = 'block';
                        errorDiv.innerText = 'Por favor, escribe tu usuario/email y contraseña.';
                    } else {
                        errorDiv.style.display = 'none';
                    }
                });
            }

            const registerForm = document.getElementById('form-register-general');
            if (registerForm) {
                registerForm.addEventListener('submit', function (e) {
                    const user = this.querySelector('input[name="username"]').value.trim();
                    const email = this.querySelector('input[name="email"]').value.trim();
                    const pass = this.querySelector('input[name="password"]').value.trim();
                    const errorDiv = document.getElementById('register-js-error');

                    if (!user || !email || !pass) {
                        e.preventDefault();
                        errorDiv.style.display = 'block';
                        errorDiv.innerText = 'Todos los campos son obligatorios.';
                    } else {
                        errorDiv.style.display = 'none';
                    }
                });
            }

            const formRecovery = document.getElementById('form-recovery');
            if (formRecovery) {
                formRecovery.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const btn = this.querySelector('button');
                    const msg = document.getElementById('msg-recovery');
                    const emailInput = this.querySelector('input[name="email"]');

                    if (!emailInput.value.trim()) {
                        msg.style.display = 'block';
                        msg.style.color = '#ff4757';
                        msg.style.border = '1px solid #ff4757';
                        msg.style.background = 'rgba(229, 9, 20, 0.2)';
                        msg.innerText = 'Debes escribir un email para recuperarlo.';
                        return; 
                    }

                    const formData = new FormData(this);
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
                    btn.disabled = true;
                    msg.style.display = 'none';

                    fetch('<?= base_url("auth/recuperar-password") ?>', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.token) {
                            $('.txt_csrftoken').val(data.token);
                        }
                        msg.innerHTML = data.msg;
                        msg.style.display = 'block';
                        if (data.status === 'success') {
                            msg.style.background = 'rgba(70, 211, 105, 0.2)';
                            msg.style.color = '#46d369';
                            msg.style.border = '1px solid #46d369';
                        } else {
                            msg.style.background = 'rgba(229, 9, 20, 0.2)';
                            msg.style.color = '#ff4757';
                            msg.style.border = '1px solid #ff4757';
                        }
                    })
                    .catch(err => {
                        msg.innerText = "Error de conexión.";
                        msg.style.display = 'block';
                        msg.style.color = '#ff4757';
                    })
                    .finally(() => {
                        btn.innerHTML = 'Restablecer Contraseña';
                        btn.disabled = false;
                    });
                });
            }
        });

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