<?= view('backend/templates/header') ?>
<?php
    $esSerie = (strpos(current_url(), 'series') !== false); 
    $rutaCrear = $esSerie ? 'admin/series/create' : 'admin/peliculas/create';
    $titulo = $esSerie ? 'Series' : 'Películas';
?>

<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <h2 class="h3 mb-0 text-gray-800">Administrar <?= $titulo ?></h2>
        
        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fa fa-search text-secondary"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Buscar <?= strtolower($titulo) ?>...">
            </div>

            <a href="<?= base_url($rutaCrear) ?>" class="btn btn-primary text-nowrap">
                <i class="fa fa-plus"></i> <span class="d-none d-sm-inline">Agregar</span> <?= $esSerie ? 'Serie' : 'Película' ?>
            </a>
        </div>
    </div>

    <?php if(session()->getFlashdata('msg')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('msg') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="mediaTable">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="ps-4">Portada</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Plan</th>
                            <th class="text-center">Destacado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($peliculas)): ?>
                            <?php foreach($peliculas as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <?php $img = str_starts_with($p['imagen'], 'http') ? $p['imagen'] : base_url('assets/img/'.$p['imagen']); ?>
                                    <div style="width: 40px; height: 60px; background: #f0f0f0; border-radius: 4px; overflow: hidden;">
                                        <img src="<?= $img ?>" width="100%" height="100%" style="object-fit:cover;" loading="lazy" alt="Portada">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark"><?= esc($p['titulo']) ?></span>
                                        <small class="text-muted"><?= esc($p['anio']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?= ($p['tipo_id'] == 1) 
                                        ? '<span class="badge bg-secondary">Película</span>' 
                                        : '<span class="badge bg-info text-dark">Serie</span>' ?>
                                </td>
                                <td>
                                    <?= ($p['nivel_acceso'] == 1) 
                                        ? '<span class="badge bg-success">Free</span>' 
                                        : '<span class="badge bg-warning text-dark">Premium</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?= ($p['destacada'] == 1) 
                                        ? '<i class="fa fa-star text-warning fa-lg" title="Destacada"></i>' 
                                        : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    <a href="<?= base_url('admin/peliculas/editar/'.$p['id']) ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/peliculas/borrar/'.$p['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('¿Seguro que quieres eliminar este contenido?');" title="Eliminar">
                                       <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay contenidos registrados.</td>
                            </tr>
                        <?php endif; ?>
                        
                        <tr id="noResultsRow" style="display: none;">
                            <td colspan="6" class="text-center py-4 text-muted">No se encontraron resultados para tu búsqueda.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <?= $pager->links() ?>
    </div>
</div>

<?= view('backend/templates/footer') ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("mediaTable");
    const rows = table.getElementsByTagName("tr");
    const noResultsRow = document.getElementById("noResultsRow");

    searchInput.addEventListener("keyup", function() {
        const filter = searchInput.value.toLowerCase();
        let visibleCount = 0;

        // Empezamos en 1 para saltar el encabezado (thead)
        // Usamos rows.length - 1 para no contar la fila de "no results" si está al final
        for (let i = 1; i < rows.length; i++) {
            let row = rows[i];
            
            // Ignorar la fila de mensaje de "no resultados"
            if(row.id === "noResultsRow") continue;

            // Obtener texto de la fila
            let text = row.textContent || row.innerText;
            
            if (text.toLowerCase().indexOf(filter) > -1) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        }

        // Mostrar u ocultar mensaje de "sin resultados"
        if (visibleCount === 0) {
            noResultsRow.style.display = "";
        } else {
            noResultsRow.style.display = "none";
        }
    });
});
</script>