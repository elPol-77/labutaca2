<div class="genre-container" style="padding-top: 100px; min-height: 100vh; background-color: #141414; color: white; padding-bottom: 50px;">
    
    <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 4%;">

        <h1 id="titulo-genero" style="font-size: 2.5rem; margin-bottom: 40px; font-weight: 800;">
            Explorando: <span style="color: #00d2ff;"><?= esc($infoGenero['nombre']) ?></span>
        </h1>

        <div id="vista-filas">
            
            <?php if (!empty($peliculas)): ?>
            <div class="row-section" style="margin-bottom: 50px;">
                <div class="row-header" style="display: flex; align-items: center; gap: 20px; margin-bottom: 15px;">
                    <h2 style="font-size: 1.5rem; margin: 0;">Películas de <?= esc($infoGenero['nombre']) ?></h2>
                    
                    <button onclick="cargarGrid(1)" class="btn-ver-todo">
                        Ver todo <i class="fa fa-th"></i>
                    </button>
                </div>

                <div class="horizontal-scroll">
                    <?php foreach ($peliculas as $peli): ?>
                        <a href="<?= base_url('ver/' . $peli['id']) ?>" class="poster-card">
                            <img src="<?= $peli['imagen'] ?>" loading="lazy">
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($series)): ?>
            <div class="row-section">
                <div class="row-header" style="display: flex; align-items: center; gap: 20px; margin-bottom: 15px;">
                    <h2 style="font-size: 1.5rem; margin: 0;">Series de <?= esc($infoGenero['nombre']) ?></h2>
                    
                    <button onclick="cargarGrid(2)" class="btn-ver-todo">
                        Ver todo <i class="fa fa-th"></i>
                    </button>
                </div>

                <div class="horizontal-scroll">
                    <?php foreach ($series as $serie): ?>
                        <a href="<?= base_url('ver/' . $serie['id']) ?>" class="poster-card">
                            <img src="<?= $serie['imagen'] ?>" loading="lazy">
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
        <div id="vista-grid" style="display: none; animation: fadeIn 0.5s;">
            
            <button style="background: transparent; border: 1px solid #555; color: white; padding: 10px 20px; border-radius: 30px; cursor: pointer; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: bold z-index:1000;">
                <i class="fa fa-arrow-left"></i> <a href="<?=base_url("/")?>">Volver</a>
            </button>

            <div id="grid-content"></div>

            <div id="grid-loader" style="text-align: center; padding: 50px; display: none;">
                <i class="fa fa-spinner fa-spin" style="font-size: 3rem; color: #00d2ff;"></i>
            </div>
        </div>

    </div>
</div>

<script>
    // Variables globales
    const generoId = <?= $infoGenero['id'] ?>;
    const baseUrl = "<?= base_url() ?>";

    function cargarGrid(tipoId) {
        // 1. UI: Ocultar filas, mostrar loader
        document.getElementById('vista-filas').style.display = 'none';
        document.getElementById('vista-grid').style.display = 'block';
        document.getElementById('grid-content').innerHTML = ''; // Limpiar anterior
        document.getElementById('grid-loader').style.display = 'block';

        // 2. AJAX: Pedir el grid
        // La URL será: /genero/ID_GENERO/ID_TIPO (ej: /genero/28/1)
        fetch(`${baseUrl}genero/${generoId}/${tipoId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Importante para que CodeIgniter detecte isAJAX()
            }
        })
        .then(response => response.text())
        .then(html => {
            // 3. Inyectar HTML
            document.getElementById('grid-loader').style.display = 'none';
            document.getElementById('grid-content').innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            alert('Error al cargar el contenido.');
            cerrarGrid();
        });
    }

    function cerrarGrid() {
        document.getElementById('vista-grid').style.display = 'none';
        document.getElementById('vista-filas').style.display = 'block';
        // Opcional: Hacer scroll arriba
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .btn-ver-todo {
        background: transparent;
        border: 1px solid #555;
        color: #ccc;
        padding: 5px 15px;
        border-radius: 4px;
        cursor: pointer; /* Importante para que parezca botón */
        font-size: 0.8rem;
        font-weight: bold;
        transition: 0.3s;
        text-transform: uppercase;
    }
    .btn-ver-todo:hover {
        border-color: white;
        color: white;
        background: rgba(255,255,255,0.1);
    }
    /* Estilos del scroll y cards (Los mismos de antes) */
    .horizontal-scroll {
        display: flex; gap: 15px; overflow-x: auto; padding-bottom: 20px; scrollbar-width: thin; scrollbar-color: #333 #141414;
    }
    .poster-card {
        flex: 0 0 auto; width: 200px; aspect-ratio: 2/3; border-radius: 8px; overflow: hidden; transition: transform 0.3s;
    }
    .poster-card img { width: 100%; height: 100%; object-fit: cover; }
    .poster-card:hover { transform: scale(1.05); z-index: 2; box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
    
    @media (max-width: 768px) {
        .poster-card { width: 140px; }
    }
</style>