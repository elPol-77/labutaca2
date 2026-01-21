<?php namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Devuelve los resultados como array asociativo

    /* * 1. ALLOWED FIELDS (Campos Permitidos)
     * Aquí definimos qué columnas se pueden guardar/editar.
     * Faltaba 'email' y 'fecha_registro'.
     */
    protected $allowedFields = [
        'username', 
        'email',           // <-- FALTABA ESTE
        'password', 
        'rol', 
        'plan_id', 
        'avatar', 
        'fecha_registro'   // <-- FALTABA ESTE
    ];

    /*
     * 2. GESTIÓN AUTOMÁTICA DE FECHAS
     * CodeIgniter puede rellenar la fecha solo.
     * Le decimos que use tu columna 'fecha_registro' como 'created_at'.
     */
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro'; 
    protected $updatedField  = ''; // No tienes campo de 'updated_at' en tu SQL, lo dejamos vacío.

    /*
     * 3. REGLAS DE VALIDACIÓN (Puntos extra en TFG)
     * Esto protege tu base de datos de datos basura.
     */
    protected $validationRules = [
        'username' => 'required|min_length[3]|is_unique[usuarios.username]',
        'email'    => 'required|valid_email|is_unique[usuarios.email]',
        'password' => 'required|min_length[4]',
        'plan_id'  => 'required|integer'
    ];
}