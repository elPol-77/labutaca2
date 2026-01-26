<?= view('backend/templates/header') ?>

    <h2 class="mb-4">Resumen General</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-kpi bg-primary text-white p-4">
                <h3><?= $total_peliculas ?></h3>
                <p class="m-0">Películas</p>
                <i class="fa fa-film kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-kpi bg-success text-white p-4">
                <h3><?= $total_series ?></h3>
                <p class="m-0">Series</p>
                <i class="fa fa-tv kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-kpi bg-warning text-dark p-4">
                <h3><?= $total_usuarios ?></h3>
                <p class="m-0">Usuarios</p>
                <i class="fa fa-users kpi-icon"></i>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Último contenido agregado</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Título</th>
                        <th>Año</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ultimas as $p): ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?= $p['titulo'] ?></td>
                        <td><?= $p['anio'] ?></td>
                        <td>
                            <?php if($p['nivel_acceso'] == 1): ?>
                                <span class="badge bg-success rounded-pill">Free</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark rounded-pill">Premium</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?= view('backend/templates/footer') ?>