<?php
namespace App\Controllers\Admin;
class Series extends Peliculas 
{
    protected $tipoId = 2; 

    public function index() {
        $data = [
            'titulo' => 'Gestión de Series',
            'peliculas' => $this->model->where('tipo_id', 2)->orderBy('id', 'DESC')->paginate(10), 
            'pager' => $this->model->pager
        ];
        return view('backend/peliculas/index', $data);
    }
    
    public function store() {
        $res = parent::store(); 
        return redirect()->to('admin/series')->with('msg', 'Serie agregada.');
    }
}