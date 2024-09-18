<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('MainPages');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

$routes->get('/api', 'Api::index');
$routes->get('/api/(:any)', 'Api::index/$1');
$routes->post('/api/(:any)', 'Api::index/$1');
$routes->put('/api/(:any)', 'Api::index/$1');

/********** DEFAULT */
$routes->get('/', 'MainPages::index');
$routes->post('/', 'MainPages::index');