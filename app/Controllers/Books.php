<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '1000');
define("COLLECTION", '/books');

class Books extends BaseController
{
    public function index($act = '', $subact = '', $id = '')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci Livros - Ciência da Informação';
        $data['bg'] = 'bg-brapcilivros';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('BrapciBooks/Headers/navbar', $data);


        $q = get("query");
        if (strlen($q) > 0) {
            $act = 'search';
        }

        $act = trim($act);
        switch ($act) {
            case 'a':
                $Socials = new \App\Models\Socials();
                $cat = $Socials->getAccess("#ADM#CAT");
                if ($cat == true)
                    {
                        $RDF = new \App\Models\Rdf\RDF();

                        $link_a = PATH.'/rdf/form/editRDF/'.$subact;
                        $link_b = PATH.'/rdf/view/pdf/' . $subact;;

                        $sa = '<iframe src="'.$link_a.'" style="width: 100%; height:600px;"></iframe>';
                        $sb = '<iframe src="' . $link_b . '" style="width: 100%; height:600px;"></iframe>';

                        $sa = bsc($sa,6);
                        $sb = bsc($sb,6);
                        $sx .= bs($sa.$sb);
                    } else {
                        $sx .= bsmessage('Access not permited');
                        $sx .= bs(bsc($sx, 12));
                    }
                break;
            case 'v':
                $sx .= $this->v($subact);
                break;
            case 'xsearch':
                $data['logo'] = view('Logos/logo_benancib');
                $data['search'] = view('Benancib/Pages/search');
                $data['issues'] = '';
                if (get("di") != '') {
                    $_SESSION['search']['di'] = get("di");
                    $_SESSION['search']['df'] = get("df");
                    $_SESSION['search']['ord'] = get("ord");
                    $_SESSION['search']['field'] = get("field");
                } else {
                    if (isset($_SESSION['search']['di'])) {
                        $_GET['di'] = $_SESSION['search']['di'];
                        $_GET['df'] = $_SESSION['search']['df'];
                        $_GET['ord'] = $_SESSION['search']['ord'];
                        $_GET['field'] = $_SESSION['search']['field'];
                    } else {
                        $_GET['di'] = 1960;
                        $_GET['df'] = (date("Y") + 1);
                        $_GET['ord'] = 0;
                        $_GET['field'] = 0;
                    }
                }
                $sx .= view('Benancib/Welcome', $data);
                $SEARCH = new \App\Models\ElasticSearch\Index();
                $sx .= $SEARCH->index('search');
                break;
            case 'social':
                $Socials = new \App\Models\Socials();
                $sx .= bs(bsc($Socials->index($subact, $id), 12));
                break;

            case 'admin':
                $Books = new \App\Models\Books\Index();
                $sx .= $Books->admin($subact, $id);
                break;

            case 'about':
                $sa = '';
                $sx .= view('Brapci/Pages/under_construction');
                break;

            case 'autoloader':
                $Books = new \App\Models\Books\Index();
                $sx .= $Books->index($act, $subact, $id);
                break;

            default:
                $sx .= view('BrapciBooks/Pages/homepage');
                $Books = new \App\Models\Base\Book();
                $sx .= $Books->latest_acquisitions();
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    private function v($id = '')
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $class = $dt['concept']['c_class'];

        switch ($class) {
            case 'Subject':
                $Keywords = new \App\Models\Base\Keywords();
                $sx .= $Keywords->showHTML($dt);
                break;

            case 'Book':
                $Book = new \App\Models\Base\Book();
                $sx = $Book->showFULL($id);
                break;

            default:
                $sx .= h($class, 1);
                $sx = bs(bsc($sx));
                $sx .= $RDF->view_data($id);
                break;
        }
        return $sx;
    }
}