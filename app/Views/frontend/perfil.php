<div class="profile-container" style="padding-top: 100px; min-height: 100vh; background: #141414; color: white;">

    <div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">

        <h1
            style="font-family: 'Playfair Display', serif; border-bottom: 1px solid #333; padding-bottom: 20px; margin-bottom: 30px;">
            Editar Perfil
        </h1>

        <?php if (session()->getFlashdata('success')): ?>
            <div
                style="background: #46d369; color: white; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-family: 'Outfit', sans-serif;">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div
                style="background: #e50914; color: white; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-family: 'Outfit', sans-serif;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <div
            style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 25px; margin-bottom: 40px;">

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <h3 style="margin:0;    ; font-size:1.5rem; color:var(--accent);">
                    <?= $suscripcion['nombre_plan'] ?>
                </h3>
                <span style="font-family:'Outfit'; font-size:1.2rem; font-weight:bold;"><?= $suscripcion['precio'] ?>
                    <small>/mes</small></span>
            </div>

            <?php if ($usuario['plan_id'] > 1): ?>
                <div
                    style="display:flex; justify-content:space-between; color:#ccc; font-size:0.9rem; margin-bottom:5px; font-family:'Outfit';">
                    <span>Tiempo restante</span>
                    <span style="color:white; font-weight:bold;"><?= $suscripcion['dias_restantes'] ?> días</span>
                </div>

                <div style="width:100%; height:8px; background:#333; border-radius:10px; overflow:hidden;">
                    <div
                        style="width: <?= $suscripcion['porcentaje'] ?>%; height:100%; background: linear-gradient(90deg, #00d2ff, #3a7bd5); border-radius:10px;">
                    </div>
                </div>

                <p style="margin-top:15px; color:#888; font-size:0.9rem; font-family:'Outfit';">
                    <i class="fa fa-calendar-check"></i> Se renovará el <strong
                        style="color:white;"><?= $suscripcion['fecha_renovacion'] ?></strong>
                </p>
            <?php else: ?>
                <p style="color:#aaa; font-family:'Outfit';">Tienes el plan básico con anuncios. ¡Pásate a Premium!</p>
            <?php endif; ?>
        </div>

        <form action="<?= base_url('perfil/update') ?>" method="post">
            <?= csrf_field() ?>

            <?php if (!$esKids): ?>
                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display:block; color:#aaa; margin-bottom:10px; font-family: 'Outfit', sans-serif;">Nombre
                        del Perfil</label>
                    <input type="text" name="username" value="<?= esc($usuario['username']) ?>"
                        style="width: 100%; padding: 15px; background: #333; border: 1px solid #333; color: white; font-size: 1.2rem; border-radius: 4px; font-family: 'Outfit', sans-serif;">
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label
                        style="display:block; color:#aaa; margin-bottom:10px; font-family: 'Outfit', sans-serif;">Configuración
                        del Plan</label>
                    <div style="display: grid; gap: 15px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                        <?php foreach ($planes as $id => $nombre): ?>
                            <label class="plan-card" style="cursor: pointer;">
                                <input type="radio" name="plan_id" value="<?= $id ?>" <?= ($usuario['plan_id'] == $id) ? 'checked' : '' ?> style="display:none;">
                                <div class="card-content">
                                    <div style="font-weight: bold; margin-bottom: 5px; font-family: 'Outfit', sans-serif;">
                                        <?= $nombre ?></div>
                                    <?php if ($id == 2): ?>
                                        <span
                                            style="color: #e50914; font-size: 0.8rem; font-weight:bold; font-family: 'Outfit', sans-serif;">RECOMENDADO</span>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php else: ?>
                <div
                    style="background: #333; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #e50914;">
                    <h3 style="margin: 0 0 10px 0; font-family: 'Playfair Display', serif;">🎈 Perfil Kids</h3>
                    <p style="color: #ccc; margin: 0; font-family: 'Outfit', sans-serif;">
                        Hola <strong><?= esc($usuario['username']) ?></strong>. ¡Elige tu personaje favorito!
                    </p>
                    <input type="hidden" name="username" value="<?= esc($usuario['username']) ?>">
                    <input type="hidden" name="plan_id" value="<?= esc($usuario['plan_id']) ?>">
                </div>
            <?php endif; ?>

            <div class="form-group" style="margin-bottom: 40px;">
                <label
                    style="display:block; color:#aaa; margin-bottom:20px; font-size: 1.2rem; font-family: 'Outfit', sans-serif;">Elige
                    tu Icono</label>

                <div class="avatar-grid">
                    <?php foreach ($avatares as $img): ?>
                        <?php
                        // Lógica para detectar si es URL externa o local
                        $rutaImg = str_starts_with($img, 'http') ? $img : base_url('assets/img/avatars/' . $img);
                        ?>
                        <label class="avatar-option">
                            <input type="radio" name="avatar" value="<?= $img ?>" <?= ($usuario['avatar'] == $img) ? 'checked' : '' ?> style="display:none;">
                            <img src="<?= $rutaImg ?>" alt="Avatar">
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-top: 40px; border-top: 1px solid #333; padding-top: 20px;">
                <button type="submit" class="btn-guardar">
                    Guardar Cambios
                </button>
                <a href="<?= base_url('/') ?>" class="btn-cancelar">
                    Cancelar
                </a>
            </div>

        </form>
    </div>
</div>

<style>
    /* GRID DE AVATARES */
    .avatar-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .avatar-option {
        cursor: pointer;
        position: relative;
    }

    /* ESTILO BASE DE LAS FOTOS */
    .avatar-option img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 4px solid transparent;
        transition: all 0.2s ease-in-out;
        opacity: 0.7;
    }

    /* EFECTO HOVER */
    .avatar-option img:hover {
        opacity: 1;
        transform: scale(1.1);
        border-color: #aaa;
    }

    /* SELECCIONADO (Input Checked) */
    .avatar-option input:checked+img {
        border-color: #e50914 !important;
        /* ROJO NETFLIX */
        opacity: 1;
        transform: scale(1.15);
        box-shadow: 0 0 20px rgba(229, 9, 20, 0.6);
        z-index: 10;
    }

    /* TARJETAS DE PLANES */
    .card-content {
        background: #333;
        padding: 20px;
        border-radius: 8px;
        border: 2px solid transparent;
        text-align: center;
        transition: 0.3s;
    }

    /* Plan Seleccionado */
    .plan-card input:checked+.card-content {
        border-color: white !important;
        background: #444;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
    }

    .plan-card:hover .card-content {
        background: #444;
    }

    /* BOTONES */
    .btn-guardar {
        background: white;
        color: black;
        border: none;
        padding: 12px 30px;
        font-weight: bold;
        font-size: 1.1rem;
        cursor: pointer;
        border-radius: 4px;
        font-family: 'Outfit', sans-serif;
        transition: 0.3s;
    }

    .btn-guardar:hover {
        background: #ddd;
    }

    .btn-cancelar {
        background: transparent;
        color: #aaa;
        border: 1px solid #aaa;
        padding: 12px 30px;
        font-weight: bold;
        font-size: 1.1rem;
        text-decoration: none;
        display: inline-block;
        border-radius: 4px;
        font-family: 'Outfit', sans-serif;
        transition: 0.3s;
    }

    .btn-cancelar:hover {
        border-color: white;
        color: white;
    }
</style>

<script>
    // Detectar si el usuario cambia a Plan Premium (ID 2) para cambiar el texto del botón
    const radios = document.querySelectorAll('input[name="plan_id"]');
    const submitBtn = document.querySelector('.btn-guardar');

    // El plan actual viene de PHP
    const currentPlan = <?= $usuario['plan_id'] ?>;

    if (radios) {
        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                // Si selecciona Premium (2) y actualmente es Free (1)
                if (this.value == 2 && currentPlan == 1) {
                    submitBtn.innerText = "Ir al Pago (9.99€)";
                    submitBtn.style.background = "#e50914"; // Rojo
                    submitBtn.style.color = "white";
                } else {
                    submitBtn.innerText = "Guardar Cambios";
                    submitBtn.style.background = "white";
                    submitBtn.style.color = "black";
                }
            });
        });
    }
</script>