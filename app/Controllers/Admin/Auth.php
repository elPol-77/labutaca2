<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Auth extends BaseController
{
    // 1. Muestra el formulario (GET admin/login)
    public function index()
    {
        // Si ya es admin, para adentro
        if (session()->get('is_logged_in') && session()->get('rol') === 'admin') {
            return redirect()->to('admin');
        }
        return view('auth/login'); 
    }

    // 2. Procesa el formulario (POST admin/auth/login)
    public function login()
    {
        // Recogemos lo que has puesto en el input "username"
        $usuarioOEmail = $this->request->getPost('username'); 
        $password      = $this->request->getPost('password');

        $model = new UsuarioModel();
        
        // Buscamos si existe ese string como EMAIL o como USUARIO
        // Y además aseguramos que sea ADMIN
        $user = $model->groupStart()
                        ->where('email', $usuarioOEmail)
                        ->orWhere('username', $usuarioOEmail)
                      ->groupEnd()
                      ->where('rol', 'admin') // IMPORTANTE: Solo admins
                      ->first();

        // Verificamos contraseña
        if ($user && password_verify($password, $user['password'])) {
            
            session()->set([
                'user_id'      => $user['id'],
                'username'     => $user['username'],
                'rol'          => $user['rol'],
                'is_logged_in' => true
            ]);

            return redirect()->to('admin'); 
        }

        // Si falla
        return redirect()->back()->with('msg', 'Acceso denegado. Verifica tus credenciales.');
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('admin/login');
    }
}