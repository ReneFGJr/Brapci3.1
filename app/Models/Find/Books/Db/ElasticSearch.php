<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class ElasticSearch extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'elasticsearches';
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

    function reindex()
        {
            $type = 'books';
            $BE = new \App\Models\Find\Books\Db\BooksExpression();
            $cp = 'bk_title, be_authors, be_year, be_isbn13, be_rdf';
            $dt = $BE
                ->select($cp)
                ->Join('books', 'be_title = id_bk')
                ->findAll();
            $sx = '';

            $Elastic = new \App\Models\ElasticSearch\API();
            foreach($dt as $id=>$line)
                {
                    $line['full'] = ascii($line['bk_title'].' '.$line['be_authors']);

                    $rst = $Elastic->call('find_trunk/' . $type . '/' . $id, 'POST', $line);
                    $sx .= $id .= ' => ' . $rst['result'] . ' - '. $line['bk_title']. ' v.' . $rst['_version'] .'<br>';
                }
            return $sx;
        }
}
