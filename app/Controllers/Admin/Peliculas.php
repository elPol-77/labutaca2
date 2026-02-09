<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContenidoModel;
use App\Models\GeneroModel;

class Peliculas extends BaseController
{
    protected $model;
    protected $tipoId = 1; // 1 = Películas (Series heredará y cambiará esto a 2)
    protected $tituloController = 'Películas';

    public function __construct() {
        $this->model = new ContenidoModel();
    }

    // =================================================================
    // LISTADO
    // =================================================================
    public function index() {
        $data = [
            'titulo' => $this->tituloController,
            'peliculas' => $this->model->where('tipo_id', $this->tipoId)
                                       ->orderBy('id', 'DESC')
                                       ->paginate(10),
            'pager' => $this->model->pager
        ];
        return view('backend/peliculas/index', $data);
    }

    // =================================================================
    // CREAR (FORMULARIO)
    // =================================================================
    public function create() {
        $generoModel = new GeneroModel();
        return view('backend/peliculas/form', [
            'titulo'  => 'Crear ' . substr($this->tituloController, 0, -1),
            'action'  => 'create',
            'tipo_id' => $this->tipoId,
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll(),
            'data'    => null,
            'strings' => [] // Array vacío para evitar errores
        ]);
    }

    // =================================================================
    // GUARDAR (INSERT)
    // =================================================================
    public function store() {
        // 1. VALIDACIÓN
        $reglas = [
            'titulo' => 'required',
            'anio'   => 'required|numeric',
            // is_unique permite guardar si el ID es diferente (para remakes) pero avisa
            'imdb_id' => 'permit_empty|is_unique[contenidos.imdb_id]' 
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. RECOGER DATOS (Incluyendo IMÁGENES)
        $data = $this->recogerDatosDelFormulario();
        $data['fecha_agregada'] = date('Y-m-d H:i:s');

        // 3. TRANSACCIÓN
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // A. Insertar la Película/Serie
            $this->model->insert($data);
            $nuevoId = $this->model->getInsertID();

            // B. Procesar Relaciones (Géneros Mixtos, Actores JSON, Directores JSON)
            $this->procesarGeneros($nuevoId, $this->request->getPost('generos'));
            $this->procesarDirectores($nuevoId, $this->request->getPost('directores_json'));
            $this->procesarActores($nuevoId, $this->request->getPost('actores_json'));

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('msg', 'Error al guardar relaciones.');
            }

            $ruta = ($this->tipoId == 1) ? 'admin/peliculas' : 'admin/series';
            return redirect()->to($ruta)->with('msg', 'Contenido creado correctamente.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('msg', 'Error crítico: ' . $e->getMessage());
        }
    }

    // =================================================================
    // EDITAR (FORMULARIO)
    // =================================================================
public function edit($id) {
        // 1. Obtener todos los datos del contenido (con relaciones)
        $contenido = $this->model->getDetallesCompletos($id); 

        if (!$contenido) return redirect()->back()->with('msg', 'Contenido no encontrado');

        $generoModel = new GeneroModel();
        
        // 2. Preparar los Strings para los inputs visuales (Solo lectura al cargar)
        $actoresStr = '';
        if (!empty($contenido['actores'])) {
            $nombres = array_column($contenido['actores'], 'nombre');
            $actoresStr = implode(', ', array_slice($nombres, 0, 10)); // Top 10
        }

        $directoresStr = '';
        if (!empty($contenido['director'])) { // Nota: Tu modelo devuelve 'director' (singular) o array según tu implementación
             // Si getDetallesCompletos devuelve un solo director, ajústalo. 
             // Aquí asumo que podría venir un array o un objeto. 
             // Si es array de directores:
             // $directoresStr = implode(', ', array_column($contenido['directores'], 'nombre'));
             
             // Si es el objeto simple que vi en tu modelo anterior:
             $directoresStr = $contenido['director']['nombre'] ?? '';
        }

        return view('backend/peliculas/form', [
            'titulo'  => 'Editar Contenido',
            'action'  => 'edit',
            'tipo_id' => $contenido['tipo_id'],
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll(), // Lista completa para los checkboxes
            'data'    => $contenido, // Datos de la peli (titulo, anio, etc)
            'strings' => [
                'actores'    => $actoresStr,
                'directores' => $directoresStr
            ]
        ]);
    }

    // =================================================================
    // ACTUALIZAR (UPDATE)
    // =================================================================
   public function update($id = null)
    {
        if (!$id) return redirect()->to('admin/peliculas')->with('msg', 'Error: ID no proporcionado');

        // 1. VALIDACIÓN (¡OJO AQUÍ!)
        // Quitamos 'is_unique' del título para que te deje guardar el mismo nombre
        // O usamos la regla compleja: 'is_unique[contenidos.titulo,id,{id}]'
        $reglas = [
            'titulo' => "required", 
            'anio'   => 'required|numeric'
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. RECOGER DATOS BÁSICOS
        $data = [
            'titulo'           => $this->request->getPost('titulo'),
            'anio'             => $this->request->getPost('anio'),
            'duracion'         => $this->request->getPost('duracion'),
            'descripcion'      => $this->request->getPost('descripcion'),
            'url_video'        => $this->request->getPost('url_video'),
            'nivel_acceso'     => $this->request->getPost('nivel_acceso'),
            'edad_recomendada' => $this->request->getPost('edad_recomendada'),
            'imdb_rating'      => $this->request->getPost('imdb_rating'),
            'imdb_id'          => $this->request->getPost('imdb_id'),
            'destacada'        => $this->request->getPost('destacada') ? 1 : 0,
        ];

        // 3. GESTIÓN DE IMÁGENES (Solo actualizamos si suben nuevas)
        $imgPoster = $this->request->getFile('imagen');
        $imgBg     = $this->request->getFile('imagen_bg');
        $urlExternaPoster = $this->request->getPost('url_imagen_externa');
        $urlExternaBg     = $this->request->getPost('url_bg_externa');

        // Prioridad: 1. Archivo subido -> 2. URL Externa -> 3. No tocar (mantener actual)
        if ($imgPoster && $imgPoster->isValid() && !$imgPoster->hasMoved()) {
            $newName = $imgPoster->getRandomName();
            $imgPoster->move(FCPATH . 'assets/img', $newName);
            $data['imagen'] = $newName;
        } elseif (!empty($urlExternaPoster)) {
            $data['imagen'] = $urlExternaPoster;
        }

        if ($imgBg && $imgBg->isValid() && !$imgBg->hasMoved()) {
            $newName = $imgBg->getRandomName();
            $imgBg->move(FCPATH . 'assets/img', $newName);
            $data['imagen_bg'] = $newName;
        } elseif (!empty($urlExternaBg)) {
            $data['imagen_bg'] = $urlExternaBg;
        }

        // 4. TRANSACCIÓN DE BASE DE DATOS (Para que sea seguro)
        $db = \Config\Database::connect();
        $db->transStart();

        // A. Actualizar tabla principal
        $this->model->update($id, $data);

        // B. LIMPIEZA DE RELACIONES (¡CRUCIAL!)
        // Antes de meter los nuevos géneros/actores, borramos los viejos de este ID
        $db->table('contenido_genero')->where('contenido_id', $id)->delete();
        $db->table('contenido_actor')->where('contenido_id', $id)->delete();
        $db->table('contenido_director')->where('contenido_id', $id)->delete();

        // C. RE-INSERTAR RELACIONES
        // Usamos los mismos métodos privados que en el store()
        $this->procesarGeneros($id, $this->request->getPost('generos')); 
        $this->procesarDirectores($id, $this->request->getPost('directores_json'));
        $this->procesarActores($id, $this->request->getPost('actores_json'));

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('msg', 'Error al actualizar en BDD');
        }

        // Consultamos qué tipo es realmente este contenido para saber dónde volver
        $contenidoActual = $this->model->select('tipo_id')->find($id);

        // Si tipo_id es 2 vamos a Series, si no (1) a Películas
        $ruta = ($contenidoActual['tipo_id'] == 2) ? 'admin/series' : 'admin/peliculas';

        return redirect()->to($ruta)->with('msg', 'Contenido actualizado correctamente.');
    
    }

    public function delete($id) {
        $this->model->delete($id);
        return redirect()->back()->with('msg', 'Eliminado correctamente.');
    }

    // =================================================================
    // 🧠 HELPERS PRIVADOS (La lógica importante)
    // =================================================================

    private function recogerDatosDelFormulario() {
        $data = [
            'titulo'           => $this->request->getPost('titulo'),
            'anio'             => $this->request->getPost('anio'),
            'duracion'         => $this->request->getPost('duracion'),
            'descripcion'      => $this->request->getPost('descripcion'),
            'url_video'        => $this->request->getPost('url_video'),
            'tipo_id'          => $this->tipoId,
            'nivel_acceso'     => $this->request->getPost('nivel_acceso'),
            'edad_recomendada' => $this->request->getPost('edad_recomendada'),
            'imdb_rating'      => $this->request->getPost('imdb_rating'),
            'imdb_id'          => $this->request->getPost('imdb_id'),
            'destacada'        => $this->request->getPost('destacada') ? 1 : 0,
        ];

        // --- LÓGICA DE IMÁGENES (AQUÍ ESTABA EL PROBLEMA) ---
        
        // 1. PÓSTER
        $imgPoster = $this->request->getFile('imagen');
        if ($imgPoster && $imgPoster->isValid() && !$imgPoster->hasMoved()) {
            // A. Si suben archivo
            $newName = $imgPoster->getRandomName();
            $imgPoster->move(FCPATH . 'assets/img', $newName);
            $data['imagen'] = $newName;
        } elseif ($this->request->getPost('url_imagen_externa')) {
            // B. Si hay URL externa (TMDB)
            $data['imagen'] = $this->request->getPost('url_imagen_externa');
        }

        // 2. FONDO (BACKDROP)
        $imgBg = $this->request->getFile('imagen_bg'); // Si existiera input file para bg
        if ($this->request->getPost('url_bg_externa')) {
            $data['imagen_bg'] = $this->request->getPost('url_bg_externa');
        }
        
        return $data;
    }

    /**
     * Procesa géneros Mixtos (IDs existentes + Nombres nuevos)
     */
    private function procesarGeneros($contenidoId, $datos) {
        if (empty($datos)) return;
        
        // Normalizar entrada: puede ser array o JSON string
        $generosMixtos = is_string($datos) ? json_decode($datos, true) : $datos;
        if (!is_array($generosMixtos)) return;

        $db = \Config\Database::connect();
        $generoModel = new GeneroModel();

        // ---------------------------------------------------------
        // 📖 DICCIONARIO DE TRADUCCIÓN (MAPEO)
        // Clave = Lo que llega de la API (o posibles variantes)
        // Valor = Como lo quieres guardar en TU base de datos
        // ---------------------------------------------------------
        $diccionario = [
            'Action'          => 'Acción',
            'Adventure'       => 'Aventura',
            'Aventure'        => 'Aventura', // Caso que mencionaste
            'Sci-Fi'          => 'Ciencia Ficción',
            'Science Fiction' => 'Ciencia Ficción',
            'Animation'       => 'Animación',
            'Comedy'          => 'Comedia',
            'Crime'           => 'Crimen',
            'Documentary'     => 'Documental',
            'Drama'           => 'Drama',
            'Family'          => 'Familiar',
            'Fantasy'         => 'Fantasía',
            'History'         => 'Historia',
            'Horror'          => 'Terror',
            'Music'           => 'Música',
            'Mystery'         => 'Misterio',
            'Romance'         => 'Romance',
            'Thriller'        => 'Terror', 
            'War'             => 'Bélica',
            'Western'         => 'Western',
            'TV Movie'        => 'Película de TV'
        ];

        foreach ($generosMixtos as $dato) {
            // CASO A: Es un ID numérico (Checkbox existente marcado)
            if (is_numeric($dato)) {
                $gid = $dato;
            } 
            // CASO B: Es un Texto (Nuevo género detectado por la API)
            else {
                $nombreLimpio = trim($dato);
                if(empty($nombreLimpio)) continue;

                // 1. TRADUCIR: Verificamos si existe en el diccionario
                // Usamos ucwords para asegurar que coincida con las claves (Action, Adventure...)
                $nombreKey = ucwords(strtolower($nombreLimpio)); 
                
                if (array_key_exists($nombreKey, $diccionario)) {
                    $nombreFinal = $diccionario[$nombreKey];
                } else {
                    $nombreFinal = $nombreLimpio; // Si no está en el mapa, lo dejamos tal cual
                }

                // 2. BUSCAR O CREAR (Con el nombre ya traducido)
                // Buscamos si ya existe en la BD (ej: "Terror")
                $genero = $generoModel->where('nombre', $nombreFinal)->first();
                
                if ($genero) {
                    $gid = $genero['id'];
                } else {
                    // Si no existe, lo creamos
                    $generoModel->insert(['nombre' => $nombreFinal]);
                    $gid = $generoModel->getInsertID();
                }
            }

            // 3. VINCULAR (Relación Contenido <-> Género)
            // Usamos ignore(true) para que no falle si ya existe la relación
            $db->table('contenido_genero')->ignore(true)->insert([
                'contenido_id' => $contenidoId, 
                'genero_id'    => $gid
            ]);
        }
    }

    /**
     * Procesa Actores (JSON con nombre, personaje y FOTO)
     */
    private function procesarActores($contenidoId, $json) {
        if (empty($json)) return;
        $actores = json_decode($json, true);
        if (!is_array($actores)) return;

        $db = \Config\Database::connect();

        foreach ($actores as $actorData) {
            $nombre = trim($actorData['name'] ?? '');
            if (empty($nombre)) continue;

            // Buscar si existe
            $actorRow = $db->table('actores')->where('nombre', $nombre)->get()->getRow();

            if ($actorRow) {
                $aid = $actorRow->id;
                // Si no tenía foto y ahora traemos una, actualizamos
                if (empty($actorRow->foto) && !empty($actorData['photo'])) {
                    $db->table('actores')->where('id', $aid)->update(['foto' => $actorData['photo']]);
                }
            } else {
                // Insertar nuevo
                $foto = !empty($actorData['photo']) 
                        ? $actorData['photo'] 
                        : 'https://ui-avatars.com/api/?name='.urlencode($nombre).'&background=random';

                $db->table('actores')->insert(['nombre' => $nombre, 'foto'   => $foto]);
                $aid = $db->insertID();
            }

            // Vincular con Personaje
            $personaje = $actorData['character'] ?? '';
            $db->table('contenido_actor')->ignore(true)->insert([
                'contenido_id' => $contenidoId, 
                'actor_id'     => $aid,
                'personaje'    => $personaje
            ]);
        }
    }

    /**
     * Procesa Directores (JSON)
     */
    private function procesarDirectores($contenidoId, $json) {
        if (empty($json)) return;
        $directores = json_decode($json, true);
        if (!is_array($directores)) return;

        $db = \Config\Database::connect();

        foreach ($directores as $dirData) {
            $nombre = trim($dirData['name'] ?? '');
            if(empty($nombre)) continue;

            $dirRow = $db->table('directores')->where('nombre', $nombre)->get()->getRow();

            if ($dirRow) {
                $did = $dirRow->id;
                if (empty($dirRow->foto) && !empty($dirData['photo'])) {
                    $db->table('directores')->where('id', $did)->update(['foto' => $dirData['photo']]);
                }
            } else {
                $foto = !empty($dirData['photo']) ? $dirData['photo'] : '';
                $db->table('directores')->insert(['nombre' => $nombre, 'foto' => $foto]);
                $did = $db->insertID();
            }

            $db->table('contenido_director')->ignore(true)->insert([
                'contenido_id' => $contenidoId, 
                'director_id'  => $did
            ]);
        }
    }
}