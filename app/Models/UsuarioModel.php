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
    

    /*
     * 2. GESTIÓN AUTOMÁTICA DE FECHAS
     * CodeIgniter puede rellenar la fecha solo.
     * Le decimos que use tu columna 'fecha_registro' como 'created_at'.
     */
    // protected $useTimestamps = true;
    // protected $createdField  = 'fecha_registro'; 
    // protected $updatedField  = ''; // No tienes campo de 'updated_at' en tu SQL, lo dejamos vacío.

    // /*
    //  * 3. REGLAS DE VALIDACIÓN (Puntos extra en TFG)
    //  * Esto protege tu base de datos de datos basura.
    //  */
    // protected $validationRules = [
    //     'username' => 'required|min_length[3]|is_unique[usuarios.username]',
    //     'email'    => 'required|valid_email|is_unique[usuarios.email]',
    //     'password' => 'required|min_length[4]',
    //     'plan_id'  => 'required|integer'
    // ];
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