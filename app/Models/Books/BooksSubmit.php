<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class BooksSubmit extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'books_submit';
    protected $primaryKey       = 'id_bs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bs', 'bs_post', 'bs_status',
        'bs_title', 'b_isbn'
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

    function resume()
        {
            $sx = '';
            $dt = $this
                ->select("count(*) as total, bs_status")
                ->where('bs_status',0)
                ->groupBy('bs_status')
                ->orderBy('bs_status');

            foreach($dt as $id=>$line)
                {
                    $sx .= '<li class="text-danger">';
                    $sx .= lang('brapci.book_status_'.$line['bs_status']);
                    $sx .= ' <b>';
                    $sx .= $line['total'];
                    $sx .= '</b>';
                    $sx .= '</li>';
                }
            return $sx;
        }

    function register()
        {
            $PS = array_merge($_POST, $_GET);
            $PSj = json_encode($PS);
            $dt = [];
            $dt['bs_post'] = $PSj;
            if (isset($PS['b_titulo']))
                {
                    $dt['bs_title'] = $PS['b_titulo'];
                    $dt['b_isbn'] = $PS['b_isbn'];
                }
            $this->set($dt)->insert();
        }
}
