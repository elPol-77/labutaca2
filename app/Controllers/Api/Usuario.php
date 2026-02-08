<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Usuario extends ResourceController
{
    protected $format = 'json';
    private $omdbApiKey = '6387e3c183c454304108333c56530988'; // Tu API Key

    // 1. AÑADIR O QUITAR DE MI LISTA
    public function toggle()
    {
        if (!session()->get('is_logged_in')) return $this->failUnauthorized();

        $userId = session()->get('user_id');
        $idRecibido = $this->request->getPost('id');
        
        if (!$idRecibido) return $this->fail('Falta el ID del contenido');

        $contenidoIdReal = $idRecibido;

        // ---------------------------------------------------------
        // A. DETECTAR SI ES CONTENIDO EXTERNO (API)
        // ---------------------------------------------------------
        // Usamos strpos para evitar error 500 en versiones antiguas de PHP
        if (strpos((string)$idRecibido, 'ext-') === 0) {
            
            $imdbId = str_replace('ext-', '', $idRecibido);
            
            // Intentamos obtener el ID local (o importarlo si no existe)
            $contenidoIdReal = $this->_obtenerOImportarContenido($imdbId);

            if (!$contenidoIdReal) {
                // Si falla, devolvemos mensaje en vez de Error 500
                return $this->fail('Error importando contenido. Revisa los logs.');
            }
        }

        // ---------------------------------------------------------
        // B. LÓGICA DE FAVORITOS (Normal)
        // ---------------------------------------------------------
        $db = \Config\Database::connect();
        $builder = $db->table('mi_lista');

        // Comprobar si existe en la lista
        $existe = $builder->where(['usuario_id' => $userId, 'contenido_id' => $contenidoIdReal])->countAllResults();

        $action = '';
        if ($existe > 0) {
            // Borrar
            $builder->where(['usuario_id' => $userId, 'contenido_id' => $contenidoIdReal])->delete();
            $action = 'removed';
        } else {
            // Añadir
            $builder->insert([
                'usuario_id' => $userId, 
                'contenido_id' => $contenidoIdReal,
                'fecha_agregado' => date('Y-m-d H:i:s')
            ]);
            $action = 'added';
        }

        return $this->respond([
            'status' => 'success',
            'action' => $action,
            'token'  => csrf_hash()
        ]);
    }

    // 2. OBTENER MI LISTA COMPLETA
    public function getLista()
    {
        if (!session()->get('is_logged_in')) return $this->failUnauthorized();
        $userId = session()->get('user_id');

        $db = \Config\Database::connect();
        $builder = $db->table('mi_lista ml');
        $builder->select('c.id, c.titulo, c.imagen, c.edad_recomendada');
        $builder->join('contenidos c', 'c.id = ml.contenido_id');
        $builder->where('ml.usuario_id', $userId);
        
        // Ordenamos por fecha de agregado
        $builder->orderBy('ml.fecha_agregado', 'DESC');
        
        $lista = $builder->get()->getResultArray();

        // Arreglar imágenes
        foreach ($lista as &$item) {
            if (strpos($item['imagen'], 'http') !== 0) {
                $item['imagen'] = base_url('assets/img/') . $item['imagen'];
            }
        }

        return $this->respond(['data' => $lista]);
    }

    // =========================================================
    // FUNCIÓN PRIVADA: IMPORTAR DESDE OMDB
    // =========================================================
    private function _obtenerOImportarContenido($imdbId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('contenidos');

        // 1. Comprobar si ya existe localmente
        try {
            $local = $builder->where('imdb_id', $imdbId)->get()->getRowArray();
            if ($local) return $local['id'];
        } catch (\Exception $e) {
            // Si falla la consulta, probablemente falta la columna imdb_id
            log_message('critical', 'Falta columna imdb_id en la tabla contenidos');
            return false;
        }

        // 2. Si no existe, conectamos a la API para bajar datos
        $client = \Config\Services::curlrequest();
        
        try {
            $url = "http://www.omdbapi.com/?i={$imdbId}&apikey={$this->omdbApiKey}&plot=full";
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody(), true);

            if (isset($data['Response']) && $data['Response'] === 'True') {
                
                $tipoId = (strtolower($data['Type']) == 'series') ? 2 : 1;
                $runtime = intval($data['Runtime']); 
                $year = intval($data['Year']); // Sacamos el año como entero
                $rating = isset($data['imdbRating']) && is_numeric($data['imdbRating']) ? $data['imdbRating'] : 0.0;

                // ARRAY DE INSERCIÓN CORREGIDO (Sin fecha_lanzamiento)
                $nuevoContenido = [
                    'titulo'           => $data['Title'],
                    'descripcion'      => $data['Plot'],
                    'imagen'           => $data['Poster'], 
                    'imagen_bg'        => $data['Poster'],
                    'tipo_id'          => $tipoId,
                    'anio'             => $year,  // <--- CORREGIDO: Usamos 'anio' que sí existe en tu BD
                    'duracion'         => $runtime,
                    'edad_recomendada' => 12,
                    'nivel_acceso'     => 1, 
                    'imdb_id'          => $imdbId,
                    'imdb_rating'      => $rating, // <--- Aprovechamos tu columna imdb_rating
                    'destacada'        => 0,
                    'vistas'           => 0
                ];

                $builder->insert($nuevoContenido);
                return $db->insertID(); 
            }

        } catch (\Exception $e) {
            log_message('error', 'Error importando desde OMDb: ' . $e->getMessage());
            return false;
        }

        return false;
    }
}