<?php 
namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Controllers\BaseController;

class Auth extends BaseController
{
    // =========================================================================
    // 1. LOGIN Y GESTIÓN DE PERFILES (EXISTENTE)
    // =========================================================================

    public function index()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        $model = new UsuarioModel();
        // FILTRO: Solo usuarios con ID entre 2 y 4 (Demo)
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

        // Acceso concedido SI: (Es Plan Kids) O (La contraseña es correcta)
        $accesoConcedido = ($user['plan_id'] == 3) || password_verify($password, $user['password']);

        if ($accesoConcedido) {
            session()->set([
                'user_id'      => $user['id'],
                'username'     => $user['username'],
                'plan_id'      => $user['plan_id'], 
                'rol'          => $user['rol'],
                'avatar'       => $user['avatar'] ?? 'https://i.pinimg.com/564x/1b/a2/e6/1ba2e6d1d4874546c70c91f1024e17fb.jpg',
                'is_logged_in' => true,
            ]);
            
            session()->setFlashdata('mostrar_intro', true);

            return $this->response->setJSON(['status' => 'success', 'token' => $newToken]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Contraseña incorrecta', 'token' => $newToken]);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }

    public function login_general()
    {
        $emailOrUser = $this->request->getPost('email');
        $password    = $this->request->getPost('password');

        $model = new UsuarioModel();
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
    // 2. REGISTRO DE USUARIOS
    // =========================================================================

    public function registro()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }
        
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
        $rules = [
            'username' => 'required|min_length[3]|is_unique[usuarios.username]',
            'email'    => 'required|valid_email|is_unique[usuarios.email]|regex_match[/@gmail\.com$/]',
            'password' => 'required|min_length[4]',
            'pass_confirm' => 'required|matches[password]',
            'plan_id'  => 'required',
            'avatar'   => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $planId = $this->request->getPost('plan_id');
        
        // Preparamos los datos básicos
        $datosUsuario = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'rol'      => 'usuario',
            'plan_id'  => $planId,
            'avatar'   => $this->request->getPost('avatar')
        ];

        // SI ES PREMIUM -> Guardamos en sesión y vamos a PASARELA
        if ($planId == 2) {
            session()->set('temp_user_data', $datosUsuario);
            return redirect()->to('/pasarela'); 
        } 
        
        // SI ES FREE -> Guardamos DIRECTO en la BD
        return $this->_finalizar_registro($datosUsuario);
    }

    // =========================================================================
    // 3. PASARELA DE PAGO (STRIPE)
    // =========================================================================

    public function pasarela_pago()
    {
        if(!session()->has('temp_user_data')) return redirect()->to('/registro');
        $user = session()->get('temp_user_data');
        return view('auth/payment_gateway', ['user' => $user]);
    }

    public function procesar_pago()
    {
        if (!session()->has('temp_user_data')) return redirect()->to('/registro');

        // TU CLAVE SECRETA DE STRIPE
        \Stripe\Stripe::setApiKey('sk_test_51Syx7APTBNyzobQjQCTV8NYXHjek1Vl1ltougcjbvEkhoprL5NdIH2OqrgvDjyQyMPyOuDZqkqGLUzQEJDFJacNV00p5nd9p91');

        $datosUsuario = session()->get('temp_user_data');

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Plan PREMIUM - La Butaca Premium',
                        ],
                        'unit_amount' => 999, // 9.99 EUR
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                // Rutas de retorno (deben coincidir con Routes.php)
                'success_url' => base_url('auth/confirmar_registro?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url'  => base_url('auth/pago_cancelado'),
            ]);

            return redirect()->to($session->url);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al conectar con Stripe: ' . $e->getMessage());
        }
    }

    public function confirmar_registro()
    {
        $sessionId = $this->request->getGet('session_id');
        
        // Verificamos que venga de Stripe y que tengamos datos pendientes
        if (!$sessionId || !session()->has('temp_user_data')) {
            return redirect()->to('/registro');
        }

        $datosUsuario = session()->get('temp_user_data');
        session()->remove('temp_user_data'); // Limpiamos la sesión temporal

        // Guardamos finalmente al usuario en la BD
        return $this->_finalizar_registro($datosUsuario);
    }

    public function pago_cancelado()
    {
        // Si el usuario cancela en Stripe, vuelve a la pasarela con error
        return redirect()->to('pasarela')->with('error', 'El proceso de pago fue cancelado. Inténtalo de nuevo.');
    }

    // =========================================================================
    // 4. FUNCIÓN PRIVADA (INSERTAR EN BD + LOGIN)
    // =========================================================================

    private function _finalizar_registro($datos) {
        $model = new UsuarioModel();

        /* NOTA SOBRE IMÁGENES:
           Actualmente usas URLs de internet ($datos['avatar'] es un string http...).
           Si en el futuro cambias el formulario para subir archivos (input type="file"),
           aquí es donde renombrarías el archivo:
           
           if (isset($datos['avatar_file'])) {
               $file = $datos['avatar_file'];
               $newName = $datos['username'] . '.' . $file->getExtension(); 
               $file->move('uploads/avatares', $newName);
               $datos['avatar'] = $newName; 
           }
        */

        // Insertar en Base de Datos
        $nuevoId = $model->insert($datos);

        // Iniciar Sesión Automáticamente
        session()->set([
            'user_id'      => $nuevoId,
            'username'     => $datos['username'],
            'email'        => $datos['email'],
            'plan_id'      => $datos['plan_id'],
            'rol'          => $datos['rol'],
            'avatar'       => $datos['avatar'],
            'is_logged_in' => true
        ]);

        session()->setFlashdata('mostrar_intro', true);
        return redirect()->to('/');
    }
}