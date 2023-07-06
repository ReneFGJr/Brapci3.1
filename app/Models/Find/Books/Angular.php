<?php

namespace App\Models\Find\Books;

use CodeIgniter\Model;

class Angular extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'angulars';
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

    var $api = 'http://brp/api/';
    var $apikey = '4012a514a2fb024fa4fef06cc7a5aebe';

    function index($d1,$d2)
        {
            $sx = '';
            switch($d1)
                {
                    case 'isbn':
                        switch($d2)
                            {
                                case 'add':
                                    $sx .= $this->addISBN();
                                    break;
                            }
                    break;

                    default:
                        $sx .= 'MENU - '.$d1;
                        $menu = [];
                        $menu[PATH.'/admin/find/angular/isbn/add'] = 'Add ISBN';
                        $sx .= menu($menu);
                    break;
                }
            return $sx;
        }

        function addISBN()
            {
                $isbn = get("isbn");
                if ($isbn == '') { $isbn = '9786556891859'; }
                $sx = '';
                $sx .= '<form method="post">';
                $sx .= 'Informe o ISBN';
                $sx .= '<input name="isbn" class="form-control border border-secondary" value="'.$isbn.'">';
                $sx .= '<input name="action" type="submit" value="Enviar" class="btn btn-primary mt-3">';
                $sx .= '</form>';

                if (get("action") != '')
                    {
                        $sx .= 'Processando I - API Validação';
                        $url = $this->api.'isbn/'.$isbn;
                        $sx .= '<br><tt>'.$url.'</tt>';

                        /*********** PHASE II - Consulta API de validação do ISBN */
                        $ct = file_get_contents($url);
                        $ct = (array)json_decode($ct);
                        if ($ct['valid'] != 1)
                            {
                                $sx .= bsmessage('Erro no ISBN',3);
                            } else {
                                /*********** PHASE II - Solicita inclusão da base */
                                $url = $this->api . 'find/isbn/' . $isbn . '/add';
                                $url .= '?library=1';
                                $url .= '&apikey='.$this->apikey;
                                echo $url;
                                $ct = file_get_contents($url);
                                $ct = (array)json_decode($ct);

                                echo "Status";

                                $sx .= '<hr>';
                                pre($ct,false);
                                $sx .= $ct['status'];
                                $status = $ct['status'];
                                $isbn = $ct['isbn'];

                                if ($status == '200')
                                    {
                                        $sx .= '<hr>';
                                        $sx .= anchor(PATH . '/admin/find/angular/isbn/item?isbn=' . $isbn,'Novo Item');
                                        $sx .= ' | ';
                                        $sx .= anchor(PATH . '/admin/find/angular/isbn/edit?isbn=' . $isbn, 'Editar Metadados');
                                    } else {
                                        metarefresh(PATH.'/admin/find/angular/isbn/edit?isbn='.$isbn);
                                    }
                            }
                    }
                    echo "FIM";
                return $sx;
            }
}
