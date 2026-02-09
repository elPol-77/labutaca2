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

    // -------------------------------------------------------------------------
    // LISTAR USUARIOS
    // -------------------------------------------------------------------------
    public function index()
    {
        // Hacemos un JOIN con la tabla 'planes' para mostrar el nombre del plan real
        // en lugar de solo el número (plan_id)
        $usuarios = $this->model->select('usuarios.*, planes.nombre as nombre_plan')
                                ->join('planes', 'planes.id = usuarios.plan_id', 'left')
                                ->orderBy('id', 'ASC')
                                ->paginate(20);

        $data = [
            'titulo'   => 'Gestión de Usuarios',
            'usuarios' => $usuarios,
            'pager'    => $this->model->pager
        ];

        // Usamos tus vistas de plantilla
        echo view('backend/templates/header', $data);
        echo view('backend/usuarios/index', $data);
        echo view('backend/templates/footer', $data);
    }

    // -------------------------------------------------------------------------
    // FORMULARIO DE EDICIÓN
    // -------------------------------------------------------------------------
    public function edit($id)
    {
        $usuario = $this->model->find($id);
        
        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuario no encontrado');
        }

        // Necesitamos la lista de planes para el <select> del formulario
        $db = \Config\Database::connect();
        $planes = $db->table('planes')->get()->getResultArray();

        $data = [
            'titulo'  => 'Editar Usuario',
            'usuario' => $usuario,
            'planes'  => $planes
        ];

        echo view('backend/templates/header', $data);
        echo view('backend/usuarios/form', $data); // Asegúrate de tener esta vista creada
        echo view('backend/templates/footer', $data);
    }

    // -------------------------------------------------------------------------
    // GUARDAR CAMBIOS (UPDATE)
    // -------------------------------------------------------------------------
    public function update($id)
    {
        // 1. PROTECCIÓN SUPER ADMIN (ID 1)
        // Si es el ID 1, impedimos que le quiten el rol de admin
        if ($id == 1) {
            if ($this->request->getPost('rol') !== 'admin') {
                return redirect()->back()->with('error', '⚠️ Acción denegada: El Usuario Principal (ID 1) siempre debe ser Admin.');
            }
        }

        // 2. VALIDACIÓN
        // is_unique comprueba que no usemos un email/nombre que ya tenga OTRO usuario
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

        // 4. CONTRASEÑA (Solo si se escribe una nueva)
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            // Encriptamos la nueva contraseña
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Guardamos
        $this->model->update($id, $data);

        return redirect()->to('admin/usuarios')->with('msg', 'Usuario actualizado correctamente.');
    }

    // -------------------------------------------------------------------------
    // ELIMINAR USUARIO
    // -------------------------------------------------------------------------
    public function delete($id)
    {
        // 1. PROTECCIÓN SUPER ADMIN
        if ($id == 1) {
            return redirect()->back()->with('error', '⛔ ERROR CRÍTICO: No puedes eliminar al Admin Principal (ID 1).');
        }

        // 2. PROTECCIÓN "NO BORRARSE A UNO MISMO"
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'No puedes borrar tu propia cuenta mientras estás conectado.');
        }

        // 3. BORRADO
        // Al tener ON DELETE CASCADE en la base de datos (mi_lista, resenas), 
        // se borrará todo lo relacionado con este usuario automáticamente.
        $this->model->delete($id);

        return redirect()->back()->with('msg', 'Usuario eliminado correctamente.');
    }
}