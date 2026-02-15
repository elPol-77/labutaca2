<?php

namespace App\Models;

use CodeIgniter\Model;

class ContenidoModel extends Model
{
    protected $table = 'contenidos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tipo_id',
        'titulo',
        'descripcion',
        'anio',
        'duracion',
        'imagen',
        'imagen_bg',
        'url_video',
        'nivel_acceso',
        'vistas',
        'destacada',
        'edad_recomendada',
        'imdb_id',
        'imdb_rating',
        'fecha_agregada' 
    ];

    protected function _aplicarFiltrosPlan($builder, $planId)
    {
        $planId = 1;

        if ($planId == 1) {
            $builder->where('contenidos.nivel_acceso', 1);
        }

        elseif ($planId == 3) {
            $builder->where('contenidos.edad_recomendada <=', 11);
        }


        return $builder;
    }

    public function getTendencias($limit = 10, $planId = 1)
    {
        $builder = $this->select('contenidos.*')->orderBy('vistas', 'DESC');
        $this->_aplicarFiltrosPlan($builder, $planId); // <--- APLICA FILTRO
        return $builder->findAll($limit);
    }

    public function getContentRandom($tipoId, $limit = 10, $planId = 1)
    {
        $builder = $this->where('tipo_id', $tipoId);
        $this->_aplicarFiltrosPlan($builder, $planId);
        return $builder->orderBy('RAND()')->findAll($limit);
    }

    public function getPorGenero($generoId, $tipoId, $limit = 10, $excluirIds = [], $planId = 1)
    {
        $builder = $this->select('contenidos.*')
            ->join('contenido_genero cg', 'contenidos.id = cg.contenido_id')
            ->where('cg.genero_id', $generoId)
            ->where('contenidos.tipo_id', $tipoId);
        $this->_aplicarFiltrosPlan($builder, $planId);

        if (!empty($excluirIds)) {
            $builder->whereNotIn('contenidos.id', $excluirIds);
        }
        $builder->groupBy('contenidos.id');
        return $builder->orderBy('contenidos.fecha_agregada', 'DESC')->findAll($limit);
    }

    public function getContenidoPaginadas($planId, $limit, $offset, $generoNombre = null)
    {
        $builder = $this->select('contenidos.*');

        // 2. Si viene un Género
        if (!empty($generoNombre)) {
            $nombreLimpio = urldecode($generoNombre);
            $builder->join('contenido_genero cg', 'contenidos.id = cg.contenido_id')
                ->join('generos g', 'cg.genero_id = g.id')
                ->where('g.nombre', $nombreLimpio);
        }

        $this->_aplicarFiltrosPlan($builder, $planId);

        return $builder->orderBy('contenidos.fecha_agregada', 'DESC')
            ->findAll($limit, $offset);
    }


    public function getGeneroFavoritoUsuario($userId, $tipoContenidoId)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT g.id, g.nombre, COUNT(g.id) as frecuencia
                FROM mi_lista ml
                JOIN contenidos c ON ml.contenido_id = c.id
                JOIN contenido_genero cg ON c.id = cg.contenido_id
                JOIN generos g ON cg.genero_id = g.id
                WHERE ml.usuario_id = ? AND c.tipo_id = ?
                GROUP BY g.id
                ORDER BY frecuencia DESC LIMIT 1";

        $query = $db->query($sql, [$userId, $tipoContenidoId]);
        return $query->getRowArray();
    }

    public function getDetallesCompletos($id)
    {
        $peli = $this->find($id);
        if (!$peli)
            return null;

        $db = \Config\Database::connect();

        // Géneros
        $builder = $db->table('contenido_genero');
        $builder->select('generos.nombre')
            ->join('generos', 'generos.id = contenido_genero.genero_id')
            ->where('contenido_id', $id);
        $peli['generos'] = $builder->get()->getResultArray();

        // Actores
        $builder = $db->table('contenido_actor');
        $builder->select('actores.nombre, actores.foto, contenido_actor.personaje')
            ->join('actores', 'actores.id = contenido_actor.actor_id')
            ->where('contenido_id', $id);
        $peli['actores'] = $builder->get()->getResultArray();

        return $peli;
    }

    public function getDirector($id)
    {
        $db = \Config\Database::connect();
        return $db->table('directores')
            ->select('directores.*')
            ->join('contenido_director', 'contenido_director.director_id = directores.id')
            ->where('contenido_director.contenido_id', $id)
            ->get()
            ->getRowArray();
    }

    public function getNombreDirector($id)
    {
        $db = \Config\Database::connect();
        $director = $db->table('directores')->where('id', $id)->get()->getRow();
        return $director ? $director->nombre : 'Desconocido';
    }

    public function getPeliculasPorDirector($directorId, $planId = 1)
    {
        $builder = $this->select('contenidos.*')
            ->join('contenido_director', 'contenidos.id = contenido_director.contenido_id')
            ->where('contenido_director.director_id', $directorId);

        $this->_aplicarFiltrosPlan($builder, $planId);

        return $builder->findAll();
    }

}