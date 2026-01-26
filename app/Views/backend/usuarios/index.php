<?= view('backend/templates/header') ?>
<h2>Gestión de Usuarios</h2>
<table class="table table-striped mt-3">
    <thead>
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
            <td><?= $u['username'] ?></td>
            <td><?= $u['email'] ?></td>
            <td>
                <?php if($u['rol'] == 'admin'): ?>
                    <span class="badge bg-danger">ADMIN</span>
                <?php else: ?>
                    <span class="badge bg-secondary">USER</span>
                <?php endif; ?>
            </td>
            <td><?= $u['plan_id'] == 1 ? 'Free' : ($u['plan_id']==2 ? 'Premium' : 'Kids') ?></td>
            <td>
                <a href="<?= base_url('admin/usuarios/borrar/'.$u['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Borrar usuario?');"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $pager->links() ?>
<?= view('backend/templates/footer') ?>