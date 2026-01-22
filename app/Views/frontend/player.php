<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= esc($titulo) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/front.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/player.css') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">


</head>
<body>

    <div class="player-container">
        
        <div class="top-controls">
            <a href="<?= base_url() ?>" class="back-btn">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
            <div style="font-weight:bold; color:#aaa;">REPRODUCTOR LA BUTACA</div>
        </div>

        <div class="video-wrapper">
            <iframe 
                src="<?= $video_url ?>" 
                allow="autoplay; encrypted-media; fullscreen" 
                allowfullscreen>
            </iframe>
        </div>

        <div class="info-overlay">
            <h1 style="margin:0; font-size:3rem;"><?= esc($contenido['titulo']) ?></h1>
            <p style="font-size:1.1rem; color:#ddd; max-width:600px;">
                <?= esc($contenido['descripcion']) ?>
            </p>
        </div>

    </div>

</body>
</html>