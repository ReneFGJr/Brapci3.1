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

    function insertISBN($isbn,$library,$user)
        {
            $ISBN = new \App\Models\Functions\Isbn();
            $dd = [];
            $dt = $this
                ->join('book_library','bl_item = id_be','left')
                ->where('be_isbn13',$isbn)
                ->orderBy('id_be desc')
                ->first();
            $expressao_new = false;
            if ($dt != '')
                {
                    $sta = trim($dt['be_status']);
                    $expressao_new = false;
                    $expressao = $dt['id_be'];
                } else {
                    $expressao_new = true;
                    $expressao = 0;
                }

            if ($expressao_new == true)
                {
                    $de = [];
                    $de['be_title'] = '[em coleta '.$isbn.']';
                    $de['be_authors'] = '';
                    $de['be_year'] = '';
                    $de['be_cover'] = '';
                    $de['be_rdf'] = '';
                    $de['be_isbn13'] = $isbn;
                    $de['be_isbn10'] = $ISBN->isbn13to10($isbn);
                    $de['be_type'] = '';
                    $de['be_lang'] = '';
                    $de['be_status'] = 0;

                    $expressao = $this->set($de)->insert();
                }

                $stl = trim($dt['bl_status']);
                if ($stl == '') { $stl = 0; } else ($str = round($stl));
                if ($stl == 0)
                    {
                        $item_new = true;
                        $dd['message'] = 'Inserido com sucesso';
                        $dd['status'] = '200';
                    } else {
                        $item_new = false;
                        $dd['message'] = 'Já existe este item em edição';
                        $dd['status'] = '201';
                    }

                $dd['isbn'] = $isbn;
                $dd['library'] = $library;
                $dd['user'] = $user;
                $dd['expressao'] = $expressao;

                if ($item_new == true)
                {
                        $Item = new \App\Models\Find\Books\Db\Item();
                        $id = $Item->register($dd);
                        $dd['item'] = $id;
                } else {
                    $dd['item'] = $dt['id_bl'];
                }

            echo json_encode($dd);
            exit;
            return $dd;
        }

    function list_status($sta)
        {
            $library = $this->library();
            $cp = 'id_bl, be_title, be_authors, be_year, be_cover, be_isbn13, bl_item, bl_tombo, bl_catalogador, bl_status';
            $dt = $this
                    ->select($cp)
                    ->join('book_library','bl_item = id_be')
                    ->where('bl_library', $library)
                    ->where('bl_status', $sta)
                    ->findAll(0,20);
            echo json_encode($dt);
            exit;
        }

    function register($id, $dt)
    {
        $dd = $this->where('be_rdf', $id)->first();
        if ($dd == '') {
            $this->set($dt)->insert();
        }
        return true;
    }

    function library()
        {
            $lib = sonumero(get("library"));
            if ($lib == '')
                {
                    $dd['status'] = '500';
                    $dd['message'] = 'Biblioteca não informada';
                    $dd['time'] = date("Y-m-dTH:i:s");
                    echo json_encode($dd);
                    exit;
                }
            return $lib;
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
