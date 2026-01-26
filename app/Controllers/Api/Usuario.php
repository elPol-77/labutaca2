<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UsuarioModel;

class Usuario extends ResourceController
{
    protected $format = 'json';

    // 1. AÑADIR O QUITAR DE MI LISTA
    public function toggle()
    {
        if (!session()->get('is_logged_in')) return $this->failUnauthorized();

        $userId = session()->get('user_id');
        $contenidoId = $this->request->getPost('id');
        
        if (!$contenidoId) return $this->fail('Falta el ID del contenido');

        $db = \Config\Database::connect();
        $builder = $db->table('mi_lista');

        // Comprobar si existe
        $existe = $builder->where(['usuario_id' => $userId, 'contenido_id' => $contenidoId])->countAllResults();

        $action = '';
        if ($existe > 0) {
            // Borrar
            $builder->where(['usuario_id' => $userId, 'contenido_id' => $contenidoId])->delete();
            $action = 'removed';
        } else {
            // Añadir
            $builder->insert([
                'usuario_id' => $userId, 
                'contenido_id' => $contenidoId,
                'fecha_agregado' => date('Y-m-d H:i:s')
            ]);
            $action = 'added';
        }

        return $this->respond([
            'status' => 'success',
            'action' => $action,
            'token'  => csrf_hash() // Devolvemos nuevo token CSRF
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
        
        $lista = $builder->get()->getResultArray();

        // Arreglar imágenes
        foreach ($lista as &$item) {
            if (!str_starts_with($item['imagen'], 'http')) {
                $item['imagen'] = base_url('assets/img/') . $item['imagen'];
            }
        }

        return $this->respond(['data' => $lista]);
    }
}