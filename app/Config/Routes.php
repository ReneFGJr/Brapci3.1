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

$routes->get('/capes', 'Dci::capes');
$routes->get('/capes(:any)', 'Dci::capes/$1');
$routes->post('/capes(:any)', 'Dci::capes/$1');

/* Original */
/********* CRAWLER */
$routes->get('/dci/', 'Dci::index/');
$routes->get('/dci/(:any)', 'Dci::index/$1');
$routes->post('/dci/(:any)', 'Dci::index/$1');

$routes->get('/wp/', 'Wp::index/');
$routes->get('/wp/(:any)', 'Wp::index/$1');

/********** CHATBOOT */
$routes->get('/chat/', 'Chatbot::index/');
$routes->get('/chatbot/', 'Chatbot::index/');


/********** CDU */
$routes->get('/cdu', 'Cdu::index');
$routes->get('/cdu/(:any)', 'Cdu::index/$1');
$routes->post('/cdu/(:any)', 'Cdu::index/$1');

/********** DOWNLOAD */
$routes->get('/download', 'Download::index');
$routes->get('/download/(:any)', 'Download::download/$1');

/********** DOWNLOAD */
$routes->get('/bibliofind', 'Bibliofind::index');
$routes->get('/bibliofind/(:any)', 'Bibliofind::index/$1');

/*********** DOI */
$routes->get('/doi/(:any)', 'Doi::index/$1');

/*********** OAI-PMH */
$routes->get('/oai/(:any)', 'Oai::index/$1');
$routes->get('/oai/', 'Oai::index');

/********** BOTS */
$routes->get('/bots', 'Bots::index');
$routes->get('/bots/(:any)', 'Bots::index/$1');

/********** BOTS */
$routes->get('/guide', 'Guide::index');
$routes->get('/guide/(:any)', 'Guide::index/$1');
$routes->post('/guide/(:any)', 'Guide::index/$1');

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
$routes->put('/api/(:any)', 'Api::index/$1');

/********* SOCIAL */
$routes->get('/social', 'Social::index');
$routes->post('/social/ajax/(:any)', 'Social::ajax/$1');
$routes->get('/social/(:any)', 'Social::index/$1');
$routes->post('/social/(:any)', 'Social::index/$1');

$sc = ['pq', 'tools', 'ai', 'authoriry', 'autoridade', 'bibliofind', 'parecer', 'patente', 'guide'];
foreach ($sc as $id => $nm) {
    $routes->post('/' . $nm . '/social/(:any)', 'Social::index/$1');
    $routes->get('/' . $nm . '/social/(:any)', 'Social::index/$1');

    $routes->post('/' . $nm . '/a/(:any)', 'MainPages::index/a/$1');
    $routes->get('/' . $nm . '/a/(:any)', 'MainPages::index/a/$1');
}

/********* MANUAL */
$routes->get('/manual/', 'Manuais::index/$1/$2');
$routes->post('/manual/(:any)/(:any)', 'Manuais::index/$1/$2');
$routes->get('/manual/(:any)/(:any)', 'Manuais::index/$1/$2');

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


/* G3event */
$routes->get('/event/(:any)', 'G3vent::index/$1');
$routes->post('/event/(:any)', 'G3vent::index/$1');
$routes->get('/event/(:any)/(:any)', 'G3vent::index/$1/$2');
$routes->post('/event/(:any)/(:any)', 'G3vent::index/$1/$2');
$routes->get('/event', 'G3vent::index');

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

/********* Thesa */
$routes->get('/thesa/(:any)', 'Thesa::index/$1');
$routes->get('/thesa', 'Thesa::index');

/********** Others */
//$routes->get('(:any)', 'MainPages::index/$1');
$routes->get('(:any)', 'MainPages::index/$1');
$routes->get('(:any)/(:any)', 'MainPages::index/$1/$2');
$routes->get('(:any)/(:any)/(:any)', 'MainPages::index/$1/$2/$3');

/********** DEFAULT */
$routes->get('/', 'MainPages::index');
$routes->post('/', 'MainPages::index');