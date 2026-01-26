<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ContenidoModel;
use App\Models\GeneroModel;

class Catalogo extends ResourceController
{
    protected $format = 'json'; // Siempre devuelve JSON

    // 1. OBTENER FILAS TIPO NETFLIX (Tendencias, Recomendados, etc.)
    public function tendencias()
    {
        // Seguridad de sesión (Si no está logueado, error 401)
        if (!session()->get('is_logged_in')) return $this->failUnauthorized('No has iniciado sesión');

        $planId = session()->get('plan_id');
        $userId = session()->get('user_id');
        $esKids = ($planId == 3);
        $esFree = ($planId == 1);

        $model = new ContenidoModel();
        $secciones = [];

        // Fila 1: Tendencias Locales
        $tendencias = $model->getTendencias(10, $planId);
        $this->procesarImagenes($tendencias);
        $secciones[] = [
            'titulo' => $esKids ? 'Favoritos Kids 🎈' : 'Tendencias 🔥',
            'data'   => $tendencias
        ];

        // Fila 2: Lógica condicional (Reutilizamos la lógica de Home.php)
        if ($esKids) {
            $animacion = $model->getPorGenero(5, 1, 10, [], 3);
            $this->procesarImagenes($animacion);
            $secciones[] = ['titulo' => 'Mundo Animado ✨', 'data' => $animacion];
        } else {
            // Recomendación inteligente basada en gustos
            $generoFav = $model->getGeneroFavoritoUsuario($userId, 1); // 1 = Películas
            if ($generoFav) {
                $rec = $model->getPorGenero($generoFav['id'], 1, 10, [], $planId);
                $titulo = 'Porque viste ' . $generoFav['nombre'];
            } else {
                $rec = $model->getPorGenero(1, 1, 10, [], $planId); // Acción default
                $titulo = 'Acción y Aventura';
            }
            $this->procesarImagenes($rec);
            $secciones[] = ['titulo' => $titulo, 'data' => $rec];
        }

        return $this->respond([
            'status' => 'success',
            'sections' => $secciones
        ]);
    }

    // 2. LISTADO GENERAL CON FILTROS (Para el Grid infinito o Angular)
    public function index()
    {
        $planId = session()->get('plan_id') ?? 1; // Default Free
        $page   = $this->request->getGet('page') ?? 1;
        $genero = $this->request->getGet('genero');
        $limit  = 12;
        $offset = ($page - 1) * $limit;

        $model = new ContenidoModel();
        
        // Usamos la función inteligente del modelo que ya filtra por Plan/Edad
        $peliculas = $model->getContenidoPaginadas($planId, $limit, $offset, $genero);
        $this->procesarImagenes($peliculas);

        return $this->respond([
            'status' => 'success',
            'page'   => $page,
            'data'   => $peliculas
        ]);
    }

    // 3. DETALLE DE UNA PELÍCULA
    public function show($id = null)
    {
        $model = new ContenidoModel();
        $data = $model->getDetallesCompletos($id);

        if (!$data) return $this->failNotFound('Contenido no encontrado');

        // Procesar imagen
        if (!str_starts_with($data['imagen'], 'http')) {
            $data['imagen'] = base_url('assets/img/' . $data['imagen']);
            $data['imagen_bg'] = base_url('assets/img/' . $data['imagen_bg']);
        }

        return $this->respond($data);
    }

    // Helper privado para arreglar URLs de imágenes
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
    // ... funciones anteriores (index, show, tendencias) ...

    // 4. BUSCADOR API (AUTOCOMPLETE)
    public function autocompletar()
    {
        // 1. Recibir datos JSON o POST
        $search = $this->request->getPost('search');
        
        // Respuesta base con nuevo token CSRF
        $response = ['token' => csrf_hash(), 'data' => []];

        if ($search) {
            $planId = session()->get('plan_id') ?? 1;
            $model = new \App\Models\ContenidoModel();

            // Usamos Query Builder
            $builder = $model->select('id, titulo, imagen, anio')
                             ->like('titulo', $search)
                             ->where('tipo_id', 1); // Solo películas por ahora

            // Aplicar filtros de seguridad (Kids/Free)
            if ($planId == 3) {
                $builder->where('edad_recomendada <=', 12);
            } elseif ($planId == 1) {
                $builder->where('nivel_acceso', 1);
            }

            $listaPelis = $builder->orderBy('titulo')->findAll(5); // Top 5 resultados

            // Formatear datos para el frontend
            foreach ($listaPelis as $peli) {
                $imgUrl = str_starts_with($peli['imagen'], 'http')
                    ? $peli['imagen']
                    : base_url('assets/img/' . $peli['imagen']);

                $response['data'][] = [
                    "value" => $peli['id'],      // ID real
                    "label" => $peli['titulo'],  // Título
                    "img"   => $imgUrl,
                    "year"  => $peli['anio']
                ];
            }
        }

        return $this->respond($response);
    }

}