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
        ],
        'kids' => [
            'https://static.wikia.nocookie.net/carspelicula/images/7/79/20090407_7f8e529eaeb33d4bcde5dJOuBl55TwsF.jpg/revision/latest/scale-to-width-down/1200?cb=20100825223446&path-prefix=es',
            'https://media.revistagq.com/photos/62a8546d6b74c0e2031238a6/16:9/w_1280,c_limit/buzz.jpg',
            'https://yt3.googleusercontent.com/5VnfuQQjvC2uIfDR_R6lzSCJphVi2jTMGV71Xe24lUMW56nKa7Pu3CCCP3a7Po-G2J51xMb8tA=s900-c-k-c0x00ffffff-no-rj',
            'https://play.nintendo.com/images/profile-mk-baby-mario.7bf2a8f2.aead314d58b63e27.png'
        ]
    ];

    public function index()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $planId = session()->get('plan_id');
        $userModel = new UsuarioModel();
        $generoModel = new GeneroModel();
        
        $usuario = $userModel->find($userId);
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
            'generos' => $listaGeneros,
            'otrosPerfiles' => $otrosPerfiles, 
            'esKids' => ($planId == 3),
            'planes' => [
                1 => 'Plan Free (Con Anuncios)',
                2 => 'Plan Premium (Todo incluido)',
                3 => 'Perfil Kids (Contenido Infantil)'
            ]
        ];

        echo view('frontend/templates/header', ['titulo' => 'Editar Perfil']);
        echo view('frontend/perfil', $data);
        echo view('frontend/templates/footer');
    }

    public function update()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/auth');

        $userId = session()->get('user_id');
        $planActual = session()->get('plan_id');
        $userModel = new UsuarioModel();

        // 1. Validar reglas básicas
        $rules = [
            'avatar' => 'required'
        ];

        if ($planActual != 3) {
            // <--- CAMBIO: nombre por username
            $rules['username'] = 'required|min_length[3]'; 
            $rules['plan_id'] = 'required|in_list[1,2,3]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor revisa los datos.');
        }

        // 2. Preparar datos
        $dataUpdate = [
            'avatar' => $this->request->getPost('avatar')
        ];

        if ($planActual != 3) {
            // <--- CAMBIO CRÍTICO: Aquí asignamos a la columna 'username'
            // Y leemos del input HTML llamado 'username'
            $dataUpdate['username'] = $this->request->getPost('username'); 
            $dataUpdate['plan_id'] = $this->request->getPost('plan_id');
        }

        // 3. Guardar en Base de Datos
        $userModel->update($userId, $dataUpdate);

        // 4. ACTUALIZAR SESIÓN
        session()->set([
            // <--- CAMBIO: Actualizamos la sesión con el nuevo username
            'username' => $dataUpdate['username'] ?? session()->get('username'),
            'avatar' => $dataUpdate['avatar'],
            'plan_id' => $dataUpdate['plan_id'] ?? session()->get('plan_id')
        ]);

        return redirect()->to('/perfil')->with('success', 'Perfil actualizado correctamente.');
    }
}