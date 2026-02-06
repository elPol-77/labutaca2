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
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $planId = session()->get('plan_id');
        $model = new ContenidoModel();

        // HERO
        $baseQuery = $model->where('tipo_id', 2);
        if ($planId == 3)
            $baseQuery->where('edad_recomendada <=', 11);

        $destacada = (clone $baseQuery)->where('imagen_bg !=', '')->orderBy('id', 'DESC')->first();
        if (!$destacada)
            $destacada = (clone $baseQuery)->orderBy('id', 'DESC')->first();

        if ($destacada) {
            if (empty($destacada['imagen_bg']))
                $destacada['imagen_bg'] = $destacada['imagen'];
            if (!str_starts_with($destacada['imagen_bg'], 'http')) {
                $destacada['imagen_bg'] = base_url('assets/img/' . $destacada['imagen_bg']);
            }
            $db = \Config\Database::connect();
            $enLista = $db->table('mi_lista')->where('usuario_id', $userId)->where('contenido_id', $destacada['id'])->countAllResults() > 0;
            $destacada['en_mi_lista'] = $enLista;
        }

        $data = [
            'titulo' => 'Series - La Butaca',
            'destacada' => $destacada,
            'mostrarHero' => true,
            'splash' => false,
            'categoria' => 'Series',
            'carrusel' => [],
            'generos' => (new GeneroModel())->findAll(),
            'otrosPerfiles' => (new UsuarioModel())->where('id !=', $userId)->where('id >=', 2)->where('id <=', 4)->findAll()
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/series', $data);
        echo view('frontend/templates/footer', $data);
    }

    // =================================================================
    // CARGA AJAX A PRUEBA DE FALLOS (Retry Loop)
    // =================================================================
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
        $encontrado = false;

        // BUCLE DE SEGURIDAD
        do {
            $bloqueActual = $bloqueSolicitado + $intentos;
            $items = [];

            // ---------------------------------------------------------
            // 1. EL MAPA DE CATEGORÍAS
            // ---------------------------------------------------------
            if ($esKids) {
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

            // ---------------------------------------------------------
            // 2. GENERADOR INFINITO (Si se acaba el mapa manual)
            // ---------------------------------------------------------
            if (!isset($mapa[$bloqueActual])) {

                if ($esKids) {
                    $pool = [
                        ['id' => 10762, 'name' => 'Más Dibujos Kids'],
                        ['id' => 16, 'name' => 'Mundo Animado'],
                        ['id' => '16,35', 'name' => 'Risas Animadas'],
                        ['id' => '16,10759', 'name' => 'Acción Animada'] 
                    ];
                } else {
                    $pool = [
                        ['id' => 10759, 'name' => 'Acción'], ['id' => 35, 'name' => 'Comedia'],
                        ['id' => 18, 'name' => 'Drama'], ['id' => 10765, 'name' => 'Sci-Fi'],
                        ['id' => 9648, 'name' => 'Misterio'], ['id' => 80, 'name' => 'Crimen'],
                        ['id' => 99, 'name' => 'Documentales'], ['id' => 37, 'name' => 'Western'],
                        ['id' => 10768, 'name' => 'Guerra'], ['id' => 10764, 'name' => 'Reality'],
                        ['id' => 10766, 'name' => 'Telenovelas'], ['id' => 16, 'name' => 'Animación']
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

            // ---------------------------------------------------------
            // 3. RESTRICCIONES & CONFIGURACIÓN
            // ---------------------------------------------------------
            if ($esFree && $bloqueActual > 2) break;

            $config = $mapa[$bloqueActual];
            $saltarBloque = false;

            // Filtros de seguridad extra (por si acaso)
            if ($esKids && isset($config['params']['with_genres'])) {
                $g = (string) $config['params']['with_genres'];
                if (strpos($g, '80') !== false || strpos($g, '18') !== false)
                    $saltarBloque = true;
            }

            // ---------------------------------------------------------
            // 4. OBTENCIÓN DE DATOS
            // ---------------------------------------------------------
            if (!$saltarBloque) {
                if ($config['tipo'] === 'local') {
                    $items = $this->obtenerLocal($esKids);
                } else {
                    // AQUÍ ESTÁ EL CAMBIO CLAVE: Pasamos $esKids para filtrar
                    $items = $this->fetchTmdbDiscover($config['params'], $esKids);
                }
            }

            // ---------------------------------------------------------
            // 5. GENERACIÓN HTML
            // ---------------------------------------------------------
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
                
                $html .= '  <div class="slick-carousel-ajax" data-params="'.$paramsEncoded.'" data-page="'.$currentPage.'" data-endpoint="'.$endpointType.'">';

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
                    $iconClass = $enLista ? 'fa-check' : 'fa-plus';

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

                break; // FIN DEL BUCLE (Éxito)
            }

            // Si llegamos aquí, falló o estaba vacío -> Probamos siguiente bloque
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

    private function obtenerLocal($esKids)
    {
        $model = new ContenidoModel();
        $q = $model->where('tipo_id', 2);
        if ($esKids)
            $q->where('edad_recomendada <=', 11);

        $local = $q->orderBy('vistas', 'DESC')->findAll(20);
        $items = [];

        foreach ($local as $r) {
            $img = str_starts_with($r['imagen'], 'http') ? $r['imagen'] : base_url('assets/img/' . $r['imagen']);
            $bg = str_starts_with($r['imagen_bg'], 'http') ? $r['imagen_bg'] : base_url('assets/img/' . $r['imagen_bg']);
            if (empty($r['imagen_bg']))
                $bg = $img;

            $db = \Config\Database::connect();
            $enLista = $db->table('mi_lista')->where('usuario_id', session()->get('user_id'))->where('contenido_id', $r['id'])->countAllResults() > 0;

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

    // AÑADIMOS EL PARÁMETRO $esKids = false POR DEFECTO
    // --- FUNCIÓN DE BÚSQUEDA BLINDADA PARA KIDS ---
    private function fetchTmdbDiscover($params = [], $esKids = false)
    {
        $baseParams = array_merge([
            'api_key' => $this->tmdbKey,
            'language' => 'es-ES',
            'include_adult' => 'false',
            'page' => 1
        ], $params);

        if ($esKids) {
            // 1. OBLIGATORIO: Solo Animación (ID 16)
            // Si ya venían géneros, le pegamos el 16. Si no, lo ponemos.
            if (isset($baseParams['with_genres'])) {
                $baseParams['with_genres'] .= ',16'; 
            } else {
                $baseParams['with_genres'] = '16';
            }

            // 2. SEGURIDAD: Excluir géneros adultos
            // 80:Crimen, 18:Drama, 10768:Guerra, 37:Western, 99:Docu, 10764:Reality
            $generosProhibidos = '80,18,10768,37,99,10763,10764,10767';
            if (isset($baseParams['without_genres'])) {
                $baseParams['without_genres'] .= ',' . $generosProhibidos;
            } else {
                $baseParams['without_genres'] = $generosProhibidos;
            }

            // 3. ANTI-SOUTH PARK / PADRE DE FAMILIA
            // Excluimos keywords: adult animation (207318), sitcom (3945), black comedy (9714), adult humor (2620)
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
                        if (empty($item['poster_path'])) continue;
                        
                        // --- FILTRO FINAL DE SEGURIDAD (PHP) ---
                        if ($esKids) {
                            $g = $item['genre_ids'] ?? [];
                            
                            // A. Si tiene género Crimen o Guerra -> FUERA
                            if (in_array(80, $g) || in_array(10768, $g)) continue;

                            // B. REGLA DE ORO: Debe tener "Kids" (10762) O "Family" (10751)
                            // South Park es [16, 35] (Animación, Comedia). Al no tener 10762 ni 10751, lo echamos.
                            $esSeguro = in_array(10762, $g) || in_array(10751, $g);
                            if (!$esSeguro) continue;
                        }

                        $bg = !empty($item['backdrop_path']) ? $item['backdrop_path'] : $item['poster_path'];
                        
                        // Calcular edad visual
                        $edadCalculada = '12';
                        $g = $item['genre_ids'] ?? [];
                        if (in_array(10762, $g) || in_array(10751, $g)) $edadCalculada = 'TP';
                        
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
    // =================================================================
    // CARGAR MÁS SERIES EN HORIZONTAL (INFINITE CAROUSEL)
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

        // Decodificar qué estábamos buscando en esta fila
        $params = json_decode(base64_decode($paramsEncoded), true);

        // Aumentar página
        $nextPage = $page + 1;
        $params['page'] = $nextPage;

        $items = [];

        // Si es Local y pide más, quizás no haya más, pero dejamos la lógica por si acaso
        if ($tipo === 'local') {
            return ""; // Normalmente local cargamos todo de golpe (50), no paginamos por AJAX aquí para no complicar
        } else {
            // TMDB: Pedimos la siguiente página exacta
            $items = $this->fetchTmdbDiscover($params, $esKids);
        }

        if (empty($items))
            return "";

        // Generar SOLO el HTML de las tarjetas nuevas
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
            $edadBadge = $esKids ? 'TP' : '12+'; // Simplificado

            // Check Lista
            $db = \Config\Database::connect();
            $enLista = $db->table('mi_lista')->where('usuario_id', $userId)->where('contenido_id', $serie['id'])->countAllResults() > 0;
            $styleBtnLista = $enLista ? 'border-color: var(--accent); color: var(--accent);' : '';
            $iconClass = $enLista ? 'fa-check' : 'fa-heart';

            // HTML V3 (Exacto al anterior)
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
            $html .= '        <div class="hover-meta"><span style="color:#46d369; font-weight:bold;">'.$match.'%</span> <span class="badge badge-hd">'.$edadBadge.'</span></div>';
            $html .= '        <p style="font-size:0.75rem; color:#ccc; margin:0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">' . $desc . '</p>';
            $html .= '      </div>';
            $html .= '    </div>';
            $html .= '  </div>';
            $html .= '</div>';
        }

        return $this->response->setBody($html);
    }
}