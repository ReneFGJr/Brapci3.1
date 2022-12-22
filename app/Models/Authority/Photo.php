<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class Photo extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'photos';
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

    function dir($id)
        {
            $id = strzero($id,8);
            $dr = '.c/'.substr($id,0,2) . '/' . substr($id,2,2) . '/' . substr($id,4,2).'/'.substr($id,6,2);
            $d = explode('/',$dr);
            $dir = '';
            for($r=0;$r < count($d);$r++)
                {
                    $dir .= $d[$r] .= '/';
                    dircheck($dir);
                }
            return $dir;
        }

    function image($dt)
        {
            $dir = $this->dir($dt['a_brapci']);

            if (file_exists($dir.'photo.png')) {
                echo "OK";
            } else {
                if ($dt['a_genere'] =='F')
                    {
                        $img = 'img/genre/no_image_she.jpg';
                    } else {
                        if ($dt['a_genere'] == 'M') {
                            $img = 'img/genre/no_image_he.jpg';
                        } else {
                            $img = 'img/genre/no_image_she_he.jpg';
                        }
                    }
            }
            $img = '<img src="' . PATH.$img . '" class="img-fluid">';
            return $img;
        }
}
