<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'tools');

class Tools extends BaseController
{
    public function nlp($type='',$id='')
    {

        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', '0');
        @ini_set('implicit_flush', '1');

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_implicit_flush(true);

        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('X-Accel-Buffering: no');

        echo '<style> body { font-family: Arial, sans-serif; } </style>';
        flush();

        switch ($type) {
            case 'fulltext':
                echo '<h1>Processando Docling</h1>';
                echo h("FULLTEXT - PRE");
                flush();

                $cmd = '/usr/bin/python3 /data/Brapci3.1/bots/TOOLS/ai.py all ' . $id;
                echo '<p>' . $cmd . '</p>';
                echo troca(shell_exec($cmd), chr(10), '<br>');
                flush();

                break;

            case 'docling':
                echo '<h1>Processando Docling</h1>';
                echo h("FULLTEXT - PRE");
                flush();

                $cmd = '/usr/bin/python3 /data/Brapci3.1/bots/TOOLS/ai.py docling ' . $id;
                echo '<p>' . $cmd . '</p>';
                echo troca(shell_exec($cmd), chr(10), '<br>');
                flush();

                break;
            case 'cited':
                echo '<h1>Processando Citações</h1>';
                echo h("FULLTEXT - PRE");
                flush();

                $cmd = '/usr/bin/python3 /data/Brapci3.1/bots/TOOLS/ai.py cited ' . $id;
                echo '<p>' . $cmd . '</p>';
                echo troca(shell_exec($cmd), chr(10), '<br>');
                flush();

                break;
            default:
                echo '<h1>Processando NLP - ' . htmlspecialchars($type) . '</h1>';
                break;
        }


        echo "<hr>Fim do processo.";
        flush();
    }

    public function index($act = '', $subact = '', $id = '', $id2='',$id3='',$id4='',$id5='')
    {
        $Tools = new \App\Models\Tools\Index();
        $data['page_title'] = 'Brapci Bibliometric Tools';
        $data['bg'] = 'bg-tools';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        switch ($act) {
            case 'social':
                $Socials = new \App\Models\Socials();
                $sx .= bs(bsc($Socials->index($subact, $id), 12));
                break;

           case 'project':
                $Projects = new \App\Models\Tools\Projects();
                $sx .= $Projects->index($subact,$id,$id2, $id3, $id4, $id5);
                break;

            case 'email':
                $sx .= email_smtp_test();
                break;

           default:
                $sx .= $Tools->index($act,$subact,$id,$id2,$id3,$id4, $id5);
                break;
        }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}