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

$routes->get('/auth/login', 'Auth::login');
$routes->get('/auth/callback', 'Auth::callback');
$routes->get('/auth/status', 'Auth::status');
$routes->get('/logout', 'Auth::logout');

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

/********* OJS */
$routes->get('ojs', 'OJS::index');
$routes->post('ojs/send', 'OJS::send');
$routes->get('ojs/journal', 'OJS::journal');

$routes->group('events', function ($routes) {
    $routes->get('/', 'Event::index');
    $routes->get('create', 'Event::create');
    $routes->post('store', 'Event::store');
    $routes->get('edit/(:num)', 'Event::edit/$1');
    $routes->post('update/(:num)', 'Event::update/$1');
    $routes->get('delete/(:num)', 'Event::delete/$1');
});

/********* BrapciLAB */
//$routes->group('lab', ['filter' => 'auth'], function ($routes) {
//$routes->group('labs', function ($routes) {
$routes->group('labs', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'BrapciLab::home');
    $routes->get('profile', 'BrapciLab::profile');

    $routes->group('authority', function ($routes) {
        $routes->get('(:any)', 'BrapciLab::index_authority/$1');
        $routes->get('(:any)/(:any)', 'BrapciLab::index_authority/$1/$2');
        $routes->get('(:any)/(:any)/(:any)', 'BrapciLab::index_authority/$1/$2/$3');
        $routes->get('', 'BrapciLab::index_authority');
    });

    $routes->group('works', function ($routes) {
        $routes->get('(:any)', 'BrapciLab::index_works/$1');
        $routes->get('(:any)/(:any)', 'BrapciLab::index_works/$1/$2');
        $routes->get('(:any)/(:any)/(:any)', 'BrapciLab::index_works/$1/$2/$3');
        $routes->get('', 'BrapciLab::index_works');
    });

    $routes->group('projects', function ($routes) {
        $routes->get('select', 'BrapciLab::selectProject');
        $routes->post('set', 'BrapciLab::setProject');
        $routes->get('new', 'BrapciLab::new');
        $routes->get('edit/(:num)', 'BrapciLab::edit/$1');
        $routes->post('update/(:num)', 'BrapciLab::update/$1');
        $routes->post('create', 'BrapciLab::create');
    });

    $routes->group('oai', function ($routes) {
        $routes->get('', 'BrapciLab::OAIwelcome');
        $routes->get('select', 'BrapciLab::selectRepository');
        $routes->get('select/(:num)', 'BrapciLab::setRepository/$1');
        $routes->get('identify', 'BrapciLab::OAIidentify');
        $routes->get('sets', 'BrapciLab::OAIlistarSets');
    });

    $routes->group('ai', function ($routes) {
        $routes->get('', 'BrapciLab::AIwelcome');
        $routes->get('(:any)', 'BrapciLab::AIwelcome/$1');
        $routes->get('(:any)/(:any)', 'BrapciLab::AIwelcome/$1/$2');
        $routes->get('(:any)/(:any)/(:any)', 'BrapciLab::AIwelcome/$1/$2/$3');
        $routes->get('(:any)/(:any)/(:any)/(:any)', 'BrapciLab::AIwelcome/$1/$2/$3/$4');
    });



    $routes->get('importRIS', 'BrapciLab::uploadRIS');
    $routes->post('importRIS', 'BrapciLab::importRIS');

    $routes->group('api-library', function ($routes) {
        $routes->get('/', 'BrapciLabsApiLibrary::index');
        $routes->get('create', 'BrapciLabsApiLibrary::create');
        $routes->post('store', 'BrapciLabsApiLibrary::store');
        $routes->get('edit/(:num)', 'BrapciLabsApiLibrary::edit/$1');
        $routes->post('update/(:num)', 'BrapciLabsApiLibrary::update/$1');
        $routes->get('delete/(:num)', 'BrapciLabsApiLibrary::delete/$1');
        $routes->get('show/(:num)', 'BrapciLabsApiLibrary::show/$1');
    });

    $routes->group('project', ['filter' => 'projectRequired'], function ($routes) {
        $routes->get('codebook', 'BrapciLab::codebook');
        $routes->get('codebook/view/(:num)', 'BrapciLab::codebook_view/$1');
        $routes->get('codebook/new', 'BrapciLab::newCodebook');
        $routes->post('codebook/create', 'BrapciLab::createCodebook');
        $routes->get('codebook/edit/(:num)', 'BrapciLab::editCodebook/$1');
        $routes->post('codebook/update/(:num)', 'BrapciLab::updateCodebook/$1');
        $routes->post('codebook/delete/(:num)', 'BrapciLab::deleteCodebook/$1');

        /******* Authors */
        $routes->get('authors', 'BrapciLab::authors');
        $routes->get('authors/import', 'BrapciLab::authors_import');
        $routes->post('authors/import', 'BrapciLab::authors_import');
        $routes->get('authors/deduplicate', 'BrapciLab::authors_deduplicate');
        $routes->get('authors/check-ids', 'BrapciLab::check_ids');

        /******* Workds */
        $routes->get('works', 'BrapciLab::works');

    });
});


/********* BOOKMARKS */
$routes->get('bookmarks/search', 'Bookmarks::search');
$routes->get('bookmarks/import', 'Bookmarks::import');
$routes->get('bookmarks/folder', 'Bookmarks::folder');
$routes->get('/bookmarks/site/new/(:any)', 'Bookmarks::siteNew/$1');
$routes->post('bookmarks/site/save', 'Bookmarks::siteSave');
$routes->get('bookmarks/site/delete/(:num)', 'Bookmarks::siteDelete/$1');
$routes->post('bookmarks/folder/save', 'Bookmarks::folderSave');
$routes->get('bookmarks/folders/new', 'Bookmarks::folderNew');
$routes->get('bookmarks/folders/view/(:any)', 'Bookmarks::folderView/$1');
$routes->get('bookmarks/link/(:any)', 'Bookmarks::link/$1');
$routes->get('bookmarks', 'Bookmarks::index');

/* ********* KANBAN */
$routes->get('/kanban', 'Kanban::index');
$routes->post('/kanban/store', 'Kanban::store');
$routes->post('/kanban/update/(:num)', 'Kanban::update/$1');
$routes->post('/kanban/(:any)/comment', 'Kanban::addComment/$1');

/* G3event */
$routes->get('event/pessoas', 'G3vent::pessoas');
$routes->get('event/import', 'G3vent::import');
$routes->get('event/events', 'G3vent::events');
$routes->get('event/event/edit/(:any)', 'G3vent::events_edit/$1');
$routes->get('event/event/view/(:any)', 'G3vent::events_view/$1');
$routes->get('event/event/register/(:any)', 'G3vent::events_register/$1');
$routes->post('event/event/register/(:any)', 'G3vent::events_register/$1');
$routes->post('event/event/update/(:any)', 'G3vent::events_update/$1');
$routes->post('event/import', 'G3vent::importRun');
$routes->get('event', 'G3vent::index');




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