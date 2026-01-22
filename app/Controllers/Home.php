<?php

namespace App\Controllers;

use App\Models\ContenidoModel;
use App\Models\UsuarioModel;
use App\Models\GeneroModel;
use CodeIgniter\Controller;

class Home extends BaseController
{
    // =========================================================================
    // 1. CATÁLOGO PRINCIPAL (HOME)
    // =========================================================================
    public function index($pagina = 1)
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $planId = session()->get('plan_id'); // 1=Free, 2=Premium, 3=Kids
        $userId = session()->get('user_id'); 
        
        $esKids = ($planId == 3);
        $esFree = ($planId == 1);

        $model = new ContenidoModel();
        $userModel = new UsuarioModel();
        $generoModel = new GeneroModel();

        // Variables iniciales
        $secciones = [];
        $peliculas = []; // Importante inicializarlo
        $tituloCategoria = 'Inicio';
        $mostrarHero = true;

        // -----------------------------------------------------------
        // A. DETECTAR SI HAY FILTRO (GÉNERO)
        // -----------------------------------------------------------
        $filtroGenero = $this->request->getGet('genero');

        if ($filtroGenero) {
            // === MODO REJILLA (Estilo clásico para filtros) ===
            // Si el usuario filtra, NO mostramos filas de Netflix, mostramos el grid
            
            $nombreGenero = $generoModel->find($filtroGenero)['nombre'] ?? 'Género';
            $tituloCategoria = 'Categoría: ' . $nombreGenero;
            $mostrarHero = false; // Ocultamos el héroe para ver los resultados directos

            $porPagina = 10;
            $offset = ($pagina - 1) * $porPagina;
            
            // Usamos la paginación normal
            $peliculas = $model->getContenidoPaginadas($planId, $porPagina, $offset, $filtroGenero);
            $this->procesarMetadatos($peliculas, $userId);

        } else {
            // === MODO NETFLIX (Portada) ===
            // Solo entramos aquí si NO hay filtros
            
            // 1. TENDENCIAS
            $tendencias = $model->getTendencias(10, $planId);
            $this->procesarMetadatos($tendencias, $userId);
            
            $tituloTendencias = $esKids ? 'Los favoritos de los peques 🎈' : 'Tendencias en La Butaca';
            if ($esFree) $tituloTendencias .= ' (Gratis)';
            
            $secciones[] = ['titulo' => $tituloTendencias, 'data' => $tendencias];

            if ($esKids) {
                // --- MUNDO KIDS ---
                $animacion = $model->getPorGenero(5, 1, 10, [], 3); 
                $this->procesarMetadatos($animacion, $userId);
                $secciones[] = ['titulo' => 'Mundo Animado ✨', 'data' => $animacion];

                $aventuras = $model->getPorGenero(2, 2, 10, [], 3);
                $this->procesarMetadatos($aventuras, $userId);
                $secciones[] = ['titulo' => 'Grandes Aventuras 🚀', 'data' => $aventuras];

                $mix = $model->getContentRandom(1, 10, 3); 
                $this->procesarMetadatos($mix, $userId);
                $secciones[] = ['titulo' => '¡Descubre algo nuevo! 🎲', 'data' => $mix];

            } else {
                // --- MUNDO ADULTO ---
                $seriesRandom = $model->getContentRandom(2, 10, $planId); 
                $this->procesarMetadatos($seriesRandom, $userId);
                $secciones[] = ['titulo' => 'Series para maratonear', 'data' => $seriesRandom];

                $generoFavPeli = $model->getGeneroFavoritoUsuario($userId, 1);
                if ($generoFavPeli) {
                    $recPelis = $model->getPorGenero($generoFavPeli['id'], 1, 10, [], $planId);
                    $titulo = 'Porque viste películas de ' . $generoFavPeli['nombre'];
                } else {
                    $recPelis = $model->getPorGenero(1, 1, 10, [], $planId);
                    $titulo = 'Películas de Acción para empezar';
                }
                $this->procesarMetadatos($recPelis, $userId);
                $secciones[] = ['titulo' => $titulo, 'data' => $recPelis];

                $generoFavSerie = $model->getGeneroFavoritoUsuario($userId, 2);
                if ($generoFavSerie) {
                    $recSeries = $model->getPorGenero($generoFavSerie['id'], 2, 10, [], $planId);
                    $tituloS = 'Series de ' . $generoFavSerie['nombre'] . ' para ti';
                } else {
                    $recSeries = $model->getPorGenero(2, 2, 10, [], $planId);
                    $tituloS = 'Series de Aventura';
                }
                $this->procesarMetadatos($recSeries, $userId);
                $secciones[] = ['titulo' => $tituloS, 'data' => $recSeries];
            }
        }

        // Si es petición AJAX (Scroll infinito en modo rejilla)
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($peliculas);
        }

        // DATOS COMUNES
        $listaGeneros = $generoModel->orderBy('nombre', 'ASC')->findAll();
        $otrosPerfiles = $userModel->where('id >=', 2)->where('id <=', 4)->where('id !=', $userId)->findAll();
        
        // Carrusel Hero
        $builderCarrusel = $model->where('destacada', 1);
        if ($esFree) {
            $builderCarrusel->where('nivel_acceso', 1);
        } elseif ($esKids) {
            $builderCarrusel->where('edad_recomendada <=', 11);
        }
        $carrusel = $builderCarrusel->limit(3)->findAll();
        
        // Fallback si el carrusel está vacío
        if (empty($carrusel)) {
            $builderRelleno = $model->orderBy('anio', 'DESC');
            if ($esFree) $builderRelleno->where('nivel_acceso', 1);
            elseif ($esKids) $builderRelleno->where('edad_recomendada <=', 11);
            $carrusel = $builderRelleno->limit(3)->findAll();
        }
        $this->procesarMetadatos($carrusel, $userId);

        $data = [
            'titulo'        => 'La Butaca - ' . $tituloCategoria,
            'carrusel'      => $carrusel, 
            'secciones'     => $secciones, // Si está lleno -> Vista Netflix
            'peliculas'     => $peliculas, // Si está lleno -> Vista Grid (Filtros)
            'categoria'     => $tituloCategoria,
            'generos'       => $listaGeneros,
            'otrosPerfiles' => $otrosPerfiles,
            'mostrarHero'   => $mostrarHero,
            'splash'        => (session()->getFlashdata('mostrar_intro') === true)
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }

    private function procesarMetadatos(&$lista, $userId) {
        $userModel = new UsuarioModel();
        $idsFavoritos = $userModel->getListaIds($userId);
        
        if (!is_array($lista)) return;

        foreach ($lista as &$p) {
            $p['en_mi_lista'] = in_array($p['id'], $idsFavoritos);
            if (!str_starts_with($p['imagen'], 'http')) $p['imagen'] = base_url('assets/img/') . $p['imagen'];
            if (!str_starts_with($p['imagen_bg'], 'http')) $p['imagen_bg'] = base_url('assets/img/') . $p['imagen_bg'];
        }
    }

    // =========================================================================
    // 2. MI LISTA
    // =========================================================================
    public function miLista()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $db = \Config\Database::connect();
        $generoModel = new GeneroModel();
        $userModel = new UsuarioModel();

        $builder = $db->table('mi_lista ml');
        $builder->select('c.*');
        $builder->join('contenidos c', 'c.id = ml.contenido_id');
        $builder->where('ml.usuario_id', $userId);
        $builder->orderBy('ml.fecha_agregado', 'DESC');

        $misPelis = $builder->get()->getResultArray();

        foreach ($misPelis as &$p) {
            $p['en_mi_lista'] = true;
        }
        // Procesar imágenes también aquí por si acaso
        $this->procesarMetadatos($misPelis, $userId);

        $listaGeneros = $generoModel->orderBy('nombre', 'ASC')->findAll();
        $otrosPerfiles = $userModel->where('id >=', 2)->where('id <=', 4)->where('id !=', $userId)->findAll();

        $data = [
            'titulo'        => 'Mi Lista Personal',
            'peliculas'     => $misPelis,
            'categoria'     => 'Mi Lista',
            'mostrarHero'   => false, 
            'splash'        => false,
            'generos'       => $listaGeneros,
            'otrosPerfiles' => $otrosPerfiles
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }

    // =========================================================================
    // 3. REPRODUCTOR (TU SEGURIDAD ESTÁ BIEN AQUÍ)
    // =========================================================================
    public function ver($id)
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $model = new ContenidoModel();
        $contenido = $model->find($id);

        if (!$contenido)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $planUsuario = session()->get('plan_id');
        $nivelAcceso = $contenido['nivel_acceso']; 
        $edadRecomendada = $contenido['edad_recomendada'];

        $puedeVer = false;

        if ($planUsuario == 2) { 
            // Premium ve todo
            $puedeVer = true; 
        } 
        elseif ($planUsuario == 3) {
            // KIDS: Nivel 3 o 1 Y edad <= 11
            if (($nivelAcceso == 3 || $nivelAcceso == 1) && $edadRecomendada <= 11) {
                $puedeVer = true;
            }
        } 
        elseif ($planUsuario == 1) {
            // FREE: Solo nivel 1
            if ($nivelAcceso == 1) $puedeVer = true;
        }

        // SEGURIDAD: REDIRECT SI NO PUEDE VER
        if (!$puedeVer) {
            session()->setFlashdata('error', 'Contenido no disponible para tu perfil o plan.');
            return redirect()->to('/');
        }

        $videoUrl = $contenido['url_video'];
        
        if (strpos($videoUrl, 'youtu') !== false) {
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match)) {
                $videoUrl = 'https://www.youtube.com/embed/' . $match[1] . '?autoplay=1&rel=0&modestbranding=1';
            }
        }

        return view('frontend/player', [
            'titulo'    => 'Viendo: ' . $contenido['titulo'],
            'contenido' => $contenido,
            'video_url' => $videoUrl
        ]);
    }

    // ... (El resto de funciones detalle, autocompletar, director déjalas como las tenías) ...
    // Solo asegúrate de que 'detalle' tiene la misma lógica de $puedeVer visual.
    
    public function detalle($id)
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $model = new ContenidoModel();
        $userModel = new UsuarioModel();
        $generoModel = new GeneroModel();

        $contenido = $model->getDetallesCompletos($id);
        $director = $model->getDirector($id);

        if (!$contenido) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $db = \Config\Database::connect();
        $enLista = $db->table('mi_lista')->where('usuario_id', $userId)->where('contenido_id', $id)->countAllResults() > 0;

        $planUsuario = session()->get('plan_id');
        $nivelAcceso = $contenido['nivel_acceso'];
        $edadRecomendada = $contenido['edad_recomendada'];

        $puedeVer = false;
        if ($planUsuario == 2) { $puedeVer = true; } 
        elseif ($planUsuario == 3) {
            if (($nivelAcceso == 3 || $nivelAcceso == 1) && $edadRecomendada <= 11) $puedeVer = true;
        } 
        elseif ($planUsuario == 1) {
            if ($nivelAcceso == 1) $puedeVer = true;
        }

        $listaGeneros = $generoModel->orderBy('nombre', 'ASC')->findAll();
        $otrosPerfiles = $userModel->where('id >=', 2)->where('id <=', 4)->where('id !=', $userId)->findAll();

        $data = [
            'titulo'        => $contenido['titulo'],
            'peli'          => $contenido,
            'puede_ver'     => $puedeVer,
            'en_lista'      => $enLista,
            'director'      => $director,
            'generos'       => $listaGeneros,
            'otrosPerfiles' => $otrosPerfiles
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/detalle', $data);
        echo view('frontend/templates/footer', $data);
    }

    // =========================================================================
    // 5. BUSCADOR
    // =========================================================================
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

            if ($planId == 3) {
                $builder->where('edad_recomendada <=', 11);
            } 
            elseif ($planId == 1) {
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
                    "img"   => $imgUrl
                ];
            }
        }
        $response['data'] = $data;
        return $this->response->setJSON($response);
    }

    // =========================================================================
    // 6. DIRECTOR
    // =========================================================================
    public function director($id)
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $model = new ContenidoModel();
        $userModel = new UsuarioModel();
        $generoModel = new GeneroModel();

        $peliculas = $model->getPeliculasPorDirector($id);
        $nombreDirector = $model->getNombreDirector($id);

        $idsFavoritos = $userModel->getListaIds($userId);
        foreach ($peliculas as &$p) {
            $p['en_mi_lista'] = in_array($p['id'], $idsFavoritos);
        }
        unset($p);

        $tienePeliculas = false;
        $tieneSeries = false;

        foreach ($peliculas as $item) {
            if ($item['tipo_id'] == 1) $tienePeliculas = true;
            if ($item['tipo_id'] == 2) $tieneSeries = true;
        }

        $nombreCategoria = 'Filmografía de ' . $nombreDirector; 
        
        if ($tienePeliculas && !$tieneSeries) {
            $nombreCategoria = 'Películas de ' . $nombreDirector;
        } elseif (!$tienePeliculas && $tieneSeries) {
            $nombreCategoria = 'Series de ' . $nombreDirector;
        }

        $listaGeneros = $generoModel->orderBy('nombre', 'ASC')->findAll();
        $otrosPerfiles = $userModel->where('id >=', 2)->where('id <=', 4)->where('id !=', $userId)->findAll();

        $data = [
            'titulo'        => $nombreCategoria,
            'peliculas'     => $peliculas,
            'categoria'     => $nombreCategoria,
            'mostrarHero'   => false,
            'splash'        => false,
            'generos'       => $listaGeneros,
            'otrosPerfiles' => $otrosPerfiles
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }
}