<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Books extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'books_expression';
    protected $primaryKey       = 'id_be';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_be', 'be_title', 'be_authors',
        'be_cover', 'be_rdf', 'be_isbn13',
        'be_isbn10', 'be_type', 'be_lang',
        'be_status'
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

    function register($id,$dt)
        {
            $dd = $this->where('be_rdf',$id)->first();
            if ($dd == '')
                {
                    $this->set($dt)->insert();
                }
            return true;
        }

    function getid($id)
        {
            $dt = $this->find($id);
            return $dt;
        }

    function lastItens()
    {
        $dt = $this
                ->where('be_status <> 0 and be_status <> 9')
                ->orderby('id_be desc')
                ->findAll(0,10);
        foreach($dt as $id=>$line)
            {
                $line['be_full'] = mb_strtolower(ascii($line['be_title']));
                $dt[$id] = $line;
            }
        return $dt;
    }


}
