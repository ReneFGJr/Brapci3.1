<?php

namespace App\Models\AI\NLP\Book;

use CodeIgniter\Model;

class Sumary extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = '*';
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

    var $json = '';

    function show_form()
    {
        $txt = get('text');
        $txt = $this->process($txt);
        $sx = '';
        $sx .= form_open();
        $sx .= form_textarea(array('name' => 'text', 'value' => $txt, 'class' => 'form-control-lm', 'style' => 'width: 100%;'));
        $sx .= form_submit(array('name' => 'actiom', 'value' => lang('brapci.save')));
        $sx .= form_close();

        $sx .= 'TITULO - [autor1;autor2]';

        $sx .= '<div>' . $this->show_result($this->json) . '</div>';
        return $sx;
    }

    function show_result($j)
    {
        $sx = '';
        $js = $j;
        $j = (array)json_decode($j);

        $sx .= '<textarea class="form-control" style="width: 100%;">'.$js.'</textarea>';

        $sx .= '<ul>';
        foreach ($j as $line) {
            $line = (array)$line;
            $sx .= '<li>';
            $sx .= $line['title'];
            $pagf = 0;
            $pagi = 0;
            if (isset($line['pagf'])) $pagf = $line['pagf'];
            if (isset($line['pag'])) $pagi = $line['pag'];
            $sx .= '<i>';
            if ($pagf > 0) {
                if ($pagi > 0) {
                    $sx .= '<br>p.' . $pagi . '-' . $pagf;
                } else {
                    $sx .= '<br>p.' . $pagf;
                }
            } else {
                $sx .= '<br>p.' . $pagi;
            }
            $sx .= '</i>';
            if (count($line['autor']) > 0) {
                $sx .= '<br>Autores: ';
                $autor = $line['autor'];
                for ($r = 0; $r < count($autor); $r++) {
                    if ($r > 0) {
                        $sx .= '; ';
                    }
                    $sx .= trim($autor[$r]);
                }
            }
            $sx .= '</li>';
        }
        $sx .= '</ul>';
        return $sx;
    }

    function process($txt)
    {
        $txt = troca($txt, chr(10), chr(13));
        while ($pos = strpos($txt, chr(13) . chr(13))) {
            $txt = troca($txt, chr(13) . chr(13), chr(13));
        }
        $txt2 = $txt;
        $txt = troca($txt, '-', '.');
        while ($pos = strpos($txt, '..')) {
            $txt = troca($txt, '..', '.');
        }
        //$txt = troca($txt,'. ','.');
        $txt = troca($txt, ' .', '.');
        $txt = troca($txt, '.', '. ');
        $txt = troca($txt, chr(10), chr(13));
        $ln = explode(chr(13), $txt);

        $lr = array();

        foreach ($ln as $line) {
            if (strtolower(ascii(trim($line))) == 'sumario') {
                $line = '';
            }

            if (strlen(trim($line)) > 0) {
                $first = trim(substr($line, 0, 10));
                $last = trim(substr($line, strlen($line) - 5, 5));

                /*************************************** Elimina Números */
                if ($first == sonumero($first)) {
                    $line = trim(substr($line, strlen($first), strlen($line)));
                }

                /*********************** Remove sumeracao inicial **********/
                $sonum = sonumero($first);
                if (round($sonum) > 0) {
                    $first2 = $first;
                    $c = array('.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
                    $first2 = trim(str_replace($c, array(''), $first));
                    $line = troca($line, $first, $first2);
                }

                /*********************** Insere o número da pagina inicial */
                $sonum = sonumero(substr($line,10,strlen($line)));
                if (round($sonum) > 0) {
                    $line = troca($line, $sonum, '{p.' . $sonum);
                }


                /*********************** Guada dados em arquivo ************/
                if (strlen($line) > 0) {
                    array_push($lr, $line);
                }
            }
        }

        $w = array();
        $xpag = 0;
        $autor = array();
        foreach ($lr as $ln) {
            /*************************** PAGINA */
            $pag = 0;
            if ($pos = strpos($ln, '{p.')) {
                $pag = sonumero(trim(substr($ln, $pos + 3, 4)));
            }
            /************************** TITULO */
            $titulo = $ln;
            if ($pos = strpos($titulo, '{')) {
                $titulo = substr($titulo, 0, $pos);
            }
            /************************** Titulo */
            if ($pos = strpos($titulo, '[')) {
                 $titulo = substr($titulo, 0, $pos);
            }
            $titulo1 = mb_strtolower($titulo);
            $titulo = trim(mb_substr($titulo,0,1).mb_substr($titulo1,1,strlen($titulo1)));
            echo '<br>===>'. mb_substr($titulo, strlen($titulo) - 4,4);
            #while (substr($titulo,strlen($titulo)-1) == '.')
            #    {
            #        $titulo = mb_substr($titulo,0,strlen($titulo)-1);
            #    }

            /************************** AUTORES */
            if ($pos = strpos($ln, '[')) {
                $autor = array();

                $at = trim(substr($ln, $pos + 1, strlen($ln)));
                $at = troca($at, '[', '');
                $at = troca($at, ']', '');

                if (strpos($at,';'))
                    {

                    } else {
                        if (strpos($at, ','))
                            {
                                $at = troca($at,',',';');
                            }
                    }
                //$at = troca($at,'.','');
                $au = explode(';', $at);
                foreach ($au as $id => $nome) {
                    if (strpos($nome, '{')) {
                        $nome = trim(substr($nome, 0, strpos($nome, '{')));
                    }
                    $nome = trim($nome);
                    if (substr($nome,strlen($nome)-1,1) == '.')
                        {
                            $nome = substr($nome,0,strlen($nome)-1);
                        }
                    if (strlen(trim($nome)) > 0)
                    {
                        $nome = nbr_author($nome, 1);
                        $au[$id] = $nome;
                    }
                }
                $autor = $au;
            }

            $work = array();
            $work['title'] = $titulo;
            $work['autor'] = $autor;
            $work['pag'] = $pag;

            if (count($w) > 0) {
                $n = count($w) - 1;
                $w[$n]['pagf'] = $pag - 1;
            }
            array_push($w, $work);
        }
        $this->json = json_encode($w);
        return $txt2;
    }
}
