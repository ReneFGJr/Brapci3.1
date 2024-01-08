<?php

namespace App\Models\WP;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'about';
    protected $primaryKey       = 'id_a';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_a', 'a_page', 'a_texto',
        'a_order'
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

    function index($d1, $d2, $d3, $d4)
    {
        switch ($d2) {
            case 'about':
                $sx = $this->edit($d2, $d3);
                break;
            default:
                $sx = $this->menu();
                break;
        }

        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    function api($page)
        {
            $lang = 'pt';
            $dt = $this
                ->where('a_page',$page)
                ->where('a_lang', $lang)
                ->orderby('a_order')
                ->findAll();
            $ln = [];
            foreach($dt as $idx=>$line)
                {
                    $lnl = ['row'=>$line['a_texto'],'lang'=>$line['a_lang']];
                    array_push($ln,$lnl);
                }
            return $ln;
        }

    function edit($d2, $d3)
    {
        /*************************** */
        $ID = get("ID");
        $TXT = get("text");
        $act = get("action");
        if (($ID != '') and ($TXT != '')) {
            if ($ID == '0') {
                $d['a_page'] = $d2;
                $d['a_texto'] = $TXT;
                $d['a_order'] = $this->count_all_results($d2) + 1;
                $this->set($d)->insert();
            } else {
                $d['a_texto'] = $TXT;
                $this->set($d)->where('id_a',$ID)->update();
            }
            return metarefresh(PATH . '/admin/page/edit/' . $d2);
            exit;
        }

        /****************** Actions */
        switch ($d3) {
            case 'up':
                $this->moveUpDown($d2, get("id"),-1);
                return metarefresh(PATH . '/admin/page/edit/' . $d2);
                exit;
            case 'down':
                $this->moveUpDown($d2, get("id"),1);
                return metarefresh(PATH . '/admin/page/edit/' . $d2);
                exit;
            case 'edit':
                $id = get("id");
                $dt = $this->find($id);
                $_POST['ID'] = $dt['id_a'];
                $_POST['text'] = $dt['a_texto'];
                break;
        }

        $sx = $this->show($d2, true);
        return $sx;
    }

    function moveUpDown($d2, $id,$dec=-1)
    {
        $update = [];
        $dt = $this->where('a_page', $d2)->orderby('a_order')->findAll();
        foreach ($dt as $idx => $line) {
            if ($line['id_a'] == $id) {
                $da = [];
                $da['id'] = $line['id_a'];
                $da['order'] = $line['a_order'] +$dec;
                $order = $line['a_order'] + $dec;
                array_push($update,$da);
            }
        }

        foreach ($dt as $idx => $line) {
            if ($line['a_order'] == $order) {
                $da = [];
                $da['id'] = $line['id_a'];
                $da['order'] = $line['a_order']+ ($dec * -1);
                array_push($update, $da);
            }
        }

        foreach($update as $id=>$line)
            {
                $dd['a_order'] = $line['order'];
                $this->set($dd)->where('id_a',$line['id'])->update();
            }
        return "";
    }
    function count_all_results($d2)
    {
        $dt = $this->select('count(*) as total')
            ->where('a_page', $d2)
            ->first();
        return $dt['total'];
    }
    function show($d2, $edit = false)
    {
        $sx = '<script src="https://cdn.ckeditor.com/4.12.1/standard-all/ckeditor.js"></script>';
        $sx .= h($d2);
        $sx .= '<hr>';
        $dt = $this->where('a_page', $d2)->orderBy('a_order')->findAll();
        $l = 0;
        $lt = count($dt) - 1;
        foreach ($dt as $id => $line) {
            if ($edit != false) {
                $sx .= '<br>';
                $link = '<a href="' . PATH . '/admin/page/edit/' . $d2 . '/edit?id=' . $line['id_a'] . '">';
                $linka = '</a>';
                $sx .= $link.bsicone('edit', 24, 'me-2').$linka;

                $link = '<a href="' . PATH . '/admin/page/edit/' . $d2 . '/delete?id=' . $line['id_a'] . '" onclick="confirm(\'Can you do it?\') == True">';
                $sx .= $link.bsicone('trash', 24, 'me-2').$linka;

                if ($l > 0) {
                    $link = '<a href="' . PATH . '/admin/page/edit/' . $d2 . '/up?id=' . $line['id_a'] . '">';
                    $linka = '</a>';
                    $sx .= $link . bsicone('up', 24, 'me-2') . $linka;
                }
                if ($l < $lt) {
                    $link = '<a href="' . PATH . '/admin/page/edit/' . $d2 . '/down?id=' . $line['id_a'] . '">';
                    $linka = '</a>';
                    $sx .= $link . bsicone('down', 24, 'me-2').$linka;
                }
                $sx .= '<p>' . $line['a_texto'] . '</p>';
                $sx .= '<hr>';
                $l++;
            }
        }
        $sx .= bsicone('plus', 24, 'me-2');
        $sx .= form_open(PATH . 'admin/page/edit/' . $d2);
        $sx .= form_textarea('text', get("text"), ['rows' => 10, 'id' => 'editor2', 'name' => 'editor2', 'class' => 'ckeditor mt-3 form-control full border border-secondary']);
        $sx .= form_submit('action', lang('brapci.save'), ['class' => 'mt-2 btn btn-outline-primary']);
        $sx .= form_hidden('ID', get("ID"));
        $sx .= form_close();

        $sx .= "<script>
                    CKEDITOR.replace('editor2', {
                        height: 260,
                        /* Default CKEditor styles are included as well to avoid copying default styles. */
                        contentsCss: [
                        'http://cdn.ckeditor.com/4.12.1/full-all/contents.css',
                        'https://ckeditor.com/docs/vendors/4.12.1/ckeditor/assets/css/classic.css'
                        ]
                    });
                    </script>";
        return $sx;
    }
    function menu()
    {
        $m = [];
        $m['#Conteúdo'] = '#';
        $m[PATH . '/admin/page/edit/about'] = 'Pagina [ABOUT]';
        return menu($m);
    }
}
