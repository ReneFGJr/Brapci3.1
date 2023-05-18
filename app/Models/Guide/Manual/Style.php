<?php

namespace App\Models\Guide\Manual;

use CodeIgniter\Model;

class Style extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'styles';
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
            $d1 = round('0'.$d1);
            $dir = '_repository/guide/' . $d1 . '/';
            dircheck($dir);

            $file = $dir.'guide.css';
            if (!file_exists($file))
                {
                    file_put_contents($file,'');
                }

            $action = get("action");
            if ($action != '')
                {
                    $style = get("style");
                    file_put_contents($file, $style);
                } else {
                    $style = file_get_contents($file);
                }

            $sx = form_open();
            $sx .= form_label('Style CSS');
            $sx .= form_textarea('style',$style,['class'=>'mb-form-control full']);
            $sx .= form_submit('action',lang('guide.save'),['class'=>'btn btn-primary']);
            $sx .= form_close();
            $sx = bs(bsc($sx));
            return $sx;
        }
}
