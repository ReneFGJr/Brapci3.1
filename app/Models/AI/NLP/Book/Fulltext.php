<?php

namespace App\Models\AI\NLP\Book;

use CodeIgniter\Model;

class Fulltext extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'fulltexts';
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

    function index($d1,$d2='')
        {
            $RDF = new \App\Models\Rdf\RDF();
            $dt = $RDF->le($d1);

            $id_file = $RDF->extract($dt, 'hasFileStorage');
            if (count($id_file) > 0)
                {
                    $dtf = $RDF->le($id_file[0]);
                    $file = $dtf['concept']['n_name'];
                    $fileTXT = troca($file,'.pdf','.txt');
                    echo h($file);
                    echo h($fileTXT);
                    if (file_exists($fileTXT))
                        {
                            echo "OK";
                        }
                } else {
                    return bsmessage(lang('brapci.no_file_to_process'));
                }



        }
}
