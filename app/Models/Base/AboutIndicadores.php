<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class AboutIndicadores extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cms_indicador';
    protected $primaryKey       = 'id_cmsi';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cmsi', 'cmsi_indicador', 'cmsi_valor'
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


    function indicators()
        {
            $dt = $this->findAll();
            $d = [];
            foreach($dt as $id=>$line)
                {
                    $class = $line['cmsi_indicador'];
                    $d[$class] = $line['cmsi_valor'];
                }
            return $d;
        }


    function makeIndicators()
    {
        $cp = 'type, pdf, id_jnl';
        $cpc = ', count(*) as total';
        $Search = new \App\Models\ElasticSearch\Register();
        $dt = $Search
            ->select($cp . $cpc)
            ->groupBy($cp)
            ->findAll();

        $rst = [];
        $rst['journal'] = [];
        foreach ($dt as $id => $line) {
            $type = $line['type'];
            $total = $line['total'];
            if (!isset($rst[$type])) {
                $rst[$type] = 0;
            }
            $rst[$type] = $rst[$type] + $total;

            $pdf = $line['pdf'];
            if (!isset($rst['pad'])) {
                $rst['pdf'] = [0, 0];
            }
            $rst['pdf'][$pdf] = $rst['pdf'][$pdf] + $total;

            $jid = $line['id_jnl'];
            if (!isset($rst['journal'][$type])) {
                $rst['journal'][$type] = [];
            }
            if ($jid > 0) {
                if (!isset($rst['journal'][$type][$jid])) {
                    $rst['journal'][$type][$jid] = $total;
                } else {
                    $rst['journal'][$type][$jid] = $rst['journal'][$type][$jid] + $total;
                }
            }
        }
        if (isset($rst['journal']['Article']))
            {
                $rst['journals'] = count($rst['journal']['Article']);
            } else {
                $rst['journals'] = 0;
            }


        $ids = ['Book', 'BookChapter', 'Proceeding', 'Article', 'journals'];
        foreach($ids as $idx=>$var)
            {
                if (isset($rst[$var])) {
                    $this->register($var, $rst[$var]);
                }
            }
    }

    function register($var,$total)
        {
            $dt = $this->where('cmsi_indicador',$var)->first();
            $data['cmsi_indicador'] = $var;
            $data['cmsi_valor'] = $total;
            if ($dt == '')
                {
                    $this->set($data)->insert();
                } else {
                    $this->set($data)->where('cmsi_indicador',$var)->update();
                }
        }
}
