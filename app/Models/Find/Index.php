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
        $sx = '';
        $b = [];
        $b['Admin'] = PATH . '/admin/';
        $b['Find']= PATH.'/admin/find/';
        $sx = breadcrumbs($b);
        switch ($d1) {
            case 'library':
                $sx .= $this->library($d2,$d3);
                break;
            case 'clear':
                $sx .= $this->clear_catalog();
                break;
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
            case 'form':
                $sx .= '<hr>';
                $sx .= $this->form();
                break;
            case 'resume':
                $sx .= h('CATALOG', 3);
                $sx .= $this->catalog();
                break;
            case 'getId':
                $sx .= h('CATALOG', 3);
                $sx .= $this->getId($d2);
                break;
            case 'elastic':
                $ES = new \App\Models\Find\Books\Db\ElasticSearch();
                $sx .= $ES->reindex();
                break;
            default:
                $menu[PATH . 'admin/find/library'] = 'Bibliotecas';
                $menu[PATH . 'admin/find/form'] = 'Form Imput ISBN';
                $menu[PATH . 'admin/find/elastic'] = 'Export ElasticSearch';
                $menu[PATH . 'admin/find/inport'] = 'Find Import';
                $menu[PATH . '/admin/find/resume'] = 'Find Books Cataloged - Trunk';
                $menu[PATH . '/admin/find/clear'] = '** Clear Catalog **';
                $menu[PATH . '/admin/find/angular'] = 'Find Simulate Angular';
                $sx .= menu($menu);
        }
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    function library($d1,$d2)
        {
            $Library = new \App\Models\Find\Books\Db\Library();
            return $Library->index($d1,$d2);
        }

    function clear_catalog()
        {
            $sx = '';
            $sqlDB = ["books","rdf_data", "rdf_name", "rdf_concept", "rdf_concept", "books_expression"];
            foreach($sqlDB as $id=>$sql)
                {
                    $sql = 'TRUNCATE find.'.$sql;
                    $this->db->query($sql);
                    $sx .= '<hr>';
                    $sx .= $sql;
                }
            $sx = bs(bsc($sx,12));
            return $sx;
        }

    function form()
        {
            $sx = '<label>Informe o número do ISBN</label>';
            $sx .= '<form>';
            $sx .= form_input('isbn',get("isbn"),['class'=>'form-control border-secondary border','style'=>'width: 250px;']);
            $sx .= '<br>';
            $sx .= form_submit('act','Incluir >>>');
            $sx .= '</form>';

            $isbn = get('isbn');
            if ($isbn != '')
                {
                    $url = URL.'/api/isbn/'.$isbn;
                    $dt = file_get_contents($url);
                    $dt = (array)json_decode($dt);

                    if ($dt['valid'] == 1)
                        {
                            $url = URL . '/api/find/isbn/' . $isbn.'/add';
                            $url .= '?library=1';
                            $url .= '&apikey=ff63a314d1ddd425517550f446e4175e';
                            $sx .=  anchor($url);
                            $sx .= '<hr>';
                            $dta = file_get_contents($url);
                            $dta = (array)json_decode($dta);
                            pre($dta,false);
                        }
                        else {
                            $sx .= bsmessage("ERRO NO Número do ISBN ".$isbn,3);
                        }
                }
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

        if ($dt != '') {
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
