<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Benancib extends BaseController
{
    public function index()
    {
        $data['page_title'] = 'Brapci-Benancib';
        $sx = '';
        $sx .= view('Brapci/Headers/header',$data);
        $sx .= view('Benancib/Headers/navbar',$data);
        $sx .= view('Benancib/Svg/logo_benancib',$data);
        $sx .= view('Brapci/Headers/footer',$data);
        return $sx;
    }
}
