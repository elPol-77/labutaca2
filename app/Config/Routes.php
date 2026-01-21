<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('auth', 'Auth::index');       // Pantalla perfiles
$routes->post('auth/login', 'Auth::login'); // Proceso login AJAX
$routes->get('logout', 'Auth::logout');    // Salir