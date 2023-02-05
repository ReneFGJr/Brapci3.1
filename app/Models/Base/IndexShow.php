<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Indexshow extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'keywords';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function showHTML($dt)
    {
        $sx = view('RDF/subject', $dt);
        return $sx;
    }

    function show_index($key, $title = 'keywords')
    {
        if (!is_array($key))
            {
                return "INDEX SHOW $title";
            }
        $RDF = new \App\Models\Rdf\RDF();
        arsort($key);
        $tot = '';
        if (count($key) > 0)
            {
                $tot .= ' (' . count($key) . ')';
            }
        $ul = '<ul class="text_75" style="list-style-type: none; margin: 0px; padding: 0px; ">';
        $ulu = '</ul></div>'.cr();
        $sx = h(lang('brapci.' . trim($title)).$tot, 5,'border_title');
        $block = 0;
        $block_nr = 20;
        $bln = 0;
        $dsp = 0;
        foreach ($key as $keys => $total) {
            $dsp++;
            if ($bln == 0) {
                $sw = '';
                if ($block > 0) {
                    $sx .= '<span id="nblock' . $title . $block . '" class="view_more"
                            onclick="$(\'#block' . $title . $block . '\').toggle(\'slow\'); $(\'#nblock' . $title . $block . '\').toggle(\'slow\');"
                            style="cursor: pointer;">'.lang('brapci.view_more').  '</span>';
                    $sx .= $ulu;
                    $block_nr = $block_nr * 2;
                }

                if ($block > 0) {
                    $sw = 'style="display: none;';
                }
                $sx .= '<div id="block' . $title . $block . '" ' . $sw . '">';
                $sx .= $ul;
            }
            $xkey = explode(";",$keys);
            $link = '<a href="'.PATH.'/v/'.$xkey[1].'">';
            $linka = '</a>';

            $tot = '<span class="bullet">' . $total . '</span>';
            $sx .= '<li>' . $link . $xkey[0] . $linka . ' ' . $tot . '</li>';
            $bln++;
            if ($bln == $block_nr) {
                $bln = 0;
                $block++;
            }
        }
        if ($dsp > 0) {
            $sx .= $ulu;
        }
        return $sx;
    }

    function index_keys($key = array(), $id = '')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        $file = $dir . 'keywords.json';
        if (file_exists($file)) {
            $dt = file_get_contents($file);
            $dt = json_decode($dt);

            for ($r = 0; $r < count($dt); $r++) {
                $tt = (array)$dt[$r];
                if (isset($tt['term'])) {
                    $t = (string)$tt['term'];
                } else {
                    $tt = $dt[$r];
                    $t = trim($tt);
                }



                if (strlen($t) > 0) {
                    $term = strip_tags($t);
                    $id = substr($t, strpos($t, '/v/') + 2, strlen($t));
                    $id = sonumero(substr($id, 0, strpos($id, '"')));
                    $term .= ';' . $id;
                    if (isset($key[$term])) {
                        $key[$term]++;
                    } else {
                        $key[$term] = 1;
                    }
                }
            }
        }
        return $key;
    }
}