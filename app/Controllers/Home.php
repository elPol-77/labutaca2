<?php

namespace App\Controllers;

use App\Models\ContenidoModel;
use App\Models\UsuarioModel;
use App\Models\GeneroModel;
use App\Models\MiListaModel;
use CodeIgniter\Controller;

class Home extends BaseController
{
    private $tmdbKey = '6387e3c183c454304108333c56530988';

    public function index()
    {
        // 1. CHEQUEO DE SESIÓN
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $userModel = new UsuarioModel();
        $usuario = $userModel->find($userId);

        $this->_verificarSuscripcion($usuario);

        // 3. REFRESCAR DATOS
        $planId = session()->get('plan_id');
        $esFree = ($planId == 1);
        $esKids = ($planId == 3);

        $destacada = null;
        $model = new ContenidoModel();

        // 4. LÓGICA DE HERO 
        // Si es Kids, forzamos 'movie' porque suelen tener mejores imágenes. Si no, aleatorio.
        $tipoHero = ($esKids) ? 'movie' : ((rand(0, 1) == 0) ? 'movie' : 'tv');

        // --- CASO A: USUARIO FREE (Local) 
        if ($esFree) {
            $localRandom = $model->where('nivel_acceso', 1)
                ->where('imagen_bg !=', '')
                ->orderBy('RAND()')
                ->first();

            if ($localRandom) {
                $destacada = $this->formatearLocal($localRandom);
            }
        }

        // --- CASO B: PREMIUM/KIDS (API TMDB) ---
        else {
            // OPTIMIZACIÓN 1: Pedimos SIEMPRE la página 1.
            // Es mucho más rápido (respuesta < 100ms). La variedad la da el shuffle() de abajo.
            $params = ['sort_by' => 'popularity.desc', 'page' => 1];

            $resultados = $this->fetchTmdbMixed($tipoHero, $params, $esKids);

            if (!empty($resultados)) {
                shuffle($resultados); // <--- Aquí mezclamos para que no salga siempre la #1
                foreach ($resultados as $item) {
                    if (!empty($item['imagen_bg'])) {
                        $destacada = $item;
                        break;
                    }
                }
            }
        }

        // FALLBACKS
        if (empty($destacada)) {
            $backup = $model->where('imagen_bg !=', '')->orderBy('RAND()')->first();
            if ($backup)
                $destacada = $this->formatearLocal($backup);
        }

        if (empty($destacada)) {
            $destacada = [
                'id' => 0,
                'titulo' => 'Bienvenido',
                'descripcion' => 'Explora nuestro contenido.',
                'imagen_bg' => 'https://image.tmdb.org/t/p/w1280/mBaXZ95R2OxueZhvQbcEWy2DqyO.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/w1280/mBaXZ95R2OxueZhvQbcEWy2DqyO.jpg',
                'link_ver' => '#',
                'link_detalle' => '#'
            ];
        }

        $data = [
            'titulo' => 'Inicio - La Butaca',
            'destacada' => $destacada,
            'mostrarHero' => true,
            'splash' => (session()->getFlashdata('mostrar_intro') === true),
            'categoria' => 'Inicio',
            'generos' => (new GeneroModel())->findAll(),
            'otrosPerfiles' => $userModel->where('id !=', $userId)->where('id >=', 2)->where('id <=', 4)->findAll()
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }


    public function ajaxCargarFila()
    {
        $bloqueSolicitado = intval($this->request->getPost('bloque'));
        $planId = session()->get('plan_id');
        $userId = session()->get('user_id');
        $esKids = ($planId == 3);
        $esFree = ($planId == 1);

        $html = "";
        $intentos = 0;
        $maxIntentos = 5;

        do {
            $bloqueActual = $bloqueSolicitado + $intentos;
            $items = [];

            // --- MAPAS ---
            if ($esFree) {
                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Novedades Gratis', 'params' => []],
                    1 => ['tipo' => 'local', 'titulo' => 'Películas de Acción', 'params' => ['tipo_id' => 1, 'with_genres' => 1]],
                    2 => ['tipo' => 'local', 'titulo' => 'Series de Comedia', 'params' => ['tipo_id' => 2, 'with_genres' => 7]],
                    3 => ['tipo' => 'local', 'titulo' => 'Cine Familiar', 'params' => ['tipo_id' => 1, 'with_genres' => 5]],
                    4 => ['tipo' => 'local', 'titulo' => 'Series de Drama', 'params' => ['tipo_id' => 2, 'with_genres' => 4]],
                ];
            } elseif ($esKids) {
                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Tus Favoritos'],
                    1 => ['tipo' => 'tmdb', 'api_type' => 'movie', 'titulo' => 'Películas Disney', 'params' => ['with_companies' => '2', 'sort_by' => 'popularity.desc']],
                    2 => ['tipo' => 'tmdb', 'api_type' => 'tv', 'titulo' => 'Series de Dibujos', 'params' => ['with_genres' => '16', 'sort_by' => 'popularity.desc']],
                    3 => ['tipo' => 'tmdb', 'api_type' => 'movie', 'titulo' => 'Pixar', 'params' => ['with_companies' => '3']],
                    4 => ['tipo' => 'tmdb', 'api_type' => 'tv', 'titulo' => 'Nick Jr.', 'params' => ['with_networks' => '13']],
                ];
            } else {
                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Agregado Recientemente'],
                    1 => ['tipo' => 'tmdb', 'api_type' => 'movie', 'titulo' => 'Películas Populares', 'params' => ['sort_by' => 'popularity.desc']],
                    2 => ['tipo' => 'tmdb', 'api_type' => 'tv', 'titulo' => 'Series: Top Mundial', 'params' => ['sort_by' => 'popularity.desc']],
                    3 => ['tipo' => 'tmdb', 'api_type' => 'movie', 'titulo' => 'Cine de Acción', 'params' => ['with_genres' => '28']],
                    4 => ['tipo' => 'tmdb', 'api_type' => 'tv', 'titulo' => 'Series de Sci-Fi', 'params' => ['with_genres' => '10765']],
                    5 => ['tipo' => 'tmdb', 'api_type' => 'movie', 'titulo' => 'Comedias', 'params' => ['with_genres' => '35']],
                    6 => ['tipo' => 'tmdb', 'api_type' => 'tv', 'titulo' => 'Dramas de TV', 'params' => ['with_genres' => '18']],
                    7 => ['tipo' => 'tmdb', 'api_type' => 'movie', 'titulo' => 'Terror', 'params' => ['with_genres' => '27']],
                    8 => ['tipo' => 'tmdb', 'api_type' => 'tv', 'titulo' => 'Documentales', 'params' => ['with_genres' => '99']],
                    9 => ['tipo' => 'tmdb', 'api_type' => 'movie', 'titulo' => 'Marvel Studios', 'params' => ['with_companies' => '420']],
                ];
            }

            // Generador Infinito
            if (!isset($mapa[$bloqueActual])) {
                if ($esFree)
                    break;
                if ($esKids) {
                    $pool = [['type' => 'movie', 'id' => 16, 'name' => 'Cine Animado'], ['type' => 'tv', 'id' => 10762, 'name' => 'Series Kids']];
                } else {
                    $pool = [
                        ['type' => 'movie', 'id' => 28, 'name' => 'Cine: Acción'],
                        ['type' => 'tv', 'id' => 10759, 'name' => 'Series: Acción'],
                        ['type' => 'movie', 'id' => 878, 'name' => 'Cine: Sci-Fi'],
                        ['type' => 'tv', 'id' => 10765, 'name' => 'Series: Fantasía'],
                        ['type' => 'movie', 'id' => 35, 'name' => 'Cine: Comedia'],
                        ['type' => 'tv', 'id' => 35, 'name' => 'Series: Comedia']
                    ];
                }
                $idx = $bloqueActual % count($pool);
                $sel = $pool[$idx];
                $page = floor(($bloqueActual - 8) / count($pool)) + 2;

                $mapa[$bloqueActual] = [
                    'tipo' => 'tmdb',
                    'api_type' => $sel['type'],
                    'titulo' => 'Descubre: ' . $sel['name'],
                    'params' => ['with_genres' => $sel['id'], 'page' => $page, 'sort_by' => 'popularity.desc']
                ];
            }

            $config = $mapa[$bloqueActual];

            if ($config['tipo'] === 'local') {
                $items = $this->obtenerLocal($esKids, $esFree, $config['params'] ?? []);
            } else {
                $items = $this->fetchTmdbMixed($config['api_type'], $config['params'], $esKids);
            }

            if (!empty($items)) {
                $paramsEncoded = base64_encode(json_encode($config['params'] ?? []));
                $startPage = $config['params']['page'] ?? 1;
                $apiType = $config['api_type'] ?? 'local';

                $html .= '<div class="category-row mb-5" style="padding: 0 4%; opacity:0; transition: opacity 1s;" onload="this.style.opacity=1">';
                $html .= '  <h3 class="row-title text-white fw-bold mb-3" style="font-family: Outfit; font-size: 1.4rem;">' . esc($config['titulo']) . '</h3>';
                $html .= '  <div class="slick-carousel-ajax" data-params="' . $paramsEncoded . '" data-page="' . ($startPage + 1) . '" data-endpoint="' . $apiType . '">';

                foreach ($items as $item) {
                    $this->renderCard($html, $item, $userId, $esKids);
                }
                $html .= '  </div></div>';
                break;
            }
            $intentos++;
        } while ($intentos < $maxIntentos);

        return $this->response->setBody($html);
    }

    public function ajaxExpandirFila()
    {
        $paramsEncoded = $this->request->getPost('params');
        $page = intval($this->request->getPost('page'));
        $tipo = $this->request->getPost('tipo');
        $esKids = (session()->get('plan_id') == 3);

        if (!$paramsEncoded || $tipo == 'local')
            return "";

        $params = json_decode(base64_decode($paramsEncoded), true);
        $params['page'] = $page + 1;

        $items = $this->fetchTmdbMixed($tipo, $params, $esKids);
        if (empty($items))
            return "";

        $html = "";
        $userId = session()->get('user_id');
        foreach ($items as $item) {
            $this->renderCard($html, $item, $userId, $esKids);
        }
        return $this->response->setBody($html);
    }


    private function fetchTmdbMixed($tipo, $params = [], $esKids = false)
    {
        $baseParams = array_merge([
            'api_key' => $this->tmdbKey,
            'language' => 'es-ES',
            'include_adult' => 'false',
            'page' => 1
        ], $params);

        if ($esKids) {
            $baseParams['with_genres'] = isset($baseParams['with_genres']) ? $baseParams['with_genres'] . ',16' : '16,10751';
            $baseParams['without_genres'] = '27,80,18,10752,53';
        }

        // OPTIMIZACIÓN 3: Timeout corto (3s) para no colgar el servidor si la API tarda
        $ctx = stream_context_create([
            "ssl" => ["verify_peer" => false], 
            "http" => ["ignore_errors" => true, "timeout" => 3.0]
        ]);
        
        $url = "https://api.themoviedb.org/3/discover/{$tipo}?" . http_build_query($baseParams);
        $json = @file_get_contents($url, false, $ctx);
        $results = [];

        if ($json) {
            $data = json_decode($json, true);
            if (!empty($data['results'])) {
                foreach ($data['results'] as $item) {
                    if (empty($item['poster_path']))
                        continue;
                    
                    if ($esKids && (in_array(27, $item['genre_ids'] ?? []) || in_array(80, $item['genre_ids'] ?? [])))
                        continue;

                    $bg = $item['backdrop_path'] ?? $item['poster_path'];
                    $titulo = ($tipo == 'tv') ? ($item['name'] ?? '') : ($item['title'] ?? '');
                    $fecha = ($tipo == 'tv') ? ($item['first_air_date'] ?? '') : ($item['release_date'] ?? '');
                    $prefix = ($tipo == 'tv') ? 'tmdb_tv_' : 'tmdb_movie_';

                    // CAMBIO CLAVE: Usamos w1280 (aprox 200KB) en lugar de original (aprox 5MB)
                    $fullBg = "https://image.tmdb.org/t/p/w1280" . $bg;

                    $results[] = [
                        'id' => $prefix . $item['id'],
                        'titulo' => $titulo,
                        'imagen' => "https://image.tmdb.org/t/p/w300" . $item['poster_path'],
                        'imagen_bg' => $fullBg,
                        'backdrop' => $fullBg,
                        'descripcion' => $item['overview'],
                        'anio' => substr($fecha, 0, 4),
                        'edad' => $esKids ? 'TP' : '12',
                        'link_ver' => base_url('ver/' . $prefix . $item['id']),
                        'link_detalle' => base_url('detalle/' . $prefix . $item['id']),
                        'tipo_id' => ($tipo == 'tv') ? 2 : 1,
                    ];
                }
            }
        }
        return $results;
    }

    private function obtenerLocal($esKids, $esFree, $params = [])
    {
        $model = new ContenidoModel();
        $q = $model->select('contenidos.*');
        if (isset($params['tipo_id']))
            $q->where('contenidos.tipo_id', $params['tipo_id']);
        if ($esFree)
            $q->where('contenidos.nivel_acceso', 1);
        if ($esKids)
            $q->where('contenidos.edad_recomendada <=', 11);

        if (isset($params['with_genres'])) {
            $q->join('contenido_genero', 'contenido_genero.contenido_id = contenidos.id');
            $q->where('contenido_genero.genero_id', $params['with_genres']);
            $q->groupBy('contenidos.id');
        }
        $local = $q->orderBy('contenidos.id', 'DESC')->findAll(limit: 50);

        $items = [];
        foreach ($local as $r) {
            $items[] = $this->formatearLocal($r);
        }
        return $items;
    }

    private function formatearLocal($r)
    {
        $bg = str_starts_with($r['imagen_bg'] ?? '', 'http') ? $r['imagen_bg'] : base_url('assets/img/' . ($r['imagen_bg'] ?? $r['imagen']));
        $img = str_starts_with($r['imagen'], 'http') ? $r['imagen'] : base_url('assets/img/' . $r['imagen']);
        if (empty($r['imagen_bg']))
            $bg = $img;

        return [
            'id' => $r['id'],
            'titulo' => $r['titulo'],
            'descripcion' => $r['descripcion'],
            'imagen' => $img,
            'imagen_bg' => $bg,
            'backdrop' => $bg,
            'anio' => $r['anio'],
            'edad' => $r['edad_recomendada'],
            'link_ver' => base_url('ver/' . $r['id']),
            'link_detalle' => base_url('detalle/' . $r['id']),
            'tipo_id' => $r['tipo_id'],

        ];
    }

    private function renderCard(&$html, $item, $userId, $esKids)
    {
        $db = \Config\Database::connect();
        $enLista = $db->table('mi_lista')->where('usuario_id', $userId)->where('contenido_id', $item['id'])->countAllResults() > 0;
        $styleBtn = $enLista ? 'border-color: var(--accent); color: var(--accent);' : '';
        $iconClass = $enLista ? 'fa-check' : 'fa-heart';
        $edadBadge = ($esKids || $item['edad'] == 'TP') ? 'TP' : '+' . $item['edad'];

        $html .= '<div class="slick-slide-item" style="padding: 0 5px;">
          <div class="movie-card">
            <div class="poster-visible"><img src="' . $item['imagen'] . '" alt="' . esc($item['titulo']) . '"></div>
            <div class="hover-details-card">
              <div class="hover-backdrop" style="background-image: url(\'' . $item['imagen_bg'] . '\');" onclick="window.location.href=\'' . $item['link_detalle'] . '\'"></div>
              <div class="hover-info">
                <div class="hover-buttons">
                  <button class="btn-mini-play" onclick="playCinematic(\'' . $item['link_ver'] . '\')"><i class="fa fa-play"></i></button>
                  <button class="btn-mini-icon btn-lista-' . $item['id'] . '" onclick="toggleMiLista(\'' . $item['id'] . '\')" style="' . $styleBtn . '"><i class="fa ' . $iconClass . '"></i></button>
                </div>
                <h4 onclick="window.location.href=\'' . $item['link_detalle'] . '\'">' . esc($item['titulo']) . '</h4>
                <div class="hover-meta">
                  <span style="color:#46d369; font-weight:bold;">' . rand(85, 99) . '%</span>
                  <span class="badge badge-hd">' . $edadBadge . '</span>
                </div>
                <p class="desc-clamp">' . esc($item['descripcion']) . '</p>
              </div>
            </div>
          </div>
        </div>';
    }

    public function miLista()
    {
        // 1. Seguridad: Si no está logueado, fuera
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');

        // 2. Conexión y Modelos
        $db = \Config\Database::connect();
        $generoModel = new GeneroModel;
        $userModel = new UsuarioModel;

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

        if ($esTmdb) {
            // Buscamos en la API (y calculamos la edad real)
            $contenido = $this->obtenerDetalleExterno($idLimpio, $tipoBusqueda);

            if ($contenido) {
                $esExterno = true;
                $prefix = ($tipoBusqueda === 'tv') ? 'tmdb_tv_' : 'tmdb_movie_';
                $contenido['id'] = $prefix . $contenido['id'];
            }
        } else {
            // =========================================================
            // LÓGICA HÍBRIDA: CONTENIDO LOCAL + DATOS TMDB
            // =========================================================
            $localData = $model->getDetallesCompletos($id);

            if ($localData) {
                $esLocal = true;
                $director = $model->getDirector($id); // Respaldo del director local

                // 1. Buscamos el título local en la API de TMDB
                $tituloBusqueda = urlencode($localData['titulo']);
                $apiKey = '6387e3c183c454304108333c56530988';
                $searchUrl = "https://api.themoviedb.org/3/search/multi?api_key={$apiKey}&language=es-ES&query={$tituloBusqueda}";

                $arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false], "http" => ["ignore_errors" => true]];
                $jsonSearch = @file_get_contents($searchUrl, false, stream_context_create($arrContextOptions));

                $tmdbData = null;
                if ($jsonSearch) {
                    $searchResult = json_decode($jsonSearch, true);

                    // Si TMDB encuentra resultados con ese nombre...
                    if (!empty($searchResult['results'])) {
                        $primerResultado = $searchResult['results'][0];
                        $tmdbId = $primerResultado['id'];
                        $tipoTmdb = ($primerResultado['media_type'] === 'tv') ? 'tv' : 'movie';

                        // 2. Traemos todos los detalles ricos usando tu función
                        $tmdbData = $this->obtenerDetalleExterno($tmdbId, $tipoTmdb);
                    }
                }

                // 3. FUSIÓN DE DATOS (El truco maestro)
                if ($tmdbData) {
                    // Usamos la información espectacular de TMDB (Fotos, Actores, etc)
                    $contenido = $tmdbData;

                    // ¡SUPER IMPORTANTE! Sobrescribimos el ID de TMDB con tu ID Local.
                    // Si no hacemos esto, el botón "Reproducir" intentaría buscar
                    // el vídeo en TMDB en lugar de en tu servidor.
                    $contenido['id'] = $localData['id'];

                    // Conservamos el nivel de acceso (Gratis/Premium) de tu BD local
                    $contenido['nivel_acceso'] = $localData['nivel_acceso'] ?? 1;

                    // Actualizamos la variable $director para la vista
                    if (!empty($tmdbData['director_externo'])) {
                        $director = $tmdbData['director_externo'];
                    }
                } else {
                    // FALLBACK: Si subes un vídeo casero que TMDB no conoce, 
                    // simplemente usamos tus datos locales para que no falle.
                    $contenido = $localData;
                }
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
                return ['nombre' => $g['name']];
            }, $data['genres'] ?? []),
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

    public function autocompletar()
    {
        $request = service('request');
        $search = $request->getPost('search'); // O 'term' si usas jQuery UI por defecto

        // Si no llega nada, devuelve vacío
        if (!$search || strlen($search) < 2) {
            return $this->response->setJSON(['token' => csrf_hash(), 'data' => []]);
        }

        $planId = session()->get('plan_id') ?? 1;
        $response = ['token' => csrf_hash()];
        $data = [];

        $titulosRegistrados = [];

        // ---------------------------------------------------------
        // 1. BÚSQUEDA LOCAL
        // ---------------------------------------------------------
        $model = new ContenidoModel(); // Asegúrate del namespace correcto
        $builder = $model->select('id, titulo, imagen, edad_recomendada, nivel_acceso')
            ->like('titulo', $search);

        if ($planId == 3) { // Kids
            $builder->where('edad_recomendada <=', 11);
        } elseif ($planId == 1) { // Free
            $builder->where('nivel_acceso', 1);
        }

        $locales = $builder->orderBy('titulo', 'ASC')->findAll(5);

        foreach ($locales as $peli) {
            // Guardamos el título en minúsculas para comparar luego
            $titulosRegistrados[] = mb_strtolower(trim($peli['titulo']));

            $imgUrl = str_starts_with($peli['imagen'], 'http') ? $peli['imagen'] : base_url('assets/img/' . $peli['imagen']);

            $data[] = [
                "id" => $peli['id'],
                "value" => $peli['titulo'],
                "label" => $peli['titulo'], // Etiqueta limpia para local
                "img" => $imgUrl,
                "type" => "local"
            ];
        }

        // ---------------------------------------------------------
        // 2. BÚSQUEDA EXTERNA (TMDB)
        // ---------------------------------------------------------
        // Solo buscamos fuera si no somos usuario Free y tenemos hueco en la lista
        if (count($data) < 10 && $planId != 1) {

            $apiKey = '6387e3c183c454304108333c56530988'; // Tu API Key
            $query = urlencode($search);
            $url = "https://api.themoviedb.org/3/search/multi?api_key={$apiKey}&language=es-ES&query={$query}&include_adult=false";

            // Contexto para evitar errores de SSL en local
            $ctx = stream_context_create(["ssl" => ["verify_peer" => false], "http" => ["ignore_errors" => true]]);
            $json = @file_get_contents($url, false, $ctx);

            if ($json) {
                $tmdbResults = json_decode($json, true);

                if (!empty($tmdbResults['results'])) {
                    foreach ($tmdbResults['results'] as $item) {
                        // Límite total de resultados
                        if (count($data) >= 10)
                            break;

                        // Solo Pelis y Series
                        $mediaType = $item['media_type'] ?? '';
                        if ($mediaType != 'movie' && $mediaType != 'tv')
                            continue;

                        // Obtener Título según sea Peli o Serie
                        $tituloTmdb = ($mediaType == 'tv') ? ($item['name'] ?? '') : ($item['title'] ?? '');

                        // --- FILTRO ANTI-DUPLICADOS ---
                        // Si el título de TMDB ya está en nuestro array de locales, LO SALTAMOS
                        if (in_array(mb_strtolower(trim($tituloTmdb)), $titulosRegistrados)) {
                            continue;
                        }

                        // Preparar datos visuales
                        $prefix = ($mediaType == 'tv') ? "tmdb_tv_" : "tmdb_movie_";
                        $fecha = ($mediaType == 'tv') ? ($item['first_air_date'] ?? '') : ($item['release_date'] ?? '');
                        $anio = substr($fecha, 0, 4);
                        $tipoLabel = ($mediaType == 'tv') ? " (Serie)" : "";

                        $poster = !empty($item['poster_path'])
                            ? "https://image.tmdb.org/t/p/w92" . $item['poster_path']
                            : base_url('assets/img/no-poster.jpg');

                        $data[] = [
                            "id" => $prefix . $item['id'],
                            "value" => $tituloTmdb,
                            "label" => $tituloTmdb . ($anio ? " ($anio)" : "") . $tipoLabel,
                            "img" => $poster,
                            "type" => "tmdb"
                        ];
                    }
                }
            }
        }

        $response['data'] = $data;
        return $this->response->setJSON($response);
    }

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
    // VISTA ANGULAR (ZONA GLOBAL)

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
            'titulo' => 'Zona Global - La Butaca',
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll(), // <--- ESTO ES LO QUE FALTA
            'user_token' => csrf_hash(),
            'user_id' => session()->get('user_id'),
            'otrosPerfiles' => (new UsuarioModel())->where('id !=', session()->get('user_id'))->where('id >=', 2)->where('id <=', 4)->findAll()
        ];

        // Ahora el Header ya tendrá la variable $generos para dibujar el menú
        echo view('frontend/templates/header', $data);
        echo view('frontend/global', $data);
        echo view('frontend/templates/footer', $data);
    }


    // PERFIL DE PERSONA (ACTOR/DIRECTOR)

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
        $formatearCredito = function ($item) use ($basePoster) {
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
        foreach ($rawCrew as $c) {
            if (isset($c['job']) && $c['job'] === 'Director') {
                $directing[] = $formatearCredito($c);
            }
        }

        // Ordenar por popularidad (lo más famoso primero)
        usort($acting, function ($a, $b) {
            return $b['popularidad'] <=> $a['popularidad'];
        });
        usort($directing, function ($a, $b) {
            return $b['popularidad'] <=> $a['popularidad'];
        });

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

    public function ayuda()
    {
        $data = ['titulo' => 'Centro de Ayuda - La Butaca'];
        return view('frontend/help', $data);
    }

    public function verGenero($idGenero, $tipoEspecifico = null)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $generoModel = new GeneroModel();
        $userModel = new UsuarioModel();

        $infoGenero = $generoModel->find($idGenero);
        if (!$infoGenero)
            return redirect()->to('/');

        // 1. CAPTURAR LA PÁGINA (Para el Scroll Infinito)
        $pagina = (int) ($this->request->getVar('page') ?? 1);
        $limite = 20; // Cargaremos de 20 en 20 para que vaya rapidísimo

        $data = [
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll(),
            'otrosPerfiles' => $userModel->where('id !=', session()->get('user_id'))->findAll(),
            'mostrarHero' => false,
            'splash' => false,
            'carrusel' => []
        ];

        // --- CASO A: MODO "VER TODO" (GRID CON SCROLL INFINITO) ---
        if ($tipoEspecifico !== null) {

            // Pasamos $limite y $pagina a nuestro helper
            $contenidos = $this->_getContenidoCombinado($idGenero, $tipoEspecifico, $limite, $pagina);

            // === MAGIA AJAX (LO QUE PIDE EL JS AL HACER SCROLL) ===
            if ($this->request->isAJAX()) {
                // Si la API ya no devuelve nada, enviamos vacío para que el JS sepa que ha terminado
                if (empty($contenidos)) {
                    return '';
                }

                // Generamos SOLO el HTML de las tarjetas nuevas para apilar
                $html = '';
                foreach ($contenidos as $item) {
                    $html .= '<a href="' . base_url('detalle/' . $item['id']) . '" class="poster-card" style="text-decoration:none;">';
                    $html .= '<img src="' . $item['imagen'] . '" loading="lazy" alt="' . esc($item['titulo']) . '">';
                    // Opcional: Puedes añadir aquí el título debajo de la foto si quieres
                    $html .= '</a>';
                }
                return $html;
            }

            // Si el usuario recarga la página a la fuerza escribiendo la URL (Fallback)
            $nombreTipo = ($tipoEspecifico == 1) ? 'Películas' : 'Series';
            $data['titulo'] = $nombreTipo . ' de ' . $infoGenero['nombre'];
            $data['peliculas'] = $contenidos;

            echo view('frontend/templates/header', $data);
            echo view('frontend/peliculas', $data); // Tu vista grid completa
            echo view('frontend/templates/footer', $data);
            return;
        }

        // --- CASO B: MODO RESUMEN (FILAS) ---
        $peliculas = $this->_getContenidoCombinado($idGenero, 1, 20, 1);
        $series = $this->_getContenidoCombinado($idGenero, 2, 20, 1);

        $data['titulo'] = 'Explorando: ' . $infoGenero['nombre'];
        $data['infoGenero'] = $infoGenero;
        $data['peliculas'] = $peliculas;
        $data['series'] = $series;
        $data['otrosPerfiles'] = (new UsuarioModel())->where('id !=', session()->get('user_id'))->where('id >=', 2)->where('id <=', 4)->findAll();


        echo view('frontend/templates/header', $data);
        echo view('frontend/genero_filas', $data);
        echo view('frontend/templates/footer', $data);
    }


    private function _getContenidoCombinado($idGenero, $tipoId, $limit = 20, $page = 1)
    {
        $planId = session()->get('plan_id');
        $model = new ContenidoModel();

        // 1. LOCAL
        $offset = ($page - 1) * $limit;
        $contenido = $model->getPorGenero($idGenero, $tipoId, $limit, [], $planId);

        // Filtro Kids Local
        if ($planId == 3) {
            $contenido = array_filter($contenido, fn($c) => $c['edad_recomendada'] <= 11);
        }

        // 2. EXTERNO (TMDB)
        // REGLA APLICADA: Solo Premium (2) o Kids (3). El Free (1) no entra.
        if ($planId == 2 || $planId == 3) {

            // MAPA A: Si estamos buscando Películas ($tipoId == 1)
            $mapaMovie = [
                1 => 28,   // Acción
                2 => 12,   // Aventura
                3 => 878,  // Ciencia Ficción
                4 => 18,   // Drama
                5 => 16,   // Animación
                6 => 80,   // Crimen
                7 => 35,   // Comedia
                8 => 27,   // Terror
                9 => 10749,// Romance
                10 => 14,  // Fantasía
                12 => 9648,// Misterio
                13 => 53   // Suspense
            ];

            // MAPA B: Si estamos buscando Series ($tipoId == 2)
            $mapaTv = [
                1 => 10759, // Action & Adventure (TMDB las junta en TV)
                2 => 10759, // Action & Adventure
                3 => 10765, // Sci-Fi & Fantasy (TMDB las junta en TV)
                4 => 18,    // Drama
                5 => 16,    // Animación
                6 => 80,    // Crimen
                7 => 35,    // Comedia
                8 => 9648,  // Terror puro casi no hay en TV, usamos Misterio
                9 => 10749, // Romance
                10 => 10765,// Sci-Fi & Fantasy
                12 => 9648, // Misterio
                13 => 10759 // Suspense -> Action & Adventure
            ];

            // Seleccionamos el mapa correcto según lo que estemos buscando
            $mapaActivo = ($tipoId == 2) ? $mapaTv : $mapaMovie;

            if (isset($mapaActivo[$idGenero])) {
                $idTmdb = $mapaActivo[$idGenero];
                $modoTmdb = ($tipoId == 2) ? 'tv' : 'movie';
                $esKids = ($planId == 3);

                $externos = $this->obtenerDeTmdbPorGenero($idTmdb, $modoTmdb, $esKids, $page);

                if (count($externos) > $limit) {
                    $externos = array_slice($externos, 0, $limit);
                }

                if ($page > 1) {
                    $contenido = $externos;
                } else {
                    $contenido = array_merge($contenido, $externos);
                }
            }
        }

        // 3. SANITIZACIÓN DE IMÁGENES
        foreach ($contenido as &$c) {
            if (empty($c['imagen'])) {
                $c['imagen'] = base_url('assets/img/no-poster.jpg');
            } elseif (!str_starts_with($c['imagen'], 'http')) {
                $c['imagen'] = base_url('assets/img/' . $c['imagen']);
            }

            if (empty($c['imagen_bg'])) {
                $c['imagen_bg'] = $c['imagen'];
            } elseif (!str_starts_with($c['imagen_bg'], 'http')) {
                $c['imagen_bg'] = base_url('assets/img/' . $c['imagen_bg']);
            }

            if (!isset($c['nivel_acceso'])) {
                $c['nivel_acceso'] = 1;
            }
        }

        return $contenido;
    }

    private function obtenerDeTmdbPorGenero($genreIdTmdb, $tipo = 'movie', $esKids = false, $page = 1)
    {
        $apiKey = '6387e3c183c454304108333c56530988';
        $lang = 'es-ES';

        $context = stream_context_create([
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false],
            "http" => ["ignore_errors" => true]
        ]);

        $url = "https://api.themoviedb.org/3/discover/{$tipo}?api_key={$apiKey}&language={$lang}&sort_by=popularity.desc&include_adult=false&page={$page}";

        // --- GESTIÓN DE GÉNEROS ---
        $generosAFiltrar = [$genreIdTmdb];

        // --- FILTRO KIDS ---
        if ($esKids) {
            if ($tipo == 'movie') {
                $url .= "&certification_country=ES&certification.lte=7";
            } else {
                $generosAFiltrar[] = 10762;
            }
        }

        $url .= "&with_genres=" . implode(',', $generosAFiltrar);

        // --- LLAMADA A LA API ---
        $json = @file_get_contents($url, false, $context);
        if (!$json)
            return [];

        $data = json_decode($json, true);
        $resultados = [];
        $baseImg = "https://image.tmdb.org/t/p/w300";
        $baseBackdrop = "https://image.tmdb.org/t/p/w1280";

        if (!empty($data['results'])) {
            foreach ($data['results'] as $item) {
                if (empty($item['poster_path']))
                    continue;

                $prefix = ($tipo == 'tv') ? 'tmdb_tv_' : 'tmdb_movie_';
                $titulo = ($tipo == 'tv') ? ($item['name'] ?? '') : ($item['title'] ?? '');
                $fecha = ($tipo == 'tv') ? ($item['first_air_date'] ?? '') : ($item['release_date'] ?? '');

                $resultados[] = [
                    'id' => $prefix . $item['id'],
                    'titulo' => $titulo,
                    'imagen' => $baseImg . $item['poster_path'],
                    'imagen_bg' => !empty($item['backdrop_path']) ? $baseBackdrop . $item['backdrop_path'] : ($baseImg . $item['poster_path']),
                    'anio' => substr($fecha, 0, 4),
                    'edad_recomendada' => $esKids ? 0 : 12,
                    'tipo_id' => ($tipo == 'tv') ? 2 : 1,
                    'vistas' => $item['popularity'],
                    'nivel_acceso' => 0
                ];
            }
        }

        return $resultados;
    }
    // FUNCIÓN PARA REVISAR CADUCIDAD
    private function _verificarSuscripcion($usuario)
    {
        if ($usuario['plan_id'] == 1)
            return;

        if (empty($usuario['fecha_fin_suscripcion']))
            return;

        $fechaFin = new \DateTime($usuario['fecha_fin_suscripcion']);
        $ahora = new \DateTime();

        if ($ahora > $fechaFin) {
            $userModel = new UsuarioModel();

            $userModel->update($usuario['id'], [
                'plan_id' => 1,
                'fecha_fin_suscripcion' => null
            ]);

            session()->set('plan_id', 1);

            session()->setFlashdata('error', 'Tu suscripción ha caducado. Has vuelto al plan Free.');
        }
    }
}