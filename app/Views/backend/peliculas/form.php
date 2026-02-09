<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<?= view('backend/templates/header') ?>

<style>
    /* Autocomplete con fotos */
    .ui-autocomplete { 
        z-index: 1050 !important;
        max-height: 300px; 
        overflow-y: auto; 
        background: white;
        border: 1px solid #ccc;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .ui-menu-item { border-bottom: 1px solid #eee; }
    .ui-menu-item-wrapper { display: flex; align-items: center; gap: 15px; padding: 8px; cursor: pointer; }
    .ui-menu-item-wrapper:hover { background-color: #f8f9fa; }
    .ui-menu-item-wrapper img { width: 45px; height: 68px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

    .checkbox-container {
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        padding: 15px;
        border-radius: 0.375rem;
        background: #fff;
    }
    .new-genre-badge {
        background-color: #fff3cd;
        border: 1px solid #ffecb5;
        padding: 2px 8px;
        border-radius: 4px;
        margin-bottom: 5px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-12">
        <h2 class="mb-4">
            <?= (isset($action) && $action == 'edit') ? 'Editar Contenido' : ((isset($tipo_id) && $tipo_id == 2) ? 'Nueva Serie' : 'Nueva Película') ?>
        </h2>
        <?php if (session()->has('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

        <div class="card mb-4 border-primary">
            <div class="card-body bg-light">
                <label class="fw-bold text-primary mb-2"><i class="fa fa-magic"></i> Importación TMDB</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
                    <input type="text" id="tmdb_autocomplete" class="form-control" placeholder="Busca título para rellenar o actualizar datos...">
                </div>
                <small class="text-muted">Si seleccionas una película aquí, se <b>sobreescribirán</b> los datos actuales del formulario.</small>
            </div>
        </div>

        <?php 
            // Determinar la URL de destino
            $urlAction = (isset($action) && $action == 'edit') 
                ? base_url('admin/peliculas/update/' . $data['id']) 
                : base_url((isset($tipo_id) && $tipo_id == 2) ? 'admin/series/store' : 'admin/peliculas/store');
        ?>

        <form id="formContenido" action="<?= $urlAction ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <input type="hidden" name="imdb_id" id="imdb_id" value="<?= $data['imdb_id'] ?? '' ?>"> 
            
            <input type="hidden" name="actores_json" id="actores_json">
            <input type="hidden" name="directores_json" id="directores_json">

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold">1. Ficha Técnica</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="fw-bold">Título</label>
                                <input type="text" name="titulo" id="titulo" class="form-control" value="<?= esc($data['titulo'] ?? '') ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Año</label>
                                    <input type="number" name="anio" id="anio" class="form-control" value="<?= esc($data['anio'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Duración (min)</label>
                                    <input type="number" name="duracion" id="duracion" class="form-control" value="<?= esc($data['duracion'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Edad Recomendada</label>
                                    <select name="edad_recomendada" id="edad_recomendada" class="form-select">
                                        <?php $edad = $data['edad_recomendada'] ?? 12; ?>
                                        <option value="0" <?= $edad == 0 ? 'selected' : '' ?>>TP (Todos)</option>
                                        <option value="7" <?= $edad == 7 ? 'selected' : '' ?>>+7 Años</option>
                                        <option value="12" <?= $edad == 12 ? 'selected' : '' ?>>+12 Años</option>
                                        <option value="16" <?= $edad == 16 ? 'selected' : '' ?>>+16 Años</option>
                                        <option value="18" <?= $edad == 18 ? 'selected' : '' ?>>+18 Años</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Sinopsis</label>
                                <textarea name="descripcion" id="descripcion" class="form-control" rows="3"><?= esc($data['descripcion'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold">2. Reparto y Categorías</div>
                        <div class="card-body">
                            
                            <div class="mb-3">
                                <label class="fw-bold text-success d-flex justify-content-between">
                                    <span>Géneros</span>
                                    <span class="badge bg-secondary" style="cursor:pointer" onclick="$('.genero-checkbox').prop('checked', false)">Limpiar</span>
                                </label>
                                
                                <div class="checkbox-container">
                                    <div class="row" id="container_generos_db">
                                        <?php 
                                            // IDs ya seleccionados (si estamos editando)
                                            $selectedIds = [];
                                            if(isset($data['generos']) && is_array($data['generos'])) {
                                                $selectedIds = array_column($data['generos'], 'id');
                                            }
                                        ?>
                                        <?php foreach($generos as $g): ?>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input genero-checkbox" 
                                                           type="checkbox" 
                                                           name="generos[]" 
                                                           value="<?= $g['id'] ?>" 
                                                           id="gen_<?= $g['id'] ?>"
                                                           data-nombre="<?= strtolower($g['nombre']) ?>"
                                                           <?= in_array($g['id'], $selectedIds) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="gen_<?= $g['id'] ?>">
                                                        <?= esc($g['nombre']) ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <hr class="my-2">
                                    <label class="small text-muted mb-2">Nuevos detectados (se añadirán):</label>
                                    <div class="row" id="container_generos_nuevos"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-info">Director(es)</label>
                                <input type="text" id="directores_visual" class="form-control" readonly style="background: #e9ecef;" 
                                       value="<?= esc($strings['directores'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-warning">Reparto</label>
                                <input type="text" id="actores_visual" class="form-control" readonly style="background: #e9ecef;" 
                                       value="<?= esc($strings['actores'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold">3. Multimedia</div>
                        <div class="card-body text-center">
                            <?php 
                                $posterUrl = 'https://via.placeholder.com/200x300?text=Poster';
                                if (!empty($data['imagen'])) {
                                    $posterUrl = str_starts_with($data['imagen'], 'http') ? $data['imagen'] : base_url('assets/img/' . $data['imagen']);
                                }
                            ?>
                            <img id="preview_poster" src="<?= $posterUrl ?>" class="img-fluid rounded shadow mb-3" style="max-height: 250px;">
                            
                            <div class="mb-3 text-start">
                                <label class="fw-bold small">URL Póster</label>
                                <input type="text" name="url_imagen_externa" id="url_imagen_externa" class="form-control form-control-sm" 
                                       value="<?= (!empty($data['imagen']) && str_starts_with($data['imagen'], 'http')) ? esc($data['imagen']) : '' ?>"
                                       onchange="actualizarPreview(this.value)">
                            </div>
                            <div class="mb-3 text-start">
                                <label class="fw-bold small">URL Fondo</label>
                                <input type="text" name="url_bg_externa" id="url_bg_externa" class="form-control form-control-sm"
                                       value="<?= (!empty($data['imagen_bg']) && str_starts_with($data['imagen_bg'], 'http')) ? esc($data['imagen_bg']) : '' ?>">
                            </div>
                            <div class="mb-3 text-start">
                                <label class="fw-bold small text-danger"><i class="fa fa-youtube"></i> Trailer</label>
                                <input type="text" name="url_video" id="url_video" class="form-control form-control-sm" 
                                       value="<?= esc($data['url_video'] ?? '') ?>">
                            </div>
                            <hr>
                            <label class="small text-muted">Subir imagen local:</label>
                            <input type="file" name="imagen" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold">4. Configuración</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Puntuación</label>
                                <input type="text" name="imdb_rating" id="imdb_rating" class="form-control" value="<?= esc($data['imdb_rating'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label>Plan</label>
                                <select name="nivel_acceso" id="nivel_acceso" class="form-select">
                                    <option value="1" <?= ($data['nivel_acceso'] ?? 1) == 1 ? 'selected' : '' ?>>Gratis</option>
                                    <option value="2" <?= ($data['nivel_acceso'] ?? 1) == 2 ? 'selected' : '' ?>>Premium</option>
                                </select>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="destacada" value="1" id="destacada" <?= ($data['destacada'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label">Destacada</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function actualizarPreview(url) {
        $('#preview_poster').attr('src', (url && url.length > 5) ? url : 'https://via.placeholder.com/200x300?text=Sin+Imagen');
    }

    function limpiarFormulario() {
        $('#titulo, #anio, #duracion, #descripcion, #actores_visual, #directores_visual, #imdb_rating, #url_imagen_externa, #url_bg_externa, #url_video, #imdb_id').val('');
        $('#actores_json, #directores_json').val('');
        $('#edad_recomendada').val('12'); 
        $('.genero-checkbox').prop('checked', false);
        $('#container_generos_nuevos').empty();
        actualizarPreview('');
    }

    // MAPEO DE GÉNEROS
    const mapaGeneros = {
        'action': 'acción', 'adventure': 'aventura', 'science fiction': 'ciencia ficción', 'sci-fi': 'ciencia ficción',
        'animation': 'animación', 'comedy': 'comedia', 'crime': 'crimen', 'documentary': 'documental',
        'drama': 'drama', 'family': 'familiar', 'fantasy': 'fantasía', 'history': 'historia',
        'horror': 'terror', 'music': 'música', 'mystery': 'misterio', 'romance': 'romance',
        'thriller': 'terror', 'war': 'bélica', 'western': 'western', 'tv movie': 'película de tv'
    };

    $(document).ready(function () {
        const apiKey = '6387e3c183c454304108333c56530988'; 
        const baseUrlImg = 'https://image.tmdb.org/t/p/original';
        let tipoBusqueda = '<?= (isset($tipo_id) && $tipo_id == 2) ? 'tv' : 'movie' ?>';
        if (window.location.href.indexOf("series") > -1) tipoBusqueda = 'tv';

        var $input = $("#tmdb_autocomplete").autocomplete({
            minLength: 3,
            source: function (request, response) {
                $.ajax({
                    url: `https://api.themoviedb.org/3/search/${tipoBusqueda}`,
                    dataType: "json",
                    data: { api_key: apiKey, query: request.term, language: 'es-ES', include_adult: false },
                    success: function (data) {
                        if (data.results) {
                            response($.map(data.results, function (item) {
                                let title = (tipoBusqueda === 'movie') ? item.title : item.name;
                                let year = (tipoBusqueda === 'movie') ? (item.release_date || '') : (item.first_air_date || '');
                                return { label: title, value: title, id: item.id, poster: item.poster_path, year: year.substring(0, 4) }
                            }));
                        }
                    }
                });
            },
            select: function (event, ui) {
                if (!ui.item.id) return false;
                limpiarFormulario(); // IMPORTANTE: Al seleccionar en edit, limpia todo lo viejo

                // PEDIR DATOS COMPLETOS
                let append = 'credits,videos';
                if(tipoBusqueda === 'movie') append += ',release_dates'; else append += ',content_ratings';

                fetch(`https://api.themoviedb.org/3/${tipoBusqueda}/${ui.item.id}?api_key=${apiKey}&language=es-ES&append_to_response=${append}`)
                    .then(r => r.json())
                    .then(data => {
                        let titulo = (tipoBusqueda === 'movie') ? data.title : data.name;
                        let anio = (tipoBusqueda === 'movie') ? data.release_date : data.first_air_date;
                        $('#titulo').val(titulo);
                        $('#anio').val(anio ? anio.substring(0, 4) : '');
                        $('#descripcion').val(data.overview);
                        $('#imdb_id').val(data.id);
                        $('#imdb_rating').val(data.vote_average.toFixed(1));
                        
                        let duracion = (tipoBusqueda === 'movie') ? data.runtime : (data.episode_run_time?.[0] || 0);
                        $('#duracion').val(duracion);

                        // GÉNEROS
                        if(data.genres) {
                            data.genres.forEach(g => {
                                let nombreOriginal = g.name.toLowerCase();
                                let nombreBuscado = mapaGeneros[nombreOriginal] || nombreOriginal;
                                let $existingCheck = $(`.genero-checkbox[data-nombre="${nombreBuscado}"]`);
                                
                                if($existingCheck.length > 0) {
                                    $existingCheck.prop('checked', true);
                                } else {
                                    let label = g.name;
                                    if(mapaGeneros[nombreOriginal]) label = `${mapaGeneros[nombreOriginal]} (${g.name})`;
                                    let newCheckHtml = `<div class="col-md-6 new-genre-badge"><div class="form-check"><input class="form-check-input" type="checkbox" name="generos[]" value="${label}" checked><label class="form-check-label fw-bold text-primary">${label} (Nuevo)</label></div></div>`;
                                    $('#container_generos_nuevos').append(newCheckHtml);
                                }
                            });
                        }

                        // EDAD RECOMENDADA
                        let cert = null;
                        if(tipoBusqueda === 'movie' && data.release_dates) {
                            let esData = data.release_dates.results.find(r => r.iso_3166_1 === 'ES') || data.release_dates.results.find(r => r.iso_3166_1 === 'US');
                            if(esData && esData.release_dates.length > 0) cert = esData.release_dates[0].certification;
                        } else if (tipoBusqueda === 'tv' && data.content_ratings) {
                            let esData = data.content_ratings.results.find(r => r.iso_3166_1 === 'ES') || data.content_ratings.results.find(r => r.iso_3166_1 === 'US');
                            if(esData) cert = esData.rating;
                        }
                        if(cert) {
                            let edadVal = '12';
                            cert = cert.toLowerCase();
                            if(['a', 'tp', 'g', 'tv-y', 'tv-g', '0'].includes(cert)) edadVal = '0';
                            else if(['7', 'pg', 'tv-pg'].includes(cert)) edadVal = '7';
                            else if(['12', '12a', 'pg-13', 'tv-14'].includes(cert)) edadVal = '12';
                            else if(['16', '15'].includes(cert)) edadVal = '16';
                            else if(['18', 'r', 'nc-17', 'tv-ma'].includes(cert)) edadVal = '18';
                            $('#edad_recomendada').val(edadVal);
                        }

                        // CRÉDITOS & MULTIMEDIA
                        if(data.credits) {
                            let directoresRaw = (tipoBusqueda === 'movie') ? data.credits.crew.filter(c => c.job === 'Director') : (data.created_by || []);
                            let directoresData = directoresRaw.slice(0, 3).map(d => ({ name: d.name, photo: d.profile_path ? baseUrlImg + d.profile_path : '' }));
                            $('#directores_json').val(JSON.stringify(directoresData));
                            $('#directores_visual').val(directoresData.map(d => d.name).join(', '));

                            let actoresData = data.credits.cast.slice(0, 15).map(a => ({ name: a.name, character: a.character, photo: a.profile_path ? baseUrlImg + a.profile_path : '' }));
                            $('#actores_json').val(JSON.stringify(actoresData));
                            $('#actores_visual').val(actoresData.map(a => a.name).join(', '));
                        }
                        if(data.videos?.results) {
                            let trailer = data.videos.results.find(v => v.site === 'YouTube' && v.type === 'Trailer') || data.videos.results[0];
                            if(trailer) $('#url_video').val(`https://www.youtube.com/watch?v=${trailer.key}`);
                        }
                        if (data.poster_path) {
                            let poster = baseUrlImg + data.poster_path;
                            $('#url_imagen_externa').val(poster);
                            actualizarPreview(poster);
                        }
                        if (data.backdrop_path) $('#url_bg_externa').val(baseUrlImg + data.backdrop_path);
                    });
            }
        });

        // RENDERIZADO VISUAL DEL AUTOCOMPLETE
        $input.autocomplete("instance")._renderItem = function (ul, item) {
            const imgUrl = (item.poster) ? `https://image.tmdb.org/t/p/w92${item.poster}` : "https://via.placeholder.com/45x68?text=NO";
            return $("<li>").append(`<div class="ui-menu-item-wrapper"><img src="${imgUrl}"><div><strong>${item.label}</strong> <br> <small class="text-muted">${item.year}</small></div></div>`).appendTo(ul);
        };
    });
</script>

<?= view('backend/templates/footer') ?>