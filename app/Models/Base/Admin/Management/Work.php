<?php

namespace App\Models\Base\Admin\Management;

use CodeIgniter\Model;

class Work extends Model
{
    protected $DBGroup          = 'management';
    protected $table            = 'works';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'w_work','w_issue','w_journal','w_section','w_status', 'w_class'
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

    function check()
        {
            $sx = '';
            $RDF = new \App\Models\Rdf\RDF();
            $class = $RDF->getClass('Article', false);
            $sx .= bs(bsc(h('Articles',4)));
            $sx .= $this->check_class($class);

            $sx .= bs(bsc(h('Proceedings', 4)));
            $class = $RDF->getClass('Proceeding', false);
            $sx .= $this->check_class($class);

            $sx .= $this->complete();

            return $sx;
        }
    function complete()
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $this->where('w_issue <= 0')->findAll();

        pre($dt);
    }

    function check_class($class)
        {
            $sx = '';
            $dt = $this
                ->join('brapci.rdf_concept', 'id_cc = w_work', 'RIGHT')
                ->where('cc_class',$class)
                ->where('w_work is null')
                ->orderBy('id_cc')
                ->findAll(10000);
                //echo $this->getlastquery();

            foreach($dt as $id=>$line)
                {
                    $d = [];
                    $d['w_work'] = $line['id_cc'];
                    $d['w_issue'] = 0;
                    $d['w_class'] = $class;
                    $d['w_journal'] = 0;
                    $d['w_section'] = 0;
                    $this->set($d)->insert();
                    $sx .= '. ';
                }
            return bs(bsc($sx,6));
        }
}
