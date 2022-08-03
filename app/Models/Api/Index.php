<?php

namespace App\Models\Api;

use CodeIgniter\Model;

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
        switch ($d1) {
            case 'book':
                $API = new \App\Models\Api\Endpoint\Book;
                $sx = $API->index($d1, $d2, $d3, $d4);
                break;
            default:
                $sx = $this->head();
                $sx .= bs(bsc(h('Brapci API - v0.22.08.03', 1), 12));
                $sx .= bs(bsc('Endpoint: ' . $d1, 12));
                $sx .= $this->apiCatalog();
                break;
        }
        return $sx;
    }

    function recoverTag($txt, $tag)
    {
        $title = lang('book.not informed');
        if ($pos = strpos($txt, $tag)) {
            $title = substr($txt, $pos + strlen($tag), strlen($txt));
            $pos = strpos($title, chr(10));
            $title = trim(substr($title, 0, $pos));
        }
        return ($title);
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
                $sx .= '<div class="accordion-item">' . cr();
                $sx .= '    <h2 class="accordion-header" id="' . $name . '"> ' . cr();
                $sx .= '        <button class="accordion-button collapsed"  type="button" data-bs-toggle="collapse" data-bs-target="#' . $namec . '" aria-expanded="false" aria-controls="' . $namec . '">' . cr();
                $sx .= '        ' . $this->recoverTag($txt, '@package') . cr();
                $sx .= '        </button> ' . cr();
                $sx .= '    </h2> ' . cr();
                $sx .= '<div id="' . $namec . '" class="accordion-collapse collapse" aria-labelledby="' . $name . '" data-bs-parent="#accordionAPI"> ' . cr();
                $sx .= '    <div class="accordion-body"> ' . cr();
                $sx .= '        Author(s): ' . $this->recoverTag($txt, '@author') . '<br>' . cr();
                $sx .= '        ' . $this->recoverTag($txt, '@abstract') . '<br>' . cr();
                $sx .= '        <pre>' . '<a href="' . $url_ex . '">' . $url_ex . '</pre><br>' . cr();
                $sx .= '    </div> ' . cr();
                $sx .= '</div> ' . cr();
                //$sx .= '</div>';
            }
        }
        $sx .= '</div>';
        $sx = troca($sx, '$URL/', PATH);
        $sx = bs(bsc($sx, 12));
        return $sx;
    }
}