<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<?= view('backend/templates/header') ?>

<style>
    .ui-autocomplete {
        z-index: 9999 !important;
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .ui-menu-item-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px;
    }

    .ui-menu-item-wrapper img {
        width: 30px;
        height: 45px;
        object-fit: cover;
        border-radius: 3px;
    }

    .ui-state-active {
        background: #0d6efd !important;
        border: 1px solid #0d6efd !important;
        color: white !important;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-10">

        <h2 class="mb-4">
            <?= (isset($tipo_id) && $tipo_id == 2) ? 'Agregar Nueva Serie' : 'Agregar Nueva Película' ?>
        </h2>

        <div class="card mb-4 border-primary">
            <div class="card-body bg-light">
                <label class="fw-bold text-primary mb-2"><i class="fa fa-magic"></i> Importación Automática
                    (OMDb)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
                    <input type="text" id="imdb_autocomplete" class="form-control form-control-lg"
                        placeholder="Escribe el título aquí (ej: Batman, Breaking Bad)...">
                </div>
                <small class="text-muted">Selecciona una opción de la lista para rellenar <b>automáticamente</b> ficha,
                    actores, directores y rating.</small>
            </div>
        </div>
        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    <?php foreach (session('errors') as $error): ?>
                        <li><i class="fa fa-exclamation-circle"></i> <?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>
        <form
            action="<?= base_url((isset($tipo_id) && $tipo_id == 2) ? 'admin/series/store' : 'admin/peliculas/store') ?>"
            method="post" enctype="multipart/form-data">

            <?= csrf_field() ?>

            <input type="hidden" name="generos_texto" id="generos_texto">
            <input type="hidden" name="directores_texto" id="directores_texto">
            <input type="hidden" name="actores_texto" id="actores_texto">
            <input type="hidden" name="imdb_rating" id="imdb_rating_input">

            <input type="hidden" name="url_imagen_externa" id="url_imagen_externa">
            <input type="hidden" name="url_bg_externa" id="url_bg_externa">

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold">Datos de la Ficha</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Título</label>
                                <input type="text" name="titulo" id="titulo" class="form-control" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Año</label>
                                    <input type="number" name="anio" id="anio" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Duración (min)</label>
                                    <input type="number" name="duracion" id="duracion" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Edad Recomendada</label>
                                    <select name="edad_recomendada" class="form-select">
                                        <option value="0">TP (Todos)</option>
                                        <option value="7">+7 Años</option>
                                        <option value="12" selected>+12 Años</option>
                                        <option value="16">+16 Años</option>
                                        <option value="18">+18 Años</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Sinopsis</label>
                                <textarea name="descripcion" id="descripcion" class="form-control" rows="4"></textarea>
                            </div>

                            <div class="mb-3">
                                <label>URL Video (YouTube / MP4)</label>
                                <input type="text" name="url_video" class="form-control" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold">Configuración</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Tipo</label>
                                <select name="tipo_id" class="form-select">
                                    <option value="1" <?= (isset($tipo_id) && $tipo_id == 1) ? 'selected' : '' ?>>Película
                                    </option>
                                    <option value="2" <?= (isset($tipo_id) && $tipo_id == 2) ? 'selected' : '' ?>>Serie
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Plan de Acceso</label>
                                <select name="nivel_acceso" class="form-select">
                                    <option value="1">Gratis</option>
                                    <option value="2">Premium</option>
                                </select>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="destacada" value="1"
                                    id="checkDestacada">
                                <label class="form-check-label" for="checkDestacada">Destacada (Carrusel)</label>
                            </div>

                            <hr>

                            <div class="mb-2">
                                <label class="small text-muted">Géneros Detectados</label>
                                <input type="text" id="generos_visual" class="form-control form-control-sm" readonly
                                    style="background:#e9ecef;">
                            </div>
                            <div class="mb-2">
                                <label class="small text-muted">Rating IMDb</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-warning text-dark fw-bold"><i
                                            class="fa fa-star"></i></span>
                                    <input type="text" id="imdb_rating_visual" class="form-control" readonly
                                        style="background:#fff;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-bold">Imágenes</div>
                        <div class="card-body text-center">
                            <img id="preview_poster" src="https://via.placeholder.com/150x220?text=Sin+Imagen"
                                style="width: 100%; max-width:150px; border-radius: 5px; margin-bottom: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">

                            <div class="text-start mt-3">
                                <label class="small">Subir Póster (Opcional)</label>
                                <input type="file" name="imagen" class="form-control form-control-sm">
                            </div>
                            <div class="text-start mt-2">
                                <label class="small">Subir Fondo (Opcional)</label>
                                <input type="file" name="imagen_bg" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mb-5">
                <button type="submit" class="btn btn-success btn-lg fw-bold shadow">
                    <i class="fa fa-save"></i> Guardar Contenido
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        const apiKey = '78a51c36'; // TU API KEY

        // === CORRECCIÓN AQUÍ ===
        // 1. Usamos 'let' en vez de 'const' para poder cambiarlo.
        // 2. Primero intentamos leer lo que dice PHP.
        let tipoBusqueda = '<?= (isset($tipo_id) && $tipo_id == 2) ? 'series' : 'movie' ?>';

        // 3. SEGURO ANTI-FALLOS: Si la URL del navegador dice "series", forzamos que busque series.
        if (window.location.href.indexOf("series") > -1) {
            tipoBusqueda = 'series';
        }

        console.log("Modo de búsqueda OMDb: " + tipoBusqueda); // Míralo en la consola (F12)

        $("#imdb_autocomplete").autocomplete({
            minLength: 3,
            source: function (request, response) {
                $.ajax({
                    url: "https://www.omdbapi.com/",
                    dataType: "json",
                    data: {
                        apikey: apiKey,
                        s: request.term,
                        type: tipoBusqueda // Ahora enviará 'series' si estás en la URL de series
                    },
                    success: function (data) {
                        if (data.Response === "True") {
                            response($.map(data.Search, function (item) {
                                return {
                                    label: item.Title,
                                    value: item.Title,
                                    imdbID: item.imdbID,
                                    poster: item.Poster,
                                    year: item.Year
                                }
                            }));
                        } else {
                            response([{ label: "No se encontraron resultados (" + tipoBusqueda + ")", value: "" }]);
                        }
                    }
                });
            },
            create: function () {
                $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                    if (!item.imdbID) return $("<li>").append(item.label).appendTo(ul);
                    const img = (item.poster !== "N/A") ? item.poster : "https://via.placeholder.com/30x45";
                    return $("<li>").append(`<div class="ui-menu-item-wrapper"><img src="${img}"><div><strong>${item.label}</strong> <br> <span style="color:#aaa; font-size:0.8em">${item.year}</span></div></div>`).appendTo(ul);
                };
            },
            select: function (event, ui) {
                if (!ui.item.imdbID) return false;

                // LLAMADA FINAL PARA RELLENAR TODO
                fetch(`https://www.omdbapi.com/?apikey=${apiKey}&i=${ui.item.imdbID}&plot=full`)
                    .then(r => r.json())
                    .then(data => {
                        // 1. Campos Básicos
                        $('#titulo').val(data.Title);
                        $('#anio').val(parseInt(data.Year));
                        $('#duracion').val(parseInt(data.Runtime) || 0);
                        $('#descripcion').val(data.Plot);

                        // 2. Datos para procesar en Backend (Inputs Ocultos)
                        $('#generos_texto').val(data.Genre);
                        $('#directores_texto').val(data.Director);
                        $('#actores_texto').val(data.Actors);

                        // 3. Rating
                        let rating = parseFloat(data.imdbRating) || 0;
                        $('#imdb_rating_input').val(rating);
                        $('#imdb_rating_visual').val(rating);

                        // 4. Feedback Visual
                        $('#generos_visual').val(data.Genre).css('background-color', '#d1e7dd');

                        // 5. Imágenes
                        if (data.Poster !== "N/A") {
                            $('#url_imagen_externa').val(data.Poster);
                            $('#url_bg_externa').val(data.Poster);
                            $('#preview_poster').attr('src', data.Poster).show();
                        }
                    });
            }
        });
    });
</script>

<?= view('backend/templates/footer') ?>