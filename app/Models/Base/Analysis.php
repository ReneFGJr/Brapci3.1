<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Analysis extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cms';
    protected $primaryKey       = 'id_cms';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cms', 'cms_ref', 'cms_pos',
        'cms_text', 'cms_lang'
    ];
    protected $typeFields    = [
        'hidden', 'string', '[1-99]',
        'text', 'op:pt_BR:pt_BR'
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


    function analysis($d1,$d2='',$d3='')
        {
            $RSP = [];

            $data = get("worksID");
            $data = explode(',',$data);
            $RSP = $this->analysis_get_data($data);
            return $RSP;
        }

    function analysis_get_data($data)
        {
            $Elastic = new \App\Models\ElasticSearch\Register();
            $dt = $Elastic
                    ->whereIn('ID', $data)
                    ->findAll();

            //$RSP[] = $dt;
            $RSP['YEARS'] = $this->analysis_year($dt);
            $RSP['AUTHORS'] = $this->analysis_meta($dt,'AUTHORS');
            $RSP['SUBJECTS'] = $this->analysis_meta($dt,'KEYWORDS');
            $RSP['SECTIONS'] = $this->analysis_meta($dt, 'SESSION');
            $RSP['PUBLICATIONS'] = $this->analysis_meta($dt, 'PUBLICATION');
            $RSP['TYPES'] = $this->analysis_meta($dt, 'CLASS');
            $RSP['COLLECTION'] = $this->analysis_meta($dt, 'COLLECTION');
            return $RSP;
        }

    function analysis_meta($data,$FIELD)
    {
        $META = [];
        foreach ($data as $line) {
            $dt = $line[$FIELD];
            $au = explode(';', $dt);
            foreach($au as $a) {
                $a = trim($a);
                if (strlen($a) > 0) {
                    if (!isset($META[$a])) {
                        $META[$a] = 0;
                    }
                    $META[$a]++;
                }
            }
        }
        arsort($META);
        return $META;
    }

    function analysis_year($data)
        {
            $YEAR = [];
            foreach($data as $line)
                {
                    $year = substr($line['YEAR'],0,4);
                    if (!isset($YEAR[$year])) { $YEAR[$year] = 0; }
                    $YEAR[$year]++;
                }
            return $YEAR;
        }
}
