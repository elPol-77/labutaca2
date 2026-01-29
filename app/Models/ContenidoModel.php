<?php
namespace App\Models;

use CodeIgniter\Model;

class ContenidoModel extends Model
{
    protected $table = 'contenidos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tipo_id', 'titulo', 'descripcion', 'anio', 'duracion',
        'imagen', 'imagen_bg', 'url_video', 'nivel_acceso',
        'vistas', 'destacada', 'edad_recomendada',
        'imdb_rating', 'fecha_agregada'
    ];

    // =========================================================
    // HELPER PRIVADO: LÓGICA CENTRALIZADA DE SEGURIDAD
    // =========================================================
    protected function _aplicarFiltrosPlan($builder, $planId)
    {
        // CASO 1: USUARIO FREE (ID 1)
        if ($planId == 1) {
            $builder->where('contenidos.nivel_acceso', 1);
        }
        // CASO 2: USUARIO KIDS (ID 3)
        elseif ($planId == 3) {
            $builder->where('contenidos.edad_recomendada <=', 11);
        }
        // CASO 3: PREMIUM (ID 2) - Ve todo
        
        return $builder;
    }

    // =========================================================
    // FUNCIONES DEL CATÁLOGO
    // =========================================================

    public function getTendencias($limit = 10, $planId = 1)
    {
        $builder = $this->select('contenidos.*')->orderBy('vistas', 'DESC');
        $this->_aplicarFiltrosPlan($builder, $planId);
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

        return $builder->orderBy('contenidos.fecha_agregada', 'DESC')->findAll($limit);
    }

    // =========================================================
    // [MODIFICADO] PARA QUE EL FILTRO FUNCIONE CON TEXTO
    // =========================================================
    public function getContenidoPaginadas($planId, $limit, $offset, $generoNombre = null)
    {
        // 1. Seleccionamos contenidos de tipo Película (1)
        $builder = $this->select('contenidos.*')
                        ->where('contenidos.tipo_id', 1);

        // 2. Si viene un Género (Texto: "Terror", "Ciencia Ficcion")
        if (!empty($generoNombre)) {
            // Decodificamos por si viene con %20
            $nombreLimpio = urldecode($generoNombre);

            // Hacemos el doble JOIN para llegar al nombre:
            // Contenidos -> Pivot (contenido_genero) -> Generos
            $builder->join('contenido_genero cg', 'contenidos.id = cg.contenido_id')
                    ->join('generos g', 'cg.genero_id = g.id')
                    ->where('g.nombre', $nombreLimpio);
        }

        // 3. Aplicamos seguridad (Kids/Free)
        $this->_aplicarFiltrosPlan($builder, $planId);

        // 4. Orden y Paginación
        return $builder->orderBy('contenidos.fecha_agregada', 'DESC')
                       ->findAll($limit, $offset);
    }

    // --- FUNCIONES EXTRA (Detalles, Favoritos, etc) ---

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
        if (!$peli) return null;
        
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
    { /* Tu código */
    }
    public function getPeliculasPorDirector($id)
    { /* Tu código */
    }
    public function getNombreDirector($id)
    { /* Tu código */
    }
}


