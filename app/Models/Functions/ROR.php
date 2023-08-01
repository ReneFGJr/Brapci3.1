<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class ROR extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rors';
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

    function import($id)
    {
        $Auth = new \App\Models\Authority\API\Index();

        $url = 'https://api.ror.org/organizations/' . $id;
        $txt = read_link($url);
        $dt = [];
        $dta = [];

        if ($txt != '') {
            $dt = (array)json_decode($txt);

            if (isset($dt['labels']) and (count($dt['labels']) > 0)) {
                $labels = (array)$dt['labels'];
                foreach ($labels as $id => $inst) {
                    $inst = (array)$inst;
                    if ($inst['iso639'] == 'pt') {
                        $dta['prefLabel'] = $inst['label'];
                    }
                }
            } else {
                $dta['prefLabel'] = $dt['name'];
            }
            /********** PAIS */
            if (isset($dt['country'])) {
                $country = (array)$dt['country'];
                $place = [];
                $place['country'] = $country['country_name'];
                $place['code'] = $country['country_code'];
                $dta['prop']['hasPlace'] = $place;
            }
            /********** SILGA */
            if (isset($dt['acronyms'])) {
                $sigla = (array)$dt['acronyms'];
                $silga = $dta['prop']['acronym'] = $sigla[0];
            }
        }
        $id = $Auth->register_corporate($dta);

        return $Auth->getid($id);
    }

    function getROR($id)
    {
        $url = 'https://api.ror.org/organizations/' . $id;
        /*
            ex: https://api.ror.org/organizations/041yk2d64
            */

        $txt = read_link($url);
    }
}
