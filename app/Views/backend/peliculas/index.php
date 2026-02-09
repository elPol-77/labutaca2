<?= view('backend/templates/header') ?>
<?php
$esSerie = (strpos(current_url(), 'series') !== false); 
    $rutaCrear = $esSerie ? 'admin/series/create' : 'admin/peliculas/create';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Administrar <?= $esSerie ? 'Series' : 'Películas' ?></h2>
    
    <a href="<?= base_url($rutaCrear) ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i> Agregar <?= $esSerie ? 'Serie' : 'Película' ?>
    </a>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Portada</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Plan</th>
                    <th>Estado</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($peliculas as $p): ?>
                <tr>
                    <td class="ps-4">
                        <?php $img = str_starts_with($p['imagen'], 'http') ? $p['imagen'] : base_url('assets/img/'.$p['imagen']); ?>
                        <img src="<?= $img ?>" width="40" height="60" style="object-fit:cover; border-radius:4px;">
                    </td>
                    <td class="fw-bold"><?= $p['titulo'] ?> <br> <small class="text-muted"><?= $p['anio'] ?></small></td>
                    <td><?= ($p['tipo_id'] == 1) ? '<span class="badge bg-secondary">Película</span>' : '<span class="badge bg-info">Serie</span>' ?></td>
                    <td><?= ($p['nivel_acceso'] == 1) ? '<span class="badge bg-success">Free</span>' : '<span class="badge bg-warning text-dark">Premium</span>' ?></td>
                    <td><?= ($p['destacada'] == 1) ? '<i class="fa fa-star text-warning" title="Destacada"></i>' : '' ?></td>
                    <td class="text-end pe-4">
                        <a href="<?= base_url('admin/peliculas/editar/'.$p['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>
                        <a href="<?= base_url('admin/peliculas/borrar/'.$p['id']) ?>" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Seguro que quieres borrar esto?');">
                           <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    <?= $pager->links() ?>
</div>

<?= view('backend/templates/footer') ?>