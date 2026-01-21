<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¿Quién eres? - La Butaca</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body>

    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <section id="view-profiles" class="view-section active">
        <div class="glass-panel">
            <h1 style="font-family: 'Outfit'; margin-bottom: 10px;">¿Quién está viendo esto?</h1>
            
            <div class="profile-container">
                <?php foreach ($usuarios as $user): ?>
                    <div class="profile-item" onclick="openLogin(<?= $user['id'] ?>, '<?= esc($user['username']) ?>')">
                        <div class="profile-avatar" style="background-image: url('https://ui-avatars.com/api/?name=<?= $user['username'] ?>&background=random&color=fff');"></div>
                        <span><?= esc($user['username']) ?></span>
                        <small style="color:#aaa; display:block; text-transform:uppercase; font-size:0.7rem; margin-top:5px;">
                            <?= ($user['plan_id'] == 2) ? 'Premium' : 'Free' ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="password-modal" id="modalAuth">
        <div class="modal-content">
            <h3 id="modalUser" style="margin-top:0">Usuario</h3>
            <p style="color:#aaa; margin-bottom:20px;">Introduce tu PIN</p>
            
            <input type="password" id="passwordInput" class="pin-input" placeholder="••••" autocomplete="off">
            <p class="error-msg" id="errorMsg">Contraseña incorrecta</p>
            
            <button class="btn-cancel" onclick="closeModal()">Cancelar</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>const BASE_URL = "<?= base_url() ?>";</script>
    
    <script src="<?= base_url('assets/js/auth.js') ?>"></script>

</body>
</html>