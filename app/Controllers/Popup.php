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
define("LIBRARY", '1000');

class Popup extends BaseController
{
    public function index($act = '')
    {
        $data['page_title'] = 'Brapci - POPUP - ' . ucfirst($act);
        $data['bg'] = 'bg-pq';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);

        switch ($act) {
            case  'upload':
                $Socials = new \App\Models\Socials();
                if ($Socials->getAccess("#ADM"))
                    {
                        $id = get("id");
                        $DownloadPDF = new \App\Models\Bots\DownloadPDF();
                        $sx .= $DownloadPDF->upload($id);
                    }
                break;
            case 'lattesextrator':
                $LattesExtrator = new \App\Models\LattesExtrator\Index();
                $LattesExtrator->harvesting();
                $sx = wclose();
                break;
            case 'pq_bolsista_edit':
                $Bolsistas = new \App\Models\PQ\Bolsistas();
                $id = get('id');
                $sx .= $Bolsistas->edit($id);
                break;

            case 'pq_bolsa_edit':
                $Bolsas = new \App\Models\PQ\Bolsas();
                $id = get('id');
                $sx .= $Bolsas->edit($id);
                break;

            case 'pq_bolsa_delete':
                $Bolsas = new \App\Models\PQ\Bolsas();
                $id = get('id');
                $Bolsas->delete($id);
                $sx = wclose();
                break;
        }
        return $sx;
    }
}