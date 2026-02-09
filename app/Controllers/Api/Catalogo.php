<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ContenidoModel;
use App\Models\GeneroModel;
use App\Models\MiListaModel;

class Catalogo extends ResourceController
{
    protected $format = 'json'; // Siempre devuelve JSON
    private $tmdbApiKey = '6387e3c183c454304108333c56530988';

    // =================================================================
    // 1. HOME PRINCIPAL (Datos para la portada híbrida/API)
    // =================================================================
    public function getHome()
    {
        $planId = session()->get('plan_id') ?? 1; // 1 = Free, 2 = Premium, 3 = Kids
        $userId = session()->get('user_id'); 
        $esKids = ($planId == 3);

        $model = new ContenidoModel();

        // A. CARRUSEL
        $builder = $model->orderBy('id', 'DESC'); 
        
        // --- FILTROS DE SEGURIDAD ---
        if ($esKids) $builder->where('edad_recomendada <=', 11);
        
        // Si es FREE, solo mostramos contenido nivel 1
        if ($planId == 1) $builder->where('nivel_acceso', 1);
        // ----------------------------

        $carrusel = $builder->limit(5)->find();
        $this->procesarImagenes($carrusel);
        $carrusel = $this->marcarEnMiLista($carrusel, $userId); 

        // B. SECCIONES
        $secciones = [];

        // Fila 1: Tendencias
        // NOTA: getTendencias suele ser una query personalizada en el Modelo.
        // Si tu modelo no filtra por plan dentro de esa función, las Premium podrían colarse.
        // Asumo que getTendencias($limit, $planId) ya recibe el planId y filtra dentro.
        $tendencias = $model->getTendencias(10, $planId);
        $this->procesarImagenes($tendencias);
        $tendencias = $this->marcarEnMiLista($tendencias, $userId);
        
        $secciones[] = [
            'titulo' => $esKids ? 'Favoritos Kids 🎈' : 'Tendencias 🔥',
            'data'   => $tendencias
        ];

        // Fila 2: Recomendados por Género
        $generoFav = $userId ? $model->getGeneroFavoritoUsuario($userId, 1) : null;
        if ($generoFav) {
             // Pasamos planId para que el modelo filtre
             $rec = $model->getPorGenero($generoFav['id'], 1, 10, [], $planId);
             $titulo = 'Porque viste ' . $generoFav['nombre'];
        } else {
             $rec = $model->getPorGenero(1, 1, 10, [], $planId); // 1 = Acción
             $titulo = 'Acción y Aventura';
        }
        $this->procesarImagenes($rec);
        $rec = $this->marcarEnMiLista($rec, $userId);
        
        if (!empty($rec)) {
            $secciones[] = ['titulo' => $titulo, 'data' => $rec];
        }

        return $this->respond([
            'carrusel'  => $carrusel,
            'secciones' => $secciones
        ]);
    }

    // =================================================================
    // 2. PELÍCULA DESTACADA ALEATORIA
    // =================================================================
    public function getDestacadaRandom()
    {
        $userId = session()->get('user_id');
        $planId = session()->get('plan_id') ?? 1;

        $model = new ContenidoModel();
        
        $builder = $model->where('tipo_id', 1);
        
        // FILTRO DE SEGURIDAD
        if ($planId == 1) {
            $builder->where('nivel_acceso', 1);
        }
        if ($planId == 3) {
            $builder->where('edad_recomendada <=', 11);
        }

        $peli = $builder->orderBy('RAND()')->first();

        if ($peli) {
            if (!str_starts_with($peli['imagen'], 'http')) {
                $peli['imagen'] = base_url('assets/img/' . $peli['imagen']);
            }
            if (!str_starts_with($peli['imagen_bg'], 'http')) {
                $peli['imagen_bg'] = base_url('assets/img/' . $peli['imagen_bg']);
            }

            $tempArr = [$peli];
            $tempArr = $this->marcarEnMiLista($tempArr, $userId);
            $peli = $tempArr[0];
        }

        return $this->respond($peli);
    }

    // =================================================================
    // 3. LISTADO GENERAL / GRID (Index)
    // =================================================================
    public function index()
    {
        $planId = session()->get('plan_id') ?? 1; 
        $userId = session()->get('user_id'); 
        $page   = $this->request->getVar('page') ?? 1;
        $generoId = $this->request->getVar('genero');
        
        $model = new ContenidoModel();
        $generoModel = new GeneroModel();

        // A. SI HAY GÉNERO -> Estructura Landing
        if (!empty($generoId)) {
            $nombreGenero = $generoModel->find($generoId)['nombre'] ?? 'Contenido';

            // 1. Películas (Filtradas por Plan dentro de getPorGenero)
            $pelis = $model->getPorGenero($generoId, 1, 24, [], $planId);
            $this->procesarImagenes($pelis);
            $pelis = $this->marcarEnMiLista($pelis, $userId); 

            // 2. Series (Filtradas por Plan dentro de getPorGenero)
            $series = $model->getPorGenero($generoId, 2, 24, [], $planId);
            $this->procesarImagenes($series);
            $series = $this->marcarEnMiLista($series, $userId); 

            return $this->respond([
                'status' => 'success',
                'modo'   => 'landing_genero',
                'titulo' => $nombreGenero,
                'secciones' => [
                    [
                        'titulo' => 'Películas de ' . $nombreGenero,
                        'tipo'   => 1,
                        'data'   => $pelis,
                        'ver_mas'=> true
                    ],
                    [
                        'titulo' => 'Series de ' . $nombreGenero,
                        'tipo'   => 2,
                        'data'   => $series,
                        'ver_mas'=> true
                    ]
                ]
            ]);
        } 
        
        // B. SI NO HAY GÉNERO -> Paginación Normal
        else {
            $limit  = 12;
            $offset = ($page - 1) * $limit;
            
            // getContenidoPaginadas ya debe recibir planId para filtrar
            $peliculas = $model->getContenidoPaginadas($planId, $limit, $offset);
            $this->procesarImagenes($peliculas);
            $peliculas = $this->marcarEnMiLista($peliculas, $userId); 

            return $this->respond([
                'status' => 'success',
                'modo'   => 'paginacion_normal',
                'data'   => $peliculas
            ]);
        }
    }

    // =================================================================
    // 4. DETALLE DE CONTENIDO
    // =================================================================
    public function show($id = null)
    {
        $planId = session()->get('plan_id') ?? 1;
        $model = new ContenidoModel();
        $data = $model->getDetallesCompletos($id);

        if (!$data) return $this->failNotFound('Contenido no encontrado');

        // SEGURIDAD: Si es usuario Free y el contenido es Premium (2), bloqueamos
        if ($planId == 1 && $data['nivel_acceso'] == 2) {
             return $this->failForbidden('Este contenido es exclusivo para usuarios Premium.');
        }

        if (!str_starts_with($data['imagen'], 'http')) {
            $data['imagen'] = base_url('assets/img/' . $data['imagen']);
            $data['imagen_bg'] = base_url('assets/img/' . $data['imagen_bg']);
        }

        return $this->respond($data);
    }

    // =================================================================
    // 5. BUSCADOR (Autocomplete)
    // =================================================================
    public function autocompletar()
    {
        $search = $this->request->getPost('search');
        $planId = session()->get('plan_id') ?? 1;
        $esKids = ($planId == 3);
        $esFree = ($planId == 1);

        // Añadimos 'debug' para que veas si se está ejecutando este código nuevo
        $response = ['token' => csrf_hash(), 'data' => [], 'debug' => 'VERSIÓN NUEVA ACTIVA']; 

        if ($search) {
            $model = new ContenidoModel();
            
            // 1. LOCAL: Traemos el imdb_id
            $builder = $model->select('id, titulo, imagen, anio, nivel_acceso, edad_recomendada, imdb_id')
                             ->like('titulo', $search);

            if ($esKids) $builder->where('edad_recomendada <=', 11); 
            if ($esFree) $builder->where('nivel_acceso', 1);

            $localResults = $builder->limit(5)->find();

            // LISTAS NEGRAS
            $idsLocales = []; 
            $titulosLocales = [];

            foreach ($localResults as $peli) {
                // Guardamos ID TMDB si existe
                if (!empty($peli['imdb_id'])) {
                    $idsLocales[] = (string)$peli['imdb_id'];
                }
                // Guardamos Título Normalizado
                $titulosLocales[] = $this->normalizarTexto($peli['titulo']);

                $imgUrl = str_starts_with($peli['imagen'], 'http') ? $peli['imagen'] : base_url('assets/img/' . $peli['imagen']);
                
                $response['data'][] = [
                    "value" => $peli['id'],
                    "label" => $peli['titulo'], // Label limpio
                    "img"   => $imgUrl,
                    "type"  => "local"
                ];
            }

            // 2. TMDB
            if (!$esFree) {
                $client = \Config\Services::curlrequest();
                $url = "https://api.themoviedb.org/3/search/multi?api_key={$this->tmdbApiKey}&language=es-ES&query=" . urlencode($search) . "&include_adult=false";
                
                try {
                    $res = $client->request('GET', $url, ['http_errors' => false, 'verify' => false]);
                    $tmdbData = json_decode($res->getBody(), true);

                    if (!empty($tmdbData['results'])) {
                        $count = 0;
                        foreach ($tmdbData['results'] as $item) {
                            if ($count >= 5) break; 
                            if ($item['media_type'] != 'movie' && $item['media_type'] != 'tv') continue;
                            
                            // 🛑 FILTRO ANTI-DUPLICADOS 🛑
                            $tmdbId = (string)$item['id'];
                            $tituloRaw = ($item['media_type'] == 'movie') ? $item['title'] : $item['name'];
                            $tituloNorm = $this->normalizarTexto($tituloRaw);

                            // A. CHEQUEO ID (Si Loki tiene ID 84958 en local, se salta)
                            if (in_array($tmdbId, $idsLocales)) continue;

                            // B. CHEQUEO TÍTULO (Si "loki" == "loki", se salta)
                            if (in_array($tituloNorm, $titulosLocales)) continue;

                            // Preparar Salida
                            $poster = $item['poster_path'] ? "https://image.tmdb.org/t/p/w92" . $item['poster_path'] : base_url('assets/img/no-image.png');
                            
                            // Usamos tu formato de ID para que no rompa el frontend
                            $idValue = ($item['media_type'] == 'tv') ? 'tmdb_tv_' . $item['id'] : 'tmdb_movie_' . $item['id'];
                            
                            // Label con año para diferenciar
                            $fecha = ($item['media_type'] == 'movie') ? ($item['release_date'] ?? '') : ($item['first_air_date'] ?? '');
                            $anio = substr($fecha, 0, 4);
                            $label = $tituloRaw . ($anio ? " ($anio)" : "");

                            $response['data'][] = [
                                "value" => $idValue,
                                "label" => $label,
                                "img"   => $poster,
                                "type"  => "tmdb"
                            ];
                            $count++;
                        }
                    }
                } catch (\Exception $e) {}
            }
        }
        return $this->respond($response);
    }

    private function normalizarTexto($str) {
        $str = mb_strtolower(trim($str));
        return preg_replace('/[^a-z0-9]/', '', $str);
    }

    // =================================================================
    // 6. LANDING PELÍCULAS (CARRUSEL + FILAS)
    // =================================================================
    public function getPeliculasLanding()
    {
        $planId = session()->get('plan_id') ?? 1;
        $userId = session()->get('user_id'); 

        $model = new ContenidoModel();
        
        // 1. CARRUSEL
        $builder = $model->where('tipo_id', 1)->orderBy('RAND()');
        
        // FILTRO SEGURIDAD
        if ($planId == 1) $builder->where('nivel_acceso', 1);
        if ($planId == 3) $builder->where('edad_recomendada <=', 11);
        
        $carrusel = $builder->limit(5)->find();
        $this->procesarImagenes($carrusel);
        $carrusel = $this->marcarEnMiLista($carrusel, $userId); 

        // 2. SECCIONES
        $secciones = [];

        // Fila 1: Acción
        $accion = $model->getPorGenero(1, 1, 12, [], $planId); 
        $this->procesarImagenes($accion);
        $accion = $this->marcarEnMiLista($accion, $userId); 
        if($accion) $secciones[] = ['titulo' => 'Pura Adrenalina 💥', 'data' => $accion];

        // Fila 2: Comedia
        $comedia = $model->getPorGenero(2, 1, 12, [], $planId);
        $this->procesarImagenes($comedia);
        $comedia = $this->marcarEnMiLista($comedia, $userId); 
        if($comedia) $secciones[] = ['titulo' => 'Risas aseguradas 😂', 'data' => $comedia];

        // Fila 3: Terror
        $terror = $model->getPorGenero(4, 1, 12, [], $planId);
        $this->procesarImagenes($terror);
        $terror = $this->marcarEnMiLista($terror, $userId); 
        if($terror) $secciones[] = ['titulo' => 'No apagues la luz 🕯️', 'data' => $terror];

        // Fila 4: Drama
        $drama = $model->getPorGenero(3, 1, 12, [], $planId);
        $this->procesarImagenes($drama);
        $drama = $this->marcarEnMiLista($drama, $userId); 
        if($drama) $secciones[] = ['titulo' => 'Historias profundas 🎭', 'data' => $drama];

        return $this->respond([
            'carrusel' => $carrusel,
            'secciones' => $secciones
        ]);
    }

    public function tendencias() {
        if (!session()->get('is_logged_in')) return $this->failUnauthorized();
        return $this->getHome(); 
    }

    // =================================================================
    // FUNCIONES AUXILIARES PRIVADAS
    // =================================================================
    
    private function procesarImagenes(&$lista) {
        if (!$lista) return;
        foreach ($lista as &$item) {
            if (isset($item['imagen']) && !str_starts_with($item['imagen'], 'http')) {
                $item['imagen'] = base_url('assets/img/' . $item['imagen']);
            }
            if (isset($item['imagen_bg']) && !str_starts_with($item['imagen_bg'], 'http')) {
                $item['imagen_bg'] = base_url('assets/img/' . $item['imagen_bg']);
            }
        }
    }

    private function marcarEnMiLista($contenidos, $userId) {
        if (empty($contenidos) || !$userId) return $contenidos;

        $miListaModel = new \App\Models\MiListaModel();
        
        $ids = array_column($contenidos, 'id');
        if (empty($ids)) return $contenidos;

        $favoritos = $miListaModel->where('usuario_id', $userId)
                                  ->whereIn('contenido_id', $ids)
                                  ->findColumn('contenido_id'); 

        if (!$favoritos) $favoritos = [];

        foreach ($contenidos as &$item) {
            $item['en_mi_lista'] = in_array($item['id'], $favoritos);
        }

        return $contenidos;
    }
}