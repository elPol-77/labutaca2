<div class="container-fluid p-4">
    <h2 class="mb-4 text-gray-800">Editar Usuario: <span class="text-primary"><?= esc($usuario['username']) ?></span></h2>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-left-primary">
        <div class="card-body">
            <form action="<?= base_url('admin/usuarios/update/'.$usuario['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Nombre de Usuario</label>
                        <input type="text" name="username" class="form-control" value="<?= esc($usuario['username']) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($usuario['email']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Rol</label>
                        <select name="rol" class="form-select" <?= ($usuario['id'] == 1) ? 'readonly style="pointer-events: none; background-color: #e9ecef;"' : '' ?>>
                            <option value="usuario" <?= $usuario['rol'] == 'usuario' ? 'selected' : '' ?>>Usuario Normal</option>
                            <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                        <?php if($usuario['id'] == 1): ?>
                            <small class="text-muted"><i class="fa fa-lock"></i> El rol del Super Admin no se puede cambiar.</small>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Plan de Suscripción</label>
                        <select name="plan_id" class="form-select">
                            <?php foreach($planes as $plan): ?>
                                <option value="<?= $plan['id'] ?>" <?= $usuario['plan_id'] == $plan['id'] ? 'selected' : '' ?>>
                                    <?= esc($plan['nombre']) ?> - <?= $plan['calidad'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <label class="fw-bold text-danger">Cambiar Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la contraseña actual">
                    <small class="text-muted">Solo rellena esto si quieres resetearle la clave al usuario.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="fa fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>