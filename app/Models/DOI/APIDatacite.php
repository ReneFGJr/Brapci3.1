<?php

namespace App\Models\DOI;

use CodeIgniter\Model;

class APIDatacite extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'apidatacites';
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

    function metadata($nDOI)
        {
            $DOI = new \App\Models\DOI\Index();
            $nDOI = troca($nDOI, 'https://doi.org/','');

            $dt = $DOI->where('pi_id', $nDOI)->findAll();
            if (count($dt) == 0)
                {
                    $url = 'https://api.datacite.org/dois/' . $nDOI;

                    $json = read_link($url);
                    $sta = (array)json_decode($json);
                    if (isset($sta['errors']))
                        {
                            $sta = $sta['errors'];
                            foreach($sta as $id=>$line)
                                {
                                    $line = (array)$line;
                                    echo h('DOI: '.$nDOI,3);
                                    echo $line['status'].' = '.$line['title'];
                                    echo '<hr>';

                                    $dt['pi_id'] = $nDOI;
                                    $DOI->set($dt)->insert();
                                    $sx = metarefresh('');
                                }
                            exit;
                        }
                    return $DOI->register_json($json);
                } else {
                    return $dt[0];
                }

        }
}
