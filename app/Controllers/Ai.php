<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'ai');

class Ai extends BaseController
{
    public function index($act = '', $subact = '', $id = '')
    {
        $AI = new \App\Models\AI\Index();
        $data['page_title'] = 'Brapci Artificial Inteligênce';
        $data['bg'] = 'bg-ai';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);
        switch ($act) {
            case 'chat':
                $AI = new \App\Models\AI\Chatbot\Index();
                switch($subact)
                    {
                        case 'query':
                            $sx = $AI->query($id);
                            break;
                        case 'analyse':
                            $sx .= $AI->analyse();
                            break;
                        default:
                            $sx .= $AI->chat();
                            break;
                    }
                break;
            default:
                $sx .= $AI->index($act,$subact,$id);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}