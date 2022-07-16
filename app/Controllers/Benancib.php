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
define("COLLECTION", '/benancib');

class Benancib extends BaseController
{
    public function index($act = '', $subact = '', $id = '')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Benancib';
        $data['bg'] = 'bg-benancib';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Benancib/Headers/navbar', $data);

        $q = get("query");
        if (strlen($q) > 0) {
            $sx .= 'Busca';
            $act = 'search';
        }

        $act = trim($act);
        switch ($act) {
            case 'search':
                $data['logo'] = view('Logos/logo_benancib');
                $data['issues'] = $Issues->show_list_cards($id);
                $data['search'] = view('Benancib/Pages/search');
                $sx .= view('Benancib/Welcome', $data);
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
                switch ($subact) {
                    case 'edit':
                        $Issues = new \App\Models\Base\Issues();
                        $sx .= breadcrumbs();
                        $sx .= bsc($Issues->edit($id));
                        break;

                    default:
                        $Issues = new \App\Models\Base\Issues();
                        $id = get("id");
                        $sx .= bsc($Issues->issue($id), 12);
                        $sx .= '<hr>';
                        $sx .= bsc($Issues->issue_section_works($id), 12);
                        break;
                }
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
                $data['issues'] = $Issues->show_list_cards($id);
                $data['search'] = view('Benancib/Pages/search');
                $sx .= view('Benancib/Welcome', $data);
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