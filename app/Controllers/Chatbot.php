<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Chatbot extends BaseController
{
    public function index($d1 = '',$d2 = '',$d3 = '',$d4 = '')
    {
        $Chatbot = new \App\Models\Chatbot\Index();
        return $Chatbot->index($d1,$d2,$d3,$d4);
    }
}
