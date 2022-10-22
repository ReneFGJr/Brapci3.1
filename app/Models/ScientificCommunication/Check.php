<?php

namespace App\Models\ScientificCommunication;

use CodeIgniter\Model;

class Check extends Model
{
    protected $DBGroup          = 'pgcd';
    protected $table            = 'scientific_opinion_check';
    protected $primaryKey       = 'id_chk';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_chk', 'chk_name', 'chk_avaliation', 'chk_description', 'chk_type', 'chk_order'
    ];
    protected $typeFields    = [
        'hidden', 'string', 'text', 'text', 'text', '[1-99]'
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


    function check($type)
        {

        }

    function index($d1,$d2,$d3,$d4)
        {
            $sx = '';
            $this->path = base_url(PATH.COLLECTION.'/checklist');
            $this->path_back = base_url(PATH . COLLECTION . '/checklist/table');
            switch($d1)
                {
                    case 'check':
                        $sx = $this->checking($d2);
                        break;
                    case 'edit':
                        $this->id = $d2;
                        $sx = form($this);
                        break;
                    case 'table':
                        $sx = tableview($this);
                        break;

                    default:
                    $menu = array();
                    $menu['#Checklist'] = lang('peer.checklist');
                    $menu[PATH . COLLECTION . '/checklist/check'] = lang('peer.checklist_work');

                    $menu['#Admin'] = lang('peer.checklist_admin');
                    $menu[PATH.COLLECTION.'/checklist/table'] = lang('peer.checklist_fields');

                    $sx = bsc(menu($menu),12);
                }
            return bs($sx);
        }

        function checking($id)
            {
                $sx = '';
                $sx .= bsc(h(lang('peer.Checklist')),12);
                $dt = $this->orderby('chk_order')->findAll();
                $sx .= bsc(form_open(),12);
                $sf = '';
                for ($r=0;$r < count($dt);$r++)
                    {
                        $line = $dt[$r];
                        $txt = trim($line['chk_avaliation']);
                        $checked = '';
                        $vlr = get('chk_' . $line['id_chk']);
                        $sa = '';
                        $sb = '';
                        if ($vlr == '1')
                            {
                                $sa .= form_checkbox(array('checked' => $vlr, 'name' => 'chk_' . $line['id_chk'], 'id' => 'chk_' . $line['id_chk'], 'value' => '1'));
                            } else {
                                $sa .= form_checkbox(array('name' => 'chk_' . $line['id_chk'], 'id' => 'chk_' . $line['id_chk'], 'value' => '1'));
                            }

                        $sa .= '<b>'.$line['chk_name']. '</b>';

                        if ($txt != '') { $sb .= $txt; }
                        $sf .= bsc($sa,4);
                        $sf .= bsc($sb,8);
                        $sf .= bsc('<hr>',12);
                    }
                $sf .= bsc('<br>',12);
                $sx .= $sf;
                $sx .= bsc(form_submit(array('name'=>'submit','value'=>lang('peer.save'))),12);
                $sx .= form_close();


                if (get("submit") != '')
                    {
                        for($r=0;$r < count($dt);$r++)
                            {
                                $line = $dt[$r];
                                $chk = get("chk_".$line['id_chk']);
                                if ($chk == '1')
                                    {
                                        $txt = $line['chk_description'];
                                        $txt = troca($txt,chr(13),'<br>');
                                        $sx .= h('==== '.$line['chk_name'].' ====',4);
                                        $sx .= '<p>'.$txt.'</p>'.cr();
                                    }
                            }
                    }
                return $sx;
            }
}
