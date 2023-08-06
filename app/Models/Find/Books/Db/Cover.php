<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Cover extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'covers';
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


    function saveDataCover($isbn, $data)
    {

            $RSP = [];
            $dir = substr($isbn, 0, 3) . '/' . substr($isbn, 3, 4) . '/' . substr($isbn, 7, 3) . '/' . substr($isbn , 10,3);
            $dir = '_repository/book/'.$dir;
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);

            $RSP['dir'] = $dir;
            $RSP['type'] = $type;
            $RSP['isbn'] = $isbn;
            $RSP['len'] = strlen($data);

            dircheck($dir);
            $ext = '';
            switch($type)
                {
                    case 'data:image/png':
                        $ext = '.png';
                        break;
                    case 'data:image/jpg':
                        $ext = '.jpg';
                        break;
                    default:
                        $RSP['message'] = 'Formato inválido '.$type;
                        break;
                }
            if ($ext != '')
                {
                    $BookExpression = new \App\Models\Find\Books\Db\BooksExpression();
                    file_put_contents($dir . '/' . $isbn . $ext, $data);
                    $RSP['cover'] = URL.'/'.$dir . '/'. $isbn . $ext;
                    $dt = [];
                    $dt['be_cover'] = URL . '/' . $dir . '/' . $isbn . $ext;
                    $BookExpression->set($dt)->where('be_isbn13',$isbn)->update();
                }
        return $RSP;
    }
}
