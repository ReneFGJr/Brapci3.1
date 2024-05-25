<?php

namespace App\Models\AI\NLP\Vc;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'brapci_v3';
    protected $table            = 'vocabulary';
    protected $primaryKey       = 'id_vc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_vc', 'vc_term', 'vc_pref',
        'vc_ID', 'vc_type', 'vc_size'
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

    function register($term, $pref, $id, $type)
    {
        if (strlen($term) > 2) {
            $dd['vc_term'] = $term;
            $dd['vc_pref'] = $pref;
            $dd['vc_ID'] = $id;
            $dd['vc_type'] = $type;
            $dd['vc_size'] = strlen($term);
            $this->set($dd)->insert();
        }
    }

    function exportVC($d1)
        {
            switch($d1)
                {
                    case 'Word':
                    break;
                }
        }

    function exportVCsn($d1)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $class = $RDFclass->getClass($d1);
        $cp = 'L2.n_name as Pref, L1.n_name as Alt, id_cc as ID';
        $RDFconcept
            ->select($cp)
            ->join('brapci_rdf.rdf_literal as L2', 'cc_pref_term = L2.id_n')
            ->join('brapci_rdf.rdf_data', 'd_r1 = id_cc')
            ->join('brapci_rdf.rdf_literal as L1', 'd_literal = L1.id_n')
            ->where('cc_class', $class)
            ->where('d_literal > 0');
        $dt = $RDFconcept->findAll();

        $dir = '.tmp/vc/';
        dircheck($dir);
        $file = $dir . 'subject.php';
        $sx = '<?php' . chr(13);
        $sx .= '$vc = [];' . chr(13);
        $sx .= chr(13);

        $dd = [];
        foreach ($dt as $id => $line) {
            $term = ascii($line['Alt']);
            $term = mb_strtoupper($term);
            $pref = $line['Pref'];
            $pref = troca($pref, ' ', '_');
            $ID = $line['ID'];
            $this->register($term, $pref, $ID, 1);
        }


        $cp = 'n_name as Pref, id_cc as ID';
        $RDFconcept
            ->select($cp)
            ->join('brapci_rdf.rdf_literal as L2', 'cc_pref_term = L2.id_n')
            ->where('cc_class', $class);
        $dt = $RDFconcept->findAll();
        foreach ($dt as $id => $line) {
            $term = ascii($line['Pref']);
            $term = mb_strtoupper($term);
            $pref = $line['Pref'];
            $pref = troca($pref, ' ', '_');
            $ID = $line['ID'];
            $this->register($term, $pref, $ID, 1);
        }
        $sx = bsmessage('Exportação finalizada', 1);
        return bs(bsc($sx));
    }
}
