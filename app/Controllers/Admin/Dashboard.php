<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContenidoModel;
use App\Models\UsuarioModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $contenidoModel = new ContenidoModel();
        $usuarioModel = new UsuarioModel();

        // Datos para las estadísticas (KPIs)
        $data = [
            'titulo' => 'Panel de Administración',
            'total_peliculas' => $contenidoModel->where('tipo_id', 1)->countAllResults(),
            'total_series'    => $contenidoModel->where('tipo_id', 2)->countAllResults(),
            'total_usuarios'  => $usuarioModel->countAllResults(),
            // Últimas 5 agregadas
            'ultimas' => $contenidoModel->orderBy('id', 'DESC')->findAll(5) 
        ];

        return view('backend/dashboard', $data);
    }
}