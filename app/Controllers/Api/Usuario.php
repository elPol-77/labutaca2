<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Usuario extends BaseController
{
    public function toggleLista()
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'No logueado']);
        }

        $userId = session()->get('user_id');
        $peliId = $this->request->getPost('id');
        
        $db = \Config\Database::connect();
        $builder = $db->table('mi_lista');

        // Comprobar si ya existe
        $existe = $builder->where(['usuario_id' => $userId, 'contenido_id' => $peliId])->countAllResults();

        $accion = '';
        
        if ($existe) {
            // BORRAR
            $builder->where(['usuario_id' => $userId, 'contenido_id' => $peliId])->delete();
            $accion = 'removed';
        } else {
            // AÑADIR
            $builder->insert(['usuario_id' => $userId, 'contenido_id' => $peliId]);
            $accion = 'added';
        }

        return $this->response->setJSON([
            'status' => 'success', 
            'action' => $accion,
            'token'  => csrf_hash() 
        ]);
    }
}