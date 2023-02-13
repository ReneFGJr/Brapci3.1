<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class ClassificationAncib extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'books_taxonomy';
    protected $primaryKey       = 'id_bs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bs', 'bs_rdf', 'bs_father', 'bs_order', 'bs_name', 'updated_at'
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

    function list()
        {
            $dt = $this->findALl();
            pre($dt);
        }

    function showFULL($id)
        {
            $sx = '';
            $RDF = new \App\Models\Rdf\RDF();
            $Books = new \App\Models\Base\Book();
            $dt = $RDF->le($id);
            $concept = $dt['concept'];
            $sx .= bsc(h($concept['n_name'],2),12);
            $data = $dt['data'];

            $sxa = '';

            foreach($data as $id=>$line)
                {
                    $class = $line['c_class'];
                    if ($class == 'hasClassificationAncib')
                        {
                            $sa = $Books->show($line['d_r1']);
                            $sx .= bsc($sa, 4, 'border border-secondaty');
                        }
                }

            $sx = bs($sx);

            return $sx;
        }
}
