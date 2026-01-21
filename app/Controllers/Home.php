<?php namespace App\Controllers;
use App\Models\ContenidoModel;

class Home extends BaseController
{
    public function index()
    {
        // 1. PROTECCIÓN: Si no hay sesión, mandar a elegir perfil
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        // 2. OBTENER PLAN DEL USUARIO
        $planId = session()->get('plan_id');

        // 3. PEDIR PELÍCULAS FILTRADAS
        $model = new ContenidoModel();
        $peliculas = $model->getPeliculas($planId); // Pasamos el plan

        $data = [
            'titulo'    => 'La Butaca',
            'peliculas' => $peliculas
        ];

        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }
}