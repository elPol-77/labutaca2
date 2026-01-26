<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ContenidoModel;
use App\Models\GeneroModel;

class Peliculas extends BaseController
{
    protected $model;
    protected $tipoId = 1; // 1 = Películas (Series heredará y cambiará esto a 2)

    public function __construct() {
        $this->model = new ContenidoModel();
    }

    public function index() {
        $data = [
            'titulo' => ($this->tipoId == 1) ? 'Películas' : 'Series',
            'peliculas' => $this->model->where('tipo_id', $this->tipoId)->orderBy('id', 'DESC')->paginate(10),
            'pager' => $this->model->pager
        ];
        return view('backend/peliculas/index', $data);
    }

    public function create() {
        $generoModel = new GeneroModel();
        return view('backend/peliculas/form', [
            'titulo' => 'Nuevo Contenido',
            'tipo_id' => $this->tipoId,
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll()
        ]);
    }

public function store() {
        // =====================================================================
        // 1. BLOQUE DE VALIDACIÓN (ANTI-DUPLICADOS)
        // =====================================================================
        $reglas = [
            // is_unique[tabla.columna] -> Revisa toda la tabla contenidos
            'titulo' => 'required|is_unique[contenidos.titulo]', 
            'anio'   => 'required|numeric',
        ];

        $mensajes = [
            'titulo' => [
                'is_unique' => '¡Cuidado! Ya existe una película o serie con este título en la base de datos.',
                'required'  => 'El título es obligatorio.'
            ]
        ];

        // Si la validación falla, te devuelve al formulario con los errores
        if (!$this->validate($reglas, $mensajes)) {
            return redirect()->back()
                ->withInput() // Mantiene lo que escribiste para no borrarlo
                ->with('errors', $this->validator->getErrors());
        }

        // =====================================================================
        // 2. RECOGIDA DE DATOS (Tu código original empieza aquí)
        // =====================================================================
        $data = [
            'titulo' => $this->request->getPost('titulo'),
            'anio' => $this->request->getPost('anio'),
            'duracion' => $this->request->getPost('duracion'),
            'descripcion' => $this->request->getPost('descripcion'),
            'url_video' => $this->request->getPost('url_video'),
            'tipo_id' => $this->tipoId,
            'nivel_acceso' => $this->request->getPost('nivel_acceso'),
            'edad_recomendada' => $this->request->getPost('edad_recomendada'),
            'imdb_rating' => $this->request->getPost('imdb_rating'), 
            'destacada' => $this->request->getPost('destacada') ? 1 : 0,
            'fecha_agregada' => date('Y-m-d H:i:s')
        ];

        // Lógica de imágenes (resumida)
        $imgPoster = $this->request->getFile('imagen');
        $imgBg = $this->request->getFile('imagen_bg');
        
        if ($imgPoster && $imgPoster->isValid() && !$imgPoster->hasMoved()) {
            $newName = $imgPoster->getRandomName();
            $imgPoster->move(FCPATH . 'assets/img', $newName);
            $data['imagen'] = $newName;
        } else {
            $data['imagen'] = $this->request->getPost('url_imagen_externa') ?: 'default.jpg';
        }

        if ($imgBg && $imgBg->isValid() && !$imgBg->hasMoved()) {
            $newName = $imgBg->getRandomName();
            $imgBg->move(FCPATH . 'assets/img', $newName);
            $data['imagen_bg'] = $newName;
        } else {
            $data['imagen_bg'] = $this->request->getPost('url_bg_externa') ?: 'default_bg.jpg';
        }

        // 1. INSERTAR CONTENIDO
        $this->model->insert($data);
        $nuevoId = $this->model->getInsertID();

        // 2. PROCESAR RELACIONES (MAGIA PURA ✨)
        $this->procesarGeneros($nuevoId, $this->request->getPost('generos_texto'));
        $this->procesarDirectores($nuevoId, $this->request->getPost('directores_texto'));
        $this->procesarActores($nuevoId, $this->request->getPost('actores_texto'));

        $ruta = ($this->tipoId == 1) ? 'admin/peliculas' : 'admin/series';
        return redirect()->to($ruta)->with('msg', 'Contenido importado y vinculado correctamente.');
    }

    public function delete($id) {
        $this->model->delete($id);
        return redirect()->back()->with('msg', 'Eliminado.');
    }

    // =========================================================
    // 🧠 MÉTODOS PRIVADOS DE VINCULACIÓN INTELIGENTE
    // =========================================================

    private function procesarGeneros($contenidoId, $texto) {
        if(empty($texto)) return;
        $db = \Config\Database::connect();
        $generoModel = new GeneroModel();
        
        $diccionario = ['Action'=>'Acción', 'Adventure'=>'Aventura', 'Sci-Fi'=>'Ciencia Ficción', 'Animation'=>'Animación', 'Comedy'=>'Comedia', 'Crime'=>'Crimen', 'Drama'=>'Drama', 'Family'=>'Familiar', 'Fantasy'=>'Fantasía', 'Horror'=>'Terror', 'Mystery'=>'Misterio', 'Romance'=>'Romance', 'Thriller'=>'Suspense'];

        foreach (explode(',', $texto) as $gName) {
            $gName = trim($gName);
            $nombreEs = $diccionario[$gName] ?? $gName; // Traducir

            // Buscar o Crear
            $fila = $generoModel->where('nombre', $nombreEs)->first();
            if ($fila) {
                $gid = $fila['id'];
            } else {
                $generoModel->insert(['nombre' => $nombreEs]);
                $gid = $generoModel->getInsertID();
            }
            // Vincular
            $db->table('contenido_genero')->insert(['contenido_id'=>$contenidoId, 'genero_id'=>$gid]);
        }
    }

    private function procesarDirectores($contenidoId, $texto) {
        if(empty($texto) || $texto == 'N/A') return;
        $db = \Config\Database::connect();

        foreach (explode(',', $texto) as $nombre) {
            $nombre = trim($nombre);
            
            // 1. Buscar si existe en tabla 'directores'
            $director = $db->table('directores')->where('nombre', $nombre)->get()->getRow();

            if ($director) {
                $did = $director->id;
            } else {
                // 2. Si no existe, insertar (usamos UI Avatars temporalmente para la foto)
                $db->table('directores')->insert([
                    'nombre' => $nombre,
                    'foto'   => 'https://ui-avatars.com/api/?name='.urlencode($nombre).'&background=random'
                ]);
                $did = $db->insertID();
            }

            // 3. Vincular en tabla pivote 'contenido_director'
            $db->table('contenido_director')->insert(['contenido_id'=>$contenidoId, 'director_id'=>$did]);
        }
    }

    private function procesarActores($contenidoId, $texto) {
        if(empty($texto) || $texto == 'N/A') return;
        $db = \Config\Database::connect();

        foreach (explode(',', $texto) as $nombre) {
            $nombre = trim($nombre);

            // 1. Buscar si existe en tabla 'actores'
            $actor = $db->table('actores')->where('nombre', $nombre)->get()->getRow();

            if ($actor) {
                $aid = $actor->id;
            } else {
                // 2. Si no existe, insertar
                $db->table('actores')->insert([
                    'nombre' => $nombre,
                    'foto'   => 'https://ui-avatars.com/api/?name='.urlencode($nombre).'&background=random'
                ]);
                $aid = $db->insertID();
            }

            // 3. Vincular en tabla pivote 'contenido_actor'
            $db->table('contenido_actor')->insert([
                'contenido_id' => $contenidoId, 
                'actor_id' => $aid,
                'personaje' => '' // OMDb no da el personaje fácil, lo dejamos vacío
            ]);
        }
    }
}