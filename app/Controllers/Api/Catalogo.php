<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ContenidoModel;

class Catalogo extends ResourceController
{
    protected $format = 'json'; // Siempre devuelve JSON

    // =================================================================
    // 1. HOME PRINCIPAL (Datos para la portada híbrida/API)
    // =================================================================
    public function getHome()
    {
        $planId = session()->get('plan_id') ?? 1;
        $userId = session()->get('user_id');
        $esKids = ($planId == 3);

        $model = new ContenidoModel();

        // A. CARRUSEL (5 Últimas añadidas o Destacadas si existiera la columna)
        // Usamos lógica de "Últimas" para asegurar que siempre haya datos
        $builder = $model->orderBy('id', 'DESC'); 
        
        // Filtros de seguridad
        if ($esKids) $builder->where('edad_recomendada <=', 11);
        if ($planId == 1) $builder->where('nivel_acceso', 1);

        $carrusel = $builder->limit(5)->find();

        // B. SECCIONES (Tendencias, etc.)
        $secciones = [];

        // Fila 1: Tendencias
        $tendencias = $model->getTendencias(10, $planId);
        $this->procesarImagenes($tendencias);
        
        $secciones[] = [
            'titulo' => $esKids ? 'Favoritos Kids 🎈' : 'Tendencias 🔥',
            'data'   => $tendencias
        ];

        // Fila 2: Relleno (Acción o Recomendados)
        $generoFav = $userId ? $model->getGeneroFavoritoUsuario($userId, 1) : null;
        if ($generoFav) {
             $rec = $model->getPorGenero($generoFav['id'], 1, 10, [], $planId);
             $titulo = 'Porque viste ' . $generoFav['nombre'];
        } else {
             $rec = $model->getPorGenero(1, 1, 10, [], $planId); // 1 = Acción
             $titulo = 'Acción y Aventura';
        }
        $this->procesarImagenes($rec);
        
        if (!empty($rec)) {
            $secciones[] = ['titulo' => $titulo, 'data' => $rec];
        }

        // RESPUESTA
        return $this->respond([
            'carrusel'  => $this->procesarImagenesPublic($carrusel),
            'secciones' => $secciones
        ]);
    }

    // =================================================================
    // 2. PELÍCULA DESTACADA ALEATORIA (Para sección "Películas")
    // =================================================================
    public function getDestacadaRandom()
    {
        $model = new ContenidoModel();
        
        // Buscamos 1 contenido de tipo 1 (Película)
        // Usamos 'RAND()' compatible con MySQL
        $peli = $model->where('tipo_id', 1) 
                      ->orderBy('RAND()') 
                      ->first();

        if ($peli) {
            // Arreglamos las imágenes manualmente
            if (!str_starts_with($peli['imagen'], 'http')) {
                $peli['imagen'] = base_url('assets/img/' . $peli['imagen']);
            }
            if (!str_starts_with($peli['imagen_bg'], 'http')) {
                $peli['imagen_bg'] = base_url('assets/img/' . $peli['imagen_bg']);
            }
        }

        return $this->respond($peli);
    }

    // =================================================================
    // 3. LISTADO GENERAL / GRID (Scroll Infinito)
    // =================================================================
// En App/Controllers/Api/Catalogo.php

public function index()
    {
        $planId = session()->get('plan_id') ?? 1; 
        $page   = $this->request->getVar('page') ?? 1;
        $generoId = $this->request->getVar('genero');
        
        $model = new \App\Models\ContenidoModel();
        $generoModel = new \App\Models\GeneroModel();

        // A. SI HAY GÉNERO -> Devolvemos estructura de "Landing de Género"
        if (!empty($generoId)) {
            $nombreGenero = $generoModel->find($generoId)['nombre'] ?? 'Contenido';

            // 1. Obtener Películas (Tipo 1) - Traemos 24 para llenar 2 filas
            $pelis = $model->getPorGenero($generoId, 1, 24, [], $planId);
            
            // 2. Obtener Series (Tipo 2) - Traemos 24 para llenar 2 filas
            $series = $model->getPorGenero($generoId, 2, 24, [], $planId);

            $this->procesarImagenes($pelis);
            $this->procesarImagenes($series);

            return $this->respond([
                'status' => 'success',
                'modo'   => 'landing_genero', // Bandera para el JS
                'titulo' => $nombreGenero,
                'secciones' => [
                    [
                        'titulo' => 'Películas de ' . $nombreGenero,
                        'tipo'   => 1, // 1 = Películas
                        'data'   => $pelis,
                        'ver_mas'=> true
                    ],
                    [
                        'titulo' => 'Series de ' . $nombreGenero,
                        'tipo'   => 2, // 2 = Series
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

            // Filtros rápidos de seguridad
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
    // 6. FUNCIONES DE SOPORTE (Legacy / Helpers)
    // =================================================================
    
    // Función legacy para tendencias directas si se necesita
    public function tendencias() {
        if (!session()->get('is_logged_in')) return $this->failUnauthorized();
        // Reutilizamos getHome para mantener consistencia si se llama a esto
        return $this->getHome(); 
    }

    // Helper: Procesa array por referencia
    private function procesarImagenes(&$lista) {
        foreach ($lista as &$item) {
            if (!str_starts_with($item['imagen'], 'http')) {
                $item['imagen'] = base_url('assets/img/') . $item['imagen'];
            }
            if (!str_starts_with($item['imagen_bg'], 'http')) {
                $item['imagen_bg'] = base_url('assets/img/') . $item['imagen_bg'];
            }
        }
    }
    
    // Helper: Procesa y devuelve array (para el return directo)
    private function procesarImagenesPublic($lista) {
        foreach ($lista as &$item) {
            if (!str_starts_with($item['imagen'], 'http')) {
                $item['imagen'] = base_url('assets/img/') . $item['imagen'];
            }
            if (!str_starts_with($item['imagen_bg'], 'http')) {
                $item['imagen_bg'] = base_url('assets/img/') . $item['imagen_bg'];
            }
        }
        return $lista;
    }
    // =================================================================
    // NUEVO: LANDING DE PELÍCULAS (Hero + Filas por Género)
    // =================================================================
   // =================================================================
    // LANDING PELÍCULAS (CARRUSEL + FILAS)
    // =================================================================
    public function getPeliculasLanding()
    {
        $planId = session()->get('plan_id') ?? 1;
        $model = new ContenidoModel();
        
        // 1. CARRUSEL: 5 Películas aleatorias o recientes
        $carrusel = $model->where('tipo_id', 1)
                          ->orderBy('RAND()') // O 'id', 'DESC' para lo nuevo
                          ->limit(5)
                          ->find();
        $this->procesarImagenesPublic($carrusel);

        // 2. SECCIONES: Filas por Género
        $secciones = [];

        // Fila 1: Acción (Ajusta el ID según tu tabla generos. Ej: 1)
        $accion = $model->getPorGenero(1, 1, 12, [], $planId); 
        $this->procesarImagenesPublic($accion);
        if($accion) $secciones[] = ['titulo' => 'Pura Adrenalina 💥', 'data' => $accion];

        // Fila 2: Comedia (Ej: ID 2)
        $comedia = $model->getPorGenero(2, 1, 12, [], $planId);
        $this->procesarImagenesPublic($comedia);
        if($comedia) $secciones[] = ['titulo' => 'Risas aseguradas 😂', 'data' => $comedia];

        // Fila 3: Terror (Ej: ID 4)
        $terror = $model->getPorGenero(4, 1, 12, [], $planId);
        $this->procesarImagenesPublic($terror);
        if($terror) $secciones[] = ['titulo' => 'No apagues la luz 🕯️', 'data' => $terror];

        // Fila 4: Drama (Ej: ID 3)
        $drama = $model->getPorGenero(3, 1, 12, [], $planId);
        $this->procesarImagenesPublic($drama);
        if($drama) $secciones[] = ['titulo' => 'Historias profundas 🎭', 'data' => $drama];

        return $this->respond([
            'carrusel' => $carrusel,
            'secciones' => $secciones
        ]);
    }
}