<?php
namespace App\Controllers;
use App\Models\ContenidoModel;

class Home extends BaseController
{
    // --- 1. CATÁLOGO PRINCIPAL (HOME) CON SCROLL INFINITO Y CARRUSEL ---
    public function index($pagina = 1)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $planId = session()->get('plan_id');
        $model = new ContenidoModel();

        // Configuración de paginación
        $porPagina = 10;
        $offset = ($pagina - 1) * $porPagina;

        // 1. Obtener películas paginadas (Necesitarás crear getPeliculasPaginadas en el modelo)
        $peliculas = $model->getPeliculasPaginadas($planId, $porPagina, $offset);

        // 2. Si es una petición AJAX (Scroll infinito), devolvemos solo JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($peliculas);
        }

        // 3. Si es carga normal, obtenemos también el Carrusel (3 destacadas)
        $carrusel = $model->where('destacada', 1)->limit(3)->findAll();

        // Detectamos si venimos del Login para mostrar la Intro
        $vieneDelLogin = session()->getFlashdata('mostrar_intro');

        $data = [
            'titulo' => 'La Butaca - Inicio',
            'peliculas' => $peliculas,
            'carrusel' => $carrusel, // Nueva variable para el slider
            'categoria' => 'Tendencias',
            'mostrarHero' => true,
            'splash' => ($vieneDelLogin === true)
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }

    // --- 2. MI LISTA ---
    public function miLista()
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $db = \Config\Database::connect();

        $builder = $db->table('mi_lista ml');
        $builder->select('c.*');
        $builder->join('contenidos c', 'c.id = ml.contenido_id');
        $builder->where('ml.usuario_id', $userId);
        $builder->orderBy('ml.fecha_agregado', 'DESC');

        $misPelis = $builder->get()->getResultArray();

        $data = [
            'titulo' => 'Mi Lista Personal',
            'peliculas' => $misPelis,
            'categoria' => 'Mi Lista',
            'mostrarHero' => false, // No mostramos carrusel en mi lista
            'splash' => false 
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }

    // --- 3. PLAYER ---
    public function ver($id)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $model = new \App\Models\ContenidoModel();
        $contenido = $model->find($id);

        if (!$contenido)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $planUsuario = session()->get('plan_id');
        if ($contenido['nivel_acceso'] > $planUsuario) {
            session()->setFlashdata('error', 'Contenido exclusivo Premium.');
            return redirect()->to('/');
        }

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

    // --- 4. DETALLE ---
    public function detalle($id)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $model = new \App\Models\ContenidoModel();
        $contenido = $model->getDetallesCompletos($id);
        $director = $model->getDirector($id);

        if (!$contenido)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $db = \Config\Database::connect();
        $enLista = $db->table('mi_lista')
            ->where('usuario_id', $userId)
            ->where('contenido_id', $id)
            ->countAllResults() > 0;

        $planUsuario = session()->get('plan_id');
        $puedeVer = ($contenido['nivel_acceso'] <= $planUsuario);

        $data = [
            'titulo' => $contenido['titulo'],
            'peli' => $contenido,
            'puede_ver' => $puedeVer,
            'en_lista' => $enLista,
            'director' => $director,
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/detalle', $data);
        echo view('frontend/templates/footer', $data);
    }

    // --- 5. BUSCADOR API ---
    public function autocompletar()
    {
        $request = service('request');
        $postData = $request->getPost();
        $response = [];
        $data = [];
        $response['token'] = csrf_hash();

        if (isset($postData['search'])) {
            $search = $postData['search'];
            $planId = session()->get('plan_id') ?? 1;

            $model = new ContenidoModel();
            $builder = $model->select('id, titulo, imagen')
                ->like('titulo', $search)
                ->where('tipo_id', 1);

            if ($planId == 1) {
                $builder->where('nivel_acceso', 1);
            }

            $listaPelis = $builder->orderBy('titulo')->findAll(10);

            foreach ($listaPelis as $peli) {
                $imgUrl = str_starts_with($peli['imagen'], 'http')
                    ? $peli['imagen']
                    : base_url('assets/img/' . $peli['imagen']);

                $data[] = [
                    "value" => $peli['id'],
                    "label" => $peli['titulo'],
                    "img" => $imgUrl
                ];
            }
        }
        $response['data'] = $data;
        return $this->response->setJSON($response);
    }

    // --- 6. VISTA POR DIRECTOR ---
    public function director($id)
    {
        if (!session()->get('is_logged_in'))
            return redirect()->to('/auth');

        $model = new ContenidoModel();
        $peliculas = $model->getPeliculasPorDirector($id);
        $nombreDirector = $model->getNombreDirector($id);

        $data = [
            'titulo' => 'Director: ' . $nombreDirector,
            'peliculas' => $peliculas,
            'categoria' => 'Películas de ' . $nombreDirector,
            'mostrarHero' => false,
            'splash' => false
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }
}