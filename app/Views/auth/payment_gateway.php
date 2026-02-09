<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pago Seguro - La Butaca Premium</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">


    <style>
        body {
            background: #f0f2f5;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .checkout-container {
            background: white;
            width: 100%;
            max-width: 900px;
            display: flex;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        /* COLUMNA IZQUIERDA: RESUMEN */
        .summary-col {
            background: #141414;
            color: white;
            padding: 40px;
            flex: 1;
            background-image: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_medium.jpg');
            background-size: cover;
        }

        .summary-header {
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .plan-name {
            font-size: 2rem;
            font-weight: 800;
            color: #e50914;
            margin-bottom: 5px;
        }

        .plan-price {
            font-size: 3rem;
            font-weight: 300;
            margin-bottom: 30px;
        }

        .features ul {
            list-style: none;
            padding: 0;
        }

        .features li {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .features i {
            color: #e50914;
        }

        /* COLUMNA DERECHA: PAGO */
        .payment-col {
            padding: 40px;
            flex: 1.2;
        }

        .payment-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
        }

        .card-preview {
            background: linear-gradient(135deg, #444, #111);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-chip {
            width: 40px;
            height: 30px;
            background: #e0e0e0;
            border-radius: 4px;
            margin-bottom: 15px;
            position: relative;
        }

        .card-number {
            font-size: 1.4rem;
            letter-spacing: 2px;
            margin-bottom: 15px;
            font-family: monospace;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .btn-pay {
            width: 100%;
            background: #e50914;
            color: white;
            padding: 15px;
            border: none;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            border-radius: 6px;
            transition: 0.2s;
            box-shadow: 0 5px 15px rgba(229, 9, 20, 0.3);
        }

        .btn-pay:hover {
            background: #f40612;
            transform: translateY(-2px);
        }

        .secure-note {
            text-align: center;
            margin-top: 15px;
            font-size: 0.85rem;
            color: #888;
        }

        @media(max-width: 768px) {
            .checkout-container {
                flex-direction: column;
                margin: 15px;
            }

            .summary-col {
                padding: 30px;
            }

            .payment-col {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="checkout-container">
        <div class="summary-col">
            <div class="summary-header">Estás contratando</div>
            <div class="plan-name">PREMIUM</div>
            <div class="plan-price">9.99€ <span style="font-size:1rem;">/mes</span></div>

            <div class="features">
                <ul>
                    <li><i class="fa fa-check"></i> Calidad 4K HDR + Dolby Atmos</li>
                    <li><i class="fa fa-check"></i> Sin anuncios publicitarios</li>
                    <li><i class="fa fa-check"></i> Descargas ilimitadas</li>
                    <li><i class="fa fa-check"></i> Cancela cuando quieras</li>
                </ul>
            </div>
        </div>

        <div class="payment-col">
            <div class="payment-title">Datos de Pago</div>

            <div class="card-preview">
                <div class="card-chip"></div>
                <div class="card-number">•••• •••• •••• 4242</div>
                <div class="card-footer">
                    <div>
                        <div style="font-size:0.6rem; color:#aaa;">Titular</div>
                        <div><?= esc(strtoupper($user['username'])) ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.6rem; color:#aaa;">Expira</div>
                        <div>12/28</div>
                    </div>
                </div>
                <div
                    style="position:absolute; bottom:20px; right:20px; font-weight:bold; font-style:italic; font-size:1.2rem;">
                    VISA</div>
            </div>

            <?php
            // Si venimos de un upgrade (editar perfil), vamos a 'perfil/pagar-upgrade'
            // Si venimos de registro nuevo, vamos a 'auth/pagar'
            $actionUrl = isset($is_upgrade) && $is_upgrade ? base_url('perfil/pagar-upgrade') : base_url('auth/pagar');

            // Texto del botón
            $btnText = isset($is_upgrade) && $is_upgrade ? 'Confirmar Upgrade' : 'Pagar y Suscribirse';

            // Enlace de cancelar
            $cancelUrl = isset($is_upgrade) && $is_upgrade ? base_url('perfil') : base_url('registro');
            ?>

            <form action="<?= $actionUrl ?>" method="post">
                <?= csrf_field() ?>

                <p style="color:#666; font-size:0.9rem; margin-bottom:20px;">
                    Estás utilizando una tarjeta de prueba segura. No se realizará ningún cargo real.
                </p>

                <button type="submit" class="btn-pay">
                    <i class="fa fa-lock"></i> <?= $btnText ?>
                </button>
            </form>

            <div class="security-footer">
                <i class="fa fa-shield-alt" style="color: #2ecc71;"></i>
                Pagos procesados de forma segura con encriptación SSL.
            </div>

            <div style="text-align: center; margin-top: 15px;">
                <a href="<?= $cancelUrl ?>" style="color: #999; text-decoration: none; font-size: 0.9rem;">Cancelar</a>
            </div>

</body>

</html>