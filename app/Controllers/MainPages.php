<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MainPages extends BaseController
{
    public function index($pag='')
    {
        $sx = '';
        /**** PAGES */
        $data['page_title'] = 'Brapci - '.ucfirst($pag);
        $sx .= view('Brapci/Headers/header',$data);

        /**** CHECK PAGE */
        $file = APPPATH.'Views/Brapci/Pages/'.strtolower($pag).'.php';
        if (file_exists($file))
            {
                $sx .= view('Brapci/Headers/navbar',$data);
                $sx .= view('Brapci/Pages/'.$pag);
            } else {
                throw new \CodeIgniter\Exceptions\PageNotFoundException();
            }
        /**** FOOTER */
        $sx .= view('Brapci/Headers/footer',$data);
        return $sx;
    }
}
