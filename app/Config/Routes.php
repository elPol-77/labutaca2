<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');      // Frontend
$routes->get('admin', 'Admin::index'); // Backend