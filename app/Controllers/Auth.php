<?php namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    public function index()
    {
        // 1. Si ya está logueado, al Home
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        // 2. RECUPERAR USUARIOS (¡ESTO FALTABA!)
        $model = new UsuarioModel();
        // Los ordenamos alfabéticamente
        $data['usuarios'] = $model->orderBy('username', 'ASC')->findAll(); 

        // 3. Cargar la vista pasándole los datos
        return view('frontend/profiles', $data);
    }

    public function login()
    {
        $id = $this->request->getPost('id');
        $password = $this->request->getPost('password');

        $model = new UsuarioModel();
        $user = $model->find($id);

        // Token de seguridad nuevo
        $newToken = csrf_hash();

        if ($user && password_verify($password, $user['password'])) {
            
            // Guardar datos en sesión
            session()->set([
                'user_id'      => $user['id'],
                'username'     => $user['username'],
                'plan_id'      => $user['plan_id'], 
                'rol'          => $user['rol'],
                'avatar'       => $user['avatar'],
                'is_logged_in' => true
            ]);
            
            // --- ACTIVAR INTRO PARA LA HOME ---
            session()->setFlashdata('mostrar_intro', true);
            // ----------------------------------

            return $this->response->setJSON([
                'status' => 'success', 
                'token'  => $newToken
            ]);

        } else {
            return $this->response->setJSON([
                'status' => 'error', 
                'msg'    => 'Contraseña incorrecta',
                'token'  => $newToken
            ]);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}