<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Pq extends BaseController
{
    public function index()
    {
        $data['page_title'] = 'Brapci-Benancib';
        $data['bg'] = 'bg-pq';
        $sx = '';
        $sx .= view('Brapci/Headers/header',$data);
        $sx .= view('Brapci/Headers/navbar',$data);
        $sx .= view('Pq/monitor',$data);
        $sx .= view('Brapci/Headers/footer',$data);
        return $sx;
    }
}
