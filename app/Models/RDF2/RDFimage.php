<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFimage extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfimages';
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

    function cover($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $dti = $RDF->le($ID);
        $data = $dti['data'];

        $dir = '';
        $file = '';
        $tumb = '';
        $type = '';

        foreach ($data as $id => $line) {
            $prop = $line['Property'];
            if ($prop == 'hasFileDirectory') {
                $dir = $line['Caption'];
            }
            if ($prop == 'hasContentType') {
                $type = $line['Caption'];
            }

            if ($prop == 'hasTumbNail') {
                $tumb = $line['Caption'];
            }
        }

        if ($dir != '') {
            switch($type)
                {
                    case 'image/jpeg':
                        $nfile = $dir.'image.jpg';
                        if (file_exists($nfile))
                            {
                                return PATH.$nfile;
                            }

                        if (file_exists($tumb)) {
                            return PATH.$tumb;
                        }

                    break;
                case 'image/png':
                    $nfile = $dir . 'image.png';
                    if (file_exists($nfile)) {
                        return PATH . $nfile;
                    }

                    if (file_exists($tumb)) {
                        return PATH . $tumb;
                    }

                    break;
                }
        }
        return PATH.'img/cover/no_cover.png';
    }
}
