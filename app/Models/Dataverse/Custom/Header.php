<?php

namespace App\Models\Dataverse\Custom;

use CodeIgniter\Model;

class Header extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'headers';
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

    function index($d1,$d2,$d3)
        {
            $sx = '';
            $sx .= h(lang('dataverse.custom').' '.lang('dataverse.header'),1);

            $sx .= $this->make();

            $sx = bs(bsc($sx));
            return $sx;
        }

    function make()
        {
            $sx = '';
            $sx .= '<p>Esta propriedade define uma página inicial customizada para o Dataverse.</p>';
            $sx .= '<p>A Página precisa esta no diretório Branding</p>';
            $sx .= '<code class="code">';
            $sx .= 'mkdir /var/www/dataverse/'. '<br><br>'.cr();
            $sx .= 'mkdir /var/www/dataverse/branding/' . '<br><br>' . cr();
            $sx .= 'echo "See sample homepage"' . '<br><br>' . cr();
            $sx .= "curl -X PUT -d '/var/www/dataverse/branding/custom-homepage.html' http://localhost:8080/api/admin/settings/:HomePageCustomizationFile".cr();
            $sx .= '</code>';
            return $sx;
        }

}
