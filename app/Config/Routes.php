<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =============================================================================
// 1. FRONTEND (Público)
// =============================================================================
$routes->get('/', 'Home::index');
$routes->get('detalle/(:segment)', 'Home::detalle/$1');
$routes->get('ver/(:segment)', 'Home::ver/$1');
$routes->get('mi-lista', 'Home::miLista');
$routes->get('peliculas', 'Home::paginaPeliculas');
$routes->get('series', 'Serie::index');

// Rutas AJAX Series
$routes->post('serie/ajax-fila', 'Serie::ajaxCargarFila');
$routes->post('serie/ajax-expandir-fila', 'Serie::ajaxExpandirFila');

$routes->get('director/(:num)', 'Home::director/$1');
$routes->post('autocompletar', 'Api\Catalogo::autocompletar'); // Tu buscador arreglado
$routes->get('persona/(:segment)', 'Home::persona/$1');
$routes->post('ajax/cargar-fila', 'Home::ajaxCargarFila');

// --- RUTA PARA ANGULAR (Si la usas) ---
$routes->get('global', 'Home::vistaGlobal');
$routes->get('global/(:any)', 'Home::vistaGlobal');

// =============================================================================
// 2. AUTENTICACIÓN (Perfiles y Login Admin)
// =============================================================================
// Login de Usuarios
$routes->get('auth', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('auth/logout', 'Auth::logout');

// Selección de perfiles
$routes->get('profiles', 'Profiles::index');
$routes->get('profiles/select/(:segment)', 'Profiles::select/$1');

// Gestión de Perfil
$routes->get('perfil', 'Perfil::index');
$routes->post('perfil/update', 'Perfil::update');

// Login de Administración
$routes->get('admin/login', 'Admin\Auth::index');
$routes->post('admin/auth/login', 'Admin\Auth::login');


// =============================================================================
// 3. API INTERNA RESTful
// =============================================================================
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // Catálogo y Tendencias
    $routes->get('catalogo', 'Catalogo::index');
    $routes->get('catalogo/(:segment)', 'Catalogo::show/$1');
    $routes->get('tendencias', 'Catalogo::tendencias');

    // Gestión de Usuario
    $routes->post('usuario/toggle-lista', 'Usuario::toggle');
    $routes->get('mi-lista', 'Usuario::getLista');
    $routes->get('destacada-random', 'Catalogo::getDestacadaRandom');
    $routes->get('peliculas-landing', 'Catalogo::getPeliculasLanding'); 

    // Buscador API
    $routes->post('buscador/autocompletar', 'Catalogo::autocompletar');
});

// =============================================================================
// 4. ÁREA DE ADMINISTRACIÓN (Protegida)
// =============================================================================
$routes->group('admin', ['filter' => 'adminAuth', 'namespace' => 'App\Controllers\Admin'], function ($routes) {

    // Dashboard Principal
    $routes->get('/', 'Dashboard::index');

    // --- GESTIÓN DE PELÍCULAS ---
    $routes->group('peliculas', function ($routes) {
        $routes->get('/', 'Peliculas::index');           // Listado
        $routes->get('create', 'Peliculas::create');     // Formulario Crear
        $routes->post('store', 'Peliculas::store');      // Guardar Nuevo
        
        // 👇 RUTAS NUEVAS AÑADIDAS 👇
        $routes->get('editar/(:num)', 'Peliculas::edit/$1');    // Formulario Editar
        $routes->post('update/(:num)', 'Peliculas::update/$1'); // Guardar Edición
        
        $routes->get('borrar/(:num)', 'Peliculas::delete/$1');  // Borrar
    });

    // --- GESTIÓN DE SERIES ---
    $routes->group('series', function ($routes) {
        $routes->get('/', 'Series::index');
        $routes->get('create', 'Series::create');
        $routes->post('store', 'Series::store');

        // 👇 RUTAS NUEVAS AÑADIDAS 👇
        $routes->get('editar/(:num)', 'Series::edit/$1');
        $routes->post('update/(:num)', 'Series::update/$1');

        $routes->get('borrar/(:num)', 'Series::delete/$1');
    });

    // --- GESTIÓN DE USUARIOS ---
    $routes->get('usuarios', 'Usuarios::index');
    $routes->get('usuarios/borrar/(:num)', 'Usuarios::delete/$1');
    $routes->post('usuarios/cambiar-rol', 'Usuarios::cambiarRol');
});