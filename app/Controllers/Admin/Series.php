<?php
namespace App\Controllers\Admin;
class Series extends Peliculas 
{
    protected $tipoId = 2; 

    public function index() {
        $data = [
            'titulo' => 'Gestión de Series',
            'peliculas' => $this->model->where('tipo_id', 2)->orderBy('id', 'DESC')->paginate(10), // Filtro 2
            'pager' => $this->model->pager
        ];
        // Reutilizamos la misma vista index, es compatible
        return view('backend/peliculas/index', $data);
    }
    
    public function store() {
        $res = parent::store(); 
        return redirect()->to('admin/series')->with('msg', 'Serie agregada.');
    }
}