<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'benancib');

class Benancib extends BaseController
{
    public function index($act = '', $id = '')
    {
        $Issues = new \App\Models\Base\Issues();
        $data['page_title'] = 'Brapci-Benancib';
        $data['bg'] = 'bg-benancib';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Benancib/Headers/navbar', $data);

        $act = trim($act);
        echo h($act);
        switch ($act) {
            case 'v':
                $sx .= $this->v($id);
                break;
            case 'issue_edit':
                $Issues = new \App\Models\Base\Issues();
                $id = get("id");
                $sx .= bsc($Issues->edit($id));
                break;
            case 'issue':
                $Issues = new \App\Models\Base\Issues();
                $id = get("id");
                $sx .= bsc($Issues->issue($id));
                $sx .= '<hr>';
                $sx .= bsc($Issues->issue_section_works($id));
                break;
            default:
                $id = 75;
                $data['logo'] = view('Benancib/Svg/logo_benancib');
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

        switch ($class) {
            case 'Subject':
                $Keywords = new \App\Models\Base\Keywords();
                $sx .= $Keywords->showHTML($dt);
                break;

            case 'Proceeding':
                $Proceeding = new \App\Models\Base\Proceeding();
                $sx .= $Proceeding->showHTML($dt);
                break;

            case 'work':
                $Work = new \App\Models\Base\Work();
                $RDF =
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