<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */

$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie', 'highchart']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '0000');
define("COLLECTION", '');

class MainPages extends BaseController
{

    public function index($act = '', $subact = '', $id = '', $id2='')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Revistas';
        $data['GOOGLEID'] = 'UA-12713129-1';
        $data['bg'] = 'bg-primary';
        $data['bg_color'] = '#0000ff';
        $menu = array();
        $menu[PATH . '/'] = lang('brapci.journals');
        $menu[PATH . '/books'] = lang('brapci.books');
        $menu[PATH . '/benancib'] = lang('brapci.benancib');
        $menu[PATH . '/proceedings'] = lang('brapci.proceedings');
        $menu[PATH . '/autoridade'] = lang('brapci.authoritynames');

        $data['menu'] = $menu;
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);

        $m = [];
        $m['Brapci'] = PATH;
        $m[lang('brapci.journals')] = PATH.'/journals';
        $sx .= breadcrumbs($m);

        $act = trim($act);

        switch ($act) {
            case 'indexes':
                $Indexes = new \App\Models\Base\Indexes();
                $sx .= $Indexes->show_index($subact,$id);
                break;
            case 'journals':
                $Sources = new \App\Models\Base\Sources();
                $sx .= $Sources->index($subact, $id, $id2);
                break;
            case 'mark':
                $Mark = new \App\Models\Base\Mark();
                $sx .= $Mark->index($subact, $id);
                break;
            case 'services':
                $sx .= $this->services();
                break;
            case 'saveMark':
                $Mark = new \App\Models\Base\Mark();
                $sx .= $Mark->saveMark();
                break;
            case 'analyse':
                $AnalyseStudy = new \App\Models\MetricStudy\Analyse();
                $sx .= $AnalyseStudy->index();
                break;
            case 'search':
                $data['logo'] = view('Logos/logo_brapci');
                $data['search'] = view('Brapci/Pages/search');

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
                $sx .= view('Brapci/Result', $data);
                $SEARCH = new \App\Models\ElasticSearch\Index();
                $sx .= $SEARCH->index('search','journal');
                break;
            case 'social':
                $Socials = new \App\Models\Socials();
                $sx .= bs(bsc($Socials->index($subact, $id), 12));
                break;
            case 'v':
                $V = new \App\Models\Base\V();
                $sx .= $V->v($subact);
                break;
            case 'a':
                $sx .= $this->a($subact);
                break;
            case 'issue':
                $Issues = new \App\Models\Base\Issues();
                $sx .= $Issues->index($subact, $id);
                break;
            case 'admin':
                $ADMIN = new \App\Models\Base\Admin\Index();
                $sx .= bsc(view('Logos/logo_benancib'), 12, 'text-center');
                $sx .= $ADMIN->index($subact, $id);
                break;

            case 'about':
                $About = new \App\Models\Base\About();
                $sx .= $About->about();
                break;

            case 'statistics':
                $sa = '';
                $sa .= bsc(view('Logos/logo_benancib'), 12, 'text-center');
                $sa .= bsc(view('Benancib/Pages/statistics', $data), 12);
                $data['height'] = 100;
                $sb = '';
                $sb .= bsc(view('Logos/logo_ppgci_uff.php', $data), 4, 'text-center mt-5');
                $sb .= bsc('', 4, 'mt-5');
                $sb .= bsc(view('Logos/logo_ppgcin_ufrgs.php', $data), 4, 'text-center mt-5');
                $sx .= bs($sa . $sb);
                break;
            default:
                $id = 75;
                $data['logo'] = view('Logos/logo_benancib');

                $Events = new \App\Models\Functions\Event();
                $sx .= $Events->index('cards');
                //$sx .= view('Brapci/Welcome', $data);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }



    function services()
    {
        $sx = '';
        $menu[PATH . '/tools'] = lang('brapci.service_lattes');
        foreach ($menu as $link => $name) {
            $sx .= '<a href="' . $link . '" class="btn btn-ouline-primary m-2">' . $name . '</a>';
        }
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    function a($id)
    {
        $sx = '';
        $Socials = new \App\Models\Socials();
        $cat = $Socials->getAccess("#ADM#CAT");
        if ($cat == true) {
            $RDF = new \App\Models\Rdf\RDF();

            $link_a = PATH . '/rdf/form/editRDF/' . $id;
            $link_b = PATH . '/rdf/view/pdf/' . $id;;

            $sa = '<iframe src="' . $link_a . '" style="width: 100%; height:600px;"></iframe>';
            $sb = '<iframe src="' . $link_b . '" style="width: 100%; height:600px;"></iframe>';

            $sa = bsc($sa, 6);
            $sb = bsc($sb, 6);
            $sx = bs($sa . $sb);
        } else {
            $sx .= bsmessage('Access not permited');
            $sx = bs(bsc($sx, 12));
        }
        return $sx;
    }

    public function index2($pag = '')
    {
        $data['GOOGLEID'] = 'UA-12713129-1';
        $sx = '';
        /**** PAGES */
        if ($pag == '') {
            $pag = 'search';
            //$pag = 'under_construction';
        }
        $data['page_title'] = 'Brapci - ' . ucfirst($pag);
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        /**** CHECK PAGE */

        $file = APPPATH . 'Views/Brapci/Pages/' . strtolower($pag) . '.php';
        if (file_exists($file)) {
            $sx .= view('Brapci/Pages/' . $pag);
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        /**** FOOTER */
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}