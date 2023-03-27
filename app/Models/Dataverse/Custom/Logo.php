<?php

namespace App\Models\Dataverse\Custom;

use CodeIgniter\Model;

class Logo extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'logos';
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

    function index($d1, $d2, $d3)
    {
        $dir = '_repository/dataverse/package/logo/' . date("Y-m-d-H-m");

        $sx = '';
        $sx = form_open_multipart();
        $sx .= form_upload('logo');
        $sx .= '<br>';
        $sx .= form_submit('action', lang('dataverse.upload'), 'btn btn-outline-secondar');
        $sx .= form_close();
        $sx = bs(bsc($sx, 12));

        if (isset($_FILES['logo']['tmp_name']))
            {
                $dir = '_repository/dataverse/package/logo/'.date("Y-m-d-H-m");
                dircheck($dir);

                $temp = $_FILES['logo']['tmp_name'];
                $type = $_FILES['logo']['type'];

                $ok = false;
                switch($type)
                    {
                        case 'image/png':
                            $ok = true;
                            break;
                        default:
                            $sx .= 'Invalid Type '.$type;
                    }

                if ($ok)
                    {
                        $file = $dir.'/logo.png';
                        echo h($file,3);
                        move_uploaded_file($temp,$file);
                    }

            }
            $file = $dir . '/logo.png';
            if (file_exists($file))
                {
                    $sx = '';

                }
        return $sx;
    }
}
