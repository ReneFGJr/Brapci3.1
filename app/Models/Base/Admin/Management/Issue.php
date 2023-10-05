<?php

namespace App\Models\Base\Admin\Management;

use CodeIgniter\Model;

class Issue extends Model
{
    protected $DBGroup          = 'management';
    protected $table            = 'issue';
    protected $primaryKey       = 'id_i';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'i_issue', 'i_journal', 'i_year', 'i_vol', 'i_nr'
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
        $class = $RDF->getClass('Issue', false);
        $sx .= bs(bsc(h('Issue', 4)));
        $sx .= $this->check_class($class);

        $sx .= $this->complete();

        return $sx;
    }

    function complete()
        {
            $Work = new \App\Models\Base\Admin\Management\Work();
            $RDF = new \App\Models\Rdf\RDF();
            $dt = $this->where('i_journal',0)->findAll(10);

            foreach($dt as $id=>$line)
                {
                    $dtx = $RDF->le($line['i_issue']);
                    $jnl1 = $RDF->recovery($dtx['data'], 'hasIssueOf');
                    $nr = $RDF->recovery($dtx['data'], 'hasPublicationNumber');

                    $nr = $RDF->recover($dtx, 'hasPublicationNumber');
                    $nr = $RDF->le($nr[0]);
                    $nr = $nr['concept']['n_name'];

                    $vol = $RDF->recover($dtx, 'hasPublicationVolume');
                    $vol = $RDF->le($vol[0]);
                    $vol = $vol['concept']['n_name'];

                    $year = $RDF->recover($dtx, 'dateOfPublication');
                    $year = $RDF->le($year[0]);
                    $year = $year['concept']['n_name'];

                    foreach($jnl1 as $idj=>$linej)
                        {
                            $id_article = $linej[1];
                            $d['i_journal'] = $line['i_issue'];
                            $d['i_issue'] = $line['i_issue'];
                            $d['i_year'] = $year;
                            $d['i_vol'] = $vol;
                            $d['i_nr'] = $nr;
                            pre($d);
                            echo "OK";
                            exit;
                        }
                }


            pre($dt);
        }

    function check_class($class)
    {
        $sx = '';
        $dt = $this
            ->join('brapci.rdf_concept', 'id_cc = i_issue', 'RIGHT')
            ->where('cc_class', $class)
            ->where('i_issue is null')
            ->orderBy('id_cc')
            ->findAll(10000);
        //echo $this->getlastquery();

        foreach ($dt as $id => $line) {
            $d = [];
            $d['i_issue'] = $line['id_cc'];
            $d['i_journal'] = 0;
            $d['i_year'] = 0;
            $d['i_vol'] = '';
            $d['i_nr'] = '';
            $this->set($d)->insert();
            $sx .= '. ';
        }
        return bs(bsc($sx, 6));
    }
}
