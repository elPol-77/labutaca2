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

        // NOTA: Recuerda que en el paso anterior cambiamos el modelo para que esta función
        // traiga pelis Y series. (Aunque se llame getPeliculasPaginadas)
        $peliculas = $model->getPeliculasPaginadas($planId, $porPagina, $offset);

        // 2. Si es una petición AJAX (Scroll infinito), devolvemos solo JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($peliculas);
        }

        // 3. Si es carga normal, obtenemos también el Carrusel (3 destacadas)
        $carrusel = $model->where('destacada', 1)->limit(3)->findAll();

        $vieneDelLogin = session()->getFlashdata('mostrar_intro');

        $data = [
            'titulo' => 'La Butaca - Inicio',
            'peliculas' => $peliculas,
            'carrusel' => $carrusel, 
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
            'mostrarHero' => false, 
            'splash' => false 
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }

    // --- 3. REPRODUCTOR ---
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
        // Conversión básica de YouTube a Embed
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

    // --- 4. DETALLE DEL CONTENIDO ---
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

            // --- SEGURIDAD EN BUSCADOR ---
            if ($planId == 3) {
                // Si es niño, BLOQUEAR resultados mayores de 12 años
                $builder->where('edad_recomendada <=', 12);
            } 
            elseif ($planId == 1) {
                // Si es Free, bloquear Premium
                $builder->where('nivel_acceso', 1);
            }
            // -----------------------------

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

        // --- LÓGICA DE DIFERENCIACIÓN ---
        // Analizamos qué tipos de contenido tiene este director
        $tienePeliculas = false;
        $tieneSeries = false;

        foreach ($peliculas as $item) {
            if ($item['tipo_id'] == 1) $tienePeliculas = true;
            if ($item['tipo_id'] == 2) $tieneSeries = true;
        }

        // Definimos el título según lo encontrado
        $nombreCategoria = 'Filmografía de ' . $nombreDirector; // Por defecto (si tiene ambos)
        
        if ($tienePeliculas && !$tieneSeries) {
            $nombreCategoria = 'Películas de ' . $nombreDirector;
        } elseif (!$tienePeliculas && $tieneSeries) {
            $nombreCategoria = 'Series de ' . $nombreDirector;
        }

        $data = [
            'titulo' => $nombreCategoria,
            'peliculas' => $peliculas, // Pasamos todo junto para que la vista 'catalogo' no se rompa
            'categoria' => $nombreCategoria,
            'mostrarHero' => false,
            'splash' => false
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }
}