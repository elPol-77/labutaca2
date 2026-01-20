<?php namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        // Cargamos la estructura BACKEND
        echo view('backend/templates/header');
        echo view('backend/dashboard');
        echo view('backend/templates/footer');
    }
}