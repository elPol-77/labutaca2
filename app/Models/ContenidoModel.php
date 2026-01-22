<?php namespace App\Models;

use CodeIgniter\Model;

class ContenidoModel extends Model
{
    protected $table = 'contenidos';
    protected $primaryKey = 'id';
    
    // Campos que permitimos leer/escribir
    protected $allowedFields = [
        'tipo_id', 'titulo', 'descripcion', 'anio', 
        'duracion', 'imagen', 'imagen_bg', 'url_video', 
        'nivel_acceso', 'vistas', 'destacada','edad_recomendada'
    ];

    // Función auxiliar (Mantiene nombre original, pero trae todo)
    public function getPeliculas($planUsuario = 1) {
        if ($planUsuario == 2) {
            // Premium ve todo
            return $this->findAll();
        } else {
            // Free solo ve contenido nivel 1
            return $this->where('nivel_acceso', 1)->findAll();
        }
    }

    // Obtener detalles completos con Actores y Géneros
    public function getDetallesCompletos($id)
    {
        // 1. Datos básicos
        $peli = $this->find($id);
        if (!$peli) return null;

        // 2. Traer Géneros (Relación N:M)
        $db = \Config\Database::connect();
        $builder = $db->table('contenido_genero');
        $builder->select('generos.nombre');
        $builder->join('generos', 'generos.id = contenido_genero.genero_id');
        $builder->where('contenido_id', $id);
        $peli['generos'] = $builder->get()->getResultArray();

        // 3. Traer Actores (Relación N:M)
        $builder = $db->table('contenido_actor');
        $builder->select('actores.nombre, actores.foto, contenido_actor.personaje');
        $builder->join('actores', 'actores.id = contenido_actor.actor_id');
        $builder->where('contenido_id', $id);
        $peli['actores'] = $builder->get()->getResultArray();

        return $peli;
    }

    // Obtener el director de un contenido concreto
    public function getDirector($contenidoId)
    {
        $builder = $this->db->table('contenido_director cd');
        $builder->select('d.id, d.nombre');
        $builder->join('directores d', 'd.id = cd.director_id');
        $builder->where('cd.contenido_id', $contenidoId);
        
        return $builder->get()->getRowArray(); // Devolvemos solo un director (el principal)
    }

    // Obtener todo el contenido de un director (Mantiene nombre original)
    public function getPeliculasPorDirector($directorId)
    {
        $builder = $this->select('contenidos.*');
        $builder->join('contenido_director cd', 'cd.contenido_id = contenidos.id');
        $builder->where('cd.director_id', $directorId);
        // Se eliminó la línea: $builder->where('contenidos.tipo_id', 1);
        
        return $builder->orderBy('contenidos.anio', 'DESC')->findAll();
    }
    
    // Función auxiliar para saber el nombre del director por su ID
    public function getNombreDirector($directorId)
    {
        $getRow = $this->db->table('directores')->select('nombre')->where('id', $directorId)->get()->getRowArray();
        return $getRow ? $getRow['nombre'] : 'Director';
    }

public function getPeliculasPaginadas($planId, $limit, $offset)
    {
        $builder = $this->where('tipo_id', 1); // Solo películas

        // --- LÓGICA DE FILTRADO DE PLANES ---
        
        if ($planId == 3) {

            $builder->where('edad_recomendada <=', 11);
        } 
        elseif ($planId == 1) {
            // CASO PLAN FREE: Solo contenido de nivel 1
            $builder->where('nivel_acceso', 1);
        }
        // CASO PREMIUM (2): Ve todo, no aplicamos 'where' extra.

        // ------------------------------------

        return $builder->orderBy('fecha_agregada', 'DESC')
                       ->findAll($limit, $offset);
    }
}