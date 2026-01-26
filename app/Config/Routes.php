<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =============================================================================
// 1. FRONTEND (VISTAS HTML)
// =============================================================================
$routes->get('/', 'Home::index');

// [IMPORTANTE] Cambiamos (:num) por (:segment) para aceptar IDs de OMDb (tt12345)
$routes->get('detalle/(:segment)', 'Home::detalle/$1'); 

// Para ver película (reproductor local), seguimos exigiendo ID numérico por seguridad
$routes->get('ver/(:num)', 'Home::ver/$1'); 

$routes->get('mi-lista', 'Home::miLista');
$routes->get('director/(:num)', 'Home::director/$1');

// =============================================================================
// 2. AUTENTICACIÓN
// =============================================================================
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout'); // Alias por si acaso
$routes->get('logout', 'Auth::logout');

// =============================================================================
// 3. API INTERNA RESTful (JSON)
// =============================================================================
// Todo lo que empiece por /api/... irá aquí dentro
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    
    // --- CATÁLOGO ---
    $routes->get('catalogo', 'Catalogo::index');          // Listado general (Angular/App)
    $routes->get('catalogo/(:segment)', 'Catalogo::show/$1'); // Detalle (acepta OMDb)
    $routes->get('tendencias', 'Catalogo::tendencias');   // Filas Netflix Home
    
    // --- USUARIO (Mi Lista) ---
    // Mapeamos la ruta que usa tu JS ('api/usuario/toggle-lista') al método 'toggle'
    $routes->post('usuario/toggle-lista', 'Usuario::toggle');  
    $routes->get('mi-lista', 'Usuario::getLista');        

    // --- BUSCADOR ---
    // Movemos la lógica del buscador a la API. 
    // Nota: Crearemos esta función en el controlador Catalogo para no crear otro archivo.
    $routes->post('buscador/autocompletar', 'Catalogo::autocompletar');
});