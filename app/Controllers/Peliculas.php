<?php

namespace App\Controllers;

use App\Models\ContenidoModel;
use App\Models\UsuarioModel;
use App\Models\GeneroModel;

class Peliculas extends BaseController
{
    private $tmdbKey = '6387e3c183c454304108333c56530988';

public function index()
    {
        // 1. CHEQUEO DE SESIÓN
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $planId = session()->get('plan_id');
        $esFree = ($planId == 1);
        $esKids = ($planId == 3);
        
        $destacada = null;
        $model = new ContenidoModel();

        // =========================================================
        // 🎲 LÓGICA ALEATORIA (HERO)
        // =========================================================

        // --- CASO A: USUARIO FREE (Aleatorio Local) ---
        if ($esFree) {
            // Buscamos una película aleatoria en la BD que sea Gratis
            $localRandom = $model->where('tipo_id', 1)       // Es Película
                                 ->where('nivel_acceso', 1)  // Es Gratis
                                 ->where('imagen_bg !=', '') // Tiene fondo
                                 ->orderBy('RAND()')         // <--- ¡MAGIA! Aleatorio puro
                                 ->first();
            
            if ($localRandom) {
                $destacada = $this->formatearLocal($localRandom);
            }
        } 
        
        // --- CASO B: PREMIUM O KIDS (Aleatorio API TMDB) ---
        else {
            // Pedimos una página al azar (1-5) para variar contenido
            $paginaAleatoria = rand(1, 5);
            
            $params = [
                'sort_by' => 'popularity.desc', 
                'page' => $paginaAleatoria
            ];

            // La función fetchTmdbDiscover ya filtra si $esKids es true
            $resultados = $this->fetchTmdbDiscover($params, $esKids); 

            if (!empty($resultados)) {
                // Mezclamos para coger una cualquiera de esa página
                shuffle($resultados); 

                // Buscamos la primera con imagen de fondo válida
                foreach ($resultados as $peli) {
                    if (!empty($peli['imagen_bg'])) {
                        $s = $peli;
                        $destacada = [
                            'id' => $s['id'],
                            'titulo' => $s['titulo'],
                            'descripcion' => $s['descripcion'],
                            'backdrop' => $s['imagen_bg'],
                            'link_ver' => $s['link_ver'],
                            'link_detalle' => $s['link_detalle']
                        ];
                        break; 
                    }
                }
            }
        }

        // =========================================================
        // 🛡️ FALLBACK (Por si falla la API o no hay pelis gratis)
        // =========================================================
        if (empty($destacada)) {
            // Cogemos CUALQUIER película local aleatoria para salvar el diseño
            $backup = $model->where('tipo_id', 1)
                            ->where('imagen_bg !=', '')
                            ->orderBy('RAND()')
                            ->first();
            
            if ($backup) {
                $destacada = $this->formatearLocal($backup);
            }
        }

        // ULTIMÍSIMO RECURSO (Base de datos vacía)
        if (empty($destacada)) {
            $destacada = [
                'id' => 0,
                'titulo' => 'Catálogo Vacío',
                'descripcion' => 'No se ha encontrado contenido para mostrar.',
                'backdrop' => 'https://via.placeholder.com/1920x800/111/fff?text=Sin+Contenido',
                'link_ver' => '#',
                'link_detalle' => '#'
            ];
        }

        // VISTA
        $data = [
            'titulo' => 'Películas - La Butaca',
            'destacada' => $destacada,
            'mostrarHero' => true,
            'splash' => false,
            'categoria' => 'Películas',
            'generos' => (new GeneroModel())->findAll(),
            'otrosPerfiles' => (new UsuarioModel())->where('id !=', $userId)->where('id >=', 2)->where('id <=', 4)->findAll()
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/peliculas', $data);
        echo view('frontend/templates/footer', $data);
    }

    // --- AÑADE ESTA FUNCIÓN HELPER PRIVADA (Necesaria para el index) ---
    private function formatearLocal($r) {
        $bg = str_starts_with($r['imagen_bg'], 'http') ? $r['imagen_bg'] : base_url('assets/img/' . $r['imagen_bg']);
        
        // Si no tiene fondo, intentamos usar el póster
        if (empty($r['imagen_bg']) && !empty($r['imagen'])) {
            $bg = str_starts_with($r['imagen'], 'http') ? $r['imagen'] : base_url('assets/img/' . $r['imagen']);
        }
        
        return [
            'id' => $r['id'],
            'titulo' => $r['titulo'],
            'descripcion' => $r['descripcion'],
            'backdrop' => $bg,
            'link_ver' => base_url('ver/' . $r['id']),
            'link_detalle' => base_url('detalle/' . $r['id'])
        ];
    }
    // =================================================================
    // CARGA AJAX A PRUEBA DE FALLOS (Retry Loop)
    // =================================================================
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

        // BUCLE DE SEGURIDAD
        do {
            $bloqueActual = $bloqueSolicitado + $intentos;
            $items = [];

            // ---------------------------------------------------------
            // 1. EL MAPA DE CATEGORÍAS (PELÍCULAS)
            // ---------------------------------------------------------
            if ($esFree) {
                // SOLO LOCAL
                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Películas Gratis'],
                    1 => ['tipo' => 'local', 'titulo' => 'Acción Local', 'params' => ['with_genres' => 1]],
                    2 => ['tipo' => 'local', 'titulo' => 'Comedias de la Casa', 'params' => ['with_genres' => 7]],
                    3 => ['tipo' => 'local', 'titulo' => 'Dramas Intensos', 'params' => ['with_genres' => 4]],
                    4 => ['tipo' => 'local', 'titulo' => 'Ciencia Ficción', 'params' => ['with_genres' => 3]],
                    5 => ['tipo' => 'local', 'titulo' => 'Infantil / Animación', 'params' => ['with_genres' => 5]],
                    6 => ['tipo' => 'local', 'titulo' => 'Aventuras', 'params' => ['with_genres' => 2]],
                ];
            } elseif ($esKids) {
                // --- MAPA INFANTIL (PELIS) ---
                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Tus Pelis Favoritas'],
                    1 => ['tipo' => 'tmdb', 'titulo' => 'Éxitos de Disney', 'params' => ['with_companies' => '2', 'sort_by' => 'popularity.desc']],
                    2 => ['tipo' => 'tmdb', 'titulo' => 'Mundo Pixar', 'params' => ['with_companies' => '3', 'sort_by' => 'popularity.desc']],
                    3 => ['tipo' => 'tmdb', 'titulo' => 'Dreamworks Animation', 'params' => ['with_companies' => '521', 'sort_by' => 'popularity.desc']],
                    4 => ['tipo' => 'tmdb', 'titulo' => 'Aventuras Animadas', 'params' => ['with_genres' => '16,12', 'sort_by' => 'popularity.desc']],
                    5 => ['tipo' => 'tmdb', 'titulo' => 'Comedias Familiares', 'params' => ['with_genres' => '10751,35', 'sort_by' => 'revenue.desc']],
                    6 => ['tipo' => 'tmdb', 'titulo' => 'Animales Heroicos', 'params' => ['with_genres' => '16', 'with_keywords' => '12423']],
                    7 => ['tipo' => 'tmdb', 'titulo' => 'Musicales Mágicos', 'params' => ['with_genres' => '10751,10402']],
                    8 => ['tipo' => 'tmdb', 'titulo' => 'Películas de Superhéroes (Kids)', 'params' => ['with_genres' => '16,28', 'with_keywords' => '9748']],
                ];
            } else {
                // --- MAPA ADULTOS (PELIS) ---
                // IDs de género CINE TMDB: 28=Acción, 12=Aventura, 16=Animación, 35=Comedia, 80=Crimen, 
                // 99=Docu, 18=Drama, 10751=Familia, 14=Fantasía, 36=Historia, 27=Terror, 10402=Música, 
                // 9648=Misterio, 10749=Romance, 878=SciFi, 53=Thriller, 10752=Guerra, 37=Western
                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Tendencias en La Butaca'],
                    1 => ['tipo' => 'tmdb', 'titulo' => 'Cartelera: Lo más popular', 'params' => ['sort_by' => 'popularity.desc']],
                    2 => ['tipo' => 'tmdb', 'titulo' => 'Acción Imparable', 'params' => ['with_genres' => '28']],
                    3 => ['tipo' => 'tmdb', 'titulo' => 'Universo Cinematográfico Marvel', 'params' => ['with_companies' => '420', 'sort_by' => 'release_date.desc']],
                    4 => ['tipo' => 'tmdb', 'titulo' => 'Pasaje del Terror', 'params' => ['with_genres' => '27']],
                    5 => ['tipo' => 'tmdb', 'titulo' => 'Ciencia Ficción y Espacio', 'params' => ['with_genres' => '878']],
                    6 => ['tipo' => 'tmdb', 'titulo' => 'Comedias para reír', 'params' => ['with_genres' => '35']],
                    7 => ['tipo' => 'tmdb', 'titulo' => 'Dramas ganadores de Oscar', 'params' => ['with_genres' => '18', 'vote_average.gte' => 8]],
                    8 => ['tipo' => 'tmdb', 'titulo' => 'Cine Familiar', 'params' => ['with_genres' => '10751']],
                    9 => ['tipo' => 'tmdb', 'titulo' => 'Misterio y Suspense', 'params' => ['with_genres' => '9648,53']],
                    10 => ['tipo' => 'tmdb', 'titulo' => 'Aventuras', 'params' => ['with_genres' => '12']],
                    11 => ['tipo' => 'tmdb', 'titulo' => 'Fantasía Épica', 'params' => ['with_genres' => '14']],
                    12 => ['tipo' => 'tmdb', 'titulo' => 'Romance', 'params' => ['with_genres' => '10749']],
                    13 => ['tipo' => 'tmdb', 'titulo' => 'Animación para Adultos', 'params' => ['with_genres' => '16', 'without_genres' => '10751']],
                    14 => ['tipo' => 'tmdb', 'titulo' => 'Clásicos de los 80', 'params' => ['primary_release_date.gte' => '1980-01-01', 'primary_release_date.lte' => '1989-12-31']],
                    15 => ['tipo' => 'tmdb', 'titulo' => 'Christopher Nolan', 'params' => ['with_crew' => '525', 'sort_by' => 'popularity.desc']],
                ];
            }

            // ---------------------------------------------------------
            // 2. GENERADOR INFINITO
            // ---------------------------------------------------------
            if (!isset($mapa[$bloqueActual])) {
                // Freno Free
                if ($esFree)
                    break;

                if ($esKids) {
                    $pool = [
                        ['id' => 16, 'name' => 'Más Animación'],
                        ['id' => 10751, 'name' => 'Cine Familiar'],
                        ['id' => 12, 'name' => 'Aventuras'],
                        ['id' => 35, 'name' => 'Risas']
                    ];
                } else {
                    $pool = [
                        ['id' => 28, 'name' => 'Acción'],
                        ['id' => 12, 'name' => 'Aventura'],
                        ['id' => 16, 'name' => 'Animación'],
                        ['id' => 35, 'name' => 'Comedia'],
                        ['id' => 80, 'name' => 'Crimen'],
                        ['id' => 18, 'name' => 'Drama'],
                        ['id' => 14, 'name' => 'Fantasía'],
                        ['id' => 27, 'name' => 'Terror'],
                        ['id' => 10749, 'name' => 'Romance'],
                        ['id' => 878, 'name' => 'Sci-Fi'],
                        ['id' => 53, 'name' => 'Thriller']
                    ];
                }

                $idx = $bloqueActual % count($pool);
                $genre = $pool[$idx];
                $page = floor(($bloqueActual - 15) / count($pool)) + 2;

                $mapa[$bloqueActual] = [
                    'tipo' => 'tmdb',
                    'titulo' => 'Descubre: ' . $genre['name'],
                    'params' => ['with_genres' => $genre['id'], 'page' => $page, 'sort_by' => 'popularity.desc']
                ];
            }

            $config = $mapa[$bloqueActual];
            $saltarBloque = false;

            // Filtros de seguridad extra para Kids
            if ($esKids && isset($config['params']['with_genres'])) {
                $g = (string) $config['params']['with_genres'];
                if (strpos($g, '27') !== false || strpos($g, '80') !== false) // 27=Terror, 80=Crimen
                    $saltarBloque = true;
            }

            // ---------------------------------------------------------
            // 4. OBTENCIÓN DE DATOS
            // ---------------------------------------------------------
            if (!$saltarBloque) {
                if ($config['tipo'] === 'local') {
                    $items = $this->obtenerLocal($esKids, $esFree, $config['params'] ?? []);
                } else {
                    $items = $this->fetchTmdbDiscover($config['params'], $esKids);
                }
            }

            // ---------------------------------------------------------
            // 5. GENERACIÓN HTML
            // ---------------------------------------------------------
            if (!empty($items)) {
                $paramsEncoded = base64_encode(json_encode($config['params'] ?? []));
                $startPage = $config['params']['page'] ?? 1;
                $currentPage = $startPage + 1;
                $endpointType = ($config['tipo'] === 'local') ? 'local' : 'tmdb';

                $html .= '<div class="category-row mb-5" style="padding: 0 4%; opacity:0; transition: opacity 1s;" onload="this.style.opacity=1">';
                $html .= '  <h3 class="row-title text-white fw-bold mb-3" style="font-family: Outfit; font-size: 1.4rem;">' . esc($config['titulo']) . '</h3>';

                $html .= '  <div class="slick-carousel-ajax" data-params="' . $paramsEncoded . '" data-page="' . $currentPage . '" data-endpoint="' . $endpointType . '">';

                foreach ($items as $peli) {
                    $titulo = esc($peli['titulo']);
                    $img = $peli['imagen'];
                    $bg = $peli['imagen_bg'];
                    $desc = esc($peli['descripcion'] ?? '');
                    $linkD = $peli['link_detalle'];
                    $linkV = $peli['link_ver'];
                    $match = rand(85, 99);

                    $edadRaw = $peli['edad'] ?? '12';
                    if ($esKids || $edadRaw === 'TP')
                        $edadBadge = 'TP';
                    else
                        $edadBadge = '+' . $edadRaw;

                    $enLista = $peli['en_mi_lista'] ?? false;
                    $styleBtnLista = $enLista ? 'border-color: var(--accent); color: var(--accent);' : '';
                    $iconClass = $enLista ? 'fa-check' : 'fa-heart';

                    $html .= '<div class="slick-slide-item" style="padding: 0 5px;">';
                    $html .= '  <div class="movie-card">';
                    $html .= '    <div class="poster-visible"><img src="' . $img . '" alt="' . $titulo . '"></div>';
                    $html .= '    <div class="hover-details-card">';
                    $html .= '      <div class="hover-backdrop" style="background-image: url(\'' . $bg . '\'); cursor: pointer;" onclick="window.location.href=\'' . $linkD . '\'"></div>';
                    $html .= '      <div class="hover-info">';
                    $html .= '        <div class="hover-buttons">';
                    $html .= '          <button class="btn-mini-play" onclick="playCinematic(\'' . $linkV . '\')"><i class="fa fa-play"></i></button>';
                    $html .= '          <button class="btn-mini-icon btn-lista-' . $peli['id'] . '" onclick="toggleMiLista(\'' . $peli['id'] . '\')" style="' . $styleBtnLista . '"><i class="fa ' . $iconClass . '"></i></button>';
                    $html .= '        </div>';
                    $html .= '        <h4 style="cursor:pointer;" onclick="window.location.href=\'' . $linkD . '\'">' . $titulo . '</h4>';
                    $html .= '        <div class="hover-meta">';
                    $html .= '          <span style="color:#46d369; font-weight:bold;">' . $match . '% para ti</span>';
                    $html .= '          <span class="badge badge-hd">' . $edadBadge . '</span>';
                    $html .= '        </div>';
                    $html .= '        <p style="font-size:0.75rem; color:#ccc; margin:0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">' . $desc . '</p>';
                    $html .= '      </div>';
                    $html .= '    </div>';
                    $html .= '  </div>';
                    $html .= '</div>';
                }
                $html .= '  </div>'; // Fin slick
                $html .= '</div>'; // Fin row

                break; // Éxito
            }
            $intentos++;
        } while ($intentos < $maxIntentos);

        return $this->response->setBody($html);
    }

    // --- HELPER LOCAL (Tipo ID = 1 para Pelis) ---
    private function obtenerLocal($esKids, $esFree, $params = [])
    {
        $model = new ContenidoModel();

        $q = $model->select('contenidos.*');
        $q->where('contenidos.tipo_id', 1); 

        if ($esFree) {
            $q->where('contenidos.nivel_acceso', 1);
        }
        if ($esKids) {
            $q->where('contenidos.edad_recomendada <=', 11);
        }

        if (isset($params['with_genres'])) {
            $q->join('contenido_genero', 'contenido_genero.contenido_id = contenidos.id');
            $q->where('contenido_genero.genero_id', $params['with_genres']);
            $q->groupBy('contenidos.id');
        }

        $orden = isset($params['with_genres']) ? 'contenidos.id' : 'contenidos.vistas';

        $local = $q->orderBy($orden, 'DESC')->findAll(limit: 50);

        $items = [];
        foreach ($local as $r) {
            $img = str_starts_with($r['imagen'], 'http') ? $r['imagen'] : base_url('assets/img/' . $r['imagen']);
            $bg = str_starts_with($r['imagen_bg'], 'http') ? $r['imagen_bg'] : base_url('assets/img/' . $r['imagen_bg']);
            if (empty($r['imagen_bg']))
                $bg = $img;

            $db = \Config\Database::connect();
            $enLista = $db->table('mi_lista')
                ->where('usuario_id', session()->get('user_id'))
                ->where('contenido_id', $r['id'])
                ->countAllResults() > 0;

            $items[] = [
                'id' => $r['id'],
                'titulo' => $r['titulo'],
                'imagen' => $img,
                'imagen_bg' => $bg,
                'descripcion' => $r['descripcion'],
                'edad' => $r['edad_recomendada'],
                'en_mi_lista' => $enLista,
                'anio' => $r['anio'],
                'link_ver' => base_url('ver/' . $r['id']),
                'link_detalle' => base_url('detalle/' . $r['id'])
            ];
        }
        return $items;
    }

    // --- HELPER TMDB PARA PELÍCULAS ---
    private function fetchTmdbDiscover($params = [], $esKids = false)
    {
        $baseParams = array_merge([
            'api_key' => $this->tmdbKey,
            'language' => 'es-ES',
            'include_adult' => 'false',
            'page' => 1
        ], $params);

        if ($esKids) {
            // Si Kids -> Forzamos Animación o Familia
            if (!isset($baseParams['with_genres'])) {
                $baseParams['with_genres'] = '16,10751';
            }
            // Excluir géneros adultos (Terror, Crimen, Guerra)
            $generosProhibidos = '27,80,18,10752,53';
            if (isset($baseParams['without_genres'])) {
                $baseParams['without_genres'] .= ',' . $generosProhibidos;
            } else {
                $baseParams['without_genres'] = $generosProhibidos;
            }
        }

        $startPage = $baseParams['page'];
        $finalResults = [];

        for ($i = 0; $i < 2; $i++) {
            $baseParams['page'] = $startPage + $i;
            $url = "https://api.themoviedb.org/3/discover/movie?" . http_build_query($baseParams);

            $arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false], "http" => ["ignore_errors" => true]];
            $json = @file_get_contents($url, false, stream_context_create($arrContextOptions));

            if ($json) {
                $data = json_decode($json, true);
                if (!empty($data['results'])) {
                    foreach ($data['results'] as $item) {
                        if (empty($item['poster_path']))
                            continue;

                        if ($esKids) {
                            $g = $item['genre_ids'] ?? [];
                            if (in_array(27, $g) || in_array(80, $g))
                                continue;
                        }

                        $bg = !empty($item['backdrop_path']) ? $item['backdrop_path'] : $item['poster_path'];

                        $edadCalculada = '12';
                        // Si es Animación(16) o Familia(10751) -> TP
                        if (isset($item['genre_ids']) && (in_array(16, $item['genre_ids']) || in_array(10751, $item['genre_ids']))) {
                            $edadCalculada = 'TP';
                        }

                        $finalResults[] = [
                            'id' => 'tmdb_movie_' . $item['id'],
                            'titulo' => $item['title'],           
                            'imagen' => "https://image.tmdb.org/t/p/w300" . $item['poster_path'],
                            'imagen_bg' => "https://image.tmdb.org/t/p/w780" . $bg,
                            'descripcion' => $item['overview'],
                            'anio' => substr($item['release_date'] ?? '', 0, 4),
                            'edad' => $edadCalculada,
                            'link_ver' => base_url('ver/tmdb_movie_' . $item['id']),
                            'link_detalle' => base_url('detalle/tmdb_movie_' . $item['id'])
                        ];
                    }
                }
            }
        }
        return $finalResults;
    }

    // =================================================================
    // CARGAR MÁS PELIS EN HORIZONTAL
    // =================================================================
    public function ajaxExpandirFila()
    {
        $paramsEncoded = $this->request->getPost('params');
        $page = intval($this->request->getPost('page'));
        $tipo = $this->request->getPost('tipo');
        $userId = session()->get('user_id');
        $planId = session()->get('plan_id');
        $esKids = ($planId == 3);

        if (!$paramsEncoded)
            return "";

        $params = json_decode(base64_decode($paramsEncoded), true);
        $nextPage = $page + 1;
        $params['page'] = $nextPage;

        $items = [];
        if ($tipo === 'local') {
            return "";
        } else {
            $items = $this->fetchTmdbDiscover($params, $esKids);
        }

        if (empty($items))
            return "";

        $html = "";
        foreach ($items as $peli) {
            $titulo = esc($peli['titulo']);
            $img = $peli['imagen'];
            $bg = $peli['imagen_bg'];
            $desc = esc($peli['descripcion'] ?? '');
            $linkD = $peli['link_detalle'];
            $linkV = $peli['link_ver'];
            $match = rand(85, 99);
            $edadBadge = $esKids ? 'TP' : '12+';

            $db = \Config\Database::connect();
            $enLista = $db->table('mi_lista')->where('usuario_id', $userId)->where('contenido_id', $peli['id'])->countAllResults() > 0;
            $styleBtnLista = $enLista ? 'border-color: var(--accent); color: var(--accent);' : '';
            $iconClass = $enLista ? 'fa-check' : 'fa-heart';

            $html .= '<div class="slick-slide-item" style="padding: 0 5px;">';
            $html .= '  <div class="movie-card">';
            $html .= '    <div class="poster-visible"><img src="' . $img . '" alt="' . $titulo . '"></div>';
            $html .= '    <div class="hover-details-card">';
            $html .= '      <div class="hover-backdrop" style="background-image: url(\'' . $bg . '\'); cursor: pointer;" onclick="window.location.href=\'' . $linkD . '\'"></div>';
            $html .= '      <div class="hover-info">';
            $html .= '        <div class="hover-buttons">';
            $html .= '          <button class="btn-mini-play" onclick="playCinematic(\'' . $linkV . '\')"><i class="fa fa-play"></i></button>';
            $html .= '          <button class="btn-mini-icon btn-lista-' . $peli['id'] . '" onclick="toggleMiLista(\'' . $peli['id'] . '\')" style="' . $styleBtnLista . '"><i class="fa ' . $iconClass . '"></i></button>';
            $html .= '        </div>';
            $html .= '        <h4 onclick="window.location.href=\'' . $linkD . '\'">' . $titulo . '</h4>';
            $html .= '        <div class="hover-meta"><span style="color:#46d369; font-weight:bold;">' . $match . '%</span> <span class="badge badge-hd">' . $edadBadge . '</span></div>';
            $html .= '        <p style="font-size:0.75rem; color:#ccc; margin:0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">' . $desc . '</p>';
            $html .= '      </div>';
            $html .= '    </div>';
            $html .= '  </div>';
            $html .= '</div>';
        }

        return $this->response->setBody($html);
    }
}