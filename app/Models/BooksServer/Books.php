<?php

namespace App\Models\BooksServer;

use CodeIgniter\Model;

class Books extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'server_books';
    protected $primaryKey       = 'id_b';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_b', 'b_isbn', 'b_title',
        'b_update','b_status'
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

    function search($isbn='',$title='')
        {
            $ISBN = new \App\Models\ISBN\Index();
            $isbn = $ISBN->format($isbn);
            $isbns = $ISBN->isbns($isbn);
            $RSP = [];
            $RSP['isbns'] = $isbns;
            $isbn = $isbns['isbn13'];
            $RSP['isbn'] = $isbn;

            /********* Caso não exista */
            $ISBNdb = new \App\Models\ISBN\Isbndb\Index();
            $dt = json_decode($ISBNdb->search($isbn));
            $RSP['isbnbd'] = $ISBNdb->convert($dt);

            return $RSP;
        }
}
