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
        /* Pequeño ajuste para que las imágenes se vean perfectas */
        .profile-avatar {
            overflow: hidden; /* Asegura que la imagen no se salga del borde */
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #333; /* Fondo por si la imagen tarda en cargar */
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* CLAVE: Evita que la foto se estire/aplaste */
        }
    </style>
</head>

<body>

    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <section id="view-profiles" class="view-section active" style="display: flex;">
        <div class="glass-panel">
            <h1 style="font-family: 'Outfit'; margin-bottom: 10px;">¿Quién eres?</h1>

            <div class="profile-container">

                <?php foreach ($usuarios as $user): ?>
                    
                    <?php 
                        // --- LÓGICA DE AVATAR ---
                        $avatar = $user['avatar'];
                        
                        // 1. Si está vacío, imagen por defecto
                        if (empty($avatar)) {
                            $avatar = 'https://upload.wikimedia.org/wikipedia/commons/0/0b/Netflix-avatar.png';
                        } 
                        // 2. Si NO es una URL (es local), añadimos la ruta base
                        elseif (!str_starts_with($avatar, 'http')) {
                            $avatar = base_url('assets/img/avatars/' . $avatar);
                        }
                        // 3. Si ya es http, se queda tal cual
                    ?>

                    <div class="profile-item"
                        onclick="attemptLogin(<?= $user['id'] ?>, '<?= esc($user['username']) ?>', <?= $user['plan_id'] ?>)">

                        <div class="profile-avatar">
                            <img src="<?= $avatar ?>" alt="<?= esc($user['username']) ?>">
                        </div>

                        <span><?= esc($user['username']) ?></span>

                        <small style="color:#aaa; display:block; text-transform:uppercase; font-size:0.7rem; margin-top:5px;">
                            <?= match ($user['plan_id'] ?? '1') {
                                '2' => 'Premium',
                                '3' => 'Kids',
                                default => 'Free'
                            } ?>
                        </small>
                    </div>
                <?php endforeach; ?>

                <div class="profile-item" onclick="alert('Funcionalidad de Crear Perfil próximamente...')">
                    <div class="profile-avatar"
                        style="border: 2px dashed #666; overflow: hidden; background: rgba(0,0,0,0.5);">
                        <img src="<?= base_url('assets/img/plus_profile.jpg') ?>" alt="Añadir Perfil"
                            style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;">
                    </div>
                    <span>Añadir Perfil</span>
                </div>

            </div>
            
            <div style="margin-top: 30px; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                <a href="<?= base_url('admin/login') ?>"
                    style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.85rem; font-family: 'Outfit'; transition: 0.3s;">
                    <i class="fa fa-lock"></i> Acceso Administración
                </a>
            </div>

        </div>

        <div class="password-modal" id="modalAuth">
            <div class="modal-content">
                <h3 id="modalUser" style="margin-top:0; color: white;">Usuario</h3>
                <p style="color:#aaa; margin-bottom:20px;">Introduce tu PIN</p>

                <input type="hidden" id="selectedUserId">
                <input type="password" id="passwordInput" class="pin-input" placeholder="••••" autocomplete="off">
                <p class="error-msg" id="errorMsg" style="display:none; color: #ff4757; margin-top: 10px;">Contraseña incorrecta</p>

                <div style="display:flex; gap:10px; justify-content:center; margin-top:20px;">
                    <button class="btn-cancel" onclick="closeModal()">Cancelar</button>
                </div>
            </div>
        </div>

    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>const BASE_URL = "<?= base_url() ?>";</script>
    <script src="<?= base_url('assets/js/auth.js') ?>"></script>

</body>
</html>