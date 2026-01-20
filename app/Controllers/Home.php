<?php namespace App\Controllers;

use App\Models\ContenidoModel;

class Home extends BaseController
{
    public function index()
    {
        $model = new ContenidoModel();
        
        $data = [
            'titulo'    => 'La Butaca - Experiencia Inmersiva',
            'peliculas' => $model->getPeliculas() // Trae los datos de la DB
        ];

        // CARGAMOS LA ESTRUCTURA MODULAR
        echo view('frontend/templates/header', $data);
        echo view('frontend/catalogo', $data);
        echo view('frontend/templates/footer', $data);
    }
}