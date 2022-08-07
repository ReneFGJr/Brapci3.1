<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Mark extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_click.mark_save';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_mk', 'mk_user', 'mk_selected',
        'mk_created_at', 'mk_update_at', 'mk_name'
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

    function saveMark()
        {
            $Socials = new \App\Models\Socials();
            $sx = '';

            $user = $_SESSION['id'];
            if (isset($_SESSION['markName']))
                {

                } else {
                    $name = get("nameMark");
                    $sx .= form_open();
                    $sx .= form_input(array('name' =>'nameMark','class' =>'form-control','value' => $name));
                    $sx .= form_submit(array('name' => 'action', 'value' =>lang('brapci.save')));
                    $sx .= form_close();
                }

            if ((get("nameMark") != '') and (get("action") != ''))
                {
                    $dt['mk_user'] = $_SESSION['id'];
                    $dt['mk_name'] = get("nameMark");
                    $dt['mk_selected'] = $_SESSION['sel'];
                    $dt['mk_update_at'] = date("Y-m-dTH:i:s");
                    $this->set($dt)->insert();
                } else {

                }
            $sx = bs(bsc($sx,12));
            return $sx;
            pre($_SESSION);
        }
}
