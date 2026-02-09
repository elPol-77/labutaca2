<?php 
namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Controller;

class Cuenta extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $model = new UsuarioModel();
        $usuario = $model->find(session()->get('user_id'));

        // Simulamos datos de facturación para la vista
        $data = [
            'usuario' => $usuario,
            'titulo'  => 'Cuenta y Configuración - La Butaca',
            'tarjeta' => '•••• •••• •••• 4242', // Simulado
            'proximo_cobro' => date('d/m/Y', strtotime('+1 month')) // Simulado
        ];

        return view('frontend/account', $data);
    }

    public function cambiar_password()
    {
        $id = session()->get('user_id');
        $passActual = $this->request->getPost('pass_actual');
        $passNueva  = $this->request->getPost('pass_nueva');
        $passRepetir = $this->request->getPost('pass_repetir');

        $model = new UsuarioModel();
        $usuario = $model->find($id);

        // 1. Validar contraseña actual
        if (!password_verify($passActual, $usuario['password'])) {
            return redirect()->back()->with('error', 'La contraseña actual no es correcta.');
        }

        // 2. Validar nueva contraseña (mínimo 4 caracteres, coincidencia)
        if (strlen($passNueva) < 4) {
            return redirect()->back()->with('error', 'La nueva contraseña debe tener al menos 4 caracteres.');
        }

        if ($passNueva !== $passRepetir) {
            return redirect()->back()->with('error', 'Las contraseñas nuevas no coinciden.');
        }

        // 3. Actualizar
        $model->update($id, [
            'password' => password_hash($passNueva, PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/cuenta')->with('success', 'Contraseña actualizada correctamente.');
    }

    public function cancelar_suscripcion()
    {
        $id = session()->get('user_id');
        $model = new UsuarioModel();

        // Pasamos al plan 1 (Free)
        $model->update($id, ['plan_id' => 1]);

        // Actualizamos sesión
        session()->set('plan_id', 1);

        return redirect()->to('/cuenta')->with('info', 'Tu suscripción ha sido cancelada. Ahora eres Free.');
    }
}