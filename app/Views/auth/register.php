<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Crear Cuenta - La Butaca</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

    <style>
        * { box-sizing: border-box; }

        body {
            background-image: url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_medium.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            font-family: 'Outfit', sans-serif;
        }

        .register-container {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            padding: 50px;
            border-radius: 10px;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        }

        .step-title {
            font-size: 1.8rem;
            margin-bottom: 30px;
            text-align: center;
            color: white;
        }

        /* FORM GROUPS */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 250px;
        }

        .form-input {
            width: 100%;
            background: #333;
            border: 1px solid #444;
            padding: 15px;
            color: white;
            border-radius: 4px;
            font-family: 'Outfit';
            font-size: 16px;
        }

        .form-input:focus {
            outline: 2px solid #e50914;
            background: #444;
        }

        /* PLAN SELECTION */
        .plan-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .plan-card {
            flex: 1;
            min-width: 140px;
            border: 2px solid #444;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
            position: relative;
        }

        .plan-card:hover { border-color: #999; }

        .plan-card.selected {
            border-color: #e50914;
            background: rgba(229, 9, 20, 0.1);
        }

        .plan-card h4 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
            color: #fff;
        }

        .plan-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #e50914;
        }

        /* AVATAR SELECTION - TAMAÑO AUMENTADO */
        .avatar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(85px, 1fr));
            gap: 15px; 
            margin-bottom: 30px;
        }

        .avatar-option {
            cursor: pointer;
            border-radius: 5px;
            overflow: hidden;
            border: 3px solid transparent;
            transition: 0.2s;
            position: relative;
            aspect-ratio: 1 / 1;
        }

        .avatar-option img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .avatar-option:hover { transform: scale(1.1); z-index: 10; }
        .avatar-option.selected {
            border-color: #e50914;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.5);
            z-index: 10;
        }

        .btn-submit {
            width: 100%;
            background: #e50914;
            color: white;
            padding: 15px;
            border: none;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .btn-submit:hover { background: #f40612; }

        .error-banner {
            background: #e87c03;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: none;
        }
        
        .php-error { display: block; }

        @media (max-width: 768px) {
            .register-container { padding: 30px 20px; }
            .step-title { font-size: 1.5rem; }
            .form-row { flex-direction: column; gap: 15px; }
            .form-group { width: 100%; }
            .plan-selector { flex-direction: column; }
            
            .plan-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                text-align: left;
                padding: 15px;
            }
            .plan-card h4 { margin: 0; font-size: 1rem; }
            .plan-price { font-size: 1.2rem; }
            .plan-card small { display: none; }
            
            .avatar-grid {
                 grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            }
        }
    </style>
</head>

<body>

    <div class="register-container">
        <h2 class="step-title">Configura tu cuenta</h2>

        <?php if (session('errors')): ?>
            <div class="error-banner php-error">
                <ul style="margin:0; padding-left:20px;">
                    <?php foreach (session('errors') as $e): ?>
                        <li><?= esc($e) ?></li><?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <div id="js-error" class="error-banner" style="background-color: #ff4757; border: 1px solid #ff6b81;"></div>

        <form action="<?= base_url('auth/crear') ?>" method="post" id="regForm" novalidate>
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="username" class="form-input" placeholder="Nombre de Usuario"
                        value="<?= old('username') ?>">
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Correo Electrónico (@gmail.com)"
                        value="<?= old('email') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Contraseña">
                </div>
                <div class="form-group">
                    <input type="password" name="pass_confirm" class="form-input" placeholder="Confirmar Contraseña">
                </div>
            </div>

            <h4 style="color:#aaa; margin-bottom:15px; margin-top:20px;">Elige tu Plan</h4>

            <div class="plan-selector">
                <label class="plan-card" onclick="selectPlan(1, this)">
                    <input type="radio" name="plan_id" value="1" style="display:none;">
                    <div>
                        <h4>FREE</h4>
                        <small style="color:#ccc; display:block;">Con anuncios</small>
                    </div>
                    <div class="plan-price">0€</div>
                </label>
                
                <label class="plan-card" onclick="selectPlan(3, this)">
                    <input type="radio" name="plan_id" value="3" style="display:none;">
                    <div>
                        <h4>KIDS</h4>
                        <small style="color:#ccc; display:block;">Infantil HD</small>
                    </div>
                    <div class="plan-price">4.99€</div>
                </label>

                <label class="plan-card" onclick="selectPlan(2, this)">
                    <input type="radio" name="plan_id" value="2" style="display:none;">
                    <div>
                        <h4>PREMIUM</h4>
                        <small style="color:#ccc; display:block;">4K HDR</small>
                    </div>
                    <div class="plan-price">9.99€</div>
                </label>
            </div>

            <h4 style="color:#aaa; margin-bottom:15px;">Elige tu Avatar</h4>

            <input type="hidden" name="avatar" id="avatarInput">
            <div class="avatar-grid">

                <div class="avatar-option selected default-avatar" data-type="all" onclick="selectAvatar('default.png', this)">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/0b/Netflix-avatar.png" alt="Default">
                </div>

                <?php if (!empty($avatars)): ?>
                    <?php foreach ($avatars as $av): ?>
                        <?php
                        $url = $av['url'];
                        $type = $av['type'];
                        if (str_starts_with($url, 'http')) {
                            $rutaImagen = $url;
                            $valInput = $url; 
                        } else {
                            $rutaImagen = base_url('assets/img/avatars/' . $url);
                            $valInput = $url;
                        }
                        ?>
                        <div class="avatar-option" data-type="<?= $type ?>" onclick="selectAvatar('<?= esc($valInput) ?>', this)">
                            <img src="<?= $rutaImagen ?>" alt="Avatar">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">Crear Cuenta</button>

            <div style="text-align: center; margin-top: 20px; color: #999;">
                ¿Ya tienes cuenta? <a href="<?= base_url('auth') ?>"
                    style="color: white; text-decoration: underline;">Inicia sesión</a>
            </div>
        </form>
    </div>

    <script>
        // --- 1. VALIDACIÓN EN ESPAÑOL ---
        document.getElementById('regForm').addEventListener('submit', function(e) {
            
            const user = document.querySelector('input[name="username"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            const pass = document.querySelector('input[name="password"]').value.trim();
            const passConfirm = document.querySelector('input[name="pass_confirm"]').value.trim();
            const errorBanner = document.getElementById('js-error');
            
            let errores = [];

            // 1. Campos obligatorios
            if(user === '') errores.push("El campo Usuario es obligatorio.");
            if(email === '') errores.push("El campo Email es obligatorio.");
            if(pass === '') errores.push("La contraseña es obligatoria.");

            // 2. Validación SOLO GMAIL
            const gmailRegex = /^[^\s@]+@gmail\.com$/i;
            if(email !== '' && !gmailRegex.test(email)) {
                errores.push("El correo electrónico debe ser una cuenta de Gmail (@gmail.com).");
            }

            // 3. Validación PASSWORD ROBUSTA
            const passRegex = /^(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            if(pass !== '') {
                if(!passRegex.test(pass)) {
                    errores.push("La contraseña debe tener al menos 8 caracteres, una mayúscula y una minúscula.");
                }
                // Confirmación
                if(pass !== passConfirm) {
                    errores.push("Las contraseñas no coinciden.");
                }
            }

            // Si hay errores, mostramos alerta
            if(errores.length > 0) {
                e.preventDefault(); // Detiene el envío
                errorBanner.style.display = 'block';
                errorBanner.innerHTML = '<ul style="margin:0; padding-left:20px;">' + 
                    errores.map(err => `<li>${err}</li>`).join('') + 
                    '</ul>';
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                errorBanner.style.display = 'none';
            }
        });


        // --- 2. LÓGICA DE INTERFAZ ---
        function selectPlan(id, element) {
            document.querySelectorAll('.plan-card').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            const radio = element.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            filterAvatars(id);
        }

        function filterAvatars(planId) {
            const avatars = document.querySelectorAll('.avatar-option:not(.default-avatar)');
            let currentSelectionHidden = false;
            
            avatars.forEach(av => {
                const type = av.getAttribute('data-type');
                if (planId == 3) { // KIDS
                    if (type === 'kids') {
                        av.style.display = 'block';
                    } else {
                        av.style.display = 'none';
                        if(av.classList.contains('selected')) currentSelectionHidden = true;
                    }
                } else { // ADULT
                    if (type === 'adult') {
                        av.style.display = 'block';
                    } else {
                        av.style.display = 'none'; 
                        if(av.classList.contains('selected')) currentSelectionHidden = true;
                    }
                }
            });

            if(currentSelectionHidden) {
                const defaultAv = document.querySelector('.default-avatar');
                if(defaultAv) selectAvatar('default.png', defaultAv);
            }
        }

        function selectAvatar(filename, element) {
            document.getElementById('avatarInput').value = filename;
            document.querySelectorAll('.avatar-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const oldPlan = document.querySelector('input[name="plan_id"]:checked');
            let planIdToFilter = 1; 

            if(oldPlan) {
                oldPlan.closest('.plan-card').classList.add('selected');
                planIdToFilter = oldPlan.value;
            } else {
                const firstPlan = document.querySelector('.plan-card');
                if(firstPlan) firstPlan.click();
            }

            filterAvatars(planIdToFilter);

            const oldAvatar = document.getElementById('avatarInput').value;
            if(!oldAvatar) {
                document.getElementById('avatarInput').value = 'default.png';
            }
        });
    </script>
</body>

</html>