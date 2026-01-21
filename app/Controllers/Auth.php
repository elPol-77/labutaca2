<?php namespace App\Controllers;
use App\Models\UsuarioModel;

class Auth extends BaseController
{
    // Muestra la pantalla de selección de perfiles
    public function index()
    {
        // Si ya está logueado, lo mandamos al catálogo directo
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        $model = new UsuarioModel();
        $data['usuarios'] = $model->findAll(); // Traemos todos los perfiles

        return view('frontend/profiles', $data);
    }

    // Procesa el login vía AJAX
    public function login()
    {
        $id = $this->request->getPost('id');
        $password = $this->request->getPost('password');

        $model = new UsuarioModel();
        $user = $model->find($id);

        if ($user && $user['password'] == $password) {
            // Login correcto: Guardamos datos en sesión
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'plan_id' => $user['plan_id'], // ¡IMPORTANTE PARA EL FILTRO!
                'rol' => $user['rol'],
                'avatar' => $user['avatar'],
                'is_logged_in' => true
            ]);
            
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Contraseña incorrecta']);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}