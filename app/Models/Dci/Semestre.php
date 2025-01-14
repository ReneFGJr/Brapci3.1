<?php

namespace App\Models\Dci;

use CodeIgniter\Model;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;

class Semestre extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'semestre';
    protected $primaryKey       = 'id_sem';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_sem','sem_ano','sem_descricao','sem_semestre'
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

    function index($act='', $d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '')
    {
        $sx = '';

        $mn = [];
        $mn['Departamento'] = base_url('/dci/');
        $mn['Semestre'] = base_url('/dci/semestre/');
        $return_btn = '<br><a href="' . $mn['Departamento'] . '" class="btn btn-outline-secondary mt-2">voltar</a>';
        $sx .= breadcrumbs($mn);

        switch ($d1) {
            case 'edit':
                $sx .= $this->edit($d2);
                break;
            case 'mark':
                $sx .= $this->mark($d2, $d3, $d4, $d5);
                $sx .= $return_btn;
                break;
            case 'view':
                $sx .= $this->viewid($d2, $d3, $d4);
                break;
            default:
                $sx .= $this->list($d2);
                $sx .= $return_btn;
                break;
        }
        return bs(bsc($sx));
    }

    public function getSemestre($tp='')
    {
        $flg = 'semestre';
        switch($tp)
            {
                case 'ID':
                    $flg = 'semestreID';
                    break;
            }
        if (isset($_COOKIE[$flg]))
            {
                $txt = $_COOKIE[$flg];
                return $_COOKIE[$flg];
            }
        return '';
    }

    public function mark($id, $d2, $d3, $d4)
    {
        // Busca o registro no banco de dados
        $dt = $this->find($id);

        if (!$dt) {
            return 'Erro: Semestre não encontrado.';
        }

        // Define cookies
        $cookie = array(
            'name'   => 'semestreID',
            'value'  => $id,
            'expire' => 60*60*24*100,
            'secure' => FALSE
        );
        set_cookie($cookie);
        $cookie = array(
            'name'   => 'semestre',
            'value'  => $dt['sem_descricao'],
            'expire' => 60 * 60 * 24 * 100,
            'secure' => FALSE
        );
        set_cookie($cookie);
        // Retorna mensagem de sucesso
        return 'Semestre definido como ' . htmlspecialchars($dt['sem_descricao']);
    }

    function viewid($id)
    {
        $sx = '';
        $dt = $this
            ->find($id);

        $sx .= view('/dci/semestre',$dt);

        $sx = bs($sx);

        return $sx;
    }

    function header($dt)
    {
        $sx = '';
        $sx .= h($dt['sem_descricao'], 2);
        return $sx;
    }


    function list($curso = 0)
    {
        $sem = 1;
        $dt = $this
            ->orderby('sem_descricao DESC')
            ->findAll();

        $sx = bsc('Selecione um Ano',12);

        foreach($dt as $id=>$line)
            {
                $txt = '<a href="'.base_url('/dci/semestre/mark/'.$line['id_sem']).'">'.$line['sem_descricao']. '</a>';

                $sx .= bsc($txt,2,'text-center border-secondary border m-2 pt-3 pb-3 rounded');
            }

        $sx = bs($sx);
        return $sx;
    }
}
