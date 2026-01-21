<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- 1. FRONTEND ---
$routes->get('/', 'Home::index');
$routes->get('detalle/(:num)', 'Home::detalle/$1');
$routes->get('ver/(:num)', 'Home::ver/$1');

// --- 2. AUTENTICACIÓN ---
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');


$routes->post('api/buscador/autocompletar', 'Home::autocompletar');
$routes->post('api/usuario/toggle-lista', 'Api\Usuario::toggleLista');
$routes->get('mi-lista', 'Home::miLista');
$routes->get('director/(:num)', 'Home::director/$1');