<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Main');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

/********** DEFAULT */
$routes->get('/', 'Main::index');
$routes->post('/', 'Main::index');

/********* PGCD */
$routes->get('/pgcd/', 'Pgcd::index/');
$routes->get('/pgcd/(:any)', 'Pgcd::index/$1');
$routes->post('/pgcd/(:any)', 'Pgcd::index/$1');

/********* AJAX */
$routes->get('/ajax/(:any)', 'Ajax::index/$1');
$routes->post('/ajax/(:any)', 'Ajax::index/$1');

/********* SOCIAL */
$routes->get('/social', 'Social::index');
$routes->post('/social/ajax/(:any)', 'Social::ajax/$1');
$routes->get('/social/(:any)', 'Social::index/$1');
$routes->post('/social/(:any)', 'Social::index/$1');


/********* BENANCIB */
$routes->get('/benancib/(:any)/(:any)', 'Benancib::index/$1/$2');
$routes->post('/benancib/(:any)/(:any)', 'Benancib::index/$1/$2');
$routes->get('/benancib/(:any)/', 'Benancib::index/$1');
$routes->post('/benancib/(:any)/', 'Benancib::index/$1');
$routes->get('/benancib', 'Benancib::index');

/********* TOOLS */
$routes->get('/elasticsearch/(:any)/(:any)', 'Elasticsearch::index/$1/$2');
$routes->get('/elasticsearch/(:any)/', 'Elasticsearch::index/$1');
$routes->get('/elasticsearch', 'Elasticsearch::index');

$routes->post('/elasticsearch/(:any)/(:any)', 'Elasticsearch::index/$1/$2');
$routes->post('/elasticsearch/(:any)/', 'Elasticsearch::index/$1');

/********* POPUP */
$routes->get('/popup/(:any)', 'Popup::index/$1');
$routes->post('/popup/(:any)', 'Popup::index/$1');

/********* POPUP */
$routes->get('/admin/(:any)', 'Admin::index/$1');
$routes->post('/admin/(:any)', 'Admin::index/$1');
$routes->get('/admin/(:any)/(:any)', 'Admin::index/$1/$2');
$routes->post('/admin/(:any)/(:any)', 'Admin::index/$1/$2');
$routes->get('/admin', 'Admin::index');

/********* PQ */
$routes->get('/pq/(:any)', 'Pq::index/$1');
$routes->get('/pq', 'Pq::index');

/********** Others */
//$routes->get('(:any)', 'MainPages::index/$1');
$routes->get('(:any)', 'MainPages::index/$1');
$routes->get('(:any)/(:any)', 'MainPages::index/$1/$2');
$routes->get('(:any)/(:any)/(:any)', 'MainPages::index/$1/$2/$3');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}