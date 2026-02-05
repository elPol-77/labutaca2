<div class="profile-container" style="padding-top: 100px; min-height: 100vh; background: #141414; color: white;">
    
    <div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
        
        <h1 style="border-bottom: 1px solid #333; padding-bottom: 20px; margin-bottom: 30px;">
            Editar Perfil
        </h1>

        <?php if(session()->getFlashdata('success')): ?>
            <div style="background: #46d369; color: white; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('perfil/update') ?>" method="post">
            <?= csrf_field() ?>

            <?php if (!$esKids): ?>
                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display:block; color:#aaa; margin-bottom:10px;">Nombre del Perfil</label>
                    <input type="text" name="username" value="<?= esc($usuario['username']) ?>" 
                           style="width: 100%; padding: 15px; background: #333; border: 1px solid #333; color: white; font-size: 1.2rem; border-radius: 4px;">
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display:block; color:#aaa; margin-bottom:10px;">Configuración del Plan</label>
                    <div style="display: grid; gap: 15px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                        <?php foreach ($planes as $id => $nombre): ?>
                            <label class="plan-card" style="cursor: pointer;">
                                <input type="radio" name="plan_id" value="<?= $id ?>" <?= ($usuario['plan_id'] == $id) ? 'checked' : '' ?> style="display:none;">
                                <div class="card-content" style="background: #333; padding: 20px; border-radius: 8px; border: 2px solid transparent; text-align: center; transition: 0.3s;">
                                    <div style="font-weight: bold; margin-bottom: 5px;"><?= $nombre ?></div>
                                    <?php if ($id == 2): ?><span style="color: #e50914; font-size: 0.8rem;">RECOMENDADO</span><?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div style="background: #333; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                    <h3>👤 Perfil Kids</h3>
                    <p style="color: #aaa;">Hola <strong><?= esc($usuario['username']) ?></strong>. Aquí puedes elegir tu nuevo icono.</p>
                </div>
            <?php endif; ?>

            <div class="form-group" style="margin-bottom: 40px;">
                <label style="display:block; color:#aaa; margin-bottom:20px; font-size: 1.2rem;">Elige tu Icono</label>
                
                <div class="avatar-grid" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
                    <?php foreach ($avatares as $img): ?>
                        <label class="avatar-option" style="cursor: pointer;">
                            <input type="radio" name="avatar" value="<?= $img ?>" <?= ($usuario['avatar'] == $img) ? 'checked' : '' ?> style="display:none;">
                            <img src="<?= $img ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 3px solid transparent; transition: 0.2s;">
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-top: 40px; border-top: 1px solid #333; padding-top: 20px;">
                <button type="submit" style="background: white; color: black; border: none; padding: 12px 30px; font-weight: bold; font-size: 1.1rem; cursor: pointer;">
                    Guardar
                </button>
                <a href="<?= base_url('/') ?>" style="background: transparent; color: #aaa; border: 1px solid #aaa; padding: 12px 30px; font-weight: bold; font-size: 1.1rem; text-decoration: none; display: inline-block;">
                    Cancelar
                </a>
            </div>

        </form>
    </div>
</div>

<style>
    /* CSS ESPECÍFICO PARA EFECTOS DE SELECCIÓN */
    
    /* Efecto al seleccionar Avatar */
    .avatar-option input:checked + img {
        border-color: white !important;
        transform: scale(1.1);
        box-shadow: 0 0 15px rgba(255,255,255,0.3);
    }
    .avatar-option img:hover {
        border-color: #aaa;
    }

    /* Efecto al seleccionar Plan */
    .plan-card input:checked + .card-content {
        border-color: white !important;
        background: #444;
    }
    .plan-card:hover .card-content {
        background: #444;
    }
    <style>
    /* 1. ESTILO BASE DE LAS FOTOS */
    .avatar-option img {
        width: 100px; 
        height: 100px; 
        object-fit: cover; 
        border-radius: 4px; 
        /* Borde transparente por defecto para que no "baile" al seleccionarlo */
        border: 4px solid transparent; 
        transition: all 0.2s ease-in-out;
        opacity: 0.7; /* Un poco apagado si no está seleccionado */
    }

    /* 2. EFECTO HOVER (Al pasar el ratón) */
    .avatar-option img:hover {
        opacity: 1;
        transform: scale(1.05);
        border-color: #aaa;
    }

    /* 3. EL SELECCIONADO (El que tiene ahora o el que pincha) - MARCO ROJO */
    .avatar-option input:checked + img {
        border-color: #e50914 !important; /* <--- TU COLOR ROJO */
        opacity: 1;
        transform: scale(1.15); /* Un poco más grande para destacar */
        box-shadow: 0 0 20px rgba(229, 9, 20, 0.6); /* Resplandor rojo */
        z-index: 10; /* Para que quede por encima de los demás */
    }
</style>
</style>