<?php namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; 


    protected $allowedFields = [
        'username', 
        'email',          
        'password', 
        'rol', 
        'plan_id', 
        'avatar', 
        'fecha_registro'  ,
        'fecha_fin_suscripcion'
    ];
    
    public function getListaIds($usuarioId)
{

    $db = \Config\Database::connect();
    $builder = $db->table('mi_lista');
    $builder->select('contenido_id');
    $builder->where('usuario_id', $usuarioId);
    
    $query = $builder->get();
    
    return array_column($query->getResultArray(), 'contenido_id');
}
}