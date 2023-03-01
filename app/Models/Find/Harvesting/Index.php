<?php

namespace App\Models\Find\Harvesting;

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

    function harvesting($id='')
        {
            if ($id=='') { $id= 66311; }
            $id = 66308;
            //$id = 66310;
            $url = 'https://www.ufrgs.br/find/index.php/m/j/'.$id;
            echo h($url);
            $txt = read_link($url);
            if ($pos = strpos($txt, '<div'))
                {
                    $txt = substr($txt,0,$pos);
                }
            $h =  (array)json_decode($txt);

            if (isset($h['c']))
                {
                    $this->process($h);
                }
        }

        function process($h)
            {
                $c = (array)$h['c'];
                $class = $c['c_class'];
                switch($class)
                    {
                        case 'Work':
                            $this->process_work($h);
                            break;
                        case 'FormExpression':
                            pre($h);
                            break;
                        case 'Manifestation':
                            pre($h);
                            break;
                        case 'Expression':
                            pre($h);
                            break;
                        default:
                            echo h($class.' not process',4);
                            break;
                    }
            }

        function process_work($h)
            {
                $Lang = new \App\Models\Language\Lang();
                $ISBN = new \App\Models\ISBN\Index();
                $data = (array)$h['data'];
                $c = (array)$h['c'];

                pre($data);

                $dt = [];
                $dt['idcc'] = $c['id_cc'];
                $dt['isbn_title'] = $c['n_name'].'@'. $Lang->code($c['n_lang']);
                $dt['isbn_status'] = 1;
                foreach($data as $id=>$line)
                    {
                        $line = (array)$line;
                        $class = $line['c_class'];
                        $vlr = trim($line['n_name']);

                        echo '=>'.$class.'<br>';
                        switch($class)
                            {
                                case 'isAppellationOfExpression':
                                    $vlr = troca($vlr,'ISBN:','');
                                    $vlr = substr($vlr,0,strpos($vlr,':'));
                                    if (strlen($vlr) == 10)
                                        {
                                            $dt['isbn_ean13'] = $ISBN->isbn10to13($vlr);
                                            $dt['isbn_ean10'] = $vlr;
                                        } else {
                                            $dt['isbn_ean10'] = $ISBN->isbn13to10($vlr);
                                            $dt['isbn_ean13'] = $vlr;
                                        }

                            }
                    }

                    pre($dt);
            }
}
