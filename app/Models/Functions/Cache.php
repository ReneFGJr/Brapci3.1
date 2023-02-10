<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class Cache extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'caches';
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

    function remove_cache()
        {
        $dirname = '.c';
        system("rm -rf " . escapeshellarg($dirname));
        system('rmdir ' . escapeshellarg($dirname) . ' /s /q');
        }

    function index()
        {
            $sx = '';
            $sx .= h(lang('brapci.clear_cache'),3);

            if (get("confirm") == 'true')
                {
                    $this->remove_cache();
                    $sx = bsmessage(lang('brapci.clear_cache').' '.lang('brapci.success'),1);
                    $sx .= anchor(PATH.'admin',lang('brapci.return'));
                } else {
                    $url = PATH . 'admin/cache';
                    $url2 = PATH . 'admin/';
                    $sx .= form_confirm($url,$url2);
                }

            $sx = bs(bsc($sx, 12));
            return $sx;
        }
}
