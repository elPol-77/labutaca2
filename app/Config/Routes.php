<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =============================================================================
// 1. FRONTEND (Público)
// =============================================================================
$routes->get('/', 'Home::index'); 
$routes->post('home/ajax-fila', 'Home::ajaxCargarFila');
$routes->post('home/ajax-expandir-fila', 'Home::ajaxExpandirFila');
$routes->get('detalle/(:segment)', 'Home::detalle/$1');
$routes->get('ver/(:segment)', 'Home::ver/$1');
$routes->get('mi-lista', 'Home::miLista');
// $routes->get('peliculas', 'Home::paginaPeliculas');
$routes->get('series', 'Serie::index');
$routes->get('ayuda', 'Home::ayuda');
$routes->get('genero/(:num)', 'Home::verGenero/$1');

$routes->get('genero/(:num)/(:num)', 'Home::verGenero/$1/$2'); 
$routes->get('genero/(:num)', 'Home::verGenero/$1');
// Rutas AJAX Series
$routes->post('serie/ajax-fila', 'Serie::ajaxCargarFila');
$routes->post('serie/ajax-expandir-fila', 'Serie::ajaxExpandirFila');
$routes->get('peliculas', 'Peliculas::index');

// 2. Las peticiones AJAX (Deben ser POST)
$routes->post('peliculas/ajax-fila', 'Peliculas::ajaxCargarFila');
$routes->post('peliculas/ajax-expandir-fila', 'Peliculas::ajaxExpandirFila');

$routes->get('director/(:num)', 'Home::director/$1');
$routes->post('autocompletar', 'Home::autocompletar');
$routes->get('persona/(:any)', 'Home::persona/$1');
$routes->post('ajax/cargar-fila', 'Home::ajaxCargarFila');

// --- GESTIÓN DE CUENTA Y CONFIGURACIÓN ---
$routes->get('cuenta', 'Cuenta::index');
$routes->post('cuenta/cambiar-password', 'Cuenta::cambiar_password');
$routes->post('cuenta/cancelar-suscripcion', 'Cuenta::cancelar_suscripcion');

// --- RUTA PARA ANGULAR (Si la usas) ---
$routes->get('global', 'Home::vistaGlobal');
$routes->get('global/(:any)', 'Home::vistaGlobal');

// =============================================================================
// 2. AUTENTICACIÓN (Perfiles y Login Admin)
// =============================================================================
// Login de Usuarios
$routes->get('auth', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->get('auth', 'Auth::index');
$routes->get('login', 'Auth::index');

$routes->post('auth/ajax_login_perfil', 'Auth::login'); 

$routes->post('auth/login_general', 'Auth::login_general');
$routes->post('auth/register', 'Auth::register');


$routes->get('auth/confirmar_registro', 'Auth::confirmar_registro');
$routes->get('auth/pago_cancelado', 'Auth::pago_cancelado');

// --- REGISTRO Y PAGOS ---
$routes->get('registro', 'Auth::registro');
$routes->post('auth/crear', 'Auth::crear_usuario');

$routes->get('pasarela', 'Auth::pasarela_pago');     
$routes->post('auth/pagar', 'Auth::procesar_pago');   
$routes->get('pasarela-upgrade', 'Perfil::pasarela_upgrade');
$routes->post('perfil/pagar-upgrade', 'Perfil::procesar_upgrade');
$routes->get('perfil/confirmar_upgrade', 'Perfil::confirmar_upgrade'); 

// Logout
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
        $routes->get('/', 'Peliculas::index');
        $routes->get('create', 'Peliculas::create');
        $routes->post('store', 'Peliculas::store');
        
        // Rutas de edición
        $routes->get('editar/(:num)', 'Peliculas::edit/$1');
        $routes->post('update/(:num)', 'Peliculas::update/$1');
        
        $routes->get('borrar/(:num)', 'Peliculas::delete/$1');
    });

    // --- GESTIÓN DE SERIES ---
    $routes->group('series', function ($routes) {
        $routes->get('/', 'Series::index');
        $routes->get('create', 'Series::create');
        $routes->post('store', 'Series::store');

        // Rutas de edición
        $routes->get('editar/(:num)', 'Series::edit/$1');
        $routes->post('update/(:num)', 'Series::update/$1');

        $routes->get('borrar/(:num)', 'Series::delete/$1');
    });

    // --- GESTIÓN DE USUARIOS (CORREGIDO) ---
    $routes->get('usuarios', 'Usuarios::index');
    
    $routes->get('usuarios/editar/(:num)', 'Usuarios::edit/$1');
    $routes->post('usuarios/update/(:num)', 'Usuarios::update/$1');
    
    $routes->get('usuarios/borrar/(:num)', 'Usuarios::delete/$1');
});