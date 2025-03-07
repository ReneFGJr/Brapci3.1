<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */

$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL"));
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '0000');

class Popup extends BaseController
{
    public function index($act = '', $id = '', $id2 = '', $id3 = '', $id4 = '')
    {
        $data['page_title'] = 'Brapci - POPUP - ' . ucfirst($act);
        $data['bg'] = 'bg-pq';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        switch ($act) {
            case 'rdf':
                $RDF = new \App\Models\RDF2\RDF();
                $sx .= $RDF->index('popup',$id,$id2,$id3,$id4);
                break;
            case 'oai':
                $OAI = new \App\Models\Oaipmh\Index();
                $sx = $OAI->index('getReg', $id2, $id3, $id4);
                break;
            case  'admin':
                $Socials = new \App\Models\Socials();
                if ($Socials->getAccess("#ADM")) {
                    $Admin = new \App\Models\Base\Admin\Index();
                    $sx .= $Admin->index($id, $id2, $id3);
                }
                break;
            case 'remissive':
                $Socials = new \App\Models\Socials();
                if ($Socials->getAccess("#ADM")) {
                    $id .= get("id");
                    $RDFRemissive = new \App\Models\Rdf\RDFRemissive();
                    $sx .= $RDFRemissive->edit($id);
                }
                break;
            case  'upload':
                $Socials = new \App\Models\Socials();
                if ($Socials->getAccess("#ADM")) {
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
