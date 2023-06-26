<?php

namespace App\Models\Find\BooksOld;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'find2';
    protected $table            = 'rdf_concept';
    protected $primaryKey       = 'id_cc';
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

    function harvesting($id)
    {
        $ISBN = '';
        $DTE = [];
        $Data = new \App\Models\Find\BooksOld\Data();
        $Expression = new \App\Models\Find\Books\Db\Expression();

        $dt_author = [];
        $dt_expression = [];
        $dt_manifestation = [];

        $dt = $Expression->where('be_status', -1)->first();
        $idx = $dt['be_rdf'];

        $idf = $dt['be_rdf'];

        /* Recupera Conceito */
        $dtc = $this->leConcept($idx);
        $DTE['be_title'] = $dtc['n_name'];
        $DTE['be_lang'] = $dtc['n_lang'];
        $DTP = [];

        $dtcd = $this->leData($idx);

        foreach ($dtcd as $idc => $line) {
            $line = (array)$line;
            $class = $line['c_class'];
            switch ($class) {
                case 'hasAuthor':
                    $dt_author[$line['d_r2']] = $line['n_name2'];
                    break;
                case 'isAppellationOfExpression':
                    $dt_expression[$line['d_r2']] = $line['n_name2'];
                    break;
            }
        }

        /************************** Expression */
        foreach ($dt_expression as $ide => $linee) {
            $dte = $this->leConcept($ide);
            $dted = $this->leData($ide);
            $dted = (array)$dted;
            foreach ($dted as $idd => $lined) {
                $lined = (array)$lined;
                $class = trim($lined['c_class']);
                switch ($class) {
                    case 'isAppellationOfManifestation':
                        $dt_manifestation[$lined['d_r2']] = $lined['n_name2'];
                        $ISBN = troca($lined['n_name2'],'ISBN:','');
                        break;
                    default:
                        //echo $class . '[][][]<hr>';
                        break;
                }
            }
        }
        /************************** Manifestation */
        foreach ($dt_manifestation as $idm => $linee) {
            $dtm = $this->leConcept($idm);
            $dtem = $this->leData($idm);
            $dtem = (array)$dtem;

            foreach ($dtem as $idd => $linem) {
                $linem = (array)$linem;
                $class = trim($linem['c_class']);
                $ok = 0;
                switch ($class) {
                    case 'isAppellationOfManifestation':
                        break;
                    case 'hasPage':
                        $ok = 1;
                        break;
                    case 'description':
                        $ok = 2;
                        break;
                    case 'harvestingdescription':
                        $ok = 1;
                        break;

                    case 'dateOfPublication':
                        $ok = 1;
                        break;
                    default:
                        echo h('Error: '.$class,4).'<hr>';
                        break;
                }
                if ($ok==1)
                    {
                    if (isset($DTP[$class])) {
                        array_push($DTP[$class], [$linem['n_name2']]);
                    } else {
                        $DTP[$class] = [$linem['n_name2']];
                    }
                    }

                if ($ok == 2) {
                    if (isset($DTP[$class])) {
                        array_push($DTP[$class], [$linem['n_name']]);
                    } else {
                        $DTP[$class] = [$linem['n_name']];
                    }
                }
            }
        }
        $isbns = new \App\Models\ISBN\Index();

        if (strlen($ISBN) == 13)
            {
                $dt = $isbns->isbns($ISBN);
                $DTE['be_isbn13'] = $dt['isbn13'];
                $DTE['be_isbn10'] = $dt['isbn10'];
            }
        echo h($ISBN);
        pre($dt_author, false);
        pre($dt_expression, false);
        echo h('Manifestation',5);
        pre($dt_manifestation, false);
        echo h('DTE', 5);
        pre($DTE, false);
        echo h('DTP', 5);
        pre($DTP,false);
    }

    function leConcept($idx)
    {
        $RDFConcept = new \App\Models\Rdf\RDFConcept();
        $RDFConcept->table = 'find2.rdf_concept';

        $PREFIX = 'find2.';

        $RDFConcept->select('rdf_class.c_class, rdf_class.id_c, rdf_class.c_type, rdf_class.c_url, rdf_class.c_equivalent');
        $RDFConcept->select('rdf_name.n_name, rdf_name.n_lang, rdf_name.id_n');
        $RDFConcept->select('rdf_prefix.prefix_ref, rdf_prefix.prefix_url');
        $RDFConcept->select('rdf_concept.*');

        $RDFConcept->join($PREFIX . 'rdf_name', 'cc_pref_term = rdf_name.id_n', 'LEFT');
        $RDFConcept->join($PREFIX . 'rdf_class', 'rdf_concept.cc_class = rdf_class.id_c', 'LEFT');
        $RDFConcept->join($PREFIX . 'rdf_prefix', 'rdf_class.c_prefix = rdf_prefix.id_prefix', 'LEFT');
        $RDFConcept->where('id_cc', $idx);
        $dtp = $RDFConcept->first();
        return $dtp;
    }

    function leData($idx)
    {
        $PREFIX = 'find2.';
        $sql = "select ";
        $sql .= " DISTINCT
    		rdf_name.id_n, rdf_name.n_name, rdf_name.n_lang,
			rdf_class.c_class, rdf_class.c_prefix, rdf_class.c_type,
			rdf_prefix.prefix_ref, rdf_prefix.prefix_url,
    		rdf_data.*,
			prefix_ref, prefix_url,
			n2.n_name as n_name2,
			n2.n_lang as n_lang2
			";
        $sql .= "from " . $PREFIX . "rdf_data ";
        $sql .= "left join " . $PREFIX . "rdf_name ON d_literal = rdf_name.id_n ";
        $sql .= "left join " . $PREFIX . "rdf_class ON rdf_data.d_p = rdf_class.id_c ";
        $sql .= "left join " . $PREFIX . "rdf_prefix ON rdf_class.c_prefix = rdf_prefix.id_prefix ";

        $sql .= "left join " . $PREFIX . "rdf_concept as rc2 ON rdf_data.d_r2 = rc2.id_cc ";
        $sql .= "left join " . $PREFIX . "rdf_name as n2 ON n2.id_n = rc2.cc_pref_term ";


        $sql .= "where (d_r1 = $idx) OR (d_r2 = $idx)";
        $sql .= "order by c_class, id_d, d_r1, d_r2, n_name";
        $dt = (array)$this->db->query($sql)->getResult();
        return $dt;
    }

    function leValue($idx)
    {
        $Data = new \App\Models\Find\BooksOld\Data();
        $dd = $Data
            ->select('d_r1 as id_cc, n_name,n_lang, c_class,c_prefix,c_equivalent')
            ->join('rdf_name', 'd_literal = id_n', 'LEFT')
            ->join('rdf_class', 'd_p = id_c')
            ->where('d_r1', $idx)
            ->where('d_literal <> 0')
            ->findAll();
        return $dd;
    }

    function inport()
    {
        $Books = new \App\Models\Find\Books\Db\Books();
        $cp = 'id_cc, cc_library, id_c, c_class, d_r2, n_name, n_lang';
        $db = $this
            ->select($cp)
            ->join('rdf_data', 'id_cc = d_r1')
            ->join('rdf_class', 'd_p = id_c')
            ->join('rdf_name', 'd_literal = id_n', 'LEFT')
            ->where('cc_class', 16)
            ->where('id_c', 5)
            ->findAll(0, 10);
        foreach ($db as $id => $line) {
            $title = trim($line['n_name']);
            $id = $line['id_cc'];
            $da['be_title'] = $title;
            $da['be_rdf'] = $id;
            $da['be_status'] = -1;
            $da['be_cover'] = '';
            $da['be_authors'] = '';
            $da['be_isbn13'] = '';
            $da['be_isbn10'] = '';
            $da['be_type'] = '';
            $da['be_lang'] = 1;
            $dd = $Books->register($id, $da);
        }
        $url = PATH . 'admin/find/harvesting/0';
        $sx = anchor($url, $url);
        return $sx;
    }
}
