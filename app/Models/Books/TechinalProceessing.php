<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class TechinalProceessing extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_books.technical_processing';
    protected $primaryKey       = 'id_tp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_tp', 'tp_checksun', 'tp_up',
        'tp_file', 'tp_user', 'tp_status',
        'tp_created', 'tp_ip'
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

    function process($a)
    {
        /******************************************* Action */
        $act = get('act');
        if ($act != '') {
            $data['tp_status'] = $act;
            $this->set($data)->where('id_tp', $a)->update();
        }
        $dt = $this
            ->join('brapci_books.books_submit', 'bs_arquivo = tp_checksun')
            ->find($a);

        /****************************************** Display */
        $sx = '';
        $sx .= h($dt['tp_file']);
        $sx .= '<p>' . $dt['tp_created'] . '</p>';

        $catlog = '';
        switch ($dt['tp_status']) {
            case 0:
                $catlog .= h(lang('book.status_0'), 4);
                /************** Buttons */
                $catlog .= $this->btn_archive($a);
                $catlog .= ' ';
                $catlog .= $this->btn_aproved($a);
                $catlog .= ' ';
                $catlog .= $this->btn_inferred($a);

                $catlog .= $this->dados_submit($dt);

                break;
            case 1:
                $catlog .= h(lang('book.status_1'), 4);
                /************** Buttons */
                $catlog .= $this->btn_archive($a);
                $catlog .= ' ';
                $catlog .= $this->btn_aproved($a);
                $catlog .= ' ';
                $catlog .= $this->btn_supply($a);

                $THB = new \App\Models\Books\TechinalProceessingBook();
                $THB->create($a);
                $catlog .= $THB->edit($a);
                break;

            case 2:
                $THB = new \App\Models\Books\TechinalProceessingBook();
                $catlog .= $this->btn_catalog($a);
                $catlog .= $THB->createRDF($a);
                break;

            case 3:
                $THB = new \App\Models\Books\TechinalProceessingBook();
                //$catlog .= $THB->editRDF($a);
                echo '===>'.$a;
                $db = $THB->where('b_source', $a)->findAll();
                $idr = $db[0]['b_rdf'];

                $rdfurl = URL.'/rdf/form/editRDF/'. $idr;
                $catlog .= $this->btn_supply($a);
                $catlog .= $this->btn_publish($a);
                echo '<br>RDF: '.$rdfurl;
                $catlog .= '<iframe src="' . $rdfurl . '" style="width: 100%; height:600px;"></iframe>';
                break;

            case 6:
                $catlog = h(lang('book.status_6'), 4);
                /************** Buttons */
                $catlog .= $this->btn_aproved($a);
                break;
        }

        $sx .= bsc($catlog, 5);
        $screen = URL . '/' . $dt['tp_up'];
        echo $screen;
        $iframe = '<iframe src="' . $screen . '" style="width: 100%; height:600px;"></iframe>';
        $sx .= bsc($iframe, 7);
        $sx = bs($sx);
        return $sx;
    }

    function dados_submit($dt)
        {
            $data = $dt['bs_post'];
            $data = json_decode($data);
            $data = (array)$data;

            $sx = '';
            $sx .= 'Autor: <b>'.$data['b_autor']. '</b><br>';
            $sx .= 'e-mail:' . $data['b_email'] . '<br>';
            $sx .= 'Título:' . $data['b_titulo'] . '<br>';
            $sx .= 'ISBN:' . $data['b_isbn'] . '<br>';
            $sx .= 'Licença:' . $data['b_licenca'] . '<br>';
            return $sx;

        }

    /******************************************** BTNS */
    function btn_inferred($s)
    {
        $sx = '<a href="' . URL . '/admin/book/auto/' . $s . '?act=5" title="' . lang('book.send_to') . ' ' . lang('book.status_5') . ' "class="btn btn-ouline-primary p-2">';
        $sx .= bsicone('trash', 32);
        $sx .= 'Não aceitar';
        $sx .= '</a>';
        return '<li>' . $sx . '</li>';
    }
    function btn_supply($s)
    {
        $sx = '<a href="' . URL . '/admin/book/auto/' . $s . '?act=2" title="' . lang('book.send_to') . ' ' . lang('book.status_2') . ' "class="btn btn-ouline-primary p-2">';
        $sx .= bsicone('circle-2', 32);
        $sx .= 'Supply';
        $sx .= '</a>';
        return '<li>' . $sx . '</li>';
    }
    function btn_publish($s)
    {
        $sx = '<a href="' . URL . '/admin/book/auto/' . $s . '?act=4" title="' . lang('book.send_to') . ' ' . lang('book.status_4') . ' "class="btn btn-ouline-primary p-2">';
        $sx .= bsicone('reload', 32);
        $sx .= 'Publicar';
        $sx .= '</a>';
        return '<li>'.$sx.'</li>';
    }
    function btn_archive($s)
    {
        $sx = '<a href="' . URL . '/admin/book/auto/' . $s . '?act=4" title="' . lang('book.send_to') . ' ' . lang('book.status_4') . ' "class="btn btn-ouline-primary p-2">';
        $sx .= bsicone('trash', 32);
        $sx .= ' Arquivar';
        $sx .= '</a>';
        return '<li>' . $sx . '</li>';
    }

    function btn_aproved($s)
    {
        $sx = '<a href="' . URL . '/admin/book/auto/' . $s . '?act=1" class="btn btn-ouline-primary p-2" title="' . lang('book.send_to') . ' ' . lang('book.status_1') . '">';
        $sx .= bsicone('upload', 32);
        $sx .= ' Aprovado';
        $sx .= '</a>';
        return '<li>' . $sx . '</li>';
    }

    function btn_catalog($s)
    {
        $sx = '<a href="' . URL . '/admin/book/auto/' . $s . '?act=3" class="btn btn-ouline-primary p-2" title="' . lang('book.send_to') . ' ' . lang('book.status_1') . '">';
        $sx .= bsicone('upload', 32);
        $sx .= 'Aprovar';
        $sx .= '</a>';
        return '<li>' . $sx . '</li>';
    }


    function show_pt($a)
    {
        $sx = '';
        $dt = $this->where('tp_status', $a)->findAll();
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $link = '<a href="' . PATH . '/admin/book/auto/' . $line['id_tp'] . '">';
            $linka = '</a>';
            $sa = h('<b>' . $link . $line['tp_file'] . $linka . '</b>', 4);
            $sx .= '<p>' . lang('books.uploaded') . ': ' . $line['tp_created'];
            $sx .= ' (' . $line['tp_ip'] . ')</p>';
            $sx .= bsc($sa, 12, ' bordered');
        }
        $sx = bs($sx);
        return $sx;
    }

    function resume()
    {
        $sx = '';
        $dt = $this
            ->select('count(*) as total, tp_status')
            ->groupby('tp_status')
            ->FindAll();
        $st = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        /***************************************************** */
        foreach ($dt as $line) {
            $status = $line['tp_status'];
            $total = $line['total'];
            $st[$status] = $total;
        }

        for ($r = 0; $r <= 5; $r++) {
            $link = '<a href="' . PATH . 'admin/book/status/' . $r . '" style="color: black;">';
            $linka = '</a>';
            $label = $link . lang('book.status_' . $r) . $linka . '<br>';
            $sx .= bsc($label . '<b style="font-size: 40px">' . $link . $st[$r] . $linka . '</b>', 2, 'text-center');
        }
        $sx = bs($sx);
        return $sx;
    }

    function upload($file, $tmp)
    {
        $RSP = [];
        dircheck('../.tmp/');
        dircheck('../.tmp/books');
        $sx = '';

        /******************* Extension */
        $user = 0;
        $ext = explode('.', $file);
        $ext = strtolower($ext[count($ext) - 1]);

        $data['tp_checksun'] = md5_file($tmp);
        $data['tp_file'] = $file;
        $data['tp_user'] = $user;
        $data['tp_ip'] = ip();
        $data['tp_status'] = 0;
        $dest = '../.tmp/books/' . $data['tp_checksun'] . '.' . $ext;
        $data['tp_up'] = $dest;


        /************************* File Description */
        $file_description = '<tt>' . $file . '</tt>';
        $file_description .= ' <tt>' .
            number_format(filesize($tmp) / 1024 / 1024, 1) . 'Mbyte</tt>';

        $dt = $this->where('tp_checksun', $data['tp_checksun'])->first();

        if ($dt == '') {
            move_uploaded_file($tmp, $dest);
            $idf = $this->set($data)->insert();
            $RSP['status'] = '200';
            $RSP['idf'] = $idf;
            $RSP['message'] = lang('book.sucess_autodeosit') . ' - ' . lang('sucess_autodeosit_info');
        } else {
            $RSP['status'] = '101';
            $RSP['idf'] = $dt['id_tp'];
            $RSP['message'] = lang('book.already_autodeosit') . ' - ' .
                lang('already_autodeosit_info').' - '.$file_description;
        }
        return($RSP);
    }
}