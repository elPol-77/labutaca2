<?php
namespace App\Controllers\Admin;
// Heredamos de Peliculas para reutilizar la lógica de "store" y "procesarGeneros"
// ¡Así no escribimos el código dos veces!
class Series extends Peliculas 
{
    protected $tipoId = 2; // Esto cambia todo automáticamente a Series

    public function index() {
        $data = [
            'titulo' => 'Gestión de Series',
            'peliculas' => $this->model->where('tipo_id', 2)->orderBy('id', 'DESC')->paginate(10), // Filtro 2
            'pager' => $this->model->pager
        ];
        // Reutilizamos la misma vista index, es compatible
        return view('backend/peliculas/index', $data);
    }
    
    // Al heredar, usa el 'create' y 'store' de Peliculas, 
    // pero como cambiamos $this->tipoId a 2, guardará como serie.
    public function store() {
        // Redirigimos a admin/series al terminar
        $res = parent::store(); 
        return redirect()->to('admin/series')->with('msg', 'Serie agregada.');
    }
}