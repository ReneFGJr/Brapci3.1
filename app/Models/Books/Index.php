<?php

namespace App\Models\Books;

use App\Models\Functions\ISBNdb;
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


    function index($d1='',$d2='',$d3='')
        {
            $sx = '';
            $BookSubmit = new \App\Models\Books\BooksSubmit();
            switch($d1)
                {
                    case 'change':
                        switch($d3)
                            {
                                case '2':
                                    /* Create a Book */
                                    $Book = new \App\Models\Books\Book();
                                    $dt = $BookSubmit->find($d2);
                                    $dta = (array)json_decode($dt['bs_post']);
                                    $idc = $Book->create_book($dta);
                                    $dd['bs_rdf'] = $idc;
                                    $BookSubmit->set($dd)->where('id_bs', $dt['id_bs'])->update();
                                break;
                            }
                        $BookSubmit->chache_status($d2, $d3);
                        $sx = bs(bsc($BookSubmit->view($d2), 12));
                        break;
                    break;
                    case 'view':
                    $sx = bs(bsc($BookSubmit->view($d2), 12));
                    break;

                    case 'status':
                    $sx = bs(bsc($BookSubmit->list($d2),12,'small'));
                    break;
                    default:

                    break;
                }
            return $sx;
        }

    function Xindex($d1 = '', $d2 = '', $d3 = '')
    {
        $sx =  '';
        echo $d1.'='.$d2.'='.$d3;
        $TechinalProceessing = new \App\Models\Books\TechinalProceessing();
        switch ($d1) {
            case 'status':
                $sx .= $TechinalProceessing->show_pt($d2);
                break;
            case 'auto':
                $sx .= $TechinalProceessing->process($d2);
                break;
            case 'autoloader':
                switch ($d2) {
                    case 'ajax':
                        $this->upload();
                        return "";
                }
                $sx .= $this->autoloader();
                break;
        }

        $Socials = new \App\Models\Socials();
        $user = $Socials->getAccess("#ADM");

        if (($user) and ($d2 == '')) {
            $sx .= $TechinalProceessing->resume();
        }
        $sx = bs($sx);
        return $sx;
    }

    function admin($s = '', $a = '')
    {
        $Socials = new \App\Models\Socials();
        $user = $Socials->getAccess("#ADM");
        if ($user == 0) {
            return $Socials->access_denied();
            exit;
        }
        $TechinalProceessing = new \App\Models\Books\TechinalProceessing();
        $sx = '';
        $sx .= $TechinalProceessing->resume();

        switch ($s) {
            case 'api':
                switch($a)
                    {
                        case 'isbndb':
                            $ISBNdb = new \App\Models\ISBN\Isbndb\Index();
                            $sx .= bs(bsc($ISBNdb->form()));
                            break;
                        default:
                        $sx = bs(bsc(h("API - ".$a)));
                    }


                break;
            case 'export':
                $sx .= h(lang('brapci.export'));
                $sx = bs(bsc($sx,12));
                switch($a)
                    {
                        case 'classes':
                            $sx .= '';
                            $Export = new \App\Models\Books\Export();
                            $sx .= $Export->index_classes();
                            break;
                        case 'authors':
                            $sx .= '';
                            $Export = new \App\Models\Books\Export();
                            $sx .= $Export->index_authors();
                            break;
                        case 'authors':
                            $sx .= '';
                            $Export = new \App\Models\Books\Export();
                            $sx .= $Export->index_subject();
                        break;

                        default:
                            $sx .= h($a,5);
                            break;
                    }
                $sx = bs(bsc($sx,12));
                break;

            case 'auto':
                $TechinalProceessing = new \App\Models\Books\TechinalProceessing();
                $sx .= $TechinalProceessing->process($a);
                break;

            case 'status':
                $TechinalProceessing = new \App\Models\Books\TechinalProceessing();
                $sx .= $TechinalProceessing->show_pt($a, $s);
                break;

            default:
                $sa = '';
                    $menu = array();
                    $menu[PATH . COLLECTION . '/admin/export'] = '<b>' . lang('brapci.export') . '</b>';
                    $menu[PATH . COLLECTION . '/admin/export/classes'] = '<ul><li>' . lang('brapci.export') . ' ' . lang('brapci.classes') . '</li></ul>';
                    $menu[PATH . COLLECTION . '/admin/export/authors'] = '<ul><li>' . lang('brapci.export') . ' ' . lang('brapci.authors') . '</li></ul>';
                    $menu[PATH . COLLECTION . '/admin/export/subjects'] = '<ul><li>' . lang('brapci.export') . ' ' . lang('brapci.subjects') . '</li></ul>';
                    $menu[PATH . COLLECTION . '/admin/export/books'] = '<ul><li>' . lang('brapci.export') . ' ' . lang('brapci.books') . '</li></ul>';
                    $menu['#API'] = 'A P I';
                    $menu[PATH . COLLECTION . '/admin/api/isbndb'] = '<ul><li>' . lang('brapci.api') . ' ' . lang('brapci.isbndb') . '</li></ul>';

                    $sa .= menu($menu);
                    $sx .= bs(bsc($sa, 12));
        }

        return $sx;
    }

    function upload()
    {
        $TechinalProceessing = new \App\Models\Books\TechinalProceessing();

        if (isset($_FILES['file']['tmp_name'])) {
            $tmp = $_FILES['file']['tmp_name'];
            $file = $_FILES['file']['name'];

            /******************* Extension */
            $ext = explode('.', $file);
            $ext = strtolower($ext[count($ext) - 1]);
            switch ($ext) {
                case 'pdf':
                    echo $TechinalProceessing->upload($file, $tmp);
                    exit;
                    break;
                default:
                    echo bsmessage(lang('book.format_invalide_autodeosit - ' . $file), 3);
                    break;
            }
        } else {
            echo '<pre>--------------------------------------------';
            echo 'erro de upload';
            print_r($_FILES);
        }
    }

}