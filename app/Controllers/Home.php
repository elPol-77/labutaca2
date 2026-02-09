<?php

namespace App\Controllers;

use App\Models\ContenidoModel;
use App\Models\UsuarioModel;
use App\Models\GeneroModel;
use CodeIgniter\Controller;

class Home extends BaseController
{
    // =========================================================================
    // 1. CATÁLOGO PRINCIPAL (HOME)
    // =========================================================================
    public function index($pagina = 1)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $planId = session()->get('plan_id'); // 1=Free, 2=Premium, 3=Kids
        $userId = session()->get('user_id');

        $esKids = ($planId == 3);
        $esFree = ($planId == 1);

        $model = new ContenidoModel();
        $userModel = new UsuarioModel();
        $generoModel = new GeneroModel();

        // Variables iniciales
        $secciones = [];
        $peliculas = []; // Importante inicializarlo
        $tituloCategoria = 'Inicio';
        $mostrarHero = true;

        // -----------------------------------------------------------
        // A. DETECTAR SI HAY FILTRO (GÉNERO)
        // -----------------------------------------------------------
        $filtroGenero = $this->request->getGet('genero');

        if ($filtroGenero) {
            // === MODO REJILLA (Estilo clásico para filtros) ===
            // Si el usuario filtra, NO mostramos filas de Netflix, mostramos el grid

            $nombreGenero = $generoModel->find($filtroGenero)['nombre'] ?? 'Género';
            $tituloCategoria = 'Categoría: ' . $nombreGenero;
            $mostrarHero = false; // Ocultamos el héroe para ver los resultados directos

            $porPagina = 10;
            $offset = ($pagina - 1) * $porPagina;

            // Usamos la paginación normal
            $peliculas = $model->getContenidoPaginadas($planId, $porPagina, $offset, $filtroGenero);
            $this->procesarMetadatos($peliculas, $userId);

        } else {
            // === MODO NETFLIX (Portada) ===
            // Solo entramos aquí si NO hay filtros

            // 1. TENDENCIAS
            $tendencias = $model->getTendencias(10, $planId);
            $this->procesarMetadatos($tendencias, $userId);

            $tituloTendencias = $esKids ? 'Los favoritos de los peques 🎈' : 'Tendencias en La Butaca';
            if ($esFree)
                $tituloTendencias;

            $secciones[] = ['titulo' => $tituloTendencias, 'data' => $tendencias];

            if ($esKids) {
                // --- MUNDO KIDS ---
                $animacion = $model->getPorGenero(5, 1, 10, [], 3);
                $this->procesarMetadatos($animacion, $userId);
                $secciones[] = ['titulo' => 'Mundo Animado ✨', 'data' => $animacion];

                $aventuras = $model->getPorGenero(2, 2, 10, [], 3);
                $this->procesarMetadatos($aventuras, $userId);
                $secciones[] = ['titulo' => 'Grandes Aventuras 🚀', 'data' => $aventuras];

                $mix = $model->getContentRandom(1, 10, 3);
                $this->procesarMetadatos($mix, $userId);
                $secciones[] = ['titulo' => '¡Descubre algo nuevo! 🎲', 'data' => $mix];

            } else {
                // --- MUNDO ADULTO ---
                $seriesRandom = $model->getContentRandom(2, 10, $planId);
                $this->procesarMetadatos($seriesRandom, $userId);
                $secciones[] = ['titulo' => 'Series para maratonear', 'data' => $seriesRandom];

                $generoFavPeli = $model->getGeneroFavoritoUsuario($userId, 1);
                if ($generoFavPeli) {
                    $recPelis = $model->getPorGenero($generoFavPeli['id'], 1, 10, [], $planId);
                    $titulo = 'Porque viste películas de ' . $generoFavPeli['nombre'];
                } else {
                    $recPelis = $model->getPorGenero(1, 1, 10, [], $planId);
                    $titulo = 'Películas de Acción para empezar';
                }
                $this->procesarMetadatos($recPelis, $userId);
                $secciones[] = ['titulo' => $titulo, 'data' => $recPelis];

                $generoFavSerie = $model->getGeneroFavoritoUsuario($userId, 2);
                if ($generoFavSerie) {
                    $recSeries = $model->getPorGenero($generoFavSerie['id'], 2, 10, [], $planId);
                    $tituloS = 'Series de ' . $generoFavSerie['nombre'] . ' para ti';
                } else {
                    $recSeries = $model->getPorGenero(2, 2, 10, [], $planId);
                    $tituloS = 'Series de Aventura';
                }
                $this->procesarMetadatos($recSeries, $userId);
                $secciones[] = ['titulo' => $tituloS, 'data' => $recSeries];
            }
        }

        // Si es petición AJAX (Scroll infinito en modo rejilla)
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($peliculas);
        }

        // DATOS COMUNES
        $listaGeneros = $generoModel->orderBy('nombre', 'ASC')->findAll();
        $otrosPerfiles = $userModel->where('id >=', 2)->where('id <=', 4)->where('id !=', $userId)->findAll();

        // Carrusel Hero
        $builderCarrusel = $model->where('destacada', 1);
        if ($esFree) {
            $builderCarrusel->where('nivel_acceso', 1);
        } elseif ($esKids) {
            $builderCarrusel->where('edad_recomendada <=', 11);
        }
        $carrusel = $builderCarrusel->limit(3)->findAll();

        // Fallback si el carrusel está vacío
        if (empty($carrusel)) {
            $builderRelleno = $model->orderBy('anio', 'DESC');
            if ($esFree)
                $builderRelleno->where('nivel_acceso', 1);
            elseif ($esKids)
                $builderRelleno->where('edad_recomendada <=', 11);
            $carrusel = $builderRelleno->limit(3)->findAll();
        }
        $this->procesarMetadatos($carrusel, $userId);

        $data = [
            'titulo' => 'La Butaca - ' . $tituloCategoria,
            'carrusel' => $carrusel,
            'secciones' => $secciones, // Si está lleno -> Vista Netflix
            'peliculas' => $peliculas, // Si está lleno -> Vista Grid (Filtros)
            'categoria' => $tituloCategoria,
            'generos' => $listaGeneros,
            'otrosPerfiles' => $otrosPerfiles,
            'mostrarHero' => $mostrarHero,
            'splash' => (session()->getFlashdata('mostrar_intro') === true)
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }

    private function procesarMetadatos(&$contenidos, $userId)
    {
        if (empty($contenidos))
            return;

        // 1. Instanciamos el modelo de Mi Lista
        // Asegúrate de tener: use App\Models\MiListaModel; arriba del todo
        $miListaModel = new \App\Models\MiListaModel();

        // 2. Obtenemos los IDs de los contenidos que vamos a procesar
        $ids = array_column($contenidos, 'id');

        // 3. Consultamos cuáles de estos IDs están en la lista del usuario
        $favoritos = [];
        if (!empty($ids) && $userId) {
            $favoritos = $miListaModel->where('usuario_id', $userId)
                ->whereIn('contenido_id', $ids)
                ->findColumn('contenido_id');
            // Esto devuelve un array simple: [5, 12, 40...]
        }

        if (!$favoritos)
            $favoritos = [];

        // 4. Recorremos y modificamos
        foreach ($contenidos as &$item) {
            // A. Arreglar Imágenes (URL absoluta)
            if (isset($item['imagen']) && !str_starts_with($item['imagen'], 'http')) {
                $item['imagen'] = base_url('assets/img/' . $item['imagen']);
            }
            if (isset($item['imagen_bg']) && !str_starts_with($item['imagen_bg'], 'http')) {
                $item['imagen_bg'] = base_url('assets/img/' . $item['imagen_bg']);
            }

            // B. Marcar si está en Mi Lista
            // Creamos el campo 'en_mi_lista' que el JS espera
            $item['en_mi_lista'] = in_array($item['id'], $favoritos);
        }
    }

    // =========================================================================
    // 2. MI LISTA
    // =========================================================================
    // En app/Controllers/Home.php

    public function miLista()
    {
        // 1. Seguridad: Si no está logueado, fuera
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');

        // 2. Conexión y Modelos
        $db = \Config\Database::connect();
        $generoModel = new \App\Models\GeneroModel();
        $userModel = new \App\Models\UsuarioModel();

        // 3. CONSULTA MAESTRA: Obtener solo lo que está en 'mi_lista' de este usuario
        $builder = $db->table('mi_lista ml');
        $builder->select('c.*, ml.fecha_agregado'); // Traemos todos los datos de la peli
        $builder->join('contenidos c', 'c.id = ml.contenido_id'); // Unimos con contenidos
        $builder->where('ml.usuario_id', $userId); // FILTRO CLAVE: Solo este perfil
        $builder->orderBy('ml.fecha_agregado', 'DESC'); // Lo último añadido primero

        $misPeliculas = $builder->get()->getResultArray();

        // 4. Truco: Marcar todas como 'en_mi_lista' para que el corazón salga rojo
        foreach ($misPeliculas as &$peli) {
            $peli['en_mi_lista'] = true;
        }

        // 5. Perfiles para el header (Copiar de tu index/peliculas)
        $otrosPerfiles = $userModel->where('id >=', 2)
            ->where('id <=', 4)
            ->where('id !=', $userId)
            ->findAll();

        $data = [
            'titulo' => 'Mi Lista - La Butaca',
            'peliculas' => $misPeliculas,
            'generos' => $generoModel->findAll(),
            'otrosPerfiles' => $otrosPerfiles,
            'categoria' => 'Mi Lista',

            // Variables para evitar errores en el header
            'splash' => false,
            'mostrarHero' => false,
            'carrusel' => []
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/mi_lista', $data);
        echo view('frontend/templates/footer', $data);
    }

    // =========================================================================
    // 3. REPRODUCTOR (TU SEGURIDAD ESTÁ BIEN AQUÍ)
    // =========================================================================
    // =========================================================================
    // 3. REPRODUCTOR UNIVERSAL (SOPORTA BASE DE DATOS + TMDB)
    // =========================================================================
    public function ver($id)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');
        $model = new ContenidoModel();

        // =========================================================
        // 1. DETECCIÓN EXACTA (CINE vs TV)
        // =========================================================
        $esTmdb = false;
        $tipoBusqueda = null; // 'movie' o 'tv'
        $idLimpio = $id;

        // CASO A: Es una SERIE externa (tmdb_tv_XXXX)
        if (str_starts_with($id, 'tmdb_tv_')) {
            $esTmdb = true;
            $tipoBusqueda = 'tv';
            $idLimpio = str_replace('tmdb_tv_', '', $id);
        }
        // CASO B: Es una PELÍCULA externa (tmdb_movie_XXXX)
        elseif (str_starts_with($id, 'tmdb_movie_')) {
            $esTmdb = true;
            $tipoBusqueda = 'movie';
            $idLimpio = str_replace('tmdb_movie_', '', $id);
        }
        // CASO C: Compatibilidad antigua (tmdb_XXXX) -> Asumimos Cine
        elseif (str_starts_with($id, 'tmdb_')) {
            $esTmdb = true;
            $tipoBusqueda = 'movie';
            $idLimpio = str_replace('tmdb_', '', $id);
        }

        $contenido = null;

        // =========================================================
        // 2. BÚSQUEDA EXCLUYENTE
        // =========================================================

        // SI ES TMDB -> Vamos directo a la API con el TIPO ESPECÍFICO
        if ($esTmdb) {
            // ¡AQUÍ ESTÁ LA SOLUCIÓN! Pasamos $tipoBusqueda ('tv' o 'movie')
            $datosExternos = $this->obtenerDetalleExterno($idLimpio, $tipoBusqueda);

            if ($datosExternos && !empty($datosExternos['url_video'])) {
                // Reconstruimos el prefijo correcto
                $prefix = ($tipoBusqueda === 'tv') ? 'tmdb_tv_' : 'tmdb_movie_';

                $contenido = [
                    'id' => $prefix . $datosExternos['id'],
                    'titulo' => $datosExternos['titulo'],
                    'url_video' => $datosExternos['url_video'],
                    'nivel_acceso' => 0,
                    'edad_recomendada' => $datosExternos['edad_recomendada'],
                    'descripcion' => $datosExternos['descripcion']
                ];
            }
        }
        // SI NO ES TMDB -> Buscamos en Local
        else {
            $contenido = $model->find($idLimpio);

            // Fallback (por si acaso entra un ID numérico que no es local)
            if (!$contenido) {
                // Intentamos buscar fuera como peli por defecto
                $datosExternos = $this->obtenerDetalleExterno($idLimpio, 'movie');
                if ($datosExternos && !empty($datosExternos['url_video'])) {
                    $contenido = [
                        'id' => 'tmdb_movie_' . $datosExternos['id'],
                        'titulo' => $datosExternos['titulo'],
                        'url_video' => $datosExternos['url_video'],
                        'nivel_acceso' => 0,
                        'edad_recomendada' => $datosExternos['edad_recomendada'],
                        'descripcion' => $datosExternos['descripcion']
                    ];
                }
            }
        }

        // 3. ERROR 404
        if (!$contenido) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // --- GESTIÓN DE PERMISOS ---
        $puedeVer = true; // Por defecto sí (externas)

        if (!$esTmdb) {
            // Si es local, comprobamos los planes estrictos
            $puedeVer = false;
            $planUsuario = session()->get('plan_id');
            $nivelAcceso = $contenido['nivel_acceso'];
            // (Tu lógica de permisos local se mantiene igual)
            if ($planUsuario == 2)
                $puedeVer = true;
            elseif ($planUsuario == 3 && ($nivelAcceso == 3 || $nivelAcceso == 1))
                $puedeVer = true;
            elseif ($planUsuario == 1 && $nivelAcceso == 1)
                $puedeVer = true;
        }

        if (!$puedeVer) {
            session()->setFlashdata('error', 'Contenido restringido.');
            return redirect()->to('/');
        }

        // --- PREPARAR YOUTUBE ---
        $videoUrl = $contenido['url_video'];
        if (strpos($videoUrl, 'youtu') !== false) {
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match)) {
                $videoUrl = 'https://www.youtube.com/embed/' . $match[1] . '?autoplay=1&rel=0&modestbranding=1';
            }
        }

        return view('frontend/player', [
            'titulo' => 'Viendo: ' . $contenido['titulo'],
            'contenido' => $contenido,
            'video_url' => $videoUrl
        ]);
    }
    // =========================================================================
    // 4. DETALLE HÍBRIDO (LOCAL + GLOBAL)
    // =========================================================================
    // =========================================================================
    // 4. DETALLE HÍBRIDO (INTELIGENTE)
    // =========================================================================
    public function detalle($id)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $model = new ContenidoModel();

        // 1. DETECCIÓN DE TIPO
        $esTmdb = false;
        $tipoBusqueda = null;
        $idLimpio = $id;

        if (str_starts_with($id, 'tmdb_tv_')) {
            $esTmdb = true;
            $tipoBusqueda = 'tv';
            $idLimpio = str_replace('tmdb_tv_', '', $id);
        } elseif (str_starts_with($id, 'tmdb_movie_')) {
            $esTmdb = true;
            $tipoBusqueda = 'movie';
            $idLimpio = str_replace('tmdb_movie_', '', $id);
        } elseif (str_starts_with($id, 'tmdb_')) {
            $esTmdb = true;
            $tipoBusqueda = 'movie';
            $idLimpio = str_replace('tmdb_', '', $id);
        }

        $contenido = null;
        $director = null;
        $esExterno = false;
        $esLocal = false;

        // 2. BÚSQUEDA
        if ($esTmdb) {
            // Buscamos en la API (y calculamos la edad real)
            $contenido = $this->obtenerDetalleExterno($idLimpio, $tipoBusqueda);

            if ($contenido) {
                $esExterno = true;
                $prefix = ($tipoBusqueda === 'tv') ? 'tmdb_tv_' : 'tmdb_movie_';
                $contenido['id'] = $prefix . $contenido['id'];
            }
        } else {
            $contenido = $model->getDetallesCompletos($id);
            if ($contenido) {
                $esLocal = true;
                $director = $model->getDirector($id);
            }
        }

        if (!$contenido) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // =========================================================
        // 3. SEGURIDAD BLINDADA (ESTO ES LO QUE TE FALTABA)
        // =========================================================
        $planUsuario = session()->get('plan_id');
        $puedeVer = true;

        // REGLA A: Usuario FREE intentando ver contenido EXTERNO (TMDB)
        if ($planUsuario == 1 && $esExterno) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // REGLA B: Usuario KIDS intentando ver contenido MAYORES DE 11 AÑOS

        if ($planUsuario == 3) {
            // Si la edad recomendada es mayor de 11, lo echamos fuera
            if ($contenido['edad_recomendada'] > 11) {
                return redirect()->to('/')->with('error', 'Este contenido no es adecuado para tu edad.');
            }
        }

        // REGLA C: Usuario FREE intentando ver contenido LOCAL PREMIUM
        if ($planUsuario == 1 && $esLocal && $contenido['nivel_acceso'] > 1) {
            $puedeVer = false; // Aquí le dejamos ver la ficha, pero saldrá el candado
        }

        // REGLA D: Usuario KIDS intentando ver contenido LOCAL DE ADULTOS
        if ($planUsuario == 3 && $esLocal && $contenido['edad_recomendada'] > 11) {
            return redirect()->to('/')->with('error', 'Este contenido no es adecuado para tu edad.');
        }

        // =========================================================

        // MI LISTA
        $db = \Config\Database::connect();
        $enLista = false;
        if ($esLocal) {
            $enLista = $db->table('mi_lista')
                ->where('usuario_id', $userId)
                ->where('contenido_id', $id)
                ->countAllResults() > 0;
        }

        // VISTA
        $generoModel = new GeneroModel();
        $userModel = new UsuarioModel();

        $data = [
            'titulo' => $contenido['titulo'],
            'peli' => $contenido,
            'puede_ver' => $puedeVer,
            'en_lista' => $enLista,
            'director' => $director,
            'es_externo' => $esExterno,
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll(),
            'otrosPerfiles' => $userModel->where('id !=', $userId) // No soy yo
                ->where('id >=', 2)       // Desde el 2
                ->where('id <=', 4)       // Hasta el 4
                ->findAll(),
            'splash' => false,
            'mostrarHero' => false,
            'carrusel' => []
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/detalle', $data);
        echo view('frontend/templates/footer', $data);
    }

    // --- FUNCIÓN AUXILIAR: TRADUCIR API OMDb A TU FORMATO ---
// --- FUNCIÓN AUXILIAR: API TMDB (MODO PRO) ---
    // --- SOPORTE HÍBRIDO (CINE Y SERIES) ---
    // Aceptamos un segundo parámetro: $tipoEspecifico ('movie' o 'tv')
    private function obtenerDetalleExterno($tmdbID, $tipoEspecifico = null)
    {
        $apiKey = '6387e3c183c454304108333c56530988';
        $lang = 'es-ES';

        $arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false], "http" => ["ignore_errors" => true]];
        $context = stream_context_create($arrContextOptions);

        $json = null;
        $esSerie = false;

        // 1. SELECCIÓN DE TIPO
        if ($tipoEspecifico === 'tv') {
            $urlTV = "https://api.themoviedb.org/3/tv/{$tmdbID}?api_key={$apiKey}&language={$lang}&append_to_response=videos,credits,content_ratings";
            $json = @file_get_contents($urlTV, false, $context);
            $esSerie = true;
        } elseif ($tipoEspecifico === 'movie') {
            $urlMovie = "https://api.themoviedb.org/3/movie/{$tmdbID}?api_key={$apiKey}&language={$lang}&append_to_response=videos,credits,release_dates";
            $json = @file_get_contents($urlMovie, false, $context);
            $esSerie = false;
        } else {
            // Fallback (tu lógica anterior)
            $urlMovie = "https://api.themoviedb.org/3/movie/{$tmdbID}?api_key={$apiKey}&language={$lang}&append_to_response=videos,credits,release_dates";
            $json = @file_get_contents($urlMovie, false, $context);
            $esSerie = false;
            if (!$json || strpos($json, '"success":false') !== false) {
                $urlTV = "https://api.themoviedb.org/3/tv/{$tmdbID}?api_key={$apiKey}&language={$lang}&append_to_response=videos,credits,content_ratings";
                $json = @file_get_contents($urlTV, false, $context);
                $esSerie = true;
            }
        }

        if (!$json)
            return null;
        $data = json_decode($json, true);
        if (isset($data['success']) && $data['success'] === false)
            return null;

        // VIDEO 
        $videoKey = null;
        if (isset($data['videos']['results'])) {
            foreach ($data['videos']['results'] as $vid) {
                if ($vid['site'] === 'YouTube' && $vid['type'] === 'Trailer') {
                    $videoKey = $vid['key'];
                    break;
                }
            }
            if (!$videoKey) {
                foreach ($data['videos']['results'] as $vid) {
                    if ($vid['site'] === 'YouTube') {
                        $videoKey = $vid['key'];
                        break;
                    }
                }
            }
        }
        $finalVideoUrl = $videoKey ? "https://www.youtube.com/embed/" . $videoKey . "?autoplay=1&rel=0&modestbranding=1" : "";

        // DIRECTOR
        $directorData = null;
        if (isset($data['credits']['crew'])) {
            foreach ($data['credits']['crew'] as $crewMember) {
                if ($crewMember['job'] === 'Director') {
                    $directorData = ['id' => 'tmdb_person_' . $crewMember['id'], 'nombre' => $crewMember['name']];
                    break;
                }
            }
        }
        if (!$directorData && $esSerie && !empty($data['created_by'])) {
            $directorData = ['id' => 'tmdb_person_' . $data['created_by'][0]['id'], 'nombre' => $data['created_by'][0]['name']];
        }

        // CALCULAR EDAD RECOMENDADA REAL (
        $edad = 12;

        if ($esSerie && isset($data['content_ratings']['results'])) {
            foreach ($data['content_ratings']['results'] as $rating) {
                if ($rating['iso_3166_1'] === 'ES') {
                    $ratingVal = $rating['rating']; // Ej: "16", "TP", "7"
                    $edad = is_numeric($ratingVal) ? intval($ratingVal) : ($ratingVal === 'TP' || $ratingVal === 'A' ? 0 : 18);
                    break;
                }
            }
        } elseif (!$esSerie && isset($data['release_dates']['results'])) {
            foreach ($data['release_dates']['results'] as $release) {
                if ($release['iso_3166_1'] === 'ES') {
                    foreach ($release['release_dates'] as $dateInfo) {
                        if (!empty($dateInfo['certification'])) {
                            $ratingVal = $dateInfo['certification'];
                            $edad = is_numeric($ratingVal) ? intval($ratingVal) : ($ratingVal === 'TP' || $ratingVal === 'A' || $ratingVal === 'Ai' ? 0 : 18);
                            break 2;
                        }
                    }
                }
            }
        }

        // DATOS BÁSICOS
        $baseImg = "https://image.tmdb.org/t/p/w500";
        $baseBackdrop = "https://image.tmdb.org/t/p/original";
        $titulo = $esSerie ? ($data['name'] ?? '') : ($data['title'] ?? '');
        $fecha = $esSerie ? ($data['first_air_date'] ?? '') : ($data['release_date'] ?? '');

        return [
            'id' => $data['id'],
            'titulo' => $titulo,
            'descripcion' => $data['overview'] ?? '',
            'anio' => intval(substr($fecha, 0, 4)),
            'duracion' => $esSerie ? ($data['episode_run_time'][0] ?? 0) : ($data['runtime'] ?? 0),
            'imagen' => !empty($data['poster_path']) ? $baseImg . $data['poster_path'] : base_url('assets/img/no-poster.jpg'),
            'imagen_bg' => !empty($data['backdrop_path']) ? $baseBackdrop . $data['backdrop_path'] : '',
            'url_video' => $finalVideoUrl,
            'rating' => isset($data['vote_average']) ? round($data['vote_average'], 1) : 0,
            'director_externo' => $directorData,
            'nivel_acceso' => 0,

            'edad_recomendada' => $edad,

            'generos' => array_map(function ($g) {
                return ['nombre' => $g['name']]; }, $data['genres'] ?? []),
            'actores' => array_map(function ($a) use ($baseImg) {
                return [
                    'id' => 'tmdb_person_' . $a['id'],
                    'nombre' => $a['name'],
                    'personaje' => $a['character'] ?? '',
                    'foto' => !empty($a['profile_path']) ? $baseImg . $a['profile_path'] : null
                ];
            }, array_slice($data['credits']['cast'] ?? [], 0, 8))
        ];
    }

    // BUSCADOR

    public function autocompletar()
    {
        $request = service('request');
        $postData = $request->getPost();

        $response = [];
        $data = [];
        $response['token'] = csrf_hash();

        if (isset($postData['search']) && strlen($postData['search']) > 2) {
            $search = $postData['search'];
            $planId = session()->get('plan_id') ?? 1;

            // ---------------------------------------------------------
            // 1. BÚSQUEDA LOCAL (Lo que tienes en tu BBDD)
            // ---------------------------------------------------------
            $model = new ContenidoModel();
            $builder = $model->select('id, titulo, imagen, edad_recomendada') // Importante: traer edad
                ->like('titulo', $search);

            // Filtros de seguridad LOCAL
            if ($planId == 3) { // Kids
                $builder->where('edad_recomendada <=', 11);
            } elseif ($planId == 1) { // Free
                $builder->where('nivel_acceso', 1);
            }

            $locales = $builder->orderBy('titulo')->findAll(5);
            $idsLocales = [];

            foreach ($locales as $peli) {
                $idsLocales[] = $peli['id'];

                $imgUrl = str_starts_with($peli['imagen'], 'http')
                    ? $peli['imagen']
                    : base_url('assets/img/' . $peli['imagen']);

                $data[] = [
                    "value" => $peli['id'],
                    "label" => $peli['titulo'],
                    "img" => $imgUrl,
                    "type" => "local"
                ];
            }

            // ---------------------------------------------------------
            // 2. BÚSQUEDA EXTERNA (TMDB - CINE Y SERIES)
            // ---------------------------------------------------------

            // SEGURIDAD: Si es Plan Free (1), NO buscamos fuera.
            if (count($data) < 10 && $planId != 1) { // <--- AQUÍ ESTÁ EL CAMBIO DE SEGURIDAD

                $apiKey = '6387e3c183c454304108333c56530988';
                $query = urlencode($search);

                // include_adult=false filtra contenido explícito para todos (incluido Kids)
                $url = "https://api.themoviedb.org/3/search/multi?api_key={$apiKey}&language=es-ES&query={$query}&include_adult=false";

                // Contexto para evitar errores SSL en Localhost
                $arrContextOptions = [
                    "ssl" => ["verify_peer" => false, "verify_peer_name" => false],
                    "http" => ["ignore_errors" => true]
                ];
                $context = stream_context_create($arrContextOptions);

                $json = @file_get_contents($url, false, $context);

                if ($json) {
                    $tmdbResults = json_decode($json, true);

                    if (!empty($tmdbResults['results'])) {
                        foreach ($tmdbResults['results'] as $item) {
                            if (count($data) >= 10)
                                break;

                            if ($item['media_type'] != 'movie' && $item['media_type'] != 'tv')
                                continue;
                            if (in_array($item['id'], $idsLocales))
                                continue;

                            // --- NORMALIZACIÓN ---
                            if ($item['media_type'] == 'tv') {
                                $titulo = $item['name'];
                                $fecha = $item['first_air_date'] ?? '';
                                $tipoLabel = " (Serie)";
                                $prefix = "tmdb_tv_"; // PREFIJO TV
                            } else {
                                $titulo = $item['title'];
                                $fecha = $item['release_date'] ?? '';
                                $tipoLabel = "";
                                $prefix = "tmdb_movie_"; // PREFIJO PELI
                            }

                            $anio = substr($fecha, 0, 4);

                            $poster = $item['poster_path']
                                ? "https://image.tmdb.org/t/p/w92" . $item['poster_path']
                                : base_url('assets/img/no-poster.jpg');

                            $data[] = [
                                "value" => $prefix . $item['id'],
                                "label" => $titulo . ($anio ? " ($anio)" : "") . $tipoLabel,
                                "img" => $poster,
                                "type" => "tmdb"
                            ];
                        }
                    }
                }
            }
        }

        $response['data'] = $data;
        return $this->response->setJSON($response);
    }

    // =========================================================================
    // 6. DIRECTOR
    // =========================================================================
    public function director($id)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $model = new ContenidoModel();
        $userModel = new UsuarioModel();
        $generoModel = new GeneroModel();

        $peliculas = $model->getPeliculasPorDirector($id);
        $nombreDirector = $model->getNombreDirector($id);

        $idsFavoritos = $userModel->getListaIds($userId);
        foreach ($peliculas as &$p) {
            $p['en_mi_lista'] = in_array($p['id'], $idsFavoritos);
        }
        unset($p);

        $tienePeliculas = false;
        $tieneSeries = false;

        foreach ($peliculas as $item) {
            if ($item['tipo_id'] == 1)
                $tienePeliculas = true;
            if ($item['tipo_id'] == 2)
                $tieneSeries = true;
        }

        $nombreCategoria = 'Filmografía de ' . $nombreDirector;

        if ($tienePeliculas && !$tieneSeries) {
            $nombreCategoria = 'Películas de ' . $nombreDirector;
        } elseif (!$tienePeliculas && $tieneSeries) {
            $nombreCategoria = 'Series de ' . $nombreDirector;
        }

        $listaGeneros = $generoModel->orderBy('nombre', 'ASC')->findAll();
        $otrosPerfiles = $userModel->where('id >=', 2)->where('id <=', 4)->where('id !=', $userId)->findAll();

        $data = [
            'titulo' => $nombreCategoria,
            'peliculas' => $peliculas,
            'categoria' => $nombreCategoria,
            'mostrarHero' => false,
            'splash' => false,
            'generos' => $listaGeneros,
            'otrosPerfiles' => $otrosPerfiles
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }
    // En App/Controllers/Home.php


    public function paginaPeliculas()
    {
        // 1. Datos básicos
        $userId = session()->get('user_id');

        // 2. Modelos
        $userModel = new \App\Models\UsuarioModel();
        $generoModel = new \App\Models\GeneroModel();

        // 3. Perfiles (Lógica corregida por IDs)
        $otrosPerfiles = $userModel->where('id >=', 2)
            ->where('id <=', 4)
            ->where('id !=', $userId)
            ->findAll();

        // 4. DATOS COMPLETOS (Para que no falle el Header)
        $data = [
            'titulo' => 'Películas - La Butaca',
            'generos' => $generoModel->findAll(),
            'otrosPerfiles' => $otrosPerfiles,

            // --- VARIABLES DE SEGURIDAD (Para evitar errores en la vista) ---
            'splash' => false,   // Evita error de variable indefinida en header
            'mostrarHero' => false,   // Evita error si el header busca esta variable
            'categoria' => 'Películas', // Evita error en títulos
            'carrusel' => [],      // Por si acaso footer o header lo piden
            'secciones' => []       // Por si acaso
        ];

        // 5. Renderizado (Importante: usas echo de header/footer en tu index, aquí deberías mantener la estructura)
        // Si tu archivo 'frontend/peliculas' YA incluye el header, usa 'return view'. 
        // Si NO incluye header, usa la estructura de abajo:

        echo view('frontend/templates/header', $data);
        echo view('frontend/peliculas', $data);
        echo view('frontend/templates/footer', $data);
    }
    // =========================================================================
    // 7. VISTA ANGULAR (ZONA GLOBAL)
    // =========================================================================
    public function vistaGlobal()
{
    if (!session()->get('is_logged_in'))
        return redirect()->to('/auth');

    // BLOQUEO DE SEGURIDAD: Solo Plan 2 (Premium)
    if (session()->get('plan_id') != 2) {
        return redirect()->to('/')->with('error', 'Necesitas ser Premium para acceder a la Zona Global.');
    }

    // --- AQUÍ LA CORRECCIÓN ---
    $generoModel = new GeneroModel(); // Instanciamos el modelo
    
    $data = [
        'titulo'     => 'Zona Global - La Butaca',
        'generos'    => $generoModel->orderBy('nombre', 'ASC')->findAll(), // <--- ESTO ES LO QUE FALTA
        'user_token' => csrf_hash(),
        'user_id'    => session()->get('user_id')
    ];

    // Ahora el Header ya tendrá la variable $generos para dibujar el menú
    echo view('frontend/templates/header', $data); 
    echo view('frontend/global', $data);          
    echo view('frontend/templates/footer', $data); 
}
    // =========================================================
    // PAGINA SERIES (Igual que Películas)
    // =========================================================
    public function series()
    {
        // 1. Datos básicos
        $userId = session()->get('user_id');

        $userModel = new \App\Models\UsuarioModel();
        $generoModel = new \App\Models\GeneroModel();

        // 2. Perfiles
        $otrosPerfiles = $userModel->where('id >=', 2)
            ->where('id <=', 4)
            ->where('id !=', $userId)
            ->findAll();

        // 3. Datos
        $data = [
            'titulo' => 'Series - La Butaca',
            'generos' => $generoModel->findAll(),
            'otrosPerfiles' => $otrosPerfiles,

            // Variables de seguridad
            'splash' => false,
            'mostrarHero' => false,
            'categoria' => 'Series',
            'carrusel' => [],
            'secciones' => []
        ];

        // 4. Vista
        echo view('frontend/templates/header', $data);
        echo view('frontend/series', $data); // <--- OJO: Llama a 'series.php'
        echo view('frontend/templates/footer', $data);
    }
    // FILTRAR CONTENIDO POR PERSONA (ACTOR O DIRECTOR)
    public function persona($id)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $nombrePersona = "Filmografía";
        $peliculas = [];

        // Si es de TMDB (tmdb_person_12345)
        if (str_starts_with($id, 'tmdb_person_')) {
            $tmdbID = str_replace('tmdb_person_', '', $id);
            $apiKey = '6387e3c183c454304108333c56530988';

            // Pedimos los créditos combinados (Cine y TV)
            $url = "https://api.themoviedb.org/3/person/{$tmdbID}/combined_credits?api_key={$apiKey}&language=es-ES";

            // También pedimos info de la persona para el título
            $urlPerson = "https://api.themoviedb.org/3/person/{$tmdbID}?api_key={$apiKey}&language=es-ES";

            $arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
            $context = stream_context_create($arrContextOptions);

            $jsonPerson = @file_get_contents($urlPerson, false, $context);
            if ($jsonPerson) {
                $pData = json_decode($jsonPerson, true);
                $nombrePersona = $pData['name'];
            }

            $json = @file_get_contents($url, false, $context);
            if ($json) {
                $data = json_decode($json, true);
                // Procesamos cast (actor) y crew (director)
                $rawList = array_merge($data['cast'], $data['crew']);

                // Eliminamos duplicados y filtramos
                $seen = [];
                foreach ($rawList as $item) {
                    if (isset($seen[$item['id']]))
                        continue;
                    if ($item['media_type'] != 'movie' && $item['media_type'] != 'tv')
                        continue;
                    $seen[$item['id']] = true;

                    // Formato compatible con tu vista catalogo
                    $prefix = ($item['media_type'] == 'tv') ? 'tmdb_tv_' : 'tmdb_movie_';
                    $img = $item['poster_path'] ? "https://image.tmdb.org/t/p/w300" . $item['poster_path'] : base_url('assets/img/no-poster.jpg');

                    $peliculas[] = [
                        'id' => $prefix . $item['id'],
                        'titulo' => ($item['media_type'] == 'tv') ? ($item['name'] ?? '') : ($item['title'] ?? ''),
                        'imagen' => $img,
                        'anio' => substr(($item['release_date'] ?? $item['first_air_date'] ?? ''), 0, 4),
                        'en_mi_lista' => false // Por defecto
                    ];
                }
            }
        }
        // Si fuera local, aquí iría la lógica local ($model->getPeliculasPorActor...)

        // Renderizar vista
        $data = [
            'titulo' => $nombrePersona . ' - La Butaca',
            'categoria' => 'Filmografía de ' . $nombrePersona,
            'peliculas' => $peliculas, // La vista catalogo usará esto
            'mostrarHero' => false,
            'splash' => false,
            'generos' => (new \App\Models\GeneroModel())->findAll(),
            'otrosPerfiles' => (new \App\Models\UsuarioModel())->where('id !=', session()->get('user_id'))->findAll(),
            'carrusel' => []
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data); // Reutilizamos tu vista de rejilla
        echo view('frontend/templates/footer', $data);
    }
    public function ajaxCargarFila()
    {
        $bloque = $this->request->getPost('bloque');
        $planId = session()->get('plan_id');

        // Definimos el orden de las categorías que quieres que salgan
        // 0 = Tendencias (Local)
        // 1 = Novedades 2024-2025 (TMDB)
        // 2 = Cine de Terror (TMDB)
        // 3 = Universo Marvel (TMDB)
        // 4 = Series Populares (TMDB)
        // 5 = Comedias (TMDB)

        $html = "";
        $tituloFila = "";
        $items = [];

        // RESTRICCIÓN FREE: Si es plan 1, solo mostramos el bloque 0 (Local)
        if ($planId == 1 && $bloque > 0) {
            return $this->response->setBody(""); // Dejamos de cargar
        }

        switch ($bloque) {
            case 0:
                $tituloFila = "Tendencias en La Butaca";
                $model = new ContenidoModel();
                // Filtramos por edad si es Kids
                if ($planId == 3)
                    $model->where('edad_recomendada <=', 11);

                $resultados = $model->getTendencias(12);

                // Formateamos para la vista
                foreach ($resultados as $r) {
                    $img = str_starts_with($r['imagen'], 'http') ? $r['imagen'] : base_url('assets/img/' . $r['imagen']);
                    $items[] = ['id' => $r['id'], 'titulo' => $r['titulo'], 'imagen' => $img];
                }
                break;

            case 1:
                $tituloFila = "Novedades y Próximos Estrenos";
                // Buscamos pelis de 2024 y 2025 ordenadas por popularidad
                $items = $this->fetchTmdbDiscover('movie', ['primary_release_date.gte' => '2024-01-01', 'sort_by' => 'popularity.desc']);
                break;

            case 2:
                $tituloFila = "Pasaje del Terror";
                // Género 27 es Terror en TMDB
                $items = $this->fetchTmdbDiscover('movie', ['with_genres' => '27', 'sort_by' => 'popularity.desc']);
                break;

            case 3:
                $tituloFila = "Universo Marvel";
                // Company ID 420 es Marvel Studios
                $items = $this->fetchTmdbDiscover('movie', ['with_companies' => '420', 'sort_by' => 'primary_release_date.desc']);
                break;

            case 4:
                $tituloFila = "Series que enganchan";
                $items = $this->fetchTmdbDiscover('tv', ['sort_by' => 'popularity.desc']);
                break;

            default:
                return $this->response->setBody(""); // No hay más bloques
        }

        // SI HAY RESULTADOS, GENERAMOS EL HTML DE LA FILA
        if (!empty($items)) {
            // Pasamos los datos a una "mini vista" o construimos el HTML aquí
            // Para simplificar, construyo el HTML aquí mismo (puedes pasarlo a un view fragment)

            $html .= '<div class="category-row" style="margin-bottom: 40px; opacity:0; transition: opacity 1s;" onload="this.style.opacity=1">';
            $html .= '  <h3 class="row-title" style="margin-left:4%; font-size:1.4rem; color:#e5e5e5; margin-bottom:10px;">' . esc($tituloFila) . '</h3>';
            $html .= '  <div class="row-container" style="display:flex; overflow-x:auto; padding: 10px 4%; gap:10px; scrollbar-width:none;">';

            foreach ($items as $peli) {
                // FILTRO DE EDAD KIDS (Si viene de TMDB, intentamos filtrar lo básico)
                // Nota: TMDB Discover no siempre devuelve rating, pero filtramos 'adult'

                $link = base_url('detalle/' . $peli['id']);

                $html .= '    <a href="' . $link . '" class="poster-card" style="flex:0 0 auto; width:160px; transition:transform 0.3s;">';
                $html .= '      <div style="height:240px; border-radius:6px; overflow:hidden;">';
                $html .= '        <img src="' . $peli['imagen'] . '" style="width:100%; height:100%; object-fit:cover;" loading="lazy">';
                $html .= '      </div>';
                // Opcional: Titulo abajo
                // $html .= ' <div style="font-size:0.9rem; margin-top:5px; white-space:nowrap; overflow:hidden;">'.$peli['titulo'].'</div>';
                $html .= '    </a>';
            }

            $html .= '  </div>';
            $html .= '</div>';
        }

        return $this->response->setBody($html);
    }

    // HELPER PRIVADO PARA TRAER LISTAS DE TMDB
    private function fetchTmdbDiscover($type, $params = [])
    {
        $apiKey = '6387e3c183c454304108333c56530988';

        // Base params
        $queryParams = array_merge([
            'api_key' => $apiKey,
            'language' => 'es-ES',
            'include_adult' => 'false', // Importante para seguridad
            'page' => 1
        ], $params);

        $queryString = http_build_query($queryParams);
        $url = "https://api.themoviedb.org/3/discover/{$type}?{$queryString}";

        $arrContextOptions = ["ssl" => ["verify_peer" => false], "http" => ["ignore_errors" => true]];
        $json = @file_get_contents($url, false, stream_context_create($arrContextOptions));

        $results = [];
        if ($json) {
            $data = json_decode($json, true);
            if (!empty($data['results'])) {
                foreach ($data['results'] as $item) {
                    $prefix = ($type == 'tv') ? 'tmdb_tv_' : 'tmdb_movie_';
                    $img = $item['poster_path'] ? "https://image.tmdb.org/t/p/w300" . $item['poster_path'] : base_url('assets/img/no-poster.jpg');
                    $titulo = ($type == 'tv') ? $item['name'] : $item['title'];

                    $results[] = [
                        'id' => $prefix . $item['id'],
                        'titulo' => $titulo,
                        'imagen' => $img
                    ];
                }
            }
        }
        return $results;
    }
    public function ayuda()
    {
        $data = ['titulo' => 'Centro de Ayuda - La Butaca'];
        return view('frontend/help', $data);
    }

}