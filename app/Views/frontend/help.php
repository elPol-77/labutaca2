<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($titulo) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #141414;
            color: #fff;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .help-header {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .help-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #e50914;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-link {
            color: #fff;
            text-decoration: none;
            border: 1px solid #fff;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: 0.3s;
            white-space: nowrap;
        }

        .back-link:hover {
            background: white;
            color: black;
        }

        .help-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(20, 20, 20, 1)), url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_medium.jpg');
            background-size: cover;
            background-position: center;
            padding: 80px 20px;
            text-align: center;
        }

        .help-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .search-box {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
            width: 100%; 
        }

        .search-input {
            width: 100%;
            padding: 20px 50px 20px 20px;
            font-size: 1.1rem;
            border-radius: 4px;
            border: none;
            font-family: 'Outfit';
            outline: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        .faq-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
            width: 100%; 
        }

        .category-title {
            color: #e50914;
            font-size: 1.1rem;
            margin-top: 40px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
        }

        .faq-item {
            background: #333;
            margin-bottom: 10px;
            border-radius: 4px;
            overflow: hidden;
            transition: 0.3s;
        }

        .faq-question {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 1.1rem;
            background: #303030;
            gap: 15px;
        }

        .faq-question:hover {
            background: #444;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: #222;
            padding: 0 20px;
            color: #ccc;
            line-height: 1.6;
        }

        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px; 
        }

        .faq-icon {
            transition: 0.3s;
            min-width: 15px; 
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        /* CONTACTO FOOTER */
        .contact-section {
            text-align: center;
            padding: 50px 20px;
            border-top: 1px solid #333;
            margin-top: 40px;
        }

        .btn-contact {
            background: transparent;
            border: 2px solid #e50914;
            color: white;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            transition: 0.3s;
            margin-top: 15px;
            text-transform: uppercase;
        }

        .btn-contact:hover {
            background: #e50914;
        }

        .hidden-faq {
            display: none;
        }

        @media (max-width: 768px) {
            .help-header {
                flex-direction: column;
                padding: 15px;
                gap: 15px;
            }

            .help-logo {
                font-size: 1.3rem;
            }

            .back-link {
                width: 100%;
                text-align: center;
                display: block;
            }

            .help-hero {
                padding: 60px 20px;
            }

            .help-hero h1 {
                font-size: 1.8rem;
            }

            .faq-container {
                margin: 20px auto;
            }

            .faq-question {
                font-size: 1rem;
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <header class="help-header">
        <a href="<?= base_url() ?>" class="help-logo">
            <img src="<?= base_url('labutaca2_logo.ico') ?>" alt="Logo" width="30">
            CENTRO DE AYUDA
        </a>
        <a href="<?= base_url('/') ?>" class="back-link">Volver a La Butaca</a>
    </header>

    <div class="help-hero">
        <h1>¿Cómo podemos ayudarte?</h1>
        
        <div class="search-box">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar ayuda (ej. contraseña, plan...)">
        </div>

    </div>

    <div class="faq-container">

        <h3 class="category-title"><i class="fa fa-user-circle"></i> Mi Cuenta y Perfiles</h3>

        <div class="faq-item">
            <div class="faq-question">
                ¿Cómo cambio mi contraseña?
                <i class="fa fa-plus faq-icon"></i>
            </div>
            <div class="faq-answer">
                Puedes cambiar tu contraseña accediendo a la sección "Cuenta" en el menú de usuario. Si has olvidado tu
                contraseña, utiliza la opción "¿Olvidaste tu contraseña?" en la pantalla de inicio de sesión para
                recibir un correo de recuperación.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                ¿Cómo funcionan los perfiles Kids?
                <i class="fa fa-plus faq-icon"></i>
            </div>
            <div class="faq-answer">
                El perfil "Kids" está diseñado para ofrecer contenido seguro para niños. Por defecto, este perfil no
                requiere PIN para acceder, facilitando el uso para los más pequeños. Puedes editar el nombre y avatar de
                este perfil, pero no su configuración de edad.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                ¿Puedo tener varios perfiles?
                <i class="fa fa-plus faq-icon"></i>
            </div>
            <div class="faq-answer">
                Sí, puedes crear hasta 4 perfiles adicionales más el perfil Kids. Esto permite que cada miembro de la
                familia tenga su propia lista de seguimiento y recomendaciones personalizadas.
            </div>
        </div>

        <h3 class="category-title"><i class="fa fa-credit-card"></i> Planes y Facturación</h3>

        <div class="faq-item">
            <div class="faq-question">
                ¿Qué diferencia hay entre Free y Premium?
                <i class="fa fa-plus faq-icon"></i>
            </div>
            <div class="faq-answer">
                El plan <strong>Free</strong> incluye anuncios y calidad HD estándar. El plan <strong>Premium</strong>
                (9.99€/mes) elimina la publicidad, ofrece calidad 4K UHD, HDR y permite descargas para ver contenido
                offline.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                ¿Cómo me paso a Premium?
                <i class="fa fa-plus faq-icon"></i>
            </div>
            <div class="faq-answer">
                Ve a tu perfil, haz clic en "Editar Perfil" y selecciona el plan Premium. Serás redirigido a nuestra
                pasarela de pago segura para completar el proceso. El cambio es inmediato.
            </div>
        </div>

        <h3 class="category-title"><i class="fa fa-wrench"></i> Solución de Problemas</h3>

        <div class="faq-item">
            <div class="faq-question">
                El vídeo se detiene o carga lento
                <i class="fa fa-plus faq-icon"></i>
            </div>
            <div class="faq-answer">
                Asegúrate de tener una conexión a internet estable. Para contenido 4K (Premium), recomendamos una
                velocidad de al menos 25 Mbps. Intenta reiniciar tu router o probar en otro dispositivo.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                No puedo iniciar sesión
                <i class="fa fa-plus faq-icon"></i>
            </div>
            <div class="faq-answer">
                Verifica que estás introduciendo el correo y contraseña correctos. Recuerda que los correos deben ser
                @gmail.com. Si el problema persiste, intenta limpiar la caché de tu navegador.
            </div>
        </div>

    </div>

    <div class="contact-section">
        <h3>¿No encuentras lo que buscas?</h3>
        <p style="color:#aaa; margin-bottom:20px;">Nuestro equipo de soporte está disponible de 09:00 a 21:00.</p>
        <button class="btn-contact" onclick="alert('Llamando al soporte técnico...')">Contactar Soporte</button>
    </div>

    <script>
        const questions = document.querySelectorAll('.faq-question');

        questions.forEach(q => {
            q.addEventListener('click', () => {
                const item = q.parentElement;
                const icon = q.querySelector('.faq-icon');

                if (item.classList.contains('active')) {
                    item.classList.remove('active');
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                } else {
                    document.querySelectorAll('.faq-item').forEach(i => {
                        i.classList.remove('active');
                        const otherIcon = i.querySelector('.faq-icon');
                        if (otherIcon) {
                            otherIcon.classList.remove('fa-minus');
                            otherIcon.classList.add('fa-plus');
                        }
                    });

                    item.classList.add('active');
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                }
            });
        });

        const searchInput = document.getElementById('searchInput');
        const faqItems = document.querySelectorAll('.faq-item');
        const categories = document.querySelectorAll('.category-title');

        if (searchInput) {
            searchInput.addEventListener('keyup', function (e) {
                const term = e.target.value.toLowerCase();

                faqItems.forEach(item => {
                    const text = item.innerText.toLowerCase();
                    if (text.includes(term)) {
                        item.classList.remove('hidden-faq');
                    } else {
                        item.classList.add('hidden-faq');
                    }
                });
            });
        }
    </script>

</body>

</html>