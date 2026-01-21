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
        'nivel_acceso', 'vistas', 'destacada'
    ];

    // Función auxiliar para sacar solo películas (tipo_id = 1)
    public function getPeliculas($planUsuario = 1) {
        if ($planUsuario == 2) {
            // Premium ve todo
            return $this->where('tipo_id', 1)->findAll();
        } else {
            // Free solo ve contenido nivel 1
            return $this->where('tipo_id', 1)->where('nivel_acceso', 1)->findAll();
        }

    }
}