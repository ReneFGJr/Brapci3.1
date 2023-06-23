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
            $Data = new \App\Models\Find\BooksOld\Data();
            $Expression = new \App\Models\Find\Books\Db\Expression();
            $dt = $Expression->where('be_status',-1)->first();

            if ($dt != '')
                {
                    $idx = $dt['be_rdf'];
                    $dd = $Data
                            ->join('rdf_name', 'd_literal = id_n', 'LEFT')
                            ->where('d_r1',$idx)
                            ->where('d_literal <> 0')
                            ->findAll();
                    pre($dd,false);

                    $dd = $Data
                        ->join('rdf_class', 'd_p = id_c')
                        ->where('d_r1', $idx)
                        ->where('d_r2 <> 0')
                        ->findAll();
                    pre($dd, false);
                }
        }

    function inport()
        {
            $Books = new \App\Models\Find\Books\Db\Books();
            $cp = 'id_cc, cc_library, id_c, c_class, d_r2, n_name, n_lang';
            $db = $this
                    ->select($cp)
                    ->join('rdf_data','id_cc = d_r1')
                    ->join('rdf_class','d_p = id_c')
                    ->join('rdf_name', 'd_literal = id_n','LEFT')
                    ->where('cc_class',16)
                    ->where('id_c', 5)
                    ->findAll(0,10);
           foreach($db as $id=>$line)
            {
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
                $dd = $Books->register($id,$da);
            }
            $url = PATH . 'admin/find/harvesting/0';
            $sx = anchor($url,$url);
            return $sx;
        }
}
