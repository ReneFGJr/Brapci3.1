<?php

namespace App\Models\Base\Admin;

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
    protected $allowedFields        = [];

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

    function index($act = '', $subact = '', $id = '')
    {
        switch ($act) {
            case 'v':
                $RDF = new \App\Models\Rdf\RDF();
                $sx = $RDF->index('v', $subact);
                break;
            case 'email':
                $Email = new \App\Models\Functions\Email();
                $sx = $Email->test();
                break;
            case 'socials':
                $Socials = new \App\Models\Socials();
                $sx = $Socials->index($subact, $id);
                break;
            case 'source':
                $Sources = new \App\Models\Base\Sources();
                switch ($subact) {
                    case 'edit':
                        $sx = $Sources->editar($id);
                        break;
                    default:
                        $sx = $Sources->tableview();
                        break;
                }

                break;
            default:
                $sx = '';


                if (isset($_SESSION['user'])) {
                    $user_name = $_SESSION['user'];
                    $sx .= h(lang('brapci.Hello') . ' ' . $user_name . ' !', 2);
                    $COLLECTION = troca(COLLECTION, '/', '');
                    $sx .= '<h1>' . $COLLECTION . '</h1>';
                    switch ($COLLECTION) {
                        case '':
                            break;
                        default:
                            $sx .= $this->benancib_admin();
                            $sx .= $this->menu();
                            break;
                    }
                    $sx = bs(bsc($sx, 12));
                } else {
                    $sx .= metarefresh(PATH . COLLECTION);
                }
        }
        return $sx;
    }

    function benancib_admin()
    {
        $m['#' . 'benancib.admin.statistics'] =  '';
        $m[PATH .  COLLECTION . '/admin/statistics'] =  lang('benancib.statistics_make');
        $sx = menu($m);
        return $sx;
    }

    function menu()
    {
        $m[PATH .  COLLECTION . '/source'] =  lang('brapci.sources');
        $m[PATH .  COLLECTION . '/socials'] =  lang('brapci.Socials');
        $m['#'] =  lang('brapci.Email');
        $m[PATH .  COLLECTION . '/email'] =  lang('brapci.Email');
        $sx = menu($m);
        return $sx;
    }
}