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
define("LIBRARY", '1000');
define("COLLECTION", '');

class MainPages extends BaseController
{

    public function index($act = '', $subact = '', $id = '')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Revistas';
        $data['GOOGLEID'] = 'UA-12713129-1';
        $data['bg'] = 'bg-primary';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Benancib/Headers/navbar', $data);

        $q = get("q") . get("qs");
        if (strlen($q) > 0) {
            $act = 'search';
        }

        $act = trim($act);

        switch ($act) {
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
                $sx .= $SEARCH->index('search');
                break;
            case 'social':
                $Socials = new \App\Models\Socials();
                $sx .= bs(bsc($Socials->index($subact, $id), 12));
                break;
            case 'v':
                $sx .= $this->v($subact);
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
                $sa = '';
                $sa .= bsc(view('Logos/logo_benancib'), 12, 'text-center');
                $sa .= bsc(view('Benancib/Pages/about', $data), 12);
                $data['height'] = 100;
                $sb = '';
                $sb .= bsc(view('Logos/logo_ppgci_uff.php', $data), 4, 'text-center mt-5');
                $sb .= bsc('', 4, 'mt-5');
                $sb .= bsc(view('Logos/logo_ppgcin_ufrgs.php', $data), 4, 'text-center mt-5');
                $sx .= bs($sa . $sb);
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
                $sx .= view('Brapci/Pages/search');
                //$sx .= view('Brapci/Welcome', $data);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    function v($id)
        {
            $RDF = new \App\Models\Rdf\RDF();
            $dt = $RDF->le($id);

            if (!isset($dt['concept'])) {
                return ('Concept not found');
                exit;
            }

            $concept = $dt['concept'];
            $class = $concept['c_class'];

            $class = $dt['concept']['c_class'];

            switch($class)
                {
                    case 'journal':
                        $sx = $RDF->journal($id);
                        break;
                    case 'issue':
                        $sx = $RDF->issue($id);
                        break;
                    case 'Article':
                    $dt = $RDF->le($id);
                        $sx = view('Brapci/View/Article',$dt);
                        break;
                    default:
                        $sx = 'Class not found - '.$class;
                        break;
                }
                return($sx);
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