<?php

namespace App\Models;

use CodeIgniter\Model;

class MiListaModel extends Model
{
    protected $table            = 'mi_lista';
    protected $primaryKey       = 'usuario_id'; 
    protected $allowedFields    = ['usuario_id', 'contenido_id', 'fecha_agregado'];
    protected $useTimestamps    = false;

    public function getListaUsuario($userId)
    {
        return $this->select('contenidos.*, mi_lista.fecha_agregado')
                    ->join('contenidos', 'contenidos.id = mi_lista.contenido_id')
                    ->where('mi_lista.usuario_id', $userId)
                    ->orderBy('mi_lista.fecha_agregado', 'DESC')
                    ->findAll();
    }
}