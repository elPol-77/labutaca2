<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

    <style>
        body {
            background-color: #f3f3f3; 
            color: #333;
            font-family: 'Outfit', sans-serif;
            margin: 0;
        }

        .account-header {
            background: #141414;
            padding: 20px 40px;
            display: flex; justify-content: space-between; align-items: center;
            color: white;
        }
        .account-logo {
            font-size: 1.5rem; font-weight: 800; color: #e50914; text-decoration: none;
            display: flex; align-items: center; gap: 10px;
        }
        .back-link {
            color: #fff; text-decoration: none; border: 1px solid #fff; padding: 8px 15px;
            border-radius: 4px; font-size: 0.9rem; transition: 0.3s;
        }
        .back-link:hover { background: white; color: black; }

        .account-container {
            max-width: 1000px; margin: 40px auto; padding: 0 20px;
        }

        .section-title {
            font-size: 2rem; border-bottom: 1px solid #ccc; padding-bottom: 20px; margin-bottom: 20px;
            color: #333; font-weight: 700;
        }

        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; color: white; }
        .alert-success { background: #2ecc71; }
        .alert-error { background: #e74c3c; }
        .alert-info { background: #3498db; }
        .account-grid {
            display: grid; grid-template-columns: 250px 1fr; gap: 30px;
            border-top: 1px solid #ddd; padding-top: 20px; margin-bottom: 20px;
        }

        .label-col {
            font-weight: 600; color: #777; text-transform: uppercase; font-size: 0.9rem;
        }

        .data-col { color: #333; }

        .user-row {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;
        }
        .btn-link {
            color: #0073e6; text-decoration: none; font-size: 0.9rem; cursor: pointer;
        }
        .btn-link:hover { text-decoration: underline; }

        #formPassword {
            background: #e6e6e6; padding: 20px; border-radius: 5px; margin-top: 10px; display: none;
        }
        .pass-input {
            width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px;
        }
        .btn-save {
            background: #0073e6; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;
        }

        .plan-box {
            background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;
            display: flex; justify-content: space-between; align-items: center;
        }
        .plan-badge {
            background: #e50914; color: white; padding: 5px 10px; border-radius: 4px;
            font-weight: bold; font-size: 0.8rem; text-transform: uppercase;
        }
        .plan-badge.free { background: #777; }

        .btn-cancel {
            background: #e0e0e0; color: #333; padding: 10px 20px; text-decoration: none;
            border-radius: 4px; font-weight: 600; font-size: 0.9rem; transition: 0.2s; border: none; cursor: pointer;
        }
        .btn-cancel:hover { background: #ccc; }

        .btn-upgrade {
            background: #e50914; color: white; padding: 10px 20px; text-decoration: none;
            border-radius: 4px; font-weight: 600; font-size: 0.9rem; transition: 0.2s;
        }
        .btn-upgrade:hover { background: #f40612; }

        /* FACTURACIÓN */
        .billing-info {
            background: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 15px; font-size: 0.95rem;
            display: flex; justify-content: space-between; color: #555;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .account-grid { grid-template-columns: 1fr; gap: 10px; }
            .label-col { margin-bottom: 10px; }
        }
    </style>
</head>

<body>

    <header class="account-header">
        <a href="<?= base_url() ?>" class="account-logo">
            <img src="<?= base_url('labutaca2_logo.ico') ?>" alt="Logo" width="30">
            LA BUTACA
        </a>
        <a href="<?= base_url('/') ?>" class="back-link">Volver a Inicio</a>
    </header>

    <div class="account-container">
        
        <h1 class="section-title">Cuenta</h1>
        <p style="color:#666; margin-bottom:30px;">Miembro desde <?= date('Y') // O fecha real si la tienes ?></p>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('info')): ?>
            <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
        <?php endif; ?>


        <div class="account-grid">
            <div class="label-col">MEMBRESÍA Y FACTURACIÓN</div>
            <div class="data-col">
                
                <div class="user-row">
                    <strong><?= esc($usuario['email']) ?></strong>
                    <span class="btn-link" onclick="alert('Funcionalidad para cambiar email próximamente')">Cambiar correo</span>
                </div>
                
                <div class="user-row">
                    <span style="color:#777;">Contraseña: ********</span>
                    <span class="btn-link" onclick="togglePasswordForm()">Cambiar contraseña</span>
                </div>

                <div id="formPassword">
                    <form action="<?= base_url('cuenta/cambiar-password') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="password" name="pass_actual" class="pass-input" placeholder="Contraseña actual">
                        <input type="password" name="pass_nueva" class="pass-input" placeholder="Nueva contraseña">
                        <input type="password" name="pass_repetir" class="pass-input" placeholder="Repetir nueva contraseña">
                        
                        <button type="submit" class="btn-save">Guardar</button>
                        <button type="button" class="btn-cancel" onclick="togglePasswordForm()">Cancelar</button>
                    </form>
                </div>

            </div>
        </div>

        <div class="account-grid">
            <div class="label-col">INFORMACIÓN DEL PLAN</div>
            <div class="data-col">
                
                <div class="plan-box">
                    <div>
                        <span class="plan-badge <?= ($usuario['plan_id'] == 1) ? 'free' : '' ?>">
                            <?= ($usuario['plan_id'] == 1) ? 'Plan Free' : 'Plan Premium' ?>
                        </span>
                        <?php if($usuario['plan_id'] == 2): ?>
                            <span style="margin-left: 10px; font-size: 0.9rem; color: #e50914; font-weight: bold;">Ultra HD 4K</span>
                        <?php endif; ?>
                    </div>

                    <?php if($usuario['plan_id'] == 1): ?>
                        <form action="<?= base_url('perfil/update') ?>" method="post" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="plan_id" value="2">
                            <input type="hidden" name="username" value="<?= esc($usuario['username']) ?>">
                            <input type="hidden" name="avatar" value="<?= esc($usuario['avatar']) ?>">
                            <button type="submit" class="btn-upgrade">Pasar a Premium</button>
                        </form>
                    <?php else: ?>
                        <form action="<?= base_url('cuenta/cancelar-suscripcion') ?>" method="post" onsubmit="return confirm('¿Seguro que quieres perder los beneficios Premium?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-cancel">Cancelar suscripción</button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if($usuario['plan_id'] == 2): ?>
                    <div class="billing-info">
                        <span><i class="fa fa-credit-card"></i> <?= esc($tarjeta) ?></span>
                        <span>Próxima factura: <strong><?= esc($proximo_cobro) ?></strong></span>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="account-grid">
            <div class="label-col">CONFIGURACIÓN</div>
            <div class="data-col">
                <div class="user-row">
                    <span>Control parental</span>
                    <a href="<?= base_url('perfil') ?>" class="btn-link">Gestionar perfiles</a>
                </div>
                <div class="user-row">
                    <span>Dispositivos recientes</span>
                    <span class="btn-link" onclick="alert('Cerrando sesión en otros dispositivos...')">Cerrar sesión en todos los dispositivos</span>
                </div>
                <div class="user-row">
                    <span>Descargar información personal</span>
                    <span class="btn-link">Descargar</span>
                </div>
            </div>
        </div>

    </div>

    <script>
        function togglePasswordForm() {
            var form = document.getElementById('formPassword');
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                form.style.display = 'block';
            }
        }
    </script>

</body>
</html>