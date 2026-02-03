<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - La Butaca</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

    
    <style>
        body {
            margin: 0; padding: 0;
            font-family: 'Outfit', sans-serif;
            background: #0f0c29;
            background: linear-gradient(to right, #0f0c29, #302b63, #24243e);
            height: 100vh;
            display: flex; align-items: center; justify-content: center;
            color: white; overflow: hidden;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            padding: 3rem;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            width: 100%; max-width: 400px;
            text-align: center;
        }
        h2 { margin-bottom: 2rem; font-weight: 300; letter-spacing: 2px; }
        .input-group { position: relative; margin-bottom: 1.5rem; }
        .input-group i {
            position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa;
        }
        input {
            width: 100%; padding: 12px 15px 12px 45px;
            background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2);
            border-radius: 30px; color: white; outline: none; box-sizing: border-box; font-size: 1rem;
        }
        input:focus { border-color: #00d2ff; background: rgba(0,0,0,0.5); }
        
        button {
            width: 100%; padding: 12px;
            background: linear-gradient(90deg, #00d2ff 0%, #3a7bd5 100%);
            border: none; border-radius: 30px;
            color: white; font-weight: bold; font-size: 1rem; cursor: pointer;
            transition: 0.3s; margin-top: 10px;
        }
        button:hover { transform: scale(1.05); box-shadow: 0 0 20px rgba(0, 210, 255, 0.4); }
        
        .alert {
            background: rgba(255, 71, 87, 0.2); color: #ff4757;
            padding: 10px; border-radius: 10px; margin-bottom: 20px;
            font-size: 0.9rem; border: 1px solid rgba(255, 71, 87, 0.3);
        }
        .bg-deco {
            position: absolute; width: 300px; height: 300px;
            background: #00d2ff; filter: blur(150px); opacity: 0.2;
            border-radius: 50%; z-index: -1;
        }
    </style>
</head>
<body>

    <div class="bg-deco" style="top: -50px; left: -50px;"></div>
    <div class="bg-deco" style="bottom: -50px; right: -50px; background: #ff00de;"></div>

    <div class="login-card">
        <h2>LA BUTACA</h2>
        
        <?php if(session()->getFlashdata('msg')): ?>
            <div class="alert">
                <i class="fa fa-exclamation-circle"></i> <?= session()->getFlashdata('msg') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/auth/login') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" name="username" placeholder="Usuario o Email" required autofocus>
            </div>

            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>

            <button type="submit">ENTRAR</button>
        </form>
        
        <p style="margin-top: 20px; color: #aaa; font-size: 0.9rem;">
            ¿No tienes cuenta? <a href="#" style="color: #00d2ff; text-decoration: none;">Regístrate</a>
        </p>
    </div>

</body>
</html>