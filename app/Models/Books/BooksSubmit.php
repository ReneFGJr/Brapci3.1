<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class BooksSubmit extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'books_submit';
    protected $primaryKey       = 'id_bs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bs', 'bs_post', 'bs_status',
        'bs_title', 'b_isbn', 'bs_rdf'
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

    function view($id)
        {
            $sx = '';
            $dt = $this->find($id);

            if ($dt['bs_status'] == 2)
                {
                    if ($dt['bs_rdf'] > 0)
                        {
                            $url = PATH . 'a/' . $dt['bs_rdf'];
                            echo metarefresh($url,0);
                            exit;
                        }
                }

            $sx .= bsc($this->action($dt),12);

            if ($dt != [])
                {
                    $js = (array)json_decode($dt['bs_post']);

                    foreach($js as $key=>$value)
                        {
                            $sx .= bsc(msg('brapci.'.$key), 3, 'small mt-2');
                            $sx .= bsc($value.'&nbsp;', 9,'border-top border-secondary');
                        }
                } else {
                    $sx .= 'Registro não localizado '.$id;
                }
            $sx = bsc($sx,5);
            $iframe = $this->show_pdf($dt);
            $sx .= bsc($iframe,7);
            return bs($sx);
        }

    function chache_status($id,$sta)
        {
            $dd['bs_status'] = $sta;
            $this->set($dd)->where('id_bs',$id)->update();
            return True;
        }

    function action($dt)
        {
            $sx = '';
            $sta = $dt['bs_status'];
            $id = $dt['id_bs'];
            $btn = '<a href="'.PATH.'admin/book/status/0" class="btn btn-outline-warning ms-2">' . lang('brapci.return') . '</a>';
            switch($sta)
                {
                    case '0':
                        $sx .= '<a href="'.PATH.'admin/book/change/'.$id.'/1" class="btn btn-outline-primary">'.lang('brapci.accept').'</a>';
                        $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/9"  class="btn btn-outline-danger ms-2">' . lang('brapci.reject') . '</btn>';
                        $sx .= $btn;
                        break;
                    case '1':
                        $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/2" class="btn btn-outline-primary">' . lang('brapci.create_book') . '</a>';
                        $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/9"  class="btn btn-outline-danger ms-2">' . lang('brapci.reject') . '</btn>';
                        $sx .= $btn;
                    default:
                        $sx .= 'No actions';
                    break;
                }
            return $sx;
        }

    function show_pdf($dt)
        {
            $html = PATH.'.tmp/books/55d80388ee1992f0b77491fc0997812d.pdf';
            $sx = $html.'
            <iframe src="'.$html.'" style="width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;">
                Your browser doesnt support iframes
            </iframe>';
            return $sx;
        }

    function list($sta)
        {
            $sx = '';
            $dt = $this
                ->where('bs_status',$sta)
                ->findAll();
            foreach($dt as $id=>$line)
                {
                    $link = '<a href="'.PATH.'admin/book/view/'.$line['id_bs'].'">';
                    $linka = '</a>';
                    $js = (array)$line['bs_post'];
                    $sx .= '<li>';
                    $js = $js[0];
                    $js = (array)json_decode($js);

                    if ($js['b_titulo'])
                        {
                            $sx .= '<b>';
                            $sx .= $link . (string)$js['b_titulo'].$linka;
                            $sx .= '<br><i>' . $js['b_autor'] . '</i>';
                            $sx .= '</b>';
                        } else {
                            $sx .= '<b>';
                            $sx .= $link . 'Não informado' . $linka;
                            $sx .= '<br><i>' . 'sem autoria registrada' . '</i>';
                            $sx .= '</b>';
                        }

                    $sx .= '</li>';
                }
            return $sx;
        }

    function resume()
        {
            $sx = '';
            $dt = $this
                ->select("count(*) as total, bs_status")
                ->where('bs_status',0)
                ->groupBy('bs_status')
                ->orderBy('bs_status')
                ->findAll();
            foreach($dt as $id=>$line)
                {
                    $link = '<a class="text-danger" href="'.PATH.'admin/book/status/'.$line['bs_status'].'">';
                    $linka = '</a>';
                    $sx .= '<li class="text-danger" style="font-size: 0.7em;">';
                    $sx .= $link.lang('brapci.book_status_'.$line['bs_status']).$linka;
                    $sx .= ' <b>';
                    $sx .= '('.$line['total'].')';
                    $sx .= '</b>';
                    $sx .= '</li>';
                }
            if ($sx != '')
                {
                    $sx = '<b>Livros submetidos</b>'.$sx;
                }
            return $sx;
        }

    function register()
        {
            $PS = array_merge($_POST, $_GET);
            $PSj = json_encode($PS);
            $dt = [];
            $dt['bs_post'] = $PSj;
            if (isset($PS['b_titulo']))
                {
                    $dt['bs_title'] = $PS['b_titulo'];
                    $dt['b_isbn'] = $PS['b_isbn'];
                }
            $this->set($dt)->insert();
        }
}
