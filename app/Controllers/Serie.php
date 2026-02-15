<?php

namespace App\Controllers;

use App\Models\ContenidoModel;
use App\Models\UsuarioModel;
use App\Models\GeneroModel;

class Serie extends BaseController
{
    private $tmdbKey = '6387e3c183c454304108333c56530988';

    public function index()
    {
        // 1. CHEQUEO DE SESIÓN
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $planId = session()->get('plan_id');
        $esFree = ($planId == 1);
        $esKids = ($planId == 3);

        $destacada = null;

        if ($esFree) {
            $model = new ContenidoModel();
            $r = $model->where('tipo_id', 2)
                ->where('nivel_acceso', 1) 
                ->orderBy('RAND()')  
                ->first();  

            if ($r) {
                // Arreglar rutas de imagen
                $bg = str_starts_with($r['imagen_bg'], 'http') ? $r['imagen_bg'] : base_url('assets/img/' . $r['imagen_bg']);
                if (empty($r['imagen_bg'])) {
                    $bg = str_starts_with($r['imagen'], 'http') ? $r['imagen'] : base_url('assets/img/' . $r['imagen']);
                }

                $destacada = [
                    'id' => $r['id'],
                    'titulo' => $r['titulo'],
                    'descripcion' => $r['descripcion'],
                    'backdrop' => $bg,
                    'link_ver' => base_url('ver/' . $r['id']),
                    'link_detalle' => base_url('detalle/' . $r['id'])
                ];
            }
        }

        else {
            $params = ['sort_by' => 'popularity.desc', 'page' => 1];

            $resultados = $this->fetchTmdbDiscover($params, $esKids);

            if (!empty($resultados)) {
                shuffle($resultados);
                $s = $resultados[0];

                $destacada = [
                    'id' => $s['id'],
                    'titulo' => $s['titulo'],
                    'descripcion' => $s['descripcion'],
                    'backdrop' => $s['imagen_bg'],
                    'link_ver' => $s['link_ver'],
                    'link_detalle' => $s['link_detalle']
                ];
            }
        }

        $data = [
            'titulo' => 'Series - La Butaca',
            'destacada' => $destacada,
            'mostrarHero' => true,
            'splash' => false,
            'categoria' => 'Series',
            'generos' => (new GeneroModel())->findAll(),
            'otrosPerfiles' => (new UsuarioModel())->where('id !=', $userId)->where('id >=', 2)->where('id <=', 4)->findAll()
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/series', $data);
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
        $encontrado = false;
        do {
            $bloqueActual = $bloqueSolicitado + $intentos;
            $items = [];
            if ($esFree) {
                // MAPA PLAN FREE (SOLO LOCAL)
                // 1=Acción, 3=Sci-Fi, 4=Drama, 5=Animación, 6=Crimen, 7=Comedia, 10=Fantasía

                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Tendencias (Gratis)'],
                    1 => ['tipo' => 'local', 'titulo' => 'Acción Local', 'params' => ['with_genres' => 1]],
                    2 => ['tipo' => 'local', 'titulo' => 'Comedias de la Casa', 'params' => ['with_genres' => 7]],
                    3 => ['tipo' => 'local', 'titulo' => 'Dramas Intensos', 'params' => ['with_genres' => 4]],
                    4 => ['tipo' => 'local', 'titulo' => 'Ciencia Ficción', 'params' => ['with_genres' => 3]],
                    5 => ['tipo' => 'local', 'titulo' => 'Infantil / Animación', 'params' => ['with_genres' => 5]],
                    6 => ['tipo' => 'local', 'titulo' => 'Crimen y Misterio', 'params' => ['with_genres' => 6]],
                    7 => ['tipo' => 'local', 'titulo' => 'Mundos de Fantasía', 'params' => ['with_genres' => 10]],
                    8 => ['tipo' => 'local', 'titulo' => 'Aventuras', 'params' => ['with_genres' => 2]],
                ];
                // ---------------------------------------------------------
                // 1. EL MAPA DE CATEGORÍAS
                // ---------------------------------------------------------
            } elseif ($esKids) {
                // --- MAPA INFANTIL ---
                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Tus Dibujos Favoritos'],
                    1 => ['tipo' => 'tmdb', 'titulo' => 'Superéxitos Infantiles', 'params' => ['sort_by' => 'popularity.desc', 'with_genres' => '10762']],
                    2 => ['tipo' => 'tmdb', 'titulo' => 'Mundo Nickelodeon', 'params' => ['with_companies' => '13', 'sort_by' => 'popularity.desc']],
                    3 => ['tipo' => 'tmdb', 'titulo' => 'Cartoon Network', 'params' => ['with_companies' => '56', 'sort_by' => 'popularity.desc']],
                    4 => ['tipo' => 'tmdb', 'titulo' => 'Disney Channel Dibujos', 'params' => ['with_companies' => '54|2', 'sort_by' => 'popularity.desc']],
                    5 => ['tipo' => 'tmdb', 'titulo' => 'Para los más peques (0-4 años)', 'params' => ['with_genres' => '10762', 'with_keywords' => '270362']],
                    6 => ['tipo' => 'tmdb', 'titulo' => 'Aventuras (5-8 años)', 'params' => ['with_genres' => '10762,10759', 'sort_by' => 'popularity.desc']],
                    8 => ['tipo' => 'tmdb', 'titulo' => 'Star Wars: Animación', 'params' => ['with_keywords' => '335061', 'sort_by' => 'popularity.desc']],
                    10 => ['tipo' => 'tmdb', 'titulo' => 'Anime para Niños', 'params' => ['with_original_language' => 'ja', 'with_genres' => '10762', 'sort_by' => 'popularity.desc']],
                    11 => ['tipo' => 'tmdb', 'titulo' => 'Animales Divertidos', 'params' => ['with_genres' => '16,10751', 'with_keywords' => '12423']],
                    12 => ['tipo' => 'tmdb', 'titulo' => 'Películas de Dibujos', 'params' => ['with_genres' => '16', 'sort_by' => 'revenue.desc']],
                    13 => ['tipo' => 'tmdb', 'titulo' => 'Estilo Boing & Clan', 'params' => ['with_genres' => '10762,35', 'sort_by' => 'popularity.desc']],
                ];
            } else {
                // --- MAPA ADULTOS ---
                $mapa = [
                    0 => ['tipo' => 'local', 'titulo' => 'Tendencias en La Butaca'],
                    1 => ['tipo' => 'tmdb', 'titulo' => 'Top 10 Series Mundiales', 'params' => ['sort_by' => 'popularity.desc']],
                    2 => ['tipo' => 'tmdb', 'titulo' => 'Universo Marvel Completo', 'params' => ['with_companies' => '420|11106|7505|19551', 'sort_by' => 'popularity.desc']],
                    3 => ['tipo' => 'tmdb', 'titulo' => 'Universo DC', 'params' => ['with_companies' => '128064|429', 'sort_by' => 'popularity.desc']],

                    4 => ['tipo' => 'tmdb', 'titulo' => 'Universo Star Wars', 'params' => ['with_keywords' => '335061', 'sort_by' => 'popularity.desc']],
                    5 => ['tipo' => 'tmdb', 'titulo' => 'Mundo Animado', 'params' => ['with_genres' => '16', 'sort_by' => 'popularity.desc']],
                    6 => ['tipo' => 'tmdb', 'titulo' => 'Acción y Adrenalina', 'params' => ['with_genres' => '10759']],
                    7 => ['tipo' => 'tmdb', 'titulo' => 'Comedias', 'params' => ['with_genres' => '35']],
                    8 => ['tipo' => 'tmdb', 'titulo' => 'Sci-Fi y Espacio', 'params' => ['with_genres' => '10765']],
                    9 => ['tipo' => 'tmdb', 'titulo' => 'Anime Japonés', 'params' => ['with_genres' => '16', 'with_original_language' => 'ja']],
                    10 => ['tipo' => 'tmdb', 'titulo' => 'K-Dramas (Corea)', 'params' => ['with_original_language' => 'ko', 'sort_by' => 'popularity.desc']],
                    11 => ['tipo' => 'tmdb', 'titulo' => 'Series de HBO', 'params' => ['with_networks' => '49', 'sort_by' => 'vote_average.desc']],
                    12 => ['tipo' => 'tmdb', 'titulo' => 'Crimen y Misterio', 'params' => ['with_genres' => '80,9648']],
                    13 => ['tipo' => 'tmdb', 'titulo' => 'Para ver en Familia', 'params' => ['with_genres' => '10751']],
                    14 => ['tipo' => 'tmdb', 'titulo' => 'Dramas Aclamados', 'params' => ['with_genres' => '18', 'vote_average.gte' => 8]],
                    15 => ['tipo' => 'tmdb', 'titulo' => 'Zona Kids', 'params' => ['with_genres' => '10762']],
                    16 => ['tipo' => 'tmdb', 'titulo' => 'Documentales', 'params' => ['with_genres' => '99']],
                    17 => ['tipo' => 'tmdb', 'titulo' => 'Reality TV', 'params' => ['with_genres' => '10764']],
                    18 => ['tipo' => 'tmdb', 'titulo' => 'Fantasía Épica', 'params' => ['with_genres' => '10765', 'with_keywords' => '9951']],
                    19 => ['tipo' => 'tmdb', 'titulo' => 'Clásicos de los 90', 'params' => ['first_air_date.gte' => '1990-01-01', 'first_air_date.lte' => '1999-12-31']],
                    20 => ['tipo' => 'tmdb', 'titulo' => 'Westerns', 'params' => ['with_genres' => '37']],
                    21 => ['tipo' => 'tmdb', 'titulo' => 'Miniseries', 'params' => ['with_keywords' => '256402']],
                    22 => ['tipo' => 'tmdb', 'titulo' => 'Originales de Netflix', 'params' => ['with_networks' => '213', 'sort_by' => 'popularity.desc']],


                ];
            }

            // GENERADOR INFINITO (Si se acaba el mapa manual)
            if (!isset($mapa[$bloqueActual])) {

                if ($esFree) {
                    break;
                }

                if ($esKids) {
                    $pool = [
                        ['id' => 10762, 'name' => 'Más Dibujos Kids'],
                        ['id' => 16, 'name' => 'Mundo Animado'],
                        ['id' => '16,35', 'name' => 'Risas Animadas'],
                        ['id' => '16,10759', 'name' => 'Acción Animada']
                    ];
                } else {
                    $pool = [
                        ['id' => 10759, 'name' => 'Acción'],
                        ['id' => 35, 'name' => 'Comedia'],
                        ['id' => 18, 'name' => 'Drama'],
                        ['id' => 10765, 'name' => 'Sci-Fi'],
                        ['id' => 9648, 'name' => 'Misterio'],
                        ['id' => 80, 'name' => 'Crimen'],
                        ['id' => 99, 'name' => 'Documentales'],
                        ['id' => 37, 'name' => 'Western'],
                        ['id' => 10768, 'name' => 'Guerra'],
                        ['id' => 10764, 'name' => 'Reality'],
                        ['id' => 10766, 'name' => 'Telenovelas'],
                        ['id' => 16, 'name' => 'Animación']
                    ];
                }

                // Cálculo de rotación infinita
                $idx = $bloqueActual % count($pool);
                $genre = $pool[$idx];
                $page = floor(($bloqueActual - 21) / count($pool)) + 2;

                $mapa[$bloqueActual] = [
                    'tipo' => 'tmdb',
                    'titulo' => 'Descubre: ' . $genre['name'],
                    'params' => ['with_genres' => $genre['id'], 'page' => $page, 'sort_by' => 'popularity.desc']
                ];
            }

            // RESTRICCIONES & CONFIGURACIÓN
            // if ($esFree && $bloqueActual > 2)
            //     break;

            $config = $mapa[$bloqueActual];
            $saltarBloque = false;

            // Filtros de seguridad extra (por si acaso)
            if ($esKids && isset($config['params']['with_genres'])) {
                $g = (string) $config['params']['with_genres'];
                if (strpos($g, '80') !== false || strpos($g, '18') !== false)
                    $saltarBloque = true;
            }

            // 4. OBTENCIÓN DE DATOS
            if (!$saltarBloque) {
                if ($config['tipo'] === 'local') {
                    $items = $this->obtenerLocal($esKids, $esFree, $config['params'] ?? []);
                } else {
                    $items = $this->fetchTmdbDiscover($config['params'], $esKids);
                }
            }

            // 5. GENERACIÓN HTML
            if (!empty($items)) {
                $encontrado = true;

                if ($intentos > 0) {
                    $html .= '<div class="sync-data" data-jump="' . $intentos . '" style="display:none;"></div>';
                }

                $paramsEncoded = base64_encode(json_encode($config['params'] ?? []));
                $startPage = $config['params']['page'] ?? 1;
                $currentPage = $startPage + 1;
                $endpointType = ($config['tipo'] === 'local') ? 'local' : 'tmdb';

                $html .= '<div class="category-row mb-5" style="padding: 0 4%; opacity:0; transition: opacity 1s;" onload="this.style.opacity=1">';
                $html .= '  <h3 class="row-title text-white fw-bold mb-3" style="font-family: Outfit; font-size: 1.4rem;">' . esc($config['titulo']) . '</h3>';

                $html .= '  <div class="slick-carousel-ajax" data-params="' . $paramsEncoded . '" data-page="' . $currentPage . '" data-endpoint="' . $endpointType . '">';

                foreach ($items as $serie) {
                    $titulo = esc($serie['titulo']);
                    $img = $serie['imagen'];
                    $bg = $serie['imagen_bg'];
                    $desc = esc($serie['descripcion'] ?? '');
                    $linkD = $serie['link_detalle'];
                    $linkV = $serie['link_ver'];
                    $match = rand(85, 99);

                    $edadRaw = $serie['edad'] ?? '12';
                    if ($esKids || $edadRaw === 'TP') {
                        $edadBadge = 'TP';
                    } else {
                        $edadBadge = '+' . $edadRaw;
                    }

                    $enLista = $serie['en_mi_lista'] ?? false;
                    $styleBtnLista = $enLista ? 'border-color: var(--accent); color: var(--accent);' : '';
                    $iconClass = $enLista ? 'fa-check' : 'fa-heart';

                    $html .= '<div class="slick-slide-item" style="padding: 0 5px;">';
                    $html .= '  <div class="movie-card">';
                    $html .= '  <div class="poster-visible">';
                    $html .= '      <img src="' . $img . '" ';
                    $html .= '           loading="lazy" ';
                    $html .= '           decoding="async" ';
                    $html .= '           width="200" height="300" ';
                    $html .= '           alt="' . $titulo . '" ';
                    $html .= '           style="content-visibility: auto;" ';
                    $html .= '           onload="this.classList.add(\'loaded\')">';
                    $html .= '  </div>';
                    $html .= '    <div class="hover-details-card">';
                    $html .= '      <div class="hover-backdrop" style="background-image: url(\'' . $bg . '\'); cursor: pointer;" onclick="window.location.href=\'' . $linkD . '\'"></div>';
                    $html .= '      <div class="hover-info">';
                    $html .= '        <div class="hover-buttons">';
                    $html .= '          <button class="btn-mini-play" onclick="playCinematic(\'' . $linkV . '\')"><i class="fa fa-play"></i></button>';
                    $html .= '          <button class="btn-mini-icon btn-lista-' . $serie['id'] . '" onclick="toggleMiLista(\'' . $serie['id'] . '\')" style="' . $styleBtnLista . '"><i class="fa ' . $iconClass . '"></i></button>';
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
                $html .= '  </div>';
                $html .= '</div>';

                break;
            }

            $intentos++;

        } while ($intentos < $maxIntentos);

        return $this->response->setBody($html);
    }



    // --- MAPAS Y HELPERS ---
    private function obtenerMapa($esKids)
    {
        if ($esKids) {
            return [
                0 => ['tipo' => 'local', 'titulo' => 'Tus Dibujos Favoritos'],
                1 => ['tipo' => 'tmdb', 'titulo' => 'Top Dibujos', 'params' => ['with_genres' => '16', 'sort_by' => 'popularity.desc']],
                2 => ['tipo' => 'tmdb', 'titulo' => 'Disney Series', 'params' => ['with_companies' => '2', 'with_genres' => '16|10751']],
                3 => ['tipo' => 'tmdb', 'titulo' => 'Pixar & Co', 'params' => ['with_companies' => '3', 'with_genres' => '16']],
                4 => ['tipo' => 'tmdb', 'titulo' => 'Aventuras en Familia', 'params' => ['with_genres' => '10751', 'sort_by' => 'popularity.desc']],
            ];
        }

        return [
            0 => ['tipo' => 'local', 'titulo' => 'Tendencias en La Butaca'],
            1 => ['tipo' => 'tmdb', 'titulo' => 'Top 10 Series Mundiales', 'params' => ['sort_by' => 'popularity.desc']],

            // LOS 3 GRANDES UNIVERSOS
            2 => ['tipo' => 'tmdb', 'titulo' => 'Universo Marvel Completo', 'params' => ['with_companies' => '420|11106|7505|19551', 'sort_by' => 'popularity.desc']],
            3 => ['tipo' => 'tmdb', 'titulo' => 'Universo DC', 'params' => ['with_companies' => '128064|429', 'sort_by' => 'popularity.desc']],
            4 => ['tipo' => 'tmdb', 'titulo' => 'Universo Star Wars', 'params' => ['with_keywords' => '335061', 'sort_by' => 'popularity.desc']],

            // GÉNEROS PRINCIPALES
            5 => ['tipo' => 'tmdb', 'titulo' => 'Mundo Animado', 'params' => ['with_genres' => '16', 'sort_by' => 'popularity.desc']],
            6 => ['tipo' => 'tmdb', 'titulo' => 'Acción y Adrenalina', 'params' => ['with_genres' => '10759']],
            7 => ['tipo' => 'tmdb', 'titulo' => 'Comedias', 'params' => ['with_genres' => '35']],
            8 => ['tipo' => 'tmdb', 'titulo' => 'Sci-Fi y Espacio', 'params' => ['with_genres' => '10765']],

            // INTERNACIONAL
            9 => ['tipo' => 'tmdb', 'titulo' => 'Anime Japonés', 'params' => ['with_genres' => '16', 'with_original_language' => 'ja']],
            10 => ['tipo' => 'tmdb', 'titulo' => 'K-Dramas (Corea)', 'params' => ['with_original_language' => 'ko', 'sort_by' => 'popularity.desc']],

            // PLATAFORMAS Y ESTILOS
            11 => ['tipo' => 'tmdb', 'titulo' => 'Series de HBO', 'params' => ['with_networks' => '49', 'sort_by' => 'vote_average.desc']],
            12 => ['tipo' => 'tmdb', 'titulo' => 'Crimen y Misterio', 'params' => ['with_genres' => '80,9648']],
            13 => ['tipo' => 'tmdb', 'titulo' => 'Para ver en Familia', 'params' => ['with_genres' => '10751']],
            14 => ['tipo' => 'tmdb', 'titulo' => 'Dramas Aclamados', 'params' => ['with_genres' => '18', 'vote_average.gte' => 8]],
            15 => ['tipo' => 'tmdb', 'titulo' => 'Zona Kids (Nostalgia)', 'params' => ['with_genres' => '10762']],
            16 => ['tipo' => 'tmdb', 'titulo' => 'Documentales', 'params' => ['with_genres' => '99']],
            17 => ['tipo' => 'tmdb', 'titulo' => 'Reality TV', 'params' => ['with_genres' => '10764']],
            18 => ['tipo' => 'tmdb', 'titulo' => 'Fantasía Épica', 'params' => ['with_genres' => '10765', 'with_keywords' => '9951']],
            19 => ['tipo' => 'tmdb', 'titulo' => 'Clásicos de los 90', 'params' => ['first_air_date.gte' => '1990-01-01', 'first_air_date.lte' => '1999-12-31']],
            20 => ['tipo' => 'tmdb', 'titulo' => 'Westerns', 'params' => ['with_genres' => '37']],
            21 => ['tipo' => 'tmdb', 'titulo' => 'Miniseries', 'params' => ['with_keywords' => '256402']],
            22 => ['tipo' => 'tmdb', 'titulo' => 'Originales de Netflix', 'params' => ['with_networks' => '213', 'sort_by' => 'popularity.desc']],
        ];

    }
    private function obtenerLocal($esKids, $esFree, $params = [])
    {
        $model = new ContenidoModel();
        $q = $model->select('contenidos.*');
        $q->where('contenidos.tipo_id', 2);
        if ($esFree) {
            $q->where('contenidos.nivel_acceso', 1);
        }
        // Filtro de edad para Kids
        if ($esKids) {
            $q->where('contenidos.edad_recomendada <=', 11);
        }
        if (isset($params['with_genres'])) {
            // Hacemos JOIN con la tabla intermedia 'contenido_genero'
            $q->join('contenido_genero', 'contenido_genero.contenido_id = contenidos.id');
            // Filtramos por el ID del género en la tabla intermedia
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
    private function fetchTmdbDiscover($params = [], $esKids = false)
    {
        $baseParams = array_merge([
            'api_key' => $this->tmdbKey,
            'language' => 'es-ES',
            'include_adult' => 'false',
            'page' => 1
        ], $params);

        if ($esKids) {
            if (isset($baseParams['with_genres'])) {
                $baseParams['with_genres'] .= ',16';
            } else {
                $baseParams['with_genres'] = '16';
            }
            $generosProhibidos = '80,18,10768,37,99,10763,10764,10767';
            if (isset($baseParams['without_genres'])) {
                $baseParams['without_genres'] .= ',' . $generosProhibidos;
            } else {
                $baseParams['without_genres'] = $generosProhibidos;
            }
            $keywordsProhibidas = '207318,3945,9714,2620';
            if (isset($baseParams['without_keywords'])) {
                $baseParams['without_keywords'] .= ',' . $keywordsProhibidas;
            } else {
                $baseParams['without_keywords'] = $keywordsProhibidas;
            }
        }

        $startPage = $baseParams['page'];
        $finalResults = [];

        for ($i = 0; $i < 2; $i++) {
            $baseParams['page'] = $startPage + $i;
            $url = "https://api.themoviedb.org/3/discover/tv?" . http_build_query($baseParams);

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

                            // A. Si tiene género Crimen o Guerra -> FUERA
                            if (in_array(80, $g) || in_array(10768, $g))
                                continue;
                            $esSeguro = in_array(10762, $g) || in_array(10751, $g);
                            if (!$esSeguro)
                                continue;
                        }

                        $bg = !empty($item['backdrop_path']) ? $item['backdrop_path'] : $item['poster_path'];

                        // Calcular edad visual
                        $edadCalculada = '12';
                        $g = $item['genre_ids'] ?? [];
                        if (in_array(10762, $g) || in_array(10751, $g))
                            $edadCalculada = 'TP';

                        $finalResults[] = [
                            'id' => 'tmdb_tv_' . $item['id'],
                            'titulo' => $item['name'],
                            'imagen' => "https://image.tmdb.org/t/p/w300" . $item['poster_path'],
                            'imagen_bg' => "https://image.tmdb.org/t/p/w780" . $bg,
                            'descripcion' => $item['overview'],
                            'anio' => substr($item['first_air_date'] ?? '', 0, 4),
                            'edad' => $edadCalculada,
                            'link_ver' => base_url('ver/tmdb_tv_' . $item['id']),
                            'link_detalle' => base_url('detalle/tmdb_tv_' . $item['id'])
                        ];
                    }
                }
            }
        }
        return $finalResults;
    }
    // CARGAR MÁS SERIES EN HORIZONTAL
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

        // Decodificar qué estábamos buscando en esta fila
        $params = json_decode(base64_decode($paramsEncoded), true);

        // Aumentar página
        $nextPage = $page + 1;
        $params['page'] = $nextPage;

        $items = [];

        // Si es Local y pide más, quizás no haya más, pero dejamos la lógica por si acaso
        if ($tipo === 'local') {
            return "";
        } else {
            $items = $this->fetchTmdbDiscover($params, $esKids);
        }

        if (empty($items))
            return "";
        $html = "";
        foreach ($items as $serie) {
            $titulo = esc($serie['titulo']);
            $img = $serie['imagen'];
            $bg = $serie['imagen_bg'];
            $desc = esc($serie['descripcion'] ?? '');
            $anio = $serie['anio'] ?? '';
            $linkD = $serie['link_detalle'];
            $linkV = $serie['link_ver'];
            $match = rand(85, 99);
            $edadBadge = $esKids ? 'TP' : '12+';

            $db = \Config\Database::connect();
            $enLista = $db->table('mi_lista')->where('usuario_id', $userId)->where('contenido_id', $serie['id'])->countAllResults() > 0;
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
            $html .= '          <button class="btn-mini-icon btn-lista-' . $serie['id'] . '" onclick="toggleMiLista(\'' . $serie['id'] . '\')" style="' . $styleBtnLista . '"><i class="fa ' . $iconClass . '"></i></button>';
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