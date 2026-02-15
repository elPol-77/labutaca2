<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\GeneroModel;

class Perfil extends BaseController
{
    private $avatars = [
        'general' => [
            'https://i.blogs.es/b0add0/breaking-bad/500_333.jpeg',
            'https://i.blogs.es/c1c467/daredevil-born-again/375_375.jpeg',
            'https://images.ecestaticos.com/an3NIKmUWjvoxIwgQ4K3J0pqAqo=/0x0:828x470/1200x1200/filters:fill(white):format(jpg)/f.elconfidencial.com%2Foriginal%2F280%2F050%2F590%2F2800505903fc39bbeaf82f750b823ec3.jpg',
            'https://media.revistagq.com/photos/62a0a996223a33e985e4d59a/4:3/w_1199,h_899,c_limit/1072434_110615-cc-Darth-Vader-Thumb.jpg',
            'https://cdn.milenio.com/uploads/media/2018/06/08/estereotipo-ganster-actor-cinta-imitado.jpg',
            'https://upload.wikimedia.org/wikipedia/en/9/90/HeathJoker.png',
            'https://m.media-amazon.com/images/M/MV5BM2RkN2EwNDYtOTgzZC00Yzk4LTk1ZGQtN2U2MjlmZDQwYzMyXkEyXkFqcGc@._V1_.jpg',
        ],
        'kids' => [
            'https://m.media-amazon.com/images/M/MV5BYjVhYWQ2YTktYzIwMS00YWExLTkzYzQtMTcyMjAwZmZjNDU3XkEyXkFqcGc@._V1_.jpg',
            'https://media.revistagq.com/photos/62a8546d6b74c0e2031238a6/16:9/w_1280,c_limit/buzz.jpg',
            'https://yt3.googleusercontent.com/5VnfuQQjvC2uIfDR_R6lzSCJphVi2jTMGV71Xe24lUMW56nKa7Pu3CCCP3a7Po-G2J51xMb8tA=s900-c-k-c0x00ffffff-no-rj',
            'https://play.nintendo.com/images/profile-mk-baby-mario.7bf2a8f2.aead314d58b63e27.png'
        ]
    ];

    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $userId = session()->get('user_id');
        $userModel = new UsuarioModel();
        $generoModel = new GeneroModel();

        // 1. Buscamos el usuario
        $usuario = $userModel->find($userId);

        if (!$usuario) {
            session()->destroy();
            return redirect()->to('/auth')->with('error_general', 'Sesión no válida.');
        }

        $planId = $usuario['plan_id'];

        // =========================================================
        // 📅 CÁLCULO DE SUSCRIPCIÓN (LÓGICA REAL: FECHA FIN)
        // =========================================================

        $nombrePlan = 'Free';
        $precio = '0.00€';
        if ($planId == 2) {
            $nombrePlan = 'Premium';
            $precio = '9.99€';
        }
        if ($planId == 3) {
            $nombrePlan = 'Kids';
            $precio = '4.99€';
        }

        $diasRestantes = 0;
        $porcentajeBarra = 0;
        $fechaRenovacion = "N/A";
        $estadoSuscripcion = 'Gratuita';

        // Solo calculamos si es Premium/Kids Y si tiene una fecha de fin definida
        if ($planId > 1) {
            try {
                if (!empty($usuario['fecha_fin_suscripcion'])) {
                    // Usamos la FECHA REAL DE CADUCIDAD de la BD
                    $fechaFin = new \DateTime($usuario['fecha_fin_suscripcion']);
                    $hoy = new \DateTime();

                    if ($fechaFin > $hoy) {
                        // Aún está activa
                        $diferencia = $hoy->diff($fechaFin);
                        $diasRestantes = $diferencia->days;
                        $fechaRenovacion = $fechaFin->format('d/m/Y');
                        $estadoSuscripcion = 'Activa';

                        // Barra de progreso (Calculada sobre 30 días estándar para efecto visual)
                        $porcentajeBarra = ($diasRestantes / 30) * 100;
                        if ($porcentajeBarra > 100)
                            $porcentajeBarra = 100;
                    } else {
                        // Ya ha caducado (pero el usuario aún no ha sido degradado por el sistema)
                        $diasRestantes = 0;
                        $fechaRenovacion = "Caducada";
                        $estadoSuscripcion = 'Pendiente de Pago';
                        $porcentajeBarra = 0;
                    }
                } else {
                    // Fallback: Es Premium pero no tiene fecha (error de datos antiguos)
                    $diasRestantes = 30;
                    $fechaRenovacion = "Indefinida";
                }
            } catch (\Exception $e) {
                $diasRestantes = 0;
                $fechaRenovacion = "Error";
            }
        }

        // =========================================================

        $otrosPerfiles = $userModel->where('id !=', $userId)
            ->where('id >=', 2)
            ->where('id <=', 4)
            ->findAll();

        $listaGeneros = $generoModel->orderBy('nombre', 'ASC')->findAll();

        $avataresDisponibles = ($planId == 3)
            ? $this->avatars['kids']
            : array_merge($this->avatars['general'], $this->avatars['kids']);

        $data = [
            'usuario' => $usuario,
            'avatares' => $avataresDisponibles,
            'esKids' => ($planId == 3),
            'generos' => $listaGeneros,
            'otrosPerfiles' => $otrosPerfiles,
            'planes' => [
                1 => 'Plan Free (Con Anuncios)',
                2 => 'Plan Premium (Todo incluido)',
                3 => 'Perfil Kids (Contenido Infantil)'
            ],
            // 🟢 DATOS REALES ENVIADOS A LA VISTA
            'suscripcion' => [
                'nombre_plan' => $nombrePlan,
                'precio' => $precio,
                'dias_restantes' => $diasRestantes,
                'fecha_renovacion' => $fechaRenovacion,
                'porcentaje' => $porcentajeBarra,
                'estado' => $estadoSuscripcion
            ]
        ];

        return view('frontend/templates/header', $data)
            . view('frontend/perfil', $data)
            . view('frontend/templates/footer');
    }

    public function update()
    {
        $idUsuario = session()->get('user_id');

        // Recogemos todos los datos
        $nuevoPlan = $this->request->getPost('plan_id');
        $nuevoUser = $this->request->getPost('username');
        $nuevoAvatar = $this->request->getPost('avatar');

        // --- DATOS DE CONTRASEÑA ---
        $nuevaPass = $this->request->getPost('new_password');
        $confirmPass = $this->request->getPost('confirm_password'); // <--- RECOGEMOS LA CONFIRMACIÓN

        $model = new UsuarioModel();
        $usuarioActual = $model->find($idUsuario);
        $planActual = $usuarioActual['plan_id'];

        // 1. DETECTAR SI ES UPGRADE A PREMIUM
        if ($planActual == 1 && $nuevoPlan == 2) {
            session()->set('temp_upgrade_data', [
                'id' => $idUsuario,
                'username' => $nuevoUser,
                'plan_id' => 2,
                'avatar' => $nuevoAvatar
            ]);
            return redirect()->to('/pasarela-upgrade');
        }

        // 2. PREPARAR DATOS COMUNES
        $data = [
            'username' => $nuevoUser,
            'avatar' => $nuevoAvatar,
        ];

        if ($nuevoPlan) {
            $data['plan_id'] = $nuevoPlan;
        }

        // 3. LÓGICA DE CONTRASEÑA (VERIFICACIÓN DOBLE)
        if (!empty($nuevaPass)) {

            // A. Verificamos que coincidan (Seguridad de servidor)
            if ($nuevaPass !== $confirmPass) {
                return redirect()->back()->withInput()->with('error', 'Error: Las contraseñas no coinciden.');
            }

            // B. Validación de complejidad (Regex)
            // Min 8 chars, 1 Mayúscula, 1 Minúscula, 1 Número
            if (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $nuevaPass)) {
                $data['password'] = password_hash($nuevaPass, PASSWORD_DEFAULT);
            } else {
                return redirect()->back()->withInput()->with('error', 'La contraseña no cumple los requisitos de seguridad (Mínimo 8 caracteres, mayúscula y número).');
            }
        }

        // 4. ACTUALIZAR BASE DE DATOS
        $model->update($idUsuario, $data);

        // 5. ACTUALIZAR SESIÓN
        session()->set([
            'username' => $nuevoUser,
            'avatar' => $nuevoAvatar,
            'plan_id' => $nuevoPlan ?? $planActual
        ]);

        return redirect()->to('/perfil')->with('success', 'Perfil actualizado correctamente.');
    }

    // =========================================================================
    // 🟡 INTEGRACIÓN DE STRIPE (UPGRADE)
    // =========================================================================

    public function pasarela_upgrade()
    {
        if (!session()->has('temp_upgrade_data'))
            return redirect()->to('/perfil');

        $user = session()->get('temp_upgrade_data');
        // Pasamos 'is_upgrade' para que la vista sepa que debe cambiar el texto del botón y el enlace de cancelar
        return view('auth/payment_gateway', ['user' => $user, 'is_upgrade' => true]);
    }

    public function procesar_upgrade()
    {
        if (!session()->has('temp_upgrade_data'))
            return redirect()->to('/perfil');

        // 1. Configurar Stripe
        \Stripe\Stripe::setApiKey('sk_test_51Syx7APTBNyzobQjQCTV8NYXHjek1Vl1ltougcjbvEkhoprL5NdIH2OqrgvDjyQyMPyOuDZqkqGLUzQEJDFJacNV00p5nd9p91');

        try {
            // 2. Crear sesión de pago
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'Upgrade a Plan PREMIUM',
                            ],
                            'unit_amount' => 999, // 9.99 EUR
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                // Rutas de retorno específicas para el perfil
                'success_url' => base_url('perfil/confirmar_upgrade?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => base_url('perfil'), // Si cancela, vuelve a su perfil
            ]);

            return redirect()->to($session->url);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error con Stripe: ' . $e->getMessage());
        }
    }

    public function confirmar_upgrade()
    {
        $sessionId = $this->request->getGet('session_id');

        if (!$sessionId || !session()->has('temp_upgrade_data')) {
            return redirect()->to('/perfil');
        }

        $datos = session()->get('temp_upgrade_data');
        $model = new UsuarioModel();

        // 1. Actualizamos la Base de Datos
        $model->update($datos['id'], [
            'username' => $datos['username'],
            'plan_id' => $datos['plan_id'],
            'avatar' => $datos['avatar'],
            'fecha_fin_suscripcion' => date('Y-m-d H:i:s', strtotime('+30 days'))
        ]);


        session()->set([
            'username' => $datos['username'],
            'plan_id' => $datos['plan_id'],
            'avatar' => $datos['avatar']
        ]);

        // 3. Limpiamos datos temporales
        session()->remove('temp_upgrade_data');

        return redirect()->to('/perfil')->with('success', '¡Pago realizado con éxito! Ahora eres Premium.');
    }
}