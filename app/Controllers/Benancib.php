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
define("COLLECTION", '/benancib');

class Benancib extends BaseController
{
    public function index($act = '', $subact = '', $id = '')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Benancib';
        $data['GOOGLEID'] = 'G-B720HV20XK';
        $data['bg'] = 'bg-benancib';
        $data['menu'] = array();
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar_benancib', $data);

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
            case 'a':
                $Socials = new \App\Models\Socials();
                $cat = $Socials->getAccess("#ADM#CAT#ENA");
                if ($cat == true) {
                    $RDF = new \App\Models\Rdf\RDF();

                    $link_a = PATH . '/rdf/form/editRDF/' . $subact;
                    $link_b = PATH . '/rdf/view/pdf/' . $subact;;

                    $sa = '<iframe src="' . $link_a . '" style="width: 100%; height:600px;"></iframe>';
                    $sb = '<iframe src="' . $link_b . '" style="width: 100%; height:600px;"></iframe>';

                    $sa = bsc($sa, 6);
                    $sb = bsc($sb, 6);
                    $sx .= '<a href="'.PATH.COLLECTION.'/v/'. $subact.
                            '" class="btn btn-outline-primary">'.
                            lang('brapci.return').'</a>';
                    $sx .= bs($sa . $sb);
                } else {
                    $sm = bsmessage('Access not permited - BENANCIB');
                    $sx .= bs(bsc($sm, 12));
                }
                break;
            case 'v':
                $Proceeding = new \App\Models\Base\Proceeding();
                $sx .= $Proceeding->v($subact);
                break;
            case 'issue':
                $Issues = new \App\Models\Base\Issues();
                $sx .= $Issues->index($subact, true);
                break;
            case 'issues':
                $Issues = new \App\Models\Base\Issues();
                $sx .= $Issues->issues($subact, $id);
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
                $sa .= bsc(view('Grapho/Sankey', $data), 12);
                $sb = '';

                $sx .= bs($sa . $sb);
                break;
            default:
                $id = 75;
                $data['logo'] = view('Logos/logo_benancib');
                $data['issues'] = $Issues->show_list_cards($id);
                $data['search'] = view('Benancib/Pages/search');
                $sx .= view('Benancib/Welcome', $data);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}
