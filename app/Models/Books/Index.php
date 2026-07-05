<?php

namespace App\Models\Books;

use App\Models\Functions\ISBNdb;
use CodeIgniter\Model;
use Config\Database;

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
                                    $dta['b_isbn'] = md5_file("../.tmp/booksubmit/".$dt['bs_arquivo']);

                                    /*************** Criar Livro */
                                    $idc = $Book->create_book($dta);

                                    $dd['bs_rdf'] = $idc;
                                    $BookSubmit->set($dd)->where('id_bs', $dt['id_bs'])->update();
                                    $BookSubmit->chache_status($d2, 7);
                                break;

                                case '12':
                                    //$BookSubmit->chache_status($d2, 2);
                                    $sx = bs(bsc($BookSubmit->import_json($d2), 12));
                                    break;

                                case '13':
                                    $BookSubmit->chache_status($d2, 2);
                                    $sx = bs(bsc($BookSubmit->view($d2), 12));
                                    break;

                                default:
                                    $BookSubmit->chache_status($d2, $d3);
                                    $sx = bs(bsc($BookSubmit->view($d2), 12));
                            }
                        break;
                    break;

                    case 'preview':
                        $sx = $BookSubmit->preview($d2);
                        break;
                    case 'view':
                        $sx = bs(bsc($BookSubmit->view($d2), 12));
                        break;

                    case 'status':
                        $sx = bs(bsc($BookSubmit->list($d2),12,'small'));
                        break;
                    case 'summary':
                        $sx .= $this->book_harvesting_summary();
                        break;
                    case 'items':
                        $sx .= $this->book_harvesting_items($d2);
                        break;
                    case 'catalog':
                        $sx .= $BookSubmit->catalogHarvesting($d2);
                        break;
                    case 'detail':
                        $sx .= $this->book_harvesting_detail($d2);
                        break;
                        default:
                        $sx .= $this->book_harvesting_summary();
                        break;
                }
            return $sx;
        }

    private function book_harvesting_summary()
    {
        $db = Database::connect('default');

        $schemas = ['brapci_books', 'brapci_book'];
        $summary = [];
        $total = 0;
        $tableUsed = '';

        foreach ($schemas as $schema) {
            try {
                $sql = "
                    SELECT `status`, COUNT(*) as qtd
                    FROM {$schema}.book_harvesting
                    GROUP BY `status`
                    ORDER BY `status`
                ";
                $rows = $db->query($sql)->getResultArray();

                $summary = [];
                $total = 0;
                foreach ($rows as $row) {
                    $status = (string)$row['status'];
                    $qtd = (int)$row['qtd'];
                    $summary[$status] = $qtd;
                    $total += $qtd;
                }

                $tableUsed = $schema . '.book_harvesting';
                break;
            } catch (\Throwable $e) {
                continue;
            }
        }

        if ($tableUsed == '') {
            return bsmessage('Nao foi possivel ler brapci_books.book_harvesting ou brapci_book.book_harvesting.', 3);
        }

        $labels = [
            '0' => 'Pendente (coleta capa/DOI)',
            '1' => 'Pendente (coleta capitulos)',
            '2' => 'Processado',
            '9' => 'Erro',
        ];

        $data = [
            'tableUsed' => $tableUsed,
            'summary' => $summary,
            'labels' => $labels,
            'total' => $total,
        ];

        return view('Admin/book_status_summary', $data);
    }

    private function book_harvesting_items($status)
    {
        $db = Database::connect('default');

        $schemas = ['brapci_books', 'brapci_book'];
        $tableUsed = '';
        $items = [];

        foreach ($schemas as $schema) {
            try {
                $sql = "
                    SELECT identifier, title, `status`, DOI, coverage, datestamp, identifiers
                    FROM {$schema}.book_harvesting
                    WHERE `status` = ?
                    ORDER BY datestamp DESC
                ";
                $items = $db->query($sql, [$status])->getResultArray();
                $tableUsed = $schema . '.book_harvesting';
                break;
            } catch (\Throwable $e) {
                continue;
            }
        }

        if ($tableUsed == '') {
            return bsmessage('Nao foi possivel ler brapci_books.book_harvesting ou brapci_book.book_harvesting.', 3);
        }

        $labels = [
            '0' => 'Pendente (coleta capa/DOI)',
            '1' => 'Pendente (coleta capitulos)',
            '2' => 'Processado',
            '9' => 'Erro',
        ];

        $data = [
            'tableUsed' => $tableUsed,
            'status' => (string)$status,
            'statusLabel' => $labels[(string)$status] ?? 'Status customizado',
            'items' => $items,
        ];

        return view('Admin/book_status_items', $data);
    }

    private function book_harvesting_detail($token)
    {
        $db = Database::connect('default');

        $token = (string)$token;
        if ($token == '' || preg_match('/^[a-f0-9]+$/i', $token) !== 1 || (strlen($token) % 2) !== 0) {
            return bsmessage('Identificador invalido para detalhe do registro.', 3);
        }

        $identifier = hex2bin($token);
        if ($identifier === false || trim($identifier) == '') {
            return bsmessage('Identificador invalido para detalhe do registro.', 3);
        }

        $schemas = ['brapci_books', 'brapci_book'];
        $tableUsed = '';
        $item = [];

        foreach ($schemas as $schema) {
            try {
                $sql = "
                    SELECT *
                    FROM {$schema}.book_harvesting
                    WHERE identifier = ?
                    LIMIT 1
                ";
                $row = $db->query($sql, [$identifier])->getRowArray();
                if (is_array($row) && count($row) > 0) {
                    $item = $row;
                    $tableUsed = $schema . '.book_harvesting';
                    break;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        if ($tableUsed == '' || count($item) == 0) {
            return bsmessage('Registro nao encontrado em brapci_books.book_harvesting ou brapci_book.book_harvesting.', 3);
        }

        $data = [
            'tableUsed' => $tableUsed,
            'item' => $item,
        ];

        return view('Admin/book_status_item_detail', $data);
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
                        case 'subject':
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