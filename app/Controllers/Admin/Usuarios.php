<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    public function index()
    {
        $model = new UsuarioModel();
        $data = [
            'usuarios' => $model->paginate(20),
            'pager' => $model->pager
        ];
        return view('backend/usuarios/index', $data);
    }

    public function delete($id)
    {
        // Evitar borrarse a uno mismo
        if($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'No puedes borrarte a ti mismo.');
        }

        $model = new UsuarioModel();
        $model->delete($id);
        return redirect()->back()->with('msg', 'Usuario eliminado.');
    }
}