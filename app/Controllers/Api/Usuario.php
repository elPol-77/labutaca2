<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Usuario extends ResourceController
{
    protected $format = 'json';

    private $tmdbApiKey = '6387e3c183c454304108333c56530988';

    public function toggle()
    {
        // 1. Check de Sesión
        if (!session()->get('is_logged_in')) {
            return $this->failUnauthorized('No hay sesión activa');
        }

        $userId = session()->get('user_id');
        $idRecibido = $this->request->getPost('id');

        if (!$idRecibido)
            return $this->fail('Falta el ID del contenido');

        $contenidoIdReal = null;

        try {
            // Limpiamos el ID
            $idLimpio = $this->_limpiarId($idRecibido);

            // Verificamos si existe en local
            $db = \Config\Database::connect();

            $query = $db->table('contenidos')->groupStart()
                ->where('id', $idLimpio)
                ->orWhere('imdb_id', $idLimpio)
                ->groupEnd()
                ->get()->getRowArray();

            if ($query) {
                // YA EXISTE EN LOCAL: Usamos el ID que ya tiene
                $contenidoIdReal = $query['id'];
            } else {
                // NO EXISTE: Importamos de TMDB
                $contenidoIdReal = $this->_importarDesdeTMDB($idLimpio, $idRecibido);

                if (!$contenidoIdReal) {
                    return $this->fail('No se pudo importar. ID inválido.');
                }
            }

            // B. GESTIONAR FAVORITOS
            $builder = $db->table('mi_lista');
            $existeEnLista = $builder->where(['usuario_id' => $userId, 'contenido_id' => $contenidoIdReal])->countAllResults();

            $action = '';
            if ($existeEnLista > 0) {
                $builder->where(['usuario_id' => $userId, 'contenido_id' => $contenidoIdReal])->delete();
                $action = 'removed';
            } else {
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
                'token' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->failServerError('Error Interno: ' . $e->getMessage());
        }
    }

    public function getLista()
    {
        if (!session()->get('is_logged_in'))
            return $this->failUnauthorized();
        $userId = session()->get('user_id');

        $db = \Config\Database::connect();
        $builder = $db->table('mi_lista ml');
        $builder->select('c.id, c.titulo, c.imagen, c.edad_recomendada');
        $builder->join('contenidos c', 'c.id = ml.contenido_id');
        $builder->where('ml.usuario_id', $userId);
        $builder->orderBy('ml.fecha_agregado', 'DESC');

        $lista = $builder->get()->getResultArray();

        foreach ($lista as &$item) {
            if (strpos($item['imagen'], 'http') !== 0) {
                $item['imagen'] = base_url('assets/img/') . $item['imagen'];
            }
        }

        return $this->respond(['data' => $lista]);
    }

    // Limpiamos el id 
    private function _limpiarId($idSucio)
    {
        if (strpos((string) $idSucio, 'tt') !== false) {
            return $idSucio;
        }
        return preg_replace('/[^0-9]/', '', (string) $idSucio);
    }


    private function _importarDesdeTMDB($externalId, $originalString = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table('contenidos');
        $client = \Config\Services::curlrequest();
        $options = ['http_errors' => false, 'verify' => false];

        $finalTmdbId = $externalId;
        $tipoContenido = 'movie';

        if (strpos($originalString, '_tv_') !== false || strpos($originalString, 'tv_') !== false) {
            $tipoContenido = 'tv';
        }


        if (strpos($externalId, 'tt') === 0) {
            $urlFind = "https://api.themoviedb.org/3/find/{$externalId}?api_key={$this->tmdbApiKey}&external_source=imdb_id";
            try {
                $respFind = $client->request('GET', $urlFind, $options);
                $dataFind = json_decode($respFind->getBody(), true);
                if (!empty($dataFind['movie_results'])) {
                    $finalTmdbId = $dataFind['movie_results'][0]['id'];
                    $tipoContenido = 'movie';
                } elseif (!empty($dataFind['tv_results'])) {
                    $finalTmdbId = $dataFind['tv_results'][0]['id'];
                    $tipoContenido = 'tv';
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        // 3. Obtener Datos Principales
        $url = "https://api.themoviedb.org/3/{$tipoContenido}/{$finalTmdbId}?api_key={$this->tmdbApiKey}&language=es-ES";
        try {
            $response = $client->request('GET', $url, $options);
            if ($response->getStatusCode() !== 200 && strpos($externalId, 'tt') === false) {
                $tipoContenido = ($tipoContenido == 'movie') ? 'tv' : 'movie';
                $url = "https://api.themoviedb.org/3/{$tipoContenido}/{$finalTmdbId}?api_key={$this->tmdbApiKey}&language=es-ES";
                $response = $client->request('GET', $url, $options);
            }
            if ($response->getStatusCode() !== 200)
                return false;
            $data = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return false;
        }

        // 4. GUARDAR EN BD
        if (!empty($data)) {

            $trailerUrl = null;
            try {
                $urlVideos = "https://api.themoviedb.org/3/{$tipoContenido}/{$finalTmdbId}/videos?api_key={$this->tmdbApiKey}&language=es-ES";
                $respVideos = $client->request('GET', $urlVideos, $options);

                if ($respVideos->getStatusCode() === 200) {
                    $dataVideos = json_decode($respVideos->getBody(), true);
                    if (!empty($dataVideos['results'])) {
                        foreach ($dataVideos['results'] as $vid) {
                            if ($vid['site'] === 'YouTube' && $vid['type'] === 'Trailer') {
                                $trailerUrl = "https://www.youtube.com/embed/" . $vid['key'];
                                break;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
            }
            // ------------------------------------------------------

            $esSerie = ($tipoContenido == 'tv');
            $titulo = $esSerie ? ($data['name'] ?? 'Sin título') : ($data['title'] ?? 'Sin título');
            $fecha = $esSerie ? ($data['first_air_date'] ?? '') : ($data['release_date'] ?? '');
            $anio = (!empty($fecha)) ? intval(substr($fecha, 0, 4)) : 0;

            $duracion = 0;
            if (!$esSerie && isset($data['runtime']))
                $duracion = intval($data['runtime']);
            if ($esSerie && !empty($data['episode_run_time']))
                $duracion = intval($data['episode_run_time'][0]);

            $baseImgUrl = 'https://image.tmdb.org/t/p/w500';
            $bgImgUrl = 'https://image.tmdb.org/t/p/original';
            $poster = isset($data['poster_path']) ? $baseImgUrl . $data['poster_path'] : '';
            $backdrop = isset($data['backdrop_path']) ? $bgImgUrl . $data['backdrop_path'] : $poster;

            $nuevoContenido = [

                'id' => $originalString ?: $externalId,
                'titulo' => substr($titulo, 0, 199),
                'descripcion' => $data['overview'] ?? '',
                'imagen' => $poster,
                'imagen_bg' => $backdrop,
                'tipo_id' => $esSerie ? 2 : 1,
                'anio' => $anio,
                'duracion' => $duracion,
                'edad_recomendada' => ($data['adult'] ?? false) ? 18 : 12,
                'nivel_acceso' => 2,
                'url_video' => $trailerUrl,
                'imdb_id' => (string) $finalTmdbId,
                'imdb_rating' => isset($data['vote_average']) ? (float) $data['vote_average'] : 0.0,
                'destacada' => 0,
                'vistas' => 0
            ];

            try {
                $builder->insert($nuevoContenido);
                return $nuevoContenido['id'];
            } catch (\Exception $e) {
                log_message('error', 'Error SQL Import: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }
}