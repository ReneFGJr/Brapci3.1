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
$routes->setDefaultController('MainPages');
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

/********* CRAWLER */
$routes->get('/dci/', 'Dci::index/');
$routes->get('/dci/(:any)', 'Dci::index/$1');

/********** DOWNLOAD */
$routes->get('/catalog', 'Catalog::index');

/********** DOWNLOAD */
$routes->get('/download', 'Download::index');
$routes->get('/download/(:any)', 'Download::download/$1');

/********** DOWNLOAD */
$routes->get('/bibliofind', 'Bibliofind::index');
$routes->get('/bibliofind/(:any)', 'Bibliofind::index/$1');

/*********** DOI */
$routes->get('/doi/(:any)', 'Doi::index/$1');

/********** BOTS */
$routes->get('/bots', 'Bots::index');
$routes->get('/bots/(:any)', 'Bots::index/$1');

/********* AJAX */
$routes->get('/ajax/(:any)', 'Ajax::index/$1');
$routes->post('/ajax/(:any)', 'Ajax::index/$1');
$routes->post('/ajax/(:any)/', 'Ajax::index/$1');
$routes->post('/ajax/(:any)/(:any)', 'Ajax::index/$1/$2');

/********* API */
$routes->get('/ws', 'Api::index');
$routes->get('/ws/api', 'Api::index');
$routes->get('/ws/api/(:any)', 'Api::index/$1');
$routes->get('/ws/(:any)/(:any)', 'Api::index/$1/$2');
$routes->get('/api', 'Api::index');
$routes->get('/api/(:any)', 'Api::index/$1');
$routes->post('/api/(:any)', 'Api::index/$1');

/********* SOCIAL */
$routes->get('/social', 'Social::index');
$routes->post('/social/ajax/(:any)', 'Social::ajax/$1');
$routes->get('/social/(:any)', 'Social::index/$1');
$routes->post('/social/(:any)', 'Social::index/$1');


/********* BENANCIB */
$routes->get('/benancib/(:any)/(:any)', 'Benancib::index/$1/$2');
$routes->post('/benancib/(:any)/(:any)', 'Benancib::index/$1/$2');
$routes->get('/benancib/(:any)', 'Benancib::index/$1');
$routes->post('/benancib/(:any)', 'Benancib::index/$1');
$routes->get('/benancib', 'Benancib::index');

/********* Proceedings */
$routes->get('/proceedings/(:any)/(:any)', 'Proceedings::index/$1/$2');
$routes->post('/proceedings/(:any)/(:any)', 'Proceedings::index/$1/$2');
$routes->get('/proceedings/(:any)', 'Proceedings::index/$1');
$routes->post('/proceedings/(:any)', 'Proceedings::index/$1');
$routes->get('/proceedings', 'Proceedings::index');

/********* AI */
$routes->get('/ai', 'Ai::index');
$routes->get('/ai/(:any)', 'Ai::index/$1');
$routes->get('/ai/(:any)/(:any)', 'Ai::index/$1/$2');

$routes->post('/ai/(:any)', 'Ai::index/$1');
$routes->post('/ai/(:any)/(:any)', 'Ai::index/$1/$2');

/********* CRAWLER */
$routes->get('/crawler', 'Crawler::index');
$routes->get('/crawler/(:any)', 'Crawler::index/$1');
$routes->get('/crawler/(:any)/(:any)', 'Crawler::index/$1/$2');

$routes->post('/crawler/(:any)', 'Crawler::index/$1');
$routes->post('/crawler/(:any)/(:any)', 'Crawler::index/$1/$2');

/********* DADOS */
$routes->get('/data', 'Dados::index');
$routes->get('/dataverse', 'Dados::index/dataverse');
$routes->get('/dataverse/(:any)', 'Dados::index/dataverse/(:any)');
$routes->get('/dados', 'Dados::index');
$routes->get('/dados/(:any)', 'Dados::index/$1');
$routes->get('/dados/(:any)/(:any)', 'DAdos::index/$1/$2');
$routes->post('/dados/(:any)', 'Dados::index/$1');


/********* Books */
$routes->get('/books/(:any)/(:any)', 'Books::index/$1/$2');
$routes->post('/books/(:any)/(:any)', 'Books::index/$1/$2');
$routes->get('/books/(:any)/(:any)/(:any)', 'Books::index/$1/$2/$3');
$routes->post('/books/(:any)/(:any)/(:any)', 'Books::index/$1/$2/$3');
$routes->get('/books/(:any)', 'Books::index/$1');
$routes->post('/books/(:any)', 'Books::index/$1');
$routes->get('/books', 'Books::index');

/********* TOOLS */
//$routes->get('/rdf', 'Rdf::index');
$routes->get('/rdf/', 'Rdf::index/');
$routes->get('/rdf/(:any)', 'Rdf::index/$1');
$routes->get('/rdf/(:any)/(:any)', 'Rdf::index/$1/$2');
$routes->post('/rdf/(:any)', 'Rdf::index/$1');
$routes->post('/rdf/(:any)/(:any)', 'Rdf::index/$1/$2');
$routes->post('/rdf/(:any)/(:any)/(:any)', 'Rdf::index/$1/$2/$3');
$routes->post('/rdf/(:any)/(:any)/(:any)/(:any)', 'Rdf::index/$1/$2/$3/#4');

/********* TOOLS */
$routes->get('/elasticsearch/(:any)/(:any)', 'Elasticsearch::index/$1/$2');
$routes->get('/elasticsearch/(:any)/', 'Elasticsearch::index/$1');
$routes->get('/elasticsearch', 'Elasticsearch::index');

$routes->post('/elasticsearch/(:any)/(:any)', 'Elasticsearch::index/$1/$2');
$routes->post('/elasticsearch/(:any)/', 'Elasticsearch::index/$1');

/********* POPUP */
$routes->get('/popup/(:any)', 'Popup::index/$1');
$routes->post('/popup/(:any)', 'Popup::index/$1');

/********* ADMIN */
$routes->get('/admin/(:any)', 'Admin::index/$1');
$routes->post('/admin/(:any)', 'Admin::index/$1');
$routes->get('/admin/(:any)/(:any)', 'Admin::index/$1/$2');
$routes->post('/admin/(:any)/(:any)', 'Admin::index/$1/$2');
$routes->get('/admin', 'Admin::index');

/********* PQ */
$routes->get('/pq/(:any)', 'Pq::index/$1');
$routes->get('/pq', 'Pq::index');

/********* Patent */
$routes->get('/patente/(:any)', 'Patente::index/$1');
$routes->get('/patente', 'Patente::index');
$routes->post('/patente/(:any)', 'Patente::index/$1');

/********* Observatorio */
$routes->get('/autoridade/(:any)', 'Authority::index/$1');
$routes->get('/autoridade', 'Authority::index');
$routes->post('/autoridade/(:any)', 'Authority::index/$1');
$routes->post('/autoridade', 'Authority::index/');

/********* Observatorio */
$routes->get('/observatorio/(:any)', 'Observatorio::index/$1');
$routes->get('/observatorio', 'Observatorio::index');
$routes->post('/observatorio/(:any)', 'Observatorio::index/$1');

/********* Parecer */
$routes->get('/parecer/(:any)', 'Parecer::index/$1');
$routes->get('/parecer', 'Parecer::index');
$routes->post('/parecer/(:any)', 'Parecer::index/$1');

/********* Tools */
$routes->get('/tools/(:any)', 'Tools::index/$1');
$routes->get('/tools', 'Tools::index');
$routes->post('/tools/(:any)', 'Tools::index/$1');

/********** Others */
//$routes->get('(:any)', 'MainPages::index/$1');
$routes->get('(:any)', 'MainPages::index/$1');
$routes->get('(:any)/(:any)', 'MainPages::index/$1/$2');
$routes->get('(:any)/(:any)/(:any)', 'MainPages::index/$1/$2/$3');

/********** DEFAULT */
$routes->get('/', 'MainPages::index');
$routes->post('/', 'MainPages::index');

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