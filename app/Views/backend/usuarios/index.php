<div class="container-fluid p-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Usuarios</h1>
        
        <div class="input-group" style="max-width: 300px; width: 100%;">
            <span class="input-group-text bg-white border-end-0">
                <i class="fa fa-search text-gray-400"></i>
            </span>
            <input type="text" id="buscadorUsuarios" class="form-control border-start-0 ps-0" placeholder="Buscar usuario, email...">
        </div>
    </div>

    <?php if(session('msg')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('msg') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger fw-bold alert-dismissible fade show" role="alert">
            <i class="fa fa-lock"></i> <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="tablaUsuarios" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Plan</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= str_starts_with($u['avatar'], 'http') ? $u['avatar'] : base_url('assets/img/avatars/'.$u['avatar']) ?>" 
                                         class="rounded-circle me-2 object-fit-cover" width="35" height="35" alt="Avatar">
                                    <span class="fw-bold text-dark"><?= esc($u['username']) ?></span>
                                </div>
                            </td>
                            <td><?= esc($u['email']) ?></td>
                            <td>
                                <?php if($u['rol'] == 'admin'): ?>
                                    <span class="badge rounded-pill bg-danger"><i class="fa fa-user-shield me-1"></i> ADMIN</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-primary">Usuario</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $badgeClass = match($u['plan_id']) {
                                        '1' => 'bg-secondary', // Free
                                        '2' => 'bg-warning text-dark', // Premium
                                        '3' => 'bg-info text-white', // Kids
                                        default => 'bg-light text-dark border'
                                    };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= esc($u['nombre_plan'] ?? 'Free') ?></span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('admin/usuarios/editar/'.$u['id']) ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <?php if($u['id'] != 1): ?>
                                        <a href="<?= base_url('admin/usuarios/borrar/'.$u['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Seguro que quieres eliminar a este usuario? Se borrarán todos sus datos.');" title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="El Admin Principal no se puede borrar">
                                            <i class="fa fa-shield-alt"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="noResults" style="display: none;">
                            <td colspan="6" class="text-center text-muted py-3">
                                No se encontraron resultados.
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center mt-3">
                    <?= $pager->links() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputBusqueda = document.getElementById("buscadorUsuarios");
    const tabla = document.getElementById("tablaUsuarios");
    const filas = tabla.getElementsByTagName("tr");
    const noResults = document.getElementById("noResults");

    inputBusqueda.addEventListener("keyup", function() {
        const filtro = inputBusqueda.value.toLowerCase();
        let hayResultados = false;

        // Comenzamos desde i=1 para saltar el encabezado (thead)
        // Nota: Si tienes filas dentro de tbody que no sean datos (como el 'noResults'), ajusta la lógica.
        // Aquí asumimos que las filas de datos están en tbody
        const filasBody = tabla.querySelector("tbody").getElementsByTagName("tr");

        for (let i = 0; i < filasBody.length; i++) {
            const fila = filasBody[i];
            
            // Ignoramos la fila de "No resultados" en la búsqueda
            if (fila.id === "noResults") continue;

            const textoFila = fila.textContent || fila.innerText;

            if (textoFila.toLowerCase().indexOf(filtro) > -1) {
                fila.style.display = "";
                hayResultados = true;
            } else {
                fila.style.display = "none";
            }
        }

        // Mostrar u ocultar mensaje de "No resultados"
        if (hayResultados) {
            noResults.style.display = "none";
        } else {
            noResults.style.display = "";
        }
    });
});
</script>