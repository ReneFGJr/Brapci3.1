<?php

namespace App\Models\Find;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
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

    function index($d1, $d2, $d3)
    {
        $sx = anchor(PATH . '/admin/find/inport', 'Inport FIND');
        switch ($d1) {
            case 'harvesting':
                $BooksOld = new \App\Models\Find\BooksOld\Index();
                $sx .= '<hr>' . $d2 . '<hr>';
                $sx .= $BooksOld->harvesting($d2);
                $sx .= metarefresh('', 1);
                $sx = bs(bsc($sx));
                break;
            case 'inport':
                $BooksOld = new \App\Models\Find\BooksOld\Index();
                $sx .= '<hr>';
                $sx .= $BooksOld->inport();
                $sx .= metarefresh(PATH . 'admin/find/harvesting/0', 10);
                break;
            case 'resume':
                $sx .= h('CATALOG', 3);
                $sx .= $this->catalog();
                break;
            case 'getId':
                $sx .= h('CATALOG', 3);
                $sx .= $this->getId($d2);
                break;
            default:
                $menu[PATH . 'admin/find/inport'] = 'Find Import';
                $menu[PATH . '/admin/find/resume'] = 'Find Books Cataloged';
                $sx .= menu($menu);
        }
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    function getId($id)
    {
        $sx = '';
        $BookExpression = new \App\Models\Find\Books\Db\BooksExpression();
        $dt = $BookExpression
            ->Join('books', 'be_title = id_bk')
            ->where('be_rdf', $id)
            ->first();

        if (count($dt) > 0) {
            $ex = $dt['be_rdf'];

            $sx .= h($dt['bk_title'],2);
            $sx .= h('ISBN: '.$dt['be_isbn13'],6);

            $BookManifestation = new \App\Models\Find\Books\Db\BooksManifestation();
            $dd = $BookManifestation->getData($ex);
            foreach($dd as $id=>$line)
                {
                    $sx .= $line['c_class'].': ';
                    $sx .= $line['n_name'];
                    $sx .= '<br>';
                }
        }
        return $sx;
    }

    function catalog()
    {
        $sx = '';
        $BookExpression = new \App\Models\Find\Books\Db\BooksExpression();
        $dt = $BookExpression
            ->Join('books', 'be_title = id_bk')
            ->findAll();
        $sx = '<ol>';
        foreach ($dt as $id => $line) {
            $link = '<a href="' . PATH . '/admin/find/getId/' . $line['be_rdf'] . '">';
            $linka = '</a>';
            $sx .= '<li>';
            $sx .= $link;
            $sx .= $line['be_isbn13'];
            $sx .= $linka;
            $sx .= ' ';
            $sx .= $line['bk_title'];
            $sx .= $line['be_rdf'];
            $sx .= '</li>';
        }
        $sx .= '</ol>';
        return $sx;
    }
}
