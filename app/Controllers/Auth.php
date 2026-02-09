<?php 
namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Controllers\BaseController;

class Auth extends BaseController
{
    // =========================================================================
    // 🟢 TU CÓDIGO ORIGINAL (INTACTO)
    // =========================================================================

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
                'is_logged_in' => true,
                // Mantenemos tu línea del avatar fallback tal cual
                'avatar'       => $user['avatar'] ?? 'https://i.pinimg.com/564x/1b/a2/e6/1ba2e6d1d4874546c70c91f1024e17fb.jpg',
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

    // =========================================================================
    // 🟡 LO NUEVO (PARA EL MODAL Y BOTÓN +)
    // =========================================================================

    public function login_general()
    {
        $emailOrUser = $this->request->getPost('email');
        $password    = $this->request->getPost('password');

        $model = new UsuarioModel();
        
        // Busca en TODA la base de datos (sin restricción de ID)
        $user = $model->groupStart()
                        ->where('email', $emailOrUser)
                        ->orWhere('username', $emailOrUser)
                      ->groupEnd()
                      ->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'user_id'      => $user['id'],
                'username'     => $user['username'],
                'email'        => $user['email'],
                'plan_id'      => $user['plan_id'],
                'rol'          => $user['rol'],
                'avatar'       => $user['avatar'],
                'is_logged_in' => true
            ]);
            
            session()->setFlashdata('mostrar_intro', true);
            return redirect()->to('/');
        }

        return redirect()->back()->with('error_general', 'Usuario o contraseña incorrectos.');
    }

    // =========================================================================
    // 🟡 SISTEMA DE REGISTRO COMPLETO (MEJORADO)
    // =========================================================================

    public function registro()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        // Tus avatares originales
        $data['avatars'] = [
            'https://i.blogs.es/b0add0/breaking-bad/500_333.jpeg',
            'https://i.blogs.es/c1c467/daredevil-born-again/375_375.jpeg',
            'https://images.ecestaticos.com/an3NIKmUWjvoxIwgQ4K3J0pqAqo=/0x0:828x470/1200x1200/filters:fill(white):format(jpg)/f.elconfidencial.com%2Foriginal%2F280%2F050%2F590%2F2800505903fc39bbeaf82f750b823ec3.jpg',
            'https://media.revistagq.com/photos/62a0a996223a33e985e4d59a/4:3/w_1199,h_899,c_limit/1072434_110615-cc-Darth-Vader-Thumb.jpg',
            'https://cdn.milenio.com/uploads/media/2018/06/08/estereotipo-ganster-actor-cinta-imitado.jpg',
            'https://upload.wikimedia.org/wikipedia/en/9/90/HeathJoker.png',
            'https://m.media-amazon.com/images/M/MV5BM2RkN2EwNDYtOTgzZC00Yzk4LTk1ZGQtN2U2MjlmZDQwYzMyXkEyXkFqcGc@._V1_.jpg',
            'https://m.media-amazon.com/images/M/MV5BYjVhYWQ2YTktYzIwMS00YWExLTkzYzQtMTcyMjAwZmZjNDU3XkEyXkFqcGc@._V1_.jpg',
            'https://media.revistagq.com/photos/62a8546d6b74c0e2031238a6/16:9/w_1280,c_limit/buzz.jpg',
            'https://yt3.googleusercontent.com/5VnfuQQjvC2uIfDR_R6lzSCJphVi2jTMGV71Xe24lUMW56nKa7Pu3CCCP3a7Po-G2J51xMb8tA=s900-c-k-c0x00ffffff-no-rj',
            'https://play.nintendo.com/images/profile-mk-baby-mario.7bf2a8f2.aead314d58b63e27.png'
        ];

        return view('auth/register', $data);
    }

    public function crear_usuario()
    {
        // 1. VALIDACIÓN PERSONALIZADA
        $rules = [
            'username' => [
                'rules'  => 'required|min_length[3]|is_unique[usuarios.username]',
                'errors' => [
                    'required'   => 'El nombre de usuario es obligatorio.',
                    'min_length' => 'El usuario debe tener al menos 3 caracteres.',
                    'is_unique'  => 'Ese nombre de usuario ya existe. Prueba con otro.'
                ]
            ],
            'email' => [
                // Validamos que termine en @gmail.com
                'rules'  => 'required|valid_email|is_unique[usuarios.email]|regex_match[/@gmail\.com$/]',
                'errors' => [
                    'required'    => 'El correo electrónico es obligatorio.',
                    'valid_email' => 'El formato del correo no es válido.',
                    'is_unique'   => 'Este correo ya está registrado. ¿Quieres iniciar sesión?',
                    'regex_match' => 'Solo se permiten correos de Gmail (@gmail.com).'
                ]
            ],
            'password' => [
                'rules'  => 'required|min_length[4]',
                'errors' => [
                    'required'   => 'La contraseña es obligatoria.',
                    'min_length' => 'La contraseña debe tener al menos 4 caracteres.'
                ]
            ],
            'pass_confirm' => [
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Debes repetir la contraseña.',
                    'matches'  => 'Las contraseñas no coinciden.'
                ]
            ],
            'plan_id' => [
                'rules'  => 'required',
                'errors' => [ 'required' => 'Por favor, selecciona un plan (Free o Premium).' ]
            ],
            'avatar' => [
                'rules'  => 'required',
                'errors' => [ 'required' => 'Elige un avatar para tu perfil.' ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. RECOGIDA DE DATOS
        $planId = $this->request->getPost('plan_id');
        
        $datosUsuario = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'rol'      => 'usuario',
            'plan_id'  => $planId,
            'avatar'   => $this->request->getPost('avatar')
        ];

        // 3. SI ES PREMIUM -> PASARELA
        if ($planId == 2) {
            session()->set('temp_user_data', $datosUsuario);
            return redirect()->to('/pasarela'); 
        } 
        
        // 4. SI ES FREE -> GUARDAR DIRECTO
        return $this->_finalizar_registro($datosUsuario);
    }

    // Muestra la vista bonita de pago
    public function pasarela_pago()
    {
        if(!session()->has('temp_user_data')) return redirect()->to('/registro');
        
        // Pasamos datos a la vista por si quieres mostrar el nombre/precio
        $user = session()->get('temp_user_data');
        return view('auth/payment_gateway', ['user' => $user]);
    }

    // Procesa el pago (Simulado)
    public function procesar_pago()
    {
        if (!session()->has('temp_user_data')) return redirect()->to('/registro');

        // Aquí iría la integración real con Stripe API
        // Simulamos éxito:
        $datosUsuario = session()->get('temp_user_data');
        session()->remove('temp_user_data'); // Limpiamos sesión temporal
        
        return $this->_finalizar_registro($datosUsuario);
    }

    // Función privada para no repetir código de insert + login
    private function _finalizar_registro($datos) {
        $model = new UsuarioModel();
        $nuevoId = $model->insert($datos);

        session()->set([
            'user_id'      => $nuevoId,
            'username'     => $datos['username'],
            'email'        => $datos['email'],
            'plan_id'      => $datos['plan_id'],
            'rol'          => 'usuario',
            'avatar'       => $datos['avatar'],
            'is_logged_in' => true
        ]);

        session()->setFlashdata('mostrar_intro', true);
        return redirect()->to('/');
    }
}