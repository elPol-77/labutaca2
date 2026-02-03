<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ContenidoModel;
use App\Models\GeneroModel;
use App\Models\MiListaModel;

class Catalogo extends ResourceController
{
    protected $format = 'json'; // Siempre devuelve JSON

    // =================================================================
    // 1. HOME PRINCIPAL (Datos para la portada híbrida/API)
    // =================================================================
    public function getHome()
    {
        $planId = session()->get('plan_id') ?? 1;
        $userId = session()->get('user_id'); // <--- Necesario para favoritos
        $esKids = ($planId == 3);

        $model = new ContenidoModel();

        // A. CARRUSEL
        $builder = $model->orderBy('id', 'DESC'); 
        if ($esKids) $builder->where('edad_recomendada <=', 11);
        if ($planId == 1) $builder->where('nivel_acceso', 1);

        $carrusel = $builder->limit(5)->find();
        $this->procesarImagenes($carrusel);
        $carrusel = $this->marcarEnMiLista($carrusel, $userId); // <--- MARCAR

        // B. SECCIONES
        $secciones = [];

        // Fila 1: Tendencias
        $tendencias = $model->getTendencias(10, $planId);
        $this->procesarImagenes($tendencias);
        $tendencias = $this->marcarEnMiLista($tendencias, $userId); // <--- MARCAR
        
        $secciones[] = [
            'titulo' => $esKids ? 'Favoritos Kids 🎈' : 'Tendencias 🔥',
            'data'   => $tendencias
        ];

        // Fila 2: Relleno
        $generoFav = $userId ? $model->getGeneroFavoritoUsuario($userId, 1) : null;
        if ($generoFav) {
             $rec = $model->getPorGenero($generoFav['id'], 1, 10, [], $planId);
             $titulo = 'Porque viste ' . $generoFav['nombre'];
        } else {
             $rec = $model->getPorGenero(1, 1, 10, [], $planId);
             $titulo = 'Acción y Aventura';
        }
        $this->procesarImagenes($rec);
        $rec = $this->marcarEnMiLista($rec, $userId); // <--- MARCAR
        
        if (!empty($rec)) {
            $secciones[] = ['titulo' => $titulo, 'data' => $rec];
        }

        // RESPUESTA
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
        $model = new ContenidoModel();
        
        $peli = $model->where('tipo_id', 1) 
                      ->orderBy('RAND()') 
                      ->first();

        if ($peli) {
            if (!str_starts_with($peli['imagen'], 'http')) {
                $peli['imagen'] = base_url('assets/img/' . $peli['imagen']);
            }
            if (!str_starts_with($peli['imagen_bg'], 'http')) {
                $peli['imagen_bg'] = base_url('assets/img/' . $peli['imagen_bg']);
            }

            // Marcar si es favorita (Truco: convertimos a array de 1 elemento y extraemos)
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
        $userId = session()->get('user_id'); // <--- IMPORTANTE
        $page   = $this->request->getVar('page') ?? 1;
        $generoId = $this->request->getVar('genero');
        
        $model = new ContenidoModel();
        $generoModel = new GeneroModel();

        // A. SI HAY GÉNERO -> Devolvemos estructura de "Landing de Género"
        if (!empty($generoId)) {
            $nombreGenero = $generoModel->find($generoId)['nombre'] ?? 'Contenido';

            // 1. Películas
            $pelis = $model->getPorGenero($generoId, 1, 24, [], $planId);
            $this->procesarImagenes($pelis);
            $pelis = $this->marcarEnMiLista($pelis, $userId); // <--- MARCAR

            // 2. Series
            $series = $model->getPorGenero($generoId, 2, 24, [], $planId);
            $this->procesarImagenes($series);
            $series = $this->marcarEnMiLista($series, $userId); // <--- MARCAR

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
        
        // B. SI NO HAY GÉNERO -> Lógica normal de portada (Paginada)
        else {
            $limit  = 12;
            $offset = ($page - 1) * $limit;
            
            $peliculas = $model->getContenidoPaginadas($planId, $limit, $offset);
            $this->procesarImagenes($peliculas);
            $peliculas = $this->marcarEnMiLista($peliculas, $userId); // <--- MARCAR

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
        $model = new ContenidoModel();
        $data = $model->getDetallesCompletos($id);

        if (!$data) return $this->failNotFound('Contenido no encontrado');

        if (!str_starts_with($data['imagen'], 'http')) {
            $data['imagen'] = base_url('assets/img/' . $data['imagen']);
            $data['imagen_bg'] = base_url('assets/img/' . $data['imagen_bg']);
        }

        // Aquí no solemos marcar lista porque 'show' se usa para datos crudos, 
        // pero si lo usas en el player, podrías añadirlo.

        return $this->respond($data);
    }

    // =================================================================
    // 5. BUSCADOR (Autocomplete)
    // =================================================================
    public function autocompletar()
    {
        $search = $this->request->getPost('search');
        $response = ['token' => csrf_hash(), 'data' => []];

        if ($search) {
            $planId = session()->get('plan_id') ?? 1; 
            $model = new ContenidoModel();
            
            $builder = $model->select('id, titulo, imagen, anio, nivel_acceso, edad_recomendada')
                             ->like('titulo', $search);

            if ($planId == 3) {
                $builder->where('edad_recomendada <=', 11); 
            } elseif ($planId == 1) {
                $builder->where('nivel_acceso', 1);
            }

            $listaPelis = $builder->limit(5)->find();

            foreach ($listaPelis as $peli) {
                $imgUrl = str_starts_with($peli['imagen'], 'http')
                    ? $peli['imagen']
                    : base_url('assets/img/' . $peli['imagen']);

                $response['data'][] = [
                    "value" => $peli['id'],
                    "label" => $peli['titulo'],
                    "img"   => $imgUrl,
                    "year"  => $peli['anio']
                ];
            }
        }
        return $this->respond($response);
    }

    // =================================================================
    // 6. LANDING PELÍCULAS (CARRUSEL + FILAS)
    // =================================================================
    public function getPeliculasLanding()
    {
        $planId = session()->get('plan_id') ?? 1;
        $userId = session()->get('user_id'); // <--- IMPORTANTE

        $model = new ContenidoModel();
        
        // 1. CARRUSEL
        $carrusel = $model->where('tipo_id', 1)->orderBy('RAND()')->limit(5)->find();
        $this->procesarImagenes($carrusel);
        $carrusel = $this->marcarEnMiLista($carrusel, $userId); // <--- MARCAR

        // 2. SECCIONES
        $secciones = [];

        // Fila 1: Acción
        $accion = $model->getPorGenero(1, 1, 12, [], $planId); 
        $this->procesarImagenes($accion);
        $accion = $this->marcarEnMiLista($accion, $userId); // <--- MARCAR
        if($accion) $secciones[] = ['titulo' => 'Pura Adrenalina 💥', 'data' => $accion];

        // Fila 2: Comedia
        $comedia = $model->getPorGenero(2, 1, 12, [], $planId);
        $this->procesarImagenes($comedia);
        $comedia = $this->marcarEnMiLista($comedia, $userId); // <--- MARCAR
        if($comedia) $secciones[] = ['titulo' => 'Risas aseguradas 😂', 'data' => $comedia];

        // Fila 3: Terror
        $terror = $model->getPorGenero(4, 1, 12, [], $planId);
        $this->procesarImagenes($terror);
        $terror = $this->marcarEnMiLista($terror, $userId); // <--- MARCAR
        if($terror) $secciones[] = ['titulo' => 'No apagues la luz 🕯️', 'data' => $terror];

        // Fila 4: Drama
        $drama = $model->getPorGenero(3, 1, 12, [], $planId);
        $this->procesarImagenes($drama);
        $drama = $this->marcarEnMiLista($drama, $userId); // <--- MARCAR
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
    
    // Procesa imágenes por referencia (añade base_url)
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

    // REVISA LA BASE DE DATOS Y MARCA FAVORITOS
    private function marcarEnMiLista($contenidos, $userId) {
        if (empty($contenidos) || !$userId) return $contenidos;

        // Aseguramos cargar el modelo si no está
        $miListaModel = new \App\Models\MiListaModel();
        
        // Sacamos solo los IDs
        $ids = array_column($contenidos, 'id');

        if (empty($ids)) return $contenidos;

        // Preguntamos a la BD
        $favoritos = $miListaModel->where('usuario_id', $userId)
                                  ->whereIn('contenido_id', $ids)
                                  ->findColumn('contenido_id'); 

        if (!$favoritos) $favoritos = [];

        // Recorremos y marcamos
        foreach ($contenidos as &$item) {
            $item['en_mi_lista'] = in_array($item['id'], $favoritos);
        }

        return $contenidos;
    }
}