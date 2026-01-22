<?php namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
public function index()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        $model = new UsuarioModel();
        
        // FILTRO: Solo usuarios con ID entre 2 y 4
        $data['usuarios'] = $model->where('id >=', 2)
                                  ->where('id <=', 4)
                                  ->orderBy('username', 'ASC')
                                  ->findAll(); 

        return view('frontend/profiles', $data);
    }

    public function login()
        {
            $id = $this->request->getPost('id');
            $password = $this->request->getPost('password');

            $model = new UsuarioModel();
            $user = $model->find($id);

            $newToken = csrf_hash();

            if (!$user) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'Usuario no encontrado', 'token' => $newToken]);
            }

            // --- LÓGICA DE ACCESO ---
            // Acceso concedido SI: (Es Plan Kids) O (La contraseña es correcta)
            $accesoConcedido = ($user['plan_id'] == 3) || password_verify($password, $user['password']);

            if ($accesoConcedido) {
                
                session()->set([
                    'user_id'      => $user['id'],
                    'username'     => $user['username'],
                    'plan_id'      => $user['plan_id'], 
                    'rol'          => $user['rol'],
                    'avatar'       => $user['avatar'],
                    'is_logged_in' => true
                ]);
                
                // Activar Intro
                session()->setFlashdata('mostrar_intro', true);

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