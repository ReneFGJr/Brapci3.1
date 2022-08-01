<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'brapci_books.books';
    protected $primaryKey           = 'id_ca';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $sx =  '';
        switch ($d1) {
            case 'autoloader':
                $sx .= $this->autoloader();
                break;
        }
        $sx = bs($sx);
        return $sx;
    }

    function btnAutoDeposit()
    {
        $sx = '<button type="button" class="btn btn-primary btn-lg">Autodeposito</button>';
        return $sx;
    }

    function autoloader()
    {
        $sx = '';
        $sx .= view('BrapciBooks/Pages/autodeposit');
        $sx .= view('BrapciBooks/Terms/termBR');
        return $sx;
    }
}
