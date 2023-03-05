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
define("COLLECTION", '/proceedings');

class Proceedings extends BaseController
{
    public function index($act = '', $subact = '', $id = '')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Proceedings';
        $data['bg'] = 'bg-proceedings';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Proceeding/Headers/navbar', $data);

        $q = get("query");
        if (strlen($q) > 0) {
            $act = 'search';
        }
        $act = trim($act);

        switch ($act) {
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
                    $sx .= '<a href="' . PATH . COLLECTION . '/v/' . $subact .
                        '" class="btn btn-outline-primary">' .
                        lang('brapci.return') . '</a>';
                    $sx .= bs($sa . $sb);
                } else {
                    $sx .= bsmessage('Access not permited');
                    $sx .= bs(bsc($sx, 12));
                }
                break;
            case 'v':
                $Proceeding = new \App\Models\Base\Proceeding();
                $sx .= $Proceeding->v($subact);
                break;
            case 'oai':
                $sx .= $this->oai($subact, $id);
                break;
            case 'harvesting':
                $OAI_ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
                $sx .= $OAI_ListIdentifiers->harvesting($d2);
                break;
            case 'issue':
                $sx .= $this->issues($subact, $id);
                break;
            case 'issues':
                $Issues = new \App\Models\Base\Issues();
                $sx .= $Issues->issues($subact, $id);
                break;
            case 'source':
                $Sources = new \App\Models\Base\Sources();
                $Issues = new \App\Models\Base\Issues();
                $dt = $Sources->find($subact);
                $sx .= $Sources->journal_header($dt);

                $sx .= $Issues->show_list_cards($dt['id_jnl']);
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

            case 'about':
                $sa = '';
                $sx .= view('Brapci/Pages/under_construction');
                break;

            default:
                $sx .= view('Proceeding/Pages/home');
                $Sources = new \App\Models\Base\Sources();
                $sx .= $Sources->source_list_block();
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    function issues($subact, $id)
        {
            $Issues = new \App\Models\Base\Issues();
            $sx = $Issues->index($subact, $id);
            return $sx;
        }

    function oai($jid, $act)
    {
        $sx = '';
        switch ($act) {
            case 'status':
                $tp = substr($jid,0,1);
                if ($tp == 'I')
                    {
                        $id = round(substr($jid,1,10));
                        $st = get("status");
                    }
                $sx .= h($tp);
                break;
            case 'getrecords':
                $sx .= h('OAIPMH - GetRecords');
                $OAI_GetRecords = new \App\Models\Oaipmh\GetRecords();
                $sx .= $OAI_GetRecords->getrecord(0,$jid);
                break;
            case 'listidentifiers':
                $Sources = new \App\Models\Base\Sources();
                $dt = $Sources->find($jid);

                $sx .= h('OAIPMH - listidentifiers');
                $ListIdentifiers = new \App\Models\Oaipmh\ListIdentifiers();
                $sa = $ListIdentifiers->harvesting($dt);
                $sb = $ListIdentifiers->resume($jid);

                $sx .= $sb;
                $sx .= $sa;
                break;

            default:
                $sx .= h('OAIPMH - '.$act.' ['.$jid.']');
                break;
        }
        $sx = bs(bsc($sx,12));
        return $sx;
    }

    private function v($id = '')
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $class = $dt['concept']['c_class'];

        $sx .= '';

        switch ($class) {
            case 'Subject':
                $Keywords = new \App\Models\Base\Keywords();
                $sx .= $Keywords->showHTML($dt);
                break;

            case 'Proceeding':
                $Work = new \App\Models\Base\Work();
                $sx .= $Work->show($dt);
                break;

            case 'ProceedingSection':
                $ProceedingSection = new \App\Models\Base\ProceedingSection();
                $sx .= $ProceedingSection->show($dt);
                break;

            case 'Work':
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
