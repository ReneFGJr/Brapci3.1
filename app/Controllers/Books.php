<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", '/books');

class Books extends BaseController
{
    public function index($act = '', $subact = '', $id = '')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Proceedings';
        $data['bg'] = 'bg-brapcilivros';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Benancib/Headers/navbar', $data);

        $q = get("query");
        if (strlen($q) > 0) {
            $act = 'search';
        }
        $act = trim($act);
        switch ($act) {
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
        echo h($class);

        switch ($class) {
            case 'Subject':
                $Keywords = new \App\Models\Base\Keywords();
                $sx .= $Keywords->showHTML($dt);
                break;

            case 'Proceeding':
                $Proceeding = new \App\Models\Base\Proceeding();
                $sx .= $Proceeding->show($dt);
                break;

            case 'ProceedingSection':
                $ProceedingSection = new \App\Models\Base\ProceedingSection();
                $sx .= $ProceedingSection->show($dt);
                break;

            case 'Work':
                echo "OK";
                $Work = new \App\Models\Base\Work();
                $sx .= $Work->show($id);
                break;
            default:
                $sx .= h($class, 1);
                $sx = bs(bsc($sx));
                break;
        }
        return $sx;
    }
}