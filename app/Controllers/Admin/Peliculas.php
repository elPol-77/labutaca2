<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContenidoModel;
use App\Models\GeneroModel;

class Peliculas extends BaseController
{
    protected $model;
    protected $tipoId = 1; 
    protected $tituloController = 'Películas';

    public function __construct()
    {
        $this->model = new ContenidoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => $this->tituloController,
            'peliculas' => $this->model->where('tipo_id', $this->tipoId)
                ->orderBy('id', 'DESC')
                ->paginate(10),
            'pager' => $this->model->pager
        ];
        return view('backend/peliculas/index', $data);
    }
    public function create()
    {
        $generoModel = new GeneroModel();
        return view('backend/peliculas/form', [
            'titulo' => 'Crear ' . substr($this->tituloController, 0, -1),
            'action' => 'create',
            'tipo_id' => $this->tipoId,
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll(),
            'data' => null,
            'strings' => [] 
        ]);
    }

    public function store()
    {
        $reglas = [
            'titulo' => 'required',
            'anio' => 'required|numeric',
            'imdb_id' => 'permit_empty|is_unique[contenidos.imdb_id]'
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->recogerDatosDelFormulario();
        $data['fecha_agregada'] = date('Y-m-d H:i:s');

        // 3. TRANSACCIÓN
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->model->insert($data);
            $nuevoId = $this->model->getInsertID();

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

    public function edit($id)
    {
        $contenido = $this->model->getDetallesCompletos($id);

        if (!$contenido)
            return redirect()->back()->with('msg', 'Contenido no encontrado');

        $generoModel = new GeneroModel();

        $actoresStr = '';
        if (!empty($contenido['actores'])) {
            $nombres = array_column($contenido['actores'], 'nombre');
            $actoresStr = implode(', ', array_slice($nombres, 0, 10));
        }

        $directoresStr = '';
        if (!empty($contenido['director'])) { 
            $directoresStr = $contenido['director']['nombre'] ?? '';
        }

        return view('backend/peliculas/form', [
            'titulo' => 'Editar Contenido',
            'action' => 'edit',
            'tipo_id' => $contenido['tipo_id'],
            'generos' => $generoModel->orderBy('nombre', 'ASC')->findAll(), 
            'data' => $contenido, 
            'strings' => [
                'actores' => $actoresStr,
                'directores' => $directoresStr
            ]
        ]);
    }

    public function update($id = null)
    {
        if (!$id)
            return redirect()->to('admin/peliculas')->with('msg', 'Error: ID no proporcionado');

        $reglas = [
            'titulo' => "required",
            'anio' => 'required|numeric'
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. RECOGER DATOS BÁSICOS
        $data = [
            'titulo' => $this->request->getPost('titulo'),
            'anio' => $this->request->getPost('anio'),
            'duracion' => $this->request->getPost('duracion'),
            'descripcion' => $this->request->getPost('descripcion'),
            'url_video' => $this->request->getPost('url_video'),
            'nivel_acceso' => $this->request->getPost('nivel_acceso'),
            'edad_recomendada' => $this->request->getPost('edad_recomendada'),
            'imdb_rating' => $this->request->getPost('imdb_rating'),
            'imdb_id' => $this->request->getPost('imdb_id'),
            'destacada' => $this->request->getPost('destacada') ? 1 : 0,
        ];

        // 3. GESTIÓN DE IMÁGENES (Solo actualizamos si suben nuevas)
        $imgPoster = $this->request->getFile('imagen');
        $imgBg = $this->request->getFile('imagen_bg');
        $urlExternaPoster = $this->request->getPost('url_imagen_externa');
        $urlExternaBg = $this->request->getPost('url_bg_externa');

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

        $db = \Config\Database::connect();
        $db->transStart();

        $this->model->update($id, $data);

        $db->table('contenido_genero')->where('contenido_id', $id)->delete();
        $db->table('contenido_actor')->where('contenido_id', $id)->delete();
        $db->table('contenido_director')->where('contenido_id', $id)->delete();

        $this->procesarGeneros($id, $this->request->getPost('generos'));
        $this->procesarDirectores($id, $this->request->getPost('directores_json'));
        $this->procesarActores($id, $this->request->getPost('actores_json'));

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('msg', 'Error al actualizar en BDD');
        }

        $contenidoActual = $this->model->select('tipo_id')->find($id);

        $ruta = ($contenidoActual['tipo_id'] == 2) ? 'admin/series' : 'admin/peliculas';

        return redirect()->to($ruta)->with('msg', 'Contenido actualizado correctamente.');

    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect()->back()->with('msg', 'Eliminado correctamente.');
    }



    private function recogerDatosDelFormulario()
    {
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
            'imdb_id' => $this->request->getPost('imdb_id'),
            'destacada' => $this->request->getPost('destacada') ? 1 : 0,
        ];


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

        $imgBg = $this->request->getFile('imagen_bg');
        if ($this->request->getPost('url_bg_externa')) {
            $data['imagen_bg'] = $this->request->getPost('url_bg_externa');
        }

        return $data;
    }

    private function procesarGeneros($contenidoId, $datos)
    {
        if (empty($datos))
            return;

        $generosMixtos = is_string($datos) ? json_decode($datos, true) : $datos;
        if (!is_array($generosMixtos))
            return;

        $db = \Config\Database::connect();
        $generoModel = new GeneroModel();

        $diccionario = [
            'Action' => 'Acción',
            'Adventure' => 'Aventura',
            'Aventure' => 'Aventura',
            'Sci-Fi' => 'Ciencia Ficción',
            'Science Fiction' => 'Ciencia Ficción',
            'Animation' => 'Animación',
            'Comedy' => 'Comedia',
            'Crime' => 'Crimen',
            'Documentary' => 'Documental',
            'Drama' => 'Drama',
            'Family' => 'Familiar',
            'Fantasy' => 'Fantasía',
            'History' => 'Historia',
            'Horror' => 'Terror',
            'Music' => 'Música',
            'Mystery' => 'Misterio',
            'Romance' => 'Romance',
            'Thriller' => 'Terror',
            'War' => 'Bélica',
            'Western' => 'Western',
            'TV Movie' => 'Película de TV'
        ];

        foreach ($generosMixtos as $dato) {
            if (is_numeric($dato)) {
                $gid = $dato;
            } else {
                $nombreLimpio = trim($dato);
                if (empty($nombreLimpio))
                    continue;


                $nombreKey = ucwords(strtolower($nombreLimpio));

                if (array_key_exists($nombreKey, $diccionario)) {
                    $nombreFinal = $diccionario[$nombreKey];
                } else {
                    $nombreFinal = $nombreLimpio;
                }

                $genero = $generoModel->where('nombre', $nombreFinal)->first();

                if ($genero) {
                    $gid = $genero['id'];
                } else {
                    // Si no existe, lo creamos
                    $generoModel->insert(['nombre' => $nombreFinal]);
                    $gid = $generoModel->getInsertID();
                }
            }


            $db->table('contenido_genero')->ignore(true)->insert([
                'contenido_id' => $contenidoId,
                'genero_id' => $gid
            ]);
        }
    }

    private function procesarActores($contenidoId, $json)
    {
        if (empty($json))
            return;
        $actores = json_decode($json, true);
        if (!is_array($actores))
            return;

        $db = \Config\Database::connect();

        foreach ($actores as $actorData) {
            $nombre = trim($actorData['name'] ?? '');
            if (empty($nombre))
                continue;

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
                    : 'https://ui-avatars.com/api/?name=' . urlencode($nombre) . '&background=random';

                $db->table('actores')->insert(['nombre' => $nombre, 'foto' => $foto]);
                $aid = $db->insertID();
            }

            // Vincular con Personaje
            $personaje = $actorData['character'] ?? '';
            $db->table('contenido_actor')->ignore(true)->insert([
                'contenido_id' => $contenidoId,
                'actor_id' => $aid,
                'personaje' => $personaje
            ]);
        }
    }

    private function procesarDirectores($contenidoId, $json)
    {
        if (empty($json))
            return;
        $directores = json_decode($json, true);
        if (!is_array($directores))
            return;

        $db = \Config\Database::connect();

        foreach ($directores as $dirData) {
            $nombre = trim($dirData['name'] ?? '');
            if (empty($nombre))
                continue;

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
                'director_id' => $did
            ]);
        }
    }
}