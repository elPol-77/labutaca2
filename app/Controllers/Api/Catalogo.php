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
        // 1. Recibir término
        $search = $this->request->getPost('search');
        
        // Respuesta base
        $response = ['token' => csrf_hash(), 'data' => []];

        if ($search) {
            // OBTENEMOS EL PLAN DEL USUARIO (Si no hay sesión, asumimos Free/Restringido)
            $planId = session()->get('plan_id') ?? 1; 

            $model = new \App\Models\ContenidoModel();
            $builder = $model->select('id, titulo, imagen, anio, nivel_acceso, edad_recomendada')
                             ->like('titulo', $search);

            // =========================================================
            // 🛡️ FILTROS DE SEGURIDAD SEGÚN EL PLAN
            // =========================================================
            
            // CASO 1: PLAN KIDS (Plan ID 3)
            // "Ni a los niños películas de más de 11 años"
            if ($planId == 3) {
                $builder->where('edad_recomendada <=', 11); 
            }
            
            // CASO 2: PLAN FREE (Plan ID 1)
            // "Los usuarios free no deberían ver películas premium"
            // (Asumimos que nivel_acceso 1 es Free y 2 es Premium)
            elseif ($planId == 1) {
                $builder->where('nivel_acceso', 1);
            }

            // Si es Premium (Plan 2), no entra en ningún if y lo ve todo.
            // =========================================================

            $listaPelis = $builder->limit(5)->find();

            // Formatear datos
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

}