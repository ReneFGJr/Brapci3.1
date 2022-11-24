<?php

namespace App\Models\Lattes;

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

    /*

    */

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $sx = '';
        $sx .= h(lang('tools.Lattes_tools'), 2);

        switch ($d1) {
            case 'search':
                $sx .= $this->lattes_search_form($d2, $d3, $d4);
                $sx .= $this->lattes_search_result($d2, $d3, $d4);
                break;
            default:
                $Projects = new \App\Models\Tools\Projects();
                $sx .= $Projects->my_projects($d1, $d2, $d3, $d4);
                break;
        }
        return $sx;
    }

    function lattes_search_extrai_nome($txt)
    {
        $key = '';
        $sx = '';
        $txt = substr($txt, strpos($txt, '(por Assunto)'), strlen($txt));
        $txt_ref = '<a href="javascript:abreDetalhe(';
        //$txt = troca($txt,$txt_ref,'[ID]');

        preg_match_all("|<[^>]+>(.*)</[^>]+>|U", $txt, $matches);
        $search1 = 'javascript:abreDetalhe(';
        $search2 = "class='porcentagem'";
        $match = $matches[0];
        $rst = array();

        //pre($match);

        $next = false;
        for ($r = 0; $r < count($match); $r++) {
            $line = $match[$r];

            if ($next == true) {
                $line = strip_tags($line);
                if (strlen($line) == 0) {
                    $next = false;
                } else {
                    $rst[$key]['desc'] .= ascii(utf8_encode($line));
                    //pre($line, false);
                }
            }

            if (strpos($line, $search2))
                {
                    $percent = strip_tags($line);
                }

            if (strpos($line, $search1)) {
                $line = substr($line, strpos($line, $search1) + strlen($search1), strlen($line));
                $line = substr($line, 0, strpos($line, '</a'));
                $line = troca($line, ')">', '');
                $line = troca($line, "'", '');

                $names = explode(',', $line);
                if (!isset($rst[$names[0]])) {
                    $rst[$names[0]] = array();
                    $rst[$names[0]]['name'] = $names[3];
                    $rst[$names[0]]['name_asc'] = $names[1];
                    $rst[$names[0]]['ids'] = $names[2];
                    $rst[$names[0]]['desc'] = '';
                    $rst[$names[0]]['term'] = get("term");
                    $rst[$names[0]]['perc'] = $percent;
                    $key = $names[0];
                    $names[1] = '';
                }
                $next = true;
            }
        }
        $file = 'lattes_' . date("Ymd_His") . '.xls'    ;

        $sr = '<table class="table">';
        $sr .= '<tr>
                    <th>' . msg('brapci.IDK') . '</th>
                    <th>' . msg('brapci.name') . '</th>
                    <th>' . msg('brapci.name_asc') . '</th>
                    <th>' . msg('brapci.id') . '</th>
                    <th>' . msg('brapci.description') . '</th>
                    <th>' . msg('brapci.approximation') . '</th>
                </tr>';

        foreach($rst as $id=>$line)
            {
                $sr .= '<tr>';
                $sr .= '<td>' . $key . '</td>';
                $sr .= '<td>'.$line['name'].'</td>';
                $sr .= '<td>'.$line['name_asc'].'</td>';
                $sr .= '<td>'.$line['ids'].'</td>';
                $sr .= '<td>'.$line['desc'].'</td>';
                $sr .= '<td>' . $line['perc'] . '</td>';
                $sr .= '</tr>';
            }
        $sr .= '</table>';
        dircheck('.tmp/');
        dircheck('.tmp/Lattes/');
        dircheck('.tmp/Lattes/Export/');
        $file  = '.tmp/Lattes/Export/'.$file;
        file_put_contents($file,utf8_decode($sr));

        $sx .= h('total: '.count($rst),3);

        $sx .= '<a href="'.URL.'/'.$file.'" class="btn btn-primary">'.msg('brapci.export').'</a>';
        $sx .= $sr;

        return $sx;
    }

    function lattes_search_result()
    {
        $term = get("term");
        $sx = '';

        /*********************  */
        if ($term != '') {
            $termURL = '""'.urlencode($term).'""';
            $url = '';
            $url .= 'https://buscatextual.cnpq.br/buscatextual/busca.do?';
            $url .= 'metodo=forwardPaginaResultados';
            $url .= '&registros=0;5000';
            $url .= '&query=%28%2Bidx_assunto%3A%28%22' . $termURL . '%22%29++%2Bidx_nacionalidade%3Ae%29+or+%28%2Bidx_assunto%3A%28%22' . $termURL . '%22%29++%2Bidx_nacionalidade%3Ab+%5E500+%29';
            $url .= '&analise=cv';
            $url .= '&tipoOrdenacao=null';
            $url .= '&paginaOrigem=index.do';
            $url .= '&mostrarScore=true';
            $url .= '&mostrarBandeira=true&modoIndAdhoc=null';
            $sx .= '<hr>' . urldecode($url);
            $sx .= '<hr>' . '<textarea style="width:100%; height:190px;">' . $url . '</textarea>';

            $file = md5($url).'.txt';
            dircheck('.tmp/');
            dircheck('.tmp/Lattes/');
            dircheck('.tmp/Lattes/Inport/');
            $file  = '.tmp/Lattes/Inport/' . $file;

            if (file_exists($file)) {
                $txt = file_get_contents($file);
            } else {
                $txt = file_get_contents($url);
                file_put_contents($file, $txt);
            }
            $sx = $this->lattes_search_extrai_nome($txt);
        }


        return $sx;
    }

    function lattes_search_form()
    {
        $sx = '';
        $sx .= form_open();
        $sx .= '<label>' . lang('brapci.subject') . '</label>';
        $sx .= form_input(array('name' => 'term', 'class' => 'form-control-mb full', 'value' => get("term")));
        $sx .= form_submit('submit', lang('brapci.search'), 'class="btn btn-outline-primary"');
        $sx .= form_close();
        return $sx;
    }
}
