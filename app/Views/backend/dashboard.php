<?= view('backend/templates/header') ?>

<div class="container-fluid p-4">
    <h2 class="mb-4 text-gray-800">Resumen General</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-kpi bg-primary text-white p-4 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="display-4 fw-bold mb-0"><?= $total_peliculas ?? 0 ?></h3>
                        <p class="m-0 opacity-75">Películas</p>
                    </div>
                    <i class="fa fa-film fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-kpi bg-success text-white p-4 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="display-4 fw-bold mb-0"><?= $total_series ?? 0 ?></h3>
                        <p class="m-0 opacity-75">Series</p>
                    </div>
                    <i class="fa fa-tv fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-kpi bg-warning text-dark p-4 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="display-4 fw-bold mb-0"><?= $total_usuarios ?? 0 ?></h3>
                        <p class="m-0 opacity-75">Usuarios</p>
                    </div>
                    <i class="fa fa-users fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-gray-800">Último contenido agregado</h5>
            <a href="<?= base_url('admin/peliculas') ?>" class="btn btn-sm btn-primary">Ver todo</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Título</th>
                            <th>Tipo</th>
                            <th>Año</th>
                            <th>Acceso</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($ultimas)): ?>
                            <?php foreach($ultimas as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php 
                                            $imgUrl = 'https://via.placeholder.com/30x45?text=NA';
                                            if(!empty($p['imagen'])) {
                                                $imgUrl = str_starts_with($p['imagen'], 'http') 
                                                    ? $p['imagen'] 
                                                    : base_url('assets/img/'.$p['imagen']);
                                            }
                                        ?>
                                        <img src="<?= $imgUrl ?>" class="rounded me-2 shadow-sm" width="30" height="45" style="object-fit: cover;">
                                        <span class="fw-bold text-dark"><?= esc($p['titulo']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if($p['tipo_id'] == 1): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">Película</span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Serie</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $p['anio'] ?></td>
                                <td>
                                    <?php if($p['nivel_acceso'] == 1): ?>
                                        <span class="badge bg-secondary">Gratis</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Premium</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php 
                                        // LOGICA PARA EL LINK DE EDITAR
                                        // Detecta si es Serie (2) o Película (1) para generar la URL correcta
                                        $rutaEditar = ($p['tipo_id'] == 2) 
                                            ? base_url('admin/series/editar/' . $p['id']) 
                                            : base_url('admin/peliculas/editar/' . $p['id']);
                                    ?>
                                    <a href="<?= $rutaEditar ?>" class="btn btn-sm btn-outline-primary" title="Editar Contenido">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                                    No hay contenido registrado aún.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('backend/templates/footer') ?>