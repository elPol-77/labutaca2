<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Usuarios</h1>
    </div>

    <?php if(session('msg')): ?>
        <div class="alert alert-success"><?= session('msg') ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger fw-bold"><i class="fa fa-lock"></i> <?= session('error') ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Plan</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td>
                                <img src="<?= str_starts_with($u['avatar'], 'http') ? $u['avatar'] : base_url('assets/img/avatars/'.$u['avatar']) ?>" 
                                     class="rounded-circle me-2" width="30" height="30">
                                <strong><?= esc($u['username']) ?></strong>
                            </td>
                            <td><?= esc($u['email']) ?></td>
                            <td>
                                <?php if($u['rol'] == 'admin'): ?>
                                    <span class="badge bg-danger">ADMIN</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Usuario</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $badgeClass = match($u['plan_id']) {
                                        '1' => 'bg-secondary', // Free
                                        '2' => 'bg-warning text-dark', // Premium
                                        '3' => 'bg-info', // Kids
                                        default => 'bg-light text-dark'
                                    };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= esc($u['nombre_plan'] ?? 'Free') ?></span>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('admin/usuarios/editar/'.$u['id']) ?>" class="btn btn-sm btn-warning">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <?php if($u['id'] != 1): ?>
                                    <a href="<?= base_url('admin/usuarios/borrar/'.$u['id']) ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Seguro que quieres echar a este usuario? Se borrará su lista y reseñas.');">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled title="El Admin Principal no se borra"><i class="fa fa-shield"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    <?= $pager->links() ?>
                </div>
            </div>
        </div>
    </div>
</div>