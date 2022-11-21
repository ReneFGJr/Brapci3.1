<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPISections extends Model
{
    protected $DBGroup          = 'patent';
    protected $table            = 'rpi_section';
    protected $primaryKey       = 'id_resc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_rsec', 'rsec_code', 'rsec_name', 'rsec_status', 'rsec_group'
    ];

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

    function register($code, $desc = '')
    {
        $dt = $this->where('rsec_code', $code)->findAll();
        if (count($dt) > 0) {
            return $dt[0];
        } else {
            $data['rsec_code'] = $code;
            $data['rsec_name'] = $desc;
            $data['id_rsec'] = $this->insert($data);
            return $data;
        }
    }

    function import($id, $file)
    {
        $sx = '';
        $xml = (array)simplexml_load_file($file);

        $despacho = (array)$xml['despacho'];
        $xcode = '';
        $tot = 0;
        for ($r = 0; $r < count($despacho); $r++) {
            $reg = (array)$despacho[$r];

            $code = $reg['codigo'];
            $desc = $reg['titulo'];

            if ($code != $xcode) {
                $tot++;
                $sx .= h($code . ' - ' . $desc, 3);
                $id_sec = $this->register($code, $desc);
                $id_sec = $id_sec['id_rsec'];
                $xcode = $code;
            }
        }
        $sx = '<p>' . bsmessage(lang('patent.found'). ' ' . $tot . ' ' . lang('patent.sections')) . '</p>' . $sx;
        return $sx;
    }
}
