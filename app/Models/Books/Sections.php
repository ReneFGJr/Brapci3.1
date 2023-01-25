<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class Sections extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'books_taxonomy';
    protected $primaryKey       = 'id_bs';
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

    function sections()
        {
            $dt = $this->findAll();
            $sx = '<div id="category" class="p-2" style="width:100%; display:none; border: 1px solid #777; border-radius: 10px;">';
            $sx .= '<ul>';
            foreach($dt as $id=>$line)
                {
                    $linka = '</a>';
                    $link = '<a href="'.PATH.'/books/v/'.$line['bs_rdf'].'">';
                    $sx .= '<li>'.$link.$line['bs_name'].$linka.'</li>';
                }
            $sx .= '</ul>';
            $sx .= '</div>';
            $sx .= '<script>';
            $sx .= '$( "#btn_category" ).click(function() {
                    $("#category").toggle(\'slow\');
                    });';
            $sx .= '</script>';
            return $sx;
        }
}
