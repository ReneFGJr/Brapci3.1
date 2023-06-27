<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Books extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'books_expression';
    protected $primaryKey       = 'id_be';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_be', 'be_title', 'be_authors',
        'be_cover', 'be_rdf', 'be_isbn13',
        'be_isbn10', 'be_type', 'be_lang',
        'be_status'
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

    function register($id, $dt)
    {
        $dd = $this->where('be_rdf', $id)->first();
        if ($dd == '') {
            $this->set($dt)->insert();
        }
        return true;
    }

    function getid($id)
    {
        $dt = $this->find($id);
        $dt['data'] = $this->getData($id);
        return $dt;
    }

    function getData($id)
        {
            $cp = 'c_class, prefix_ref, prefix_url, c_type, id_cc, cc_pref_term, n_name, n_lang, id_d';
            $dt = $this
                ->select($cp)
                ->join('rdf_data', 'be_rdf = d_r1')
                ->join('rdf_concept','id_cc = d_r2')
                ->join('rdf_class', 'id_c = d_p')
                ->join('rdf_prefix', 'id_prefix = c_prefix')
                ->join('rdf_name', 'id_n = cc_pref_term')
                ->where('be_rdf',$id)
                ->orderBy('c_class, id_d')
                ->findAll();
            $rst = [];
            foreach($dt as $id=>$ln)
                {
                    $class = $ln['c_class'];
                    if (!isset($rst[$class]))
                        {
                            $rst[$class] = [$ln];
                        } else {
                            array_push($rst[$class],$ln);
                        }
                }
            return $rst;
        }

    function lastItens($start='10',$limite = '0')
    {
        $start = round($start)+1;
        $limite = round($limite);
        if ($limite == 0) { $limite = 20; }

        $dt = $this
            ->where('be_status <> 0 and be_status <> 9')
            ->orderby('id_be desc')
            ->findAll($start,$limite);
        foreach ($dt as $id => $line) {
            $line['be_full'] = mb_strtolower(ascii($line['be_title']));
            $dt[$id] = $line;
        }
        return $dt;
    }
}
