<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }


    public function index()
    {

        $usuarios = $this->model->select('usuarios.*, planes.nombre as nombre_plan')
                                ->join('planes', 'planes.id = usuarios.plan_id', 'left')
                                ->orderBy('id', 'ASC')
                                ->paginate(20);

        $data = [
            'titulo'   => 'Gestión de Usuarios',
            'usuarios' => $usuarios,
            'pager'    => $this->model->pager
        ];

        echo view('backend/templates/header', $data);
        echo view('backend/usuarios/index', $data);
        echo view('backend/templates/footer', $data);
    }

    public function edit($id)
    {
        $usuario = $this->model->find($id);
        
        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuario no encontrado');
        }


        $db = \Config\Database::connect();
        $planes = $db->table('planes')->get()->getResultArray();

        $data = [
            'titulo'  => 'Editar Usuario',
            'usuario' => $usuario,
            'planes'  => $planes
        ];

        echo view('backend/templates/header', $data);
        echo view('backend/usuarios/form', $data);
        echo view('backend/templates/footer', $data);
    }

    public function update($id)
    {
        if ($id == 1) {
            if ($this->request->getPost('rol') !== 'admin') {
                return redirect()->back()->with('error', 'Acción denegada: El Usuario Principal (ID 1) siempre debe ser Admin.');
            }
        }

        $reglas = [
            'username' => "required|is_unique[usuarios.username,id,{$id}]",
            'email'    => "required|valid_email|is_unique[usuarios.email,id,{$id}]"
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. PREPARAR DATOS
        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'rol'      => $this->request->getPost('rol'),
            'plan_id'  => $this->request->getPost('plan_id'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Guardamos
        $this->model->update($id, $data);

        return redirect()->to('admin/usuarios')->with('msg', 'Usuario actualizado correctamente.');
    }

    public function delete($id)
    {
        if ($id == 1) {
            return redirect()->back()->with('error', 'ERROR CRÍTICO: No puedes eliminar al Admin Principal (ID 1).');
        }

        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'No puedes borrar tu propia cuenta mientras estás conectado.');
        }

        $this->model->delete($id);

        return redirect()->back()->with('msg', 'Usuario eliminado correctamente.');
    }
}