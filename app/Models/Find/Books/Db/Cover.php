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

            $RSP = '';
            $isbn = get("isbn");
            $dir = substr($isbn, 0, 3) . '/' . substr($isbn, 3, 4) . '/' . substr($isbn, 7, 3) . '/' . substr($isbn / 7.3);

            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);

            $RSP['dir'] = $dir;
            $RSP['type'] = $type;
            $RSP['isbn'] = $isbn;
            $RSP['len'] = strlen($data);
            return $RSP;

            file_put_contents('/tmp/image.png', $data);

        return $RSP;
    }
}
