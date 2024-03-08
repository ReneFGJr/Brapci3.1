<?php

namespace App\Models\BrCris;

use CodeIgniter\Model;

class Person extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'people';
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

    function seek($name)
        {
            $name = "RENE FAUSTINO GABRIEL JUNIOR";
            $NM = explode(' ',$name);
            $URL = 'https://brcris.ibict.br/pt-BR/people?';
            $URL .= 'q=';
            $n = 0;
            foreach($NM as $id=>$name)
                {
                    if ($n > 0)
                        {
                         $URL .= '%20AND%20';
                        }
                    $URL .= '%28name_text%3A'.$name.'%29';
                    $n++;
                }
            $URL .= '&size=n_10_n';
        //pre($URL);
            $txt = readlink($URL);

            pre($txt);
            //https://brcris.ibict.br/api/index-stats?indexName=release2-person
            //https://brcris.ibict.br/api/search

            //https://brcris.ibict.br/api/search

            #<a target="_blank" href="http://brcris.ibict.br/vivo/individual?uri=https://brcris.ibict.br/individual/pers_c888cc57-bd54-4db4-b1ff-b91bccf83b2d&amp;lang=pt-BR" rel="noreferrer">RENE FAUSTINO GABRIEL JUNIOR</a>
        }
}
