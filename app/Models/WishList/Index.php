<?php

namespace App\Models\WishList;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
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

    function wishlist($id)
        {
        //return bsicone('heart-full');
            $sx = bsicone('heart', 32, 'red');
            if (isset($_SESSION['book_'.$id]))
                {
                    $sx = bsicone('heart-full', 32, 'red');
                    $sx = '<span class="heart" title="Adicionar a lista de desejos" onclick="wishlist(' . $id . ')">' . $sx . '</span>';
                }
            $sx = '<div id="heart" style="cursor: pointer;" onclick="heater('.$id.');">'.$sx.'</div>';
            $sx .= '
            <script>
                function heater($id)
                    {
                        alert("Clique em "+$id);
                    }
            </script>';
            return $sx;
        }
}
