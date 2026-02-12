<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - La Butaca</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

    <style>
        body {
            background-image: url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_medium.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-container {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            padding: 50px;
            border-radius: 10px;
            width: 100%;
            max-width: 700px;
            margin: 20px;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        }

        .step-title {
            font-family: 'Outfit';
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
        }

        .form-group {
            flex: 1;
        }

        .form-input {
            width: 100%;
            background: #333;
            border: 1px solid #444;
            padding: 15px;
            color: white;
            border-radius: 4px;
            font-family: 'Outfit';
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
        }

        .plan-card {
            flex: 1;
            border: 2px solid #444;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
            position: relative;
        }

        .plan-card:hover {
            border-color: #999;
        }

        .plan-card.selected {
            border-color: #e50914;
            background: rgba(229, 9, 20, 0.1);
        }

        .plan-card h4 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
        }

        .plan-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #e50914;
        }

        /* AVATAR SELECTION */
        .avatar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            gap: 10px;
            margin-bottom: 30px;
        }

        .avatar-option {
            cursor: pointer;
            border-radius: 5px;
            overflow: hidden;
            border: 3px solid transparent;
            transition: 0.2s;
            position: relative;
        }

        .avatar-option img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .avatar-option:hover {
            transform: scale(1.1);
        }

        .avatar-option.selected {
            border-color: #e50914;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.5);
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
        }

        .btn-submit:hover {
            background: #f40612;
        }

        .error-banner {
            background: #e87c03;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="register-container">
        <h2 class="step-title">Configura tu cuenta</h2>

        <?php if (session('errors')): ?>
            <div class="error-banner">
                <ul style="margin:0; padding-left:20px;">
                    <?php foreach (session('errors') as $e): ?>
                        <li><?= esc($e) ?></li><?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/crear') ?>" method="post" id="regForm">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="username" class="form-input" placeholder="Nombre de Usuario"
                        value="<?= old('username') ?>">
                </div>
                <div class="form-group">
                    <input type="text" name="email" class="form-input" placeholder="Correo Electrónico"
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
                    <h4>FREE</h4>
                    <div class="plan-price">0€</div>
                    <small style="color:#ccc;">Con anuncios<br>Calidad 720p</small>
                </label>
                <label class="plan-card" onclick="selectPlan(3, this)">
                    <input type="radio" name="plan_id" value="3" style="display:none;">
                    <h4>KIDS</h4>
                    <div class="plan-price">4.99€</div>
                    <small style="color:#ccc;">Plan Infantil<br>Calidad HD</small>
                </label>

                <label class="plan-card" onclick="selectPlan(2, this)">
                    <input type="radio" name="plan_id" value="2" style="display:none;">
                    <h4>PREMIUM</h4>
                    <div class="plan-price">9.99€</div>
                    <small style="color:#ccc;">Sin anuncios<br>Calidad 4K HDR</small>
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
                        // Obtenemos URL y TIPO
                        $url = $av['url'];
                        $type = $av['type'];

                        if (str_starts_with($url, 'http')) {
                            $rutaImagen = $url;
                            $valInput = $url; // Guardamos URL completa si es externa
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
        // JS para efectos visuales de selección
        function selectPlan(id, element) {
            // Quitar clase selected de todos
            document.querySelectorAll('.plan-card').forEach(el => el.classList.remove('selected'));
            // Añadir al clickado
            element.classList.add('selected');
            // Marcar el radio button interno
            const radio = element.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;

            // EJECUTAR FILTRO DE AVATARES
            filterAvatars(id);
        }

        function filterAvatars(planId) {
            const avatars = document.querySelectorAll('.avatar-option:not(.default-avatar)');
            let currentSelectionHidden = false;
            
            avatars.forEach(av => {
                const type = av.getAttribute('data-type');
                
                if (planId == 3) {
                    // MODO KIDS: Solo mostrar 'kids'
                    if (type === 'kids') {
                        av.style.display = 'block';
                    } else {
                        av.style.display = 'none';
                        // Si el avatar seleccionado se oculta, marcamos flag
                        if(av.classList.contains('selected')) currentSelectionHidden = true;
                    }
                } else {
                    if (type === 'adult') {
                        av.style.display = 'block';
                    } else {

                        av.style.display = 'none'; 
                        if(av.classList.contains('selected')) currentSelectionHidden = true;
                    }
                }
            });

            // Si el avatar que teníamos seleccionado desapareció, seleccionamos el default
            if(currentSelectionHidden) {
                const defaultAv = document.querySelector('.default-avatar');
                if(defaultAv) selectAvatar('default.png', defaultAv);
            }
        }

        function selectAvatar(filename, element) {
            // Actualizar input oculto
            document.getElementById('avatarInput').value = filename;
            // Visuales
            document.querySelectorAll('.avatar-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
        }

        // Seleccionar por defecto
        document.addEventListener('DOMContentLoaded', () => {
            const oldPlan = document.querySelector('input[name="plan_id"]:checked');
            let planIdToFilter = 1; // Default Free

            if(oldPlan) {
                oldPlan.closest('.plan-card').classList.add('selected');
                planIdToFilter = oldPlan.value;
            } else {
                const firstPlan = document.querySelector('.plan-card');
                if(firstPlan) firstPlan.click();
            }

            // Aplicar filtro inicial
            filterAvatars(planIdToFilter);

            const oldAvatar = document.getElementById('avatarInput').value;
            if(!oldAvatar) {
                document.getElementById('avatarInput').value = 'default.png';
            }
        });
    </script>
</body>

</html>