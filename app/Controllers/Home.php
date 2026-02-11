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
    // =========================================================
    // 👤 PERFIL DE PERSONA (ACTOR/DIRECTOR)
    // =========================================================
    public function persona($idRaw)
    {
        // 1. Limpieza de ID
        $tmdbID = str_replace('tmdb_person_', '', $idRaw);

        // 2. Configuración API
        $apiKey = '6387e3c183c454304108333c56530988'; // Tu API Key
        $lang = 'es-ES';
        
        // Contexto para evitar errores SSL en local
        $arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false], "http" => ["ignore_errors" => true]];
        $context = stream_context_create($arrContextOptions);

        // 3. Petición a TMDB (Detalles + Créditos Combinados)
        $url = "https://api.themoviedb.org/3/person/{$tmdbID}?api_key={$apiKey}&language={$lang}&append_to_response=combined_credits,images";
        $json = @file_get_contents($url, false, $context);

        if (!$json) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = json_decode($json, true);

        // 4. Procesar Datos Básicos
        $baseImg = "https://image.tmdb.org/t/p/h632"; // Calidad alta para perfil
        $basePoster = "https://image.tmdb.org/t/p/w300"; // Calidad media para grid

        $persona = [
            'nombre' => $data['name'],
            'biografia' => !empty($data['biography']) ? $data['biography'] : 'No hay biografía disponible en español.',
            'fecha_nacimiento' => $data['birthday'] ?? 'Desconocida',
            'lugar_nacimiento' => $data['place_of_birth'] ?? '',
            'foto' => !empty($data['profile_path']) ? $baseImg . $data['profile_path'] : base_url('assets/img/no-user.png'),
            'conocido_por' => $data['known_for_department'] // Acting, Directing...
        ];

        // 5. Procesar Filmografía (Cast y Crew)
        $filmografia = [];
        
        // Si es ACTOR, cogemos 'cast'. Si es DIRECTOR, cogemos 'crew' filtrando por job='Director'
        // Pero para ser completos, vamos a mostrar AMBOS si tiene.

        $rawCast = $data['combined_credits']['cast'] ?? [];
        $rawCrew = $data['combined_credits']['crew'] ?? [];

        // Función auxiliar para formatear items
        $formatearCredito = function($item) use ($basePoster) {
            $esSerie = ($item['media_type'] === 'tv');
            return [
                'id' => ($esSerie ? 'tmdb_tv_' : 'tmdb_movie_') . $item['id'],
                'titulo' => $esSerie ? ($item['name'] ?? '') : ($item['title'] ?? ''),
                'poster' => !empty($item['poster_path']) ? $basePoster . $item['poster_path'] : null,
                'anio' => substr($esSerie ? ($item['first_air_date'] ?? '') : ($item['release_date'] ?? ''), 0, 4),
                'personaje' => $item['character'] ?? '', // Para actores
                'trabajo' => $item['job'] ?? '',         // Para directores
                'popularidad' => $item['popularity'] ?? 0,
                'media_type' => $item['media_type']
            ];
        };

        // Procesar Actuación
        $acting = array_map($formatearCredito, $rawCast);
        
        // Procesar Dirección (Solo donde job sea Director)
        $directing = [];
        foreach($rawCrew as $c) {
            if (isset($c['job']) && $c['job'] === 'Director') {
                $directing[] = $formatearCredito($c);
            }
        }

        // Ordenar por popularidad (lo más famoso primero)
        usort($acting, function($a, $b) { return $b['popularidad'] <=> $a['popularidad']; });
        usort($directing, function($a, $b) { return $b['popularidad'] <=> $a['popularidad']; });

        // Eliminamos duplicados (a veces salen varias veces si tienen varios roles)
        // (Opcional, pero queda mejor limpio)
        
        $viewData = [
            'titulo' => $persona['nombre'] . ' - Filmografía',
            'persona' => $persona,
            'acting' => $acting,
            'directing' => $directing,
            'mostrarHero' => false // Para no cargar el hero gigante del home
        ];

        // Cargar Vistas
        echo view('frontend/templates/header', $viewData);
        echo view('frontend/persona', $viewData);
        echo view('frontend/templates/footer');
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
// =========================================================
    // 🎭 VISTA DE GÉNERO (HÍBRIDA: FILAS O GRID)
    // =========================================================
    public function verGenero($idGenero, $tipoEspecifico = null)
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $generoModel = new \App\Models\GeneroModel();
        $userModel = new \App\Models\UsuarioModel();
        
        $infoGenero = $generoModel->find($idGenero);
        if (!$infoGenero) return redirect()->to('/');

        $data = [
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll(),
            'otrosPerfiles' => $userModel->where('id !=', session()->get('user_id'))->findAll(),
            'mostrarHero' => false,
            'splash' => false,
            'carrusel' => []
        ];

        // --- CASO A: MODO "VER TODO" (GRID) ---
        if ($tipoEspecifico !== null) {
            
            // Cargamos MUCHOS items (ej. 100)
            $contenidos = $this->_getContenidoCombinado($idGenero, $tipoEspecifico, 100);
            
            $nombreTipo = ($tipoEspecifico == 1) ? 'Películas' : 'Series';
            $data['titulo'] = $nombreTipo . ' de ' . $infoGenero['nombre'];
            $data['peliculas'] = $contenidos;
            
            // === MAGIA AJAX ===
            if ($this->request->isAJAX()) {
                // Si es AJAX, devolvemos SOLO la vista parcial (sin header/footer)
                return view('frontend/peliculas', $data); 
            }
            
            // Si entra escribiendo la URL, carga normal
            echo view('frontend/templates/header', $data);
            echo view('frontend/peliculas', $data);
            echo view('frontend/templates/footer', $data);
            return;
        }

        // --- CASO B: MODO RESUMEN (FILAS) ---
        // Cargamos solo 20 de cada uno para que la página no pese
        $peliculas = $this->_getContenidoCombinado($idGenero, 1, 20); 
        $series = $this->_getContenidoCombinado($idGenero, 2, 20);

        $data['titulo'] = 'Explorando: ' . $infoGenero['nombre'];
        $data['infoGenero'] = $infoGenero;
        $data['peliculas'] = $peliculas;
        $data['series'] = $series;

        echo view('frontend/templates/header', $data);
        echo view('frontend/genero_filas', $data); // <--- VISTA FILAS
        echo view('frontend/templates/footer', $data);
    }

    // --- HELPER ACTUALIZADO (Con parámetro $limit) ---
    // --- HELPER PRIVADO PARA NO REPETIR CÓDIGO ---
    private function _getContenidoCombinado($idGenero, $tipoId, $limit = 20)
    {
        $planId = session()->get('plan_id');
        $model = new \App\Models\ContenidoModel();
        
        // 1. LOCAL
        $contenido = $model->getPorGenero($idGenero, $tipoId, $limit, [], $planId);

        // Filtro Kids Local
        if ($planId == 3 || $planId == 1) {
            $contenido = array_filter($contenido, fn($c) => $c['edad_recomendada'] <= 11);
        }

        // 2. EXTERNO (TMDB)
        if ($planId == 2 || $planId == 3 || $planId == 1) { 
            $mapaGeneros = [
                1 => 28, 2 => 12, 3 => 878, 4 => 18, 5 => 16, 
                6 => 80, 7 => 35, 8 => 27, 9 => 10749, 10 => 14, 
                12 => 9648, 13 => 53
            ];

            if (isset($mapaGeneros[$idGenero])) {
                $idTmdb = $mapaGeneros[$idGenero];
                $modoTmdb = ($tipoId == 2) ? 'tv' : 'movie';
                $esKids = ($planId == 3 || $planId == 1); 
                
                $externos = $this->obtenerDeTmdbPorGenero($idTmdb, $modoTmdb, $esKids);
                
                if(count($externos) > $limit) {
                    $externos = array_slice($externos, 0, $limit);
                }

                $contenido = array_merge($contenido, $externos);
            }
        }

        // 3. SANITIZACIÓN DE IMÁGENES (AQUÍ ESTÁ EL ARREGLO DEL ERROR JS)
            foreach ($contenido as &$c) {
                    
                    // A. Asegurar IMAGEN (Poster)
                    if (empty($c['imagen'])) {
                        $c['imagen'] = base_url('assets/img/no-poster.jpg');
                    } elseif (!str_starts_with($c['imagen'], 'http')) {
                        $c['imagen'] = base_url('assets/img/' . $c['imagen']);
                    }

                    // B. Asegurar IMAGEN_BG (Fondo) - CORRECCIÓN PARA TU ERROR
                    if (empty($c['imagen_bg'])) {
                        // Si no tiene fondo, usamos el poster o uno genérico
                        $c['imagen_bg'] = $c['imagen']; 
                    } elseif (!str_starts_with($c['imagen_bg'], 'http')) {
                        $c['imagen_bg'] = base_url('assets/img/' . $c['imagen_bg']);
                    }

                    // C. Asegurar NIVEL ACCESO (Para el JS 'item.nivel_acceso')
                    if (!isset($c['nivel_acceso'])) {
                        $c['nivel_acceso'] = 1; // Por defecto Free
                    }
                }

                return $contenido;
    }
    // =========================================================
    // 🔌 HELPER PRIVADO: CONEXIÓN CON TMDB
    // =========================================================
    private function obtenerDeTmdbPorGenero($genreIdTmdb, $tipo = 'movie', $esKids = false)
    {
        $apiKey = '6387e3c183c454304108333c56530988'; 
        $lang = 'es-ES';
        
        // Contexto para evitar errores de SSL en local
        $context = stream_context_create([
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false], 
            "http" => ["ignore_errors" => true]
        ]);
        
        // URL Base del endpoint 'discover'
        // include_adult=false es vital, especialmente si no es Kids
        $url = "https://api.themoviedb.org/3/discover/{$tipo}?api_key={$apiKey}&language={$lang}&sort_by=popularity.desc&include_adult=false&page=1";
        
        // --- GESTIÓN DE GÉNEROS ---
        // Empezamos filtrando por el género que pidió el usuario (ej: Acción)
        $generosAFiltrar = [$genreIdTmdb];

        // --- FILTRO MÁGICO PARA KIDS ---
        if ($esKids) {
            if ($tipo == 'movie') {
                // PELÍCULAS: Usamos el sistema de calificación por edades
                // 'certification.lte=7' significa: Apta para 7 años o menos (España)
                $url .= "&certification_country=ES&certification.lte=7";
            } else {
                // SERIES: El filtro de certificación en series es menos preciso en TMDB.
                // Estrategia: Forzamos que la serie tenga TAMBIÉN el género "Kids" (10762) o "Animación" (16)
                // Usamos coma (,) para lógica AND (Debe ser de "Acción" Y "Kids")
                $generosAFiltrar[] = 10762; 
            }
        }

        // Añadimos los géneros a la URL
        $url .= "&with_genres=" . implode(',', $generosAFiltrar);

        // --- LLAMADA A LA API ---
        // ... LLAMADA A LA API ...
        $json = @file_get_contents($url, false, $context);
        if (!$json) return [];

        $data = json_decode($json, true);
        $resultados = [];
        $baseImg = "https://image.tmdb.org/t/p/w300";
        $baseBackdrop = "https://image.tmdb.org/t/p/w1280"; // <--- NUEVO: Base para fondos

        if (!empty($data['results'])) {
            foreach ($data['results'] as $item) {
                if (empty($item['poster_path'])) continue;

                $prefix = ($tipo == 'tv') ? 'tmdb_tv_' : 'tmdb_movie_';
                $titulo = ($tipo == 'tv') ? ($item['name'] ?? '') : ($item['title'] ?? '');
                $fecha  = ($tipo == 'tv') ? ($item['first_air_date'] ?? '') : ($item['release_date'] ?? '');

                $resultados[] = [
                    'id'               => $prefix . $item['id'],
                    'titulo'           => $titulo,
                    'imagen'           => $baseImg . $item['poster_path'],
                    
                    // --- CORRECCIÓN CLAVE AQUÍ ---
                    // Añadimos imagen_bg. Si no tiene fondo, usamos el póster como fallback.
                    'imagen_bg'        => !empty($item['backdrop_path']) ? $baseBackdrop . $item['backdrop_path'] : ($baseImg . $item['poster_path']),
                    
                    'anio'             => substr($fecha, 0, 4),
                    'edad_recomendada' => $esKids ? 0 : 12,
                    'tipo_id'          => ($tipo == 'tv') ? 2 : 1,
                    'vistas'           => $item['popularity'],
                    'nivel_acceso'     => 0 // Para que el JS no falle al chequear nivel
                ];
            }
        }

        return $resultados;
    }
}