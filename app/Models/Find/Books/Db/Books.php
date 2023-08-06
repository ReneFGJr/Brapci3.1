<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Books extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'books';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bk', 'bk_title'
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

    function changeTitle($id,$name)
        {
            $dd['bk_title'] = $name;
            $this->set($dd)->where('id_bk',$id)->update();
            return true;
        }

    function register($title)
    {
        $title = nbr_title($title);
        $dt = $this->where('bk_title', $title)->first();
        if ($dt == '') {
            $id = $this->set(['bk_title' => $title])->insert();
        } else {
            $id = $dt['id_bk'];
        }
        return $id;
    }

    function lastitens($ini = '', $fim = '')
    {
        $RSP = [];
        /******************************************* CHECK LIBRATY */
        $Libraries = new \App\Models\Find\Books\Db\Library();
        $RSP = $Libraries->checkLibrary($RSP);
        if ($RSP['status'] == '200') {
            $fim = sonumero($fim);
            $ini = sonumero($ini);
            if ($ini == '') {
                $ini = 0;
            }
            if ($fim == '') {
                $fim = 12;
            }
            $lib = get('library');
            $Item = new \App\Models\Find\Books\Db\BooksLibrary();
            $cp = 'bl_ISBN, bl_library, be_authors, be_year, be_cover, bk_title';
            $dt = $Item
                ->select($cp)
                ->join('books_expression', 'be_isbn13 = bl_ISBN')
                ->join('books', 'be_title = id_bk')
                ->where('bl_library',$lib)
                ->groupBy($cp)
                ->findAll($ini, $fim);
            $RSP = [];
            foreach ($dt as $id => $line) {
                $title = $line['bk_title'];
                if (strlen($title) > 40)
                    {
                        $title = substr($title,0,40).'...';
                    }
                $dt[$id]['bk_title'] = $title;
            }
            echo json_encode($dt);
            exit;
        }
        echo json_encode($RSP);
        exit;
    }
}
