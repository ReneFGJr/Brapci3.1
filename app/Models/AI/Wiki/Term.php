<?php

namespace App\Models\AI\Wiki;

use CodeIgniter\Model;

class Term extends Model
{
    protected $DBGroup          = 'elastic';
    protected $table            = 'wiki';
    protected $primaryKey       = 'id_t';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_t', 'name', 'name_asc','lang',
        'use', 'uri', 'definition', 'status',
        'classes', 'sources', 'updated_at',
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

    var $btn_edit = '';

    function index()
        {
            $sx = '';
            $dt = $this->orderBy('updated_at desc')->first();
            if (empty($dt))
                {
                    $sx = bsmessage('brapci.no_information',1);
                } else {
                    $sx .= $this->show($dt);
                    /* Gera botão de edição */
                    $this->btn_edit = '';
                    $this->btn_edit($dt);
                    $this->btn_wiki($dt);
                }
            return $sx;
        }

    function register($dt)
        {
            $dt['name_asc'] = ascii(mb_strtolower($dt['name']));
            $dta = $this->where('name_asc',$dt['name_asc'])->first();

            if (empty($dta))
                {
                   $this->set($dt)->insert();
                   return lang('brapci.insered');
                } else {
                   return lang('brapci.update');
                }
        }

    function btn_edit($dt)
        {
            $link = '<a href="'.PATH.'/ai/wiki/edit/'.$dt['id_t'].'">';
            $linka = '</a>';
            $sx = $link.bsicone('edit').$link;
            $this->btn_edit .= $sx;
            return $sx;
        }

    function btn_wiki($dt)
    {
        $link = '<a href="https://pt.wikipedia.org/wiki/'.$dt['name'].'" target="_blank">';
        $linka = '</a>';
        $sx = $link . bsicone('edit') . $link;
        $this->btn_edit .= $sx;
        return $sx;
    }

    function catalog()
        {
            $sx = '';
            $dt = $this->orderby('name_asc')->findAll();
            $sx = '<ul>';
            foreach($dt as $line)
                {
                    $sx .= '<li>'.$this->show_term($line).'</li>';
                }
            $sx .= '</ul>';
            return $sx;
        }

    function show_term($dt)
        {
            $sx = '';
            $link = '<a href="'.PATH.'/c/'.$dt['id_t'].'#'.$dt['name'].'">';
            $linka = '</a>';
            $sx .= $link.$dt['name'].$linka;
            return $sx;
        }

    function showId($id)
        {
            $dt = $this->find($id);
            return $this->show($dt);
        }
    function show($dt)
        {
            $sx = '';

            $sx .= h($dt['name'],4);
            $sx .= '<hr>';

            return $sx;
        }

    function Import()
        {
            $Language = new \App\Models\Ai\NLP\Language();
            $terms = get('terms');
            $check = !empty(get("test"));
            $no_word = !empty(get("no_words"));
            $sx = form_open();
            $sx .= form_textarea(array('name'=>'terms','class'=>'form_control','value'=>$terms,'style'=>'width: 100%;'));

            $sx .= '<br>';
            $sx .= form_checkbox(array('name' => 'no_words', 'value' => 1, 'checked' => $no_word)) . ' ' . lang('brapci.terms');
            $sx .= '<br>';
            $sx .= form_checkbox(array('name'=>'test','value'=>1,'checked'=>$check)).' '.lang('brapci.test');
            $sx .= '<br>';
            $sx .= form_submit('action', lang('brapci.save'));
            $sx .= form_close();

            if (!empty($terms))
                {
                    $sx .= '<hr>';
                    $t = troca($terms,array(chr(13),chr(10)),';');
                    if ($no_word != 1)
                        {
                            $t = troca($t, ' ', ';');
                        }
                    $t = troca($t, '?', ';');
                    $t = troca($t, '!', ';');
                    $t = troca($t, ',', ';');
                    $tm = explode(';',$t);

                    $action = get("action");
                    $sx .= '<ul>'.$action;
                    foreach($tm as $term)
                        {
                            if (!empty($term))
                                {
                                    $lang = $Language->getTextLanguage($term);
                                    $dt['name'] =ucfirst($term);
                                    $dt['lang'] = $lang;
                                    $dt['use'] = 0;
                                    $dt['uri'] = '';
                                    $dt['definition'] = '';
                                    $classes = array('Term');
                                    $dt['classes'] = json_encode($classes);
                                    $dt['sources'] = '';
                                    $dt['status'] = -1;
                                    $dt['updated_at'] = date("Y-m-d H:i:s");

                                    if ($check)
                                        {
                                            $sx .= '<li>' . $term . ' ('.$lang.') <b><i>Test</i></b></li>';
                                        } else {
                                            $sx .= '<li>'.$term. ' (' . $lang . ') <b>'.$this->register($dt). '</b></li>';
                                        }
                                }
                        }
                    $sx .= '</ul>';
                }
            return $sx;
        }
}
