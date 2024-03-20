<?php

namespace App\Models\Api;

use CodeIgniter\Model;
use CodeIgniter\Cookie\Cookie;
use DateTime;
use DateTimeZone;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function head()
    {
        $data['title'] = 'Brapci API';
        $sx = view('Brapci/Headers/header', $data);
        return $sx;
    }

    function index($d1, $d2, $d3, $d4)
    {
        /* NAO USADO PARA AS APIS */
        header('Access-Control-Allow-Origin: *');


        if (get("test") == '') {
            if (($d2 != 'import') and ($d2 != 'in') and ($d2 != 'searchSelect')) {
                header("Access-Control-Allow-Headers: Content-Type");
                header("Content-Type: application/json");
            }
        }

        switch ($d1) {
            case 'page':
                $WP = new \App\Models\WP\Index();
                $RSP = $WP->api($d2);
                echo json_encode($RSP);
                exit;
                break;
            case 'cookies':
                $this->cookies();
                exit;
                break;
            case 'kanban':
                $Kanban = new \App\Models\Api\Endpoint\Kanban();
                $Kanban->index($d2, $d3);
                exit;
                break;
            case 'upload':
                $RDFimage = new \App\Models\RDF2\RDFimage();
                $RDFimage->upload($d2,$d3);
                exit;
                break;
            case 'event':
                $Event = new \App\Models\Api\Endpoint\Event();
                $Event->index($d2,$d3,$d4);
                break;
            case 'handle':
                $Handle = new \App\Models\Handle\Index();
                $sx = $Handle->index($d2,$d3,$d4);
                break;
            case 'authority':
                $Authority = new \App\Models\Api\Endpoint\Authority();
                $sx = $Authority->index($d2, $d3, $d4);
                break;
            case 'brapci':
                $Brapci = new \App\Models\Api\Endpoint\Brapci();
                $sx = $Brapci->index($d2, $d3, $d4);
                break;
            case 'socials':
                $Oauth = new \App\Models\Api\Endpoint\Oauth();
                $sx = $Oauth->index($d2, $d3, $d4);
                break;
            case 'isbn':
                $ISBN = new \App\Models\Api\Endpoint\Isbn();
                $sx = $ISBN->index($d2, $d3, $d4);
                break;
            case 'find':
                $Find = new \App\Models\Api\Endpoint\Find();
                $sx = $Find->index($d2,$d3,$d4);
                break;
            case 'gev3nt':
                $Gev3nt = new \App\Models\Api\Endpoint\Gev3nt();
                $sx = $Gev3nt->index($d2, $d3, $d4);
                break;
            case 'source':
                $Sources = new \App\Models\Api\Endpoint\Sources();
                $sx = $Sources->index($d2, $d3, $d4);
                break;
            case 'lattes':
                $Lattes = new \App\Models\Api\Endpoint\Lattes();
                $sx = $Lattes->index($d2,$d3,$d4);
                break;
            case 'pdf':
                $API = new \App\Models\Api\Endpoint\Pdf;
                $sx = $API->index($d1, $d2, $d3, $d4);
                break;
            case 'rdf':
                $API = new \App\Models\Api\Endpoint\Rdf;
                $sx = $API->index($d1, $d2, $d3, $d4);
                break;
            case 'book':
                $API = new \App\Models\Api\Endpoint\Book;
                $sx = $API->index($d1, $d2, $d3, $d4);
                break;
            case 'gender':
                $API = new \App\Models\Api\Endpoint\Genere;
                $sx = $API->index($d1, $d2, $d3, $d4);
                break;
            case 'label':
                $API = new \App\Models\Api\Endpoint\LabelPrint;
                $sx = $API->index($d1, $d2, $d3, $d4);
                break;
            case 'doiToFormation':
                $API = new \App\Models\Api\Endpoint\DoiLattesAuthor;
                $sx = $API->index($d1, $d2, $d3, $d4);
                break;
            case 'tools':
                $API = new \App\Models\Api\Endpoint\Tools;
                $sx = $API->index($d1, $d2, $d3, $d4);
                break;
            case 'indexs':
                $Brapci = new \App\Models\Api\Endpoint\Brapci;
                echo json_encode($Brapci->indexs($d2, $d3));
                exit;
                break;
            default:
                $sx = $this->head();
                $sx .= bs(bsc(h('Brapci API - v0.23.06.16', 1), 12));
                $sx .= bs(bsc('Endpoint: ' . $d1, 12));
                $sx .= $this->apiCatalog();

                $sx .= cr().'YouIP '.$_SERVER['REMOTE_ADDR'];
                break;
        }
        return $sx;
    }

    function cookies()
        {
            $cookie = new Cookie('section');
            $dt = $cookie->getName();
            pre($cookie);
        }

    function recoverTag($txt, $tag)
    {
        $title = lang('book.not informed');
        $txt = troca($txt,chr(10),chr(13));
        $ln = explode(chr(13),$txt);
        $rsp = '';
        foreach($ln as $id=>$txt)
            {
            if ($pos = strpos(' '.$txt, $tag)) {
                $line = trim(troca($txt,$tag,''));
                $rsp .= $line.chr(13);
            }
        }
        $rsp = $rsp;
        return ($rsp);
    }

    function apiCatalog()
    {
        $sx = cr() . '<div class="accordion" id="accordionAPI">' . cr();
        $ndir = '../app/Models/Api/Endpoint';
        $dir = scandir($ndir);
        foreach ($dir as $id => $api) {
            $file = $ndir . '/' . $api;
            $name = troca('heading' . $api, '.php', '');
            $namec = troca('collapse' . $api, '.php', '');

            if ((file_exists($file)) and ($api != '..') and ($api != '.')) {
                $txt = file_get_contents($file);
                $url_ex = $this->recoverTag($txt, '@example');
                $url_ex = troca($url_ex,'$PATH',URL);

                $title = $this->recoverTag($txt, '@package');
                $authors = $this->recoverTag($txt, '@author');
                $abstract = $this->recoverTag($txt, '@abstract');

                $sx .= '<div class="accordion-item">' . cr();
                $sx .= '    <h2 class="accordion-header" id="' . $name . '"> ' . cr();
                $sx .= '        <button class="accordion-button collapsed"  type="button" data-bs-toggle="collapse" data-bs-target="#' . $namec . '" aria-expanded="false" aria-controls="' . $namec . '">' . cr();
                $sx .= '        ' . $title . cr();
                $sx .= '        </button> ' . cr();
                $sx .= '    </h2> ' . cr();

                $sx .= '<div id="' . $namec . '" class="accordion-collapse collapse" aria-labelledby="' . $name . '" data-bs-parent="#accordionAPI"> ' . cr();
                $sx .= '    <div class="accordion-body"> ' . cr();
                $sx .= '        Author(s): ' . $authors . '<br>' . cr();
                $sx .= '        ' . $abstract . '<br>' . cr();
                $sx .= '        <pre>' . '<a href="' . $url_ex . '">' . $url_ex . '</a></pre><br>' . cr();
                $sx .= '    </div> ' . cr();
                $sx .= '</div> ' . cr();
                $sx = troca($sx, '$PATH', URL);
                //$sx .= '</div>';
            }
        }
        $sx .= '</div>';
        $sx = troca($sx, '$URL/', PATH);
        $sx = bs(bsc($sx, 12));
        return $sx;
    }
}