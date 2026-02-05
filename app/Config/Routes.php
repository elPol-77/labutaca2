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
$routes->get('series', 'Serie::index');
$routes->post('serie/ajax-fila', 'Serie::ajaxCargarFila');
$routes->post('serie/ajax-expandir-fila', 'Serie::ajaxExpandirFila');
$routes->get('director/(:num)', 'Home::director/$1');
$routes->post('autocompletar', 'Home::autocompletar');
$routes->get('persona/(:segment)', 'Home::persona/$1');
$routes->post('ajax/cargar-fila', 'Home::ajaxCargarFila');

// --- RUTA PARA ANGULAR ---
$routes->get('global', 'Home::vistaGlobal');
$routes->get('global/(:any)', 'Home::vistaGlobal');

// =============================================================================
// 2. AUTENTICACIÓN (Perfiles y Login Admin)
// =============================================================================
// Login de Usuarios (Perfiles tipo Netflix)
$routes->get('auth', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('auth/logout', 'Auth::logout');

// Selección de perfiles
$routes->get('profiles', 'Profiles::index');
$routes->get('profiles/select/(:segment)', 'Profiles::select/$1');

// GESTIÓN DE PERFIL
$routes->get('perfil', 'Perfil::index');
$routes->post('perfil/update', 'Perfil::update');

// Login de Administración (Panel Técnico)
// Estas rutas deben coincidir con el action de tu formulario HTML
$routes->get('admin/login', 'Admin\Auth::index');
$routes->post('admin/auth/login', 'Admin\Auth::login');


// =============================================================================
// 3. API INTERNA RESTful (Conectada al JS)
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
    $routes->get('peliculas-landing', 'Catalogo::getPeliculasLanding'); // <--- AÑADE ESTA

    // BUSCADOR (Esta es la ruta crítica corregida)
    // Al estar dentro del grupo 'api', la URL final será: /api/buscador/autocompletar
    $routes->post('buscador/autocompletar', 'Catalogo::autocompletar');
});

// =============================================================================
// 4. ÁREA DE ADMINISTRACIÓN (Protegida por Filtro)
// =============================================================================
$routes->group('admin', ['filter' => 'adminAuth', 'namespace' => 'App\Controllers\Admin'], function ($routes) {

    // Dashboard Principal
    $routes->get('/', 'Dashboard::index');

    // --- GESTIÓN DE PELÍCULAS ---
    $routes->group('peliculas', function ($routes) {
        $routes->get('/', 'Peliculas::index');
        $routes->get('create', 'Peliculas::create');
        $routes->post('store', 'Peliculas::store');
        $routes->get('borrar/(:num)', 'Peliculas::delete/$1');
    });

    // --- GESTIÓN DE SERIES ---
    $routes->group('series', function ($routes) {
        $routes->get('/', 'Series::index');
        $routes->get('create', 'Series::create');
        $routes->post('store', 'Series::store');
        $routes->get('borrar/(:num)', 'Series::delete/$1');
    });

    // --- GESTIÓN DE USUARIOS ---
    $routes->get('usuarios', 'Usuarios::index');
    $routes->get('usuarios/borrar/(:num)', 'Usuarios::delete/$1');
    $routes->post('usuarios/cambiar-rol', 'Usuarios::cambiarRol');
});