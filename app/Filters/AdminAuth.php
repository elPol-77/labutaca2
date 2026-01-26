<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // === ARREGLO AQUÍ ===
        // Obtenemos la URL actual
        $path = $request->getUri()->getPath();

        // Si el usuario está intentando entrar al login de admin, NO LO DETENGAS
        if (strpos($path, 'admin/login') !== false || strpos($path, 'admin/auth/login') !== false) {
            return; 
        }

        // 1. ¿Está logueado?
        if (!session()->get('is_logged_in')) {
            // Si no está logueado y no es el login, a perfiles
            return redirect()->to('auth');
        }

        // 2. ¿Es Admin?
        $rol = session()->get('rol'); 
        if ($rol !== 'admin') {
            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada
    }
}