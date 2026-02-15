<div class="genre-container"
    style="padding-top: 100px; min-height: 100vh; background-color: #0f0c29; color: white; padding-bottom: 50px;">

    <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 4%;">

        <h1 id="titulo-genero" style="font-size: 2.5rem; margin-bottom: 40px; font-weight: 800;">
            Explorando: <span style="color: #00d2ff;"><?= esc($infoGenero['nombre']) ?></span>
        </h1>

        <div id="vista-filas">

            <?php if (!empty($peliculas)): ?>
                <div class="row-section" style="margin-bottom: 50px;">
                    <div class="row-header"
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <h2 style="font-size: 1.5rem; margin: 0;">Películas de <?= esc($infoGenero['nombre']) ?></h2>
                        <button onclick="abrirGrid(1)" class="btn-ver-todo">Ver todo <i class="fa fa-th"></i></button>
                    </div>

                    <div class="carrusel-wrapper">
                        <button class="flecha-carrusel flecha-izq" onclick="desplazarFila(this, -1)">
                            <i class="fa fa-chevron-left"></i>
                        </button>

                        <div class="horizontal-scroll ocultar-scrollbar">
                            <?php foreach ($peliculas as $peli): ?>
                                <a href="<?= base_url('detalle/' . $peli['id']) ?>" class="poster-card">
                                    <img src="<?= $peli['imagen'] ?>" loading="lazy"
                                        alt="<?= esc($peli['titulo'] ?? 'Película') ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <button class="flecha-carrusel flecha-der" onclick="desplazarFila(this, 1)">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($series)): ?>
                <div class="row-section">
                    <div class="row-header"
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <h2 style="font-size: 1.5rem; margin: 0;">Series de <?= esc($infoGenero['nombre']) ?></h2>
                        <button onclick="abrirGrid(2)" class="btn-ver-todo">Ver todo <i class="fa fa-th"></i></button>
                    </div>

                    <div class="carrusel-wrapper">
                        <button class="flecha-carrusel flecha-izq" onclick="desplazarFila(this, -1)">
                            <i class="fa fa-chevron-left"></i>
                        </button>

                        <div class="horizontal-scroll ocultar-scrollbar">
                            <?php foreach ($series as $serie): ?>
                                <a href="<?= base_url('detalle/' . $serie['id']) ?>" class="poster-card">
                                    <img src="<?= $serie['imagen'] ?>" loading="lazy"
                                        alt="<?= esc($serie['titulo'] ?? 'Serie') ?>">
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <button class="flecha-carrusel flecha-der" onclick="desplazarFila(this, 1)">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div id="vista-grid" style="display: none; animation: fadeIn 0.5s;">

            <button onclick="cerrarGrid()" class="btn-volver">
                <i class="fa fa-arrow-left"></i> Volver a filas
            </button>

            <div id="grid-content" class="film-grid"></div>

            <div id="grid-loader" style="text-align: center; padding: 50px; display: none;">
                <div class="spinner"></div>
            </div>
        </div>

    </div>
</div>

<script>
    const generoId = <?= $infoGenero['id'] ?>;
    const baseUrl = "<?= base_url() ?>";

    let currentPage = 1;
    let isLoading = false;
    let hasMore = true; 
    let currentTipoId = null;

    // 1. Iniciar la vista de Grid
    function abrirGrid(tipoId) {
        currentPage = 1;
        hasMore = true;
        currentTipoId = tipoId;
        document.getElementById('grid-content').innerHTML = '';

        // Cambiar Vistas
        document.getElementById('vista-filas').style.display = 'none';
        document.getElementById('vista-grid').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });

        cargarMasPeliculas();
    }

    function cargarMasPeliculas() {
        if (isLoading || !hasMore) return;

        isLoading = true;
        document.getElementById('grid-loader').style.display = 'block';

        fetch(`${baseUrl}genero/${generoId}/${currentTipoId}?page=${currentPage}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                document.getElementById('grid-loader').style.display = 'none';

                if (html.trim() === '') {
                    hasMore = false;
                } else {
                    document.getElementById('grid-content').insertAdjacentHTML('beforeend', html);
                    currentPage++;
                }
                isLoading = false;
            })
            .catch(err => {
                console.error(err);
                isLoading = false;
                document.getElementById('grid-loader').style.display = 'none';
                alert('Error de conexión.');
            });
    }

    window.addEventListener('scroll', () => {
        if (document.getElementById('vista-grid').style.display === 'block') {

            const pos = (document.documentElement.scrollTop || document.body.scrollTop) + document.documentElement.offsetHeight;
            const max = document.documentElement.scrollHeight;

            if (pos > max - 500 && !isLoading) {
                cargarMasPeliculas();
            }
        }
    });
    function cerrarGrid() {
        document.getElementById('vista-grid').style.display = 'none';
        document.getElementById('vista-filas').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function desplazarFila(boton, direccion) {

        const contenedor = boton.parentElement.querySelector('.horizontal-scroll');
        const anchoDesplazamiento = contenedor.clientWidth * 0.8;

        contenedor.scrollBy({
            left: anchoDesplazamiento * direccion, 
            behavior: 'smooth'
        });
    }
</script>