<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/api', 'Api::index');
$routes->get('/api/(:any)', 'Api::index/$1');
$routes->post('/api/(:any)', 'Api::index/$1');
$routes->put('/api/(:any)', 'Api::index/$1');