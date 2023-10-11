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

    function issue_work($id)
        {
            $dt = $this->where('w_issue',$id)->findAll();
            pre($dt);
        }

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
        $dt = $this->where('w_issue <= 0')->findAll(10);

        foreach($dt as $id=>$line)
            {
                $dr = $RDF->le($line['w_work']);
                if (count($dr['data']) == 0)
                    {
                        $this->deleted($line['w_work']);
                        $sx .= '#d '.$line['w_work'];
                    } else {
                        echo '======================';
                        pre($dr);
                    }
            }
            return $sx;
    }

    function deleted($id)
        {
            return $this->where('w_work',$id)->delete();
        }

    function check_class($class)
        {
            $sx = '';
            $dt = $this
                ->join('brapci.rdf_concept', 'id_cc = w_work', 'RIGHT')
                ->where('cc_class',$class)
                ->where('w_work is null')
                ->orderBy('id_cc')
                ->findAll(5000);
                //echo $this->getlastquery();
            $i = 0;
            foreach($dt as $id=>$line)
                {
                    $i++;
                    $d = [];
                    $d['w_work'] = $line['id_cc'];
                    $d['w_issue'] = 0;
                    $d['w_class'] = $class;
                    $d['w_journal'] = 0;
                    $d['w_section'] = 0;
                    $this->set($d)->insert();
                    $sx .= '. ';
                }
            if ($i > 0)
                {
                    $sx .= metarefresh('');
                    $sx .= 'Continua ...';
                }
            return bs(bsc($sx,6));
        }

        function exactMath()
            {
                /* Convert */
                $RDF = new \App\Models\Rdf\RDF();
                $RDFData = new \App\Models\Rdf\RDFData();
                $c1 = $RDF->getClass('hasIssueProceedingOf',false);
                $c2 = $RDF->getClass('hasIssueOf', false);

                $sql = "update rdf_data set d_p = $c2 where d_p = $c1";
                $RDFData->db->query($sql);
                echo $sql;
            }

        function proceedings()
            {
                $sx = '';
                $RDF = new \App\Models\Rdf\RDF();
                $RDFconcept = new \App\Models\Rdf\RDFConcept();

                $class = $RDF->getClass('Article', false);
                $proceeding = $RDF->getClass('Proceeding', false);

                $Source = new \App\Models\Base\Sources();
                $cp = 'jnl_frbr, d_r1, d_p, d_r2, c_class, cc_class ';
                $dt = $Source
                        ->select($cp)
                        ->join('rdf_data','d_r2 =jnl_frbr')
                        ->join('rdf_concept','id_cc = d_r1')
                        ->join('rdf_class', 'cc_class = id_c')
                        ->where('jnl_collection','EV')
                        ->where('d_p', '126')
                        ->where('cc_class', $class)
                        ->findAll(100);
                foreach($dt as $id=>$line)
                    {
                        $id = $line['d_r1'];
                        $sql = "update brapci.rdf_concept set cc_class = $proceeding where id_cc = $id";
                        $RDFconcept->db->query($sql);
                        $sx .= '. ';
                    }

                $sx .= $this->exactMath();

                return $sx;
            }
}
