<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¿Quién eres? - La Butaca</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <style>
        /* Estilos específicos para el Modal de Contraseña */
        .password-modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); z-index: 2000;
            justify-content: center; align-items: center;
        }
        .modal-content {
            background: #1a1a1a; padding: 40px; border-radius: 10px;
            text-align: center; border: 1px solid #333; width: 300px;
        }
        .pin-input {
            background: #333; border: 1px solid #555; color: white;
            padding: 10px; font-size: 1.5rem; width: 100%; text-align: center;
            letter-spacing: 5px; margin-top: 20px; border-radius: 5px;
        }
        .error-msg { color: #e50914; margin-top: 10px; display: none; }
    </style>
</head>
<body>

    <section id="view-profiles" class="view-section active" style="display:flex;">
        <div class="glass-panel">
            <h1 style="font-family: 'Outfit'; margin-bottom: 10px;">¿Quién está viendo esto?</h1>
            
            <div class="profile-container">
                <?php foreach ($usuarios as $user): ?>
                    <div class="profile-item" onclick="openLogin(<?= $user['id'] ?>, '<?= $user['username'] ?>')">
                        <div class="profile-avatar" style="background-image: url('https://ui-avatars.com/api/?name=<?= $user['username'] ?>&background=random');"></div>
                        <span><?= $user['username'] ?></span>
                        <small style="color:#aaa; display:block;"><?= ($user['plan_id'] == 2) ? 'Premium' : 'Free' ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="password-modal" id="modalAuth">
        <div class="modal-content">
            <h3 id="modalUser">Usuario</h3>
            <p>Introduce tu contraseña</p>
            <input type="password" id="passwordInput" class="pin-input" placeholder="****" autofocus>
            <p class="error-msg" id="errorMsg">Contraseña incorrecta</p>
            <button onclick="closeModal()" style="margin-top:20px; background:transparent; border:none; color:#aaa; cursor:pointer;">Cancelar</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentUserId = null;

        function openLogin(id, name) {
            currentUserId = id;
            $('#modalUser').text(name);
            $('#passwordInput').val('');
            $('#errorMsg').hide();
            $('#modalAuth').css('display', 'flex');
            $('#passwordInput').focus();
        }

        function closeModal() {
            $('#modalAuth').hide();
        }

        // Detectar ENTER en el input
        $('#passwordInput').on('keypress', function(e) {
            if(e.which == 13) {
                doLogin();
            }
        });

        function doLogin() {
            const pass = $('#passwordInput').val();
            
            $.post('<?= base_url('auth/login') ?>', {
                id: currentUserId,
                password: pass
            }, function(response) {
                if(response.status === 'success') {
                    // Login correcto: Recargar para entrar al catálogo
                    window.location.href = '<?= base_url() ?>';
                } else {
                    $('#errorMsg').show();
                    // Efecto de vibración
                    $('.modal-content').animate({marginLeft: "-10px"}, 100).animate({marginLeft: "10px"}, 100).animate({marginLeft: "0px"}, 100);
                }
            });
        }
    </script>
</body>
</html>