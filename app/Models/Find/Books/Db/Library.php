<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Library extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'library';
    protected $primaryKey       = 'id_lb';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lb', 'lb_name', 'lb_description',
        'lb_address', 'lb_city', 'lb_logo',
        'lb_logo_mini'
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
    var $library = [];

    function index($d1,$d2)
        {
            $sx = '';
            switch($d1)
                {
                    case 'view':
                        $sx .= $this->view($d2);
                        break;
                    case 'edit':
                    break;
                    default:
                        $sx = $this->row();
                        break;
                }
            return $sx;
        }

        function view($id)
            {
                $dt = $this->find($id);
                pre($dt);
            }

        function row()
            {
                $dt = $this->orderby('lb_name')->findAll();
                $sx = '<ul>';
                foreach($dt as $id=>$line)
                    {
                        $sx .= '<li>'.
                            '<a href="'.URL.'/admin/find/library/view/'.$line['id_lb'].'">' .
                            '<img src="'.URL.'/'.$line['lb_logo'].'" height="40px">' .
                            $line['lb_name'].
                            '</a>'.
                            '</li>';
                    }
                $sx .= '</ul>';
                return $sx;

            }

    function libraries()
        {
            $dt = $this->findAll();

            foreach($dt as $id=>$line)
                {
                    $img =
                    $dt[$id]['lb_logo'] = URL.'/'.$dt[$id]['lb_logo'];
                    $dt[$id]['lb_logo_mini'] = URL . '/' . $dt[$id]['lb_logo_mini'];
                }
            return($dt);
        }

    function checkLibrary($RSP)
    {
        $library = get("library");
        if ($library == '') {
            $RSP['status'] = '202';
            $RSP['message'] = 'Biblioteca não foi informada';
            return $RSP;
        } else {
            if ($this->checkapi($library)) {
                $this->getLibrary($library);
                $RSP['status'] = '200';
            } else {
                $RSP['status'] = '202';
                $RSP['message'] = 'Falha na localização da Biblioteca';
            }
        }
        return $RSP;
    }

    function getLibrary($l)
        {
            $this->library =$this->find($l);
            return true;
        }

    function checkapi($l)
        {
            $dt=$this->where('id_lb',$l)->first();;
            if ($dt=='')
                {
                    return false;
                } else {
                    return true;
                }
        }
}
