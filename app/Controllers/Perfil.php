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

    // --- CORRECCIÓN CRÍTICA ---
    // Si el usuario no existe en la BD (aunque haya sesión), abortamos.
    if (!$usuario) {
        session()->destroy();
        return redirect()->to('/auth')->with('error_general', 'Sesión no válida o usuario inexistente.');
    }

    $planId = $usuario['plan_id']; // Usamos el plan real de la BD, no el de sesión por si acaso

    $otrosPerfiles = $userModel->where('id !=', $userId)
        ->where('id >=', 2)
        ->where('id <=', 4)
        ->findAll();

    $listaGeneros = $generoModel->orderBy('nombre', 'ASC')->findAll();

    $avataresDisponibles = ($planId == 3)
        ? $this->avatars['kids']
        : array_merge($this->avatars['general'], $this->avatars['kids']);

    $data = [
        'usuario'       => $usuario,
        'avatares'      => $avataresDisponibles,
        'esKids'        => ($planId == 3),
        'generos'       => $listaGeneros,
        'otrosPerfiles' => $otrosPerfiles,
        'planes'        => [
            1 => 'Plan Free (Con Anuncios)',
            2 => 'Plan Premium (Todo incluido)',
            3 => 'Perfil Kids (Contenido Infantil)'
        ]
    ];

    // Cargamos las vistas de forma estándar
    return view('frontend/templates/header', $data)
         . view('frontend/perfil', $data)
         . view('frontend/templates/footer');
}

    public function update()
    {
        // 1. RECOGER DATOS
        $idUsuario = session()->get('user_id');
        $nuevoPlan = $this->request->getPost('plan_id');
        $nuevoUser = $this->request->getPost('username');
        $nuevoAvatar = $this->request->getPost('avatar');

        // 2. OBTENER PLAN ACTUAL (Para comparar)
        $model = new UsuarioModel();
        $usuarioActual = $model->find($idUsuario);
        $planActual = $usuarioActual['plan_id'];

        // 3. DETECTAR SI ES UNA MEJORA A PREMIUM (UPGRADE)
        // Si antes era Free (1) y ahora elige Premium (2) -> PASARELA
        if ($planActual == 1 && $nuevoPlan == 2) {

            // Guardamos los datos que quiere cambiar en sesión temporal
            session()->set('temp_upgrade_data', [
                'id' => $idUsuario,
                'username' => $nuevoUser,
                'plan_id' => 2, // Forzamos premium
                'avatar' => $nuevoAvatar
            ]);

            // Redirigimos a una ruta especial de pago para upgrades
            return redirect()->to('/pasarela-upgrade');
        }

        // 4. SI NO ES UPGRADE (Es solo cambio de nombre, avatar o downgrade) -> GUARDAR DIRECTO
        $data = [
            'username' => $nuevoUser,
            'avatar' => $nuevoAvatar,
        ];

        // Solo actualizamos el plan si se ha enviado (y no es el caso upgrade de arriba)
        if ($nuevoPlan) {
            $data['plan_id'] = $nuevoPlan;
        }

        $model->update($idUsuario, $data);

        // Actualizamos la sesión con los nuevos datos para ver los cambios al instante
        session()->set([
            'username' => $nuevoUser,
            'avatar' => $nuevoAvatar,
            'plan_id' => $nuevoPlan ?? $planActual
        ]);

        return redirect()->to('/perfil')->with('success', 'Perfil actualizado correctamente.');
    }
    // Vista de pago específica para upgrades (o reutilizamos la otra)
    public function pasarela_upgrade()
    {
        if(!session()->has('temp_upgrade_data')) return redirect()->to('/perfil');
        
        // Podemos reutilizar la vista 'auth/payment_gateway' pasándole datos
        // o crear una nueva si quieres textos distintos. Reutilicemos por ahora:
        $user = session()->get('temp_upgrade_data');
        return view('auth/payment_gateway', ['user' => $user, 'is_upgrade' => true]);
    }

    // Procesa el pago del upgrade
    public function procesar_upgrade()
    {
        if(!session()->has('temp_upgrade_data')) return redirect()->to('/perfil');

        $datos = session()->get('temp_upgrade_data');
        $model = new UsuarioModel();

        // Actualizamos de verdad
        $model->update($datos['id'], [
            'username' => $datos['username'],
            'plan_id'  => $datos['plan_id'],
            'avatar'   => $datos['avatar']
        ]);

        // Actualizamos sesión
        session()->set([
            'username' => $datos['username'],
            'plan_id'  => $datos['plan_id'],
            'avatar'   => $datos['avatar']
        ]);

        session()->remove('temp_upgrade_data');

        return redirect()->to('/perfil')->with('success', '¡Pago realizado! Ahora eres Premium.');
    }
}