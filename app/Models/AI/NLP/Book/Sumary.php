<?php

namespace App\Models\AI\NLP\Book;

use CodeIgniter\Model;
use Exception;

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
        $options = [];
        $options[1]  = 'Método 1 - Títulos em Caixa Alta';
        $options[2] = 'method 2 - Manual Inserção';

        $txt = get('text');
        $txt = $this->process($txt);
        $sx = '';
        $sx .= form_open();
        $sx .= form_textarea(array('name' => 'text', 'value' => $txt, 'class' => 'form-control-lm', 'style' => 'width: 100%;'));
        $sx .= form_label('Method') . ': ';
        $sx .= form_dropdown('method', $options);
        $sx .= '<br>';
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

        $sx .= '<textarea class="form-control" style="width: 100%; height: 200px;">' . $js . '</textarea>';

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

    function markup($txt)
    {
        $RSP = $this->processarTexto($txt);
        if ($txt == '') {
            $RSP['status'] = '500';
            $RSP['message'] = 'Text is empty';
        }
        echo json_encode($RSP);
        exit;
    }

    function processarTexto($texto)
    {
        $Language = new \App\Models\AI\NLP\Language();
        $linhas = explode("\n", $texto);
        $RSP = [];


        $model = [
            'TITLE' => '',
            'LANGUAGE' => '',
            'AUTHORS' => [],
            'ABSTRACT' => '',
            'KEYWORD' => [],
            'PAGE_START' => '',
            'PAGE_END' => '',
        ];

        $ln = 0;
        $dt = [];

        foreach ($linhas as $linha) {
            $ch = substr($linha, 0, 1);
            $linha = substr($linha, 1, strlen($linha));
            $linha = trim($linha);

            switch ($ch) {
                    /* TITULO */
                case '*':
                    if ($ln > 0) {
                        array_push($RSP, $dt);
                    }
                    $dt = $model;
                    $pagi = '';
                    $pagf = '';
                    if (strpos($linha, '|')) {
                        $pags = trim(substr($linha, strpos($linha, '|') + 1, strlen($linha)));
                        if (strpos($pags, '-')) {
                            $pagf = trim(substr($pags, strpos($pags, '-') + 1, 10));
                            $pagi = trim(substr($pags, 0, strpos($pags, '-')));
                        } else {
                            $pagi = trim($pags);
                        }
                        $title = substr($linha, 0, strpos($linha, '|'));
                    } else {
                        $title = $linha;
                    }
                    $title = nbr_title($title);
                    $dt['TITLE'] = $title;
                    $dt['PAGE_START'] = $pagi;
                    $dt['PAGE_END'] = $pagf;
                    $dt['LANGUAGE'] = $Language->getTextLanguage($title);
                    $ln = 1;
                    break;
                case '#':
                    /* AUTHORS */
                    $linha = troca($linha, ',', ';');
                    $authors = explode(';', $linha);
                    foreach ($authors as $id => $nome) {
                        //$authors[$id] = nbr_author($nome,3);
                        $nome = trim($nome);
                        $authors[$id] = nbr_author($nome, 7);
                    }
                    $dt['AUTHORS'] = $authors;
                    break;
                case '@':
                    /* KEYWORD */
                    $linha = troca($linha, ',', ';');
                    $linha = troca($linha, '.', ';');
                    $keywords = explode(';', $linha);
                    foreach ($keywords as $id => $key) {
                        //$authors[$id] = nbr_author($nome,3);
                        $nome = trim($key);
                        $keywords[$id] = nbr_author($key, 7);
                    }
                    $dt['KEYWORD'] = $keywords;
                    break;

                case '$':
                    /* RESUMO */
                    $dt['ABSTRACT'] = $linha;
                    break;
            }
        }

        if ($ln > 0) {
            array_push($RSP, $dt);
        }

        /* Pags inference */
        foreach ($RSP as $id => $rg) {
            if (isset($rg['PAGE_END'])) {
                if (($rg['PAGE_END'] == '') and ($rg['PAGE_START'] !== '')) {
                    if (isset($RSP[$id + 1]['PAGE_START'])) {
                        try {
                            $RSP[$id]['PAGE_END'] =  '' . ($RSP[$id + 1]['PAGE_START'] - 1);
                        } catch (Exception $e) {
                            $RSP[$id]['PAGE_END'] =  '';
                        }
                    }
                }
            }
        }


        return $RSP;
    }

    /**************************************** Processamento */
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

        $continue = false;
        $line = '';

        foreach ($ln as $lline) {
            if (strtolower(ascii(trim($lline))) == 'sumario') {
                $lline = '';
            }

            if (!$continue) {
                $line = $lline;
            } else {
                $line .= $lline;
            }
            $continue = false;

            /* Remove Capítulo */
            $line = $this->remove_captitulo($line);

            if (strlen(trim($line)) > 0) {
                $first = trim(substr($line, 0, 10));
                $last = trim(substr($line, strlen($line) - 5, 5));
                $clast = trim(substr($line, strlen($line) - 1, 1));
                $cfirst = substr($line, 0, 1);
                $cfirst_low = substr(mb_strtolower($line), 0, 1);

                /*************************************** Elimina Números */
                if ($first == sonumero($first)) {
                    $line = trim(substr($line, strlen($first), strlen($line)));
                }

                /*********************** Remove sumeracao inicial **********/
                $sonum = sonumero($first);
                if (round($sonum) > 0) {
                    $first2 = $first;
                    $c = array('.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
                    $first2 = trim(str_replace($c, array(''), $first));
                    $line = troca($line, $first, $first2);
                }

                /*********************** Insere o número da pagina inicial */
                $sonum = sonumero(substr($line, 10, strlen($line)));
                if (round($sonum) > 0) {
                    $line = troca($line, $sonum, '{p.' . $sonum);
                }

                if ($clast == ',') {
                    $continue = true;
                }

                if ($cfirst_low == $cfirst) {
                    //$continue = true;
                }
                //echo '<br>'.$line." [$continue]";
                /*********************** Guada dados em arquivo ************/
                if ((strlen($line) > 0) and (!$continue)) {
                    array_push($lr, $line);
                    $continue = false;
                }
            }
        }
        $method = get("method");

        switch ($method) {
            case '2':
                $this->method02($lr);
                break;
            default:
                $this->method01($lr);
                break;
        }

        $t = '';
        for ($r = 0; $r < count($lr); $r++) {
            $t .= $lr[$r] . cr();
        }

        return $txt2;
    }

    function remove_captitulo($ln)
    {
        $v = ['CAPITULO', 'CAPÍTULO'];
        for ($r = 1; $r < 30; $r++) {
            foreach ($v as $id => $term) {
                if (strpos(' ' . $ln, $term . ' ' . $r)) {
                    $ln = troca($ln, $term . ' ' . $r, '');
                    return $ln;
                }
            }
        }
        return $ln;
    }

    /********* TITULO MISTURADO COM AUTORES */
    function method02($lr)
    {
        $ln = [];
        foreach ($lr as $id => $line) {
            array_push($ln, $line);
        }

        $this->json($ln);
        return '';
    }

    /********* TITULO EM CAIXA ALTA */
    function method01($lr)
    {
        $ln = [];
        $xln = '';
        $title = false;
        $author = false;
        $xpag = 'X';
        foreach ($lr as $id => $line) {
            $line = troca($line, '. ', '');
            $pos = strpos($line, ' ');
            if ($pos > 0) {
                $first = trim(substr($line, 0, $pos));
            } else {
                $first = $line;
            }
            $ufirst = mb_strtoupper($first);
            $upper = ($first == $ufirst);
            $pag = (strpos($line, '{p.') > 0);
            if ($upper) {
                if ($author == true) {
                    array_push($ln, $xln);
                    $xln = '';
                }
                if ($title == true) {
                    if ($pag == $xpag) {
                        array_push($ln, $xln);
                        $xln = $line;
                    } else {
                        $xln .= ' ' . $line;
                    }
                    $title = true;
                } else {
                    $title = true;
                    $xln = $line;
                }
                $author = false;
            } else {
                if ($author == true) {
                    $xln .= ';' . $line;
                } else {
                    $xln .= '[' . $line;
                }
                $title = false;
                $author = true;
            }
            $xpag = $pag;
            //echo '<br>' . $line . " A[$author] T[$title] U[$upper] P[$pag]";
        }
        if ($xln != '') {
            array_push($ln, $xln);
        }
        for ($r = 0; $r < count($ln); $r++) {
            if (!strpos($ln[$r], '[')) {
                $ln[$r] .= '[';
            }
        }

        $this->json($ln);
        return '';
    }

    function json($lr)
    {
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
            $titulo = (mb_substr($titulo, 0, 1) . mb_substr($titulo1, 1, strlen($titulo1)));
            $titulo = troca($titulo, '.', '');
            $titulo = trim($titulo);

            if (substr($titulo, strlen($titulo) - 1, 1) == '.') {
                $titulo = substr($titulo, 0, strlen($titulo) - 1);
            }

            /************************** AUTORES */
            if ($pos = strpos($ln, '[')) {
                $autor = array();

                $at = trim(substr($ln, $pos + 1, strlen($ln)));
                $at = troca($at, '[', '');
                $at = troca($at, ']', '');

                if (strpos($at, ';')) {
                } else {
                    if (strpos($at, ',')) {
                        $at = troca($at, ',', ';');
                    }
                }
                //$at = troca($at,'.','');
                $au = explode(';', $at);
                foreach ($au as $id => $nome) {
                    if (strpos($nome, '{')) {
                        $nome = trim(substr($nome, 0, strpos($nome, '{')));
                    }
                    $nome = trim($nome);
                    if (substr($nome, strlen($nome) - 1, 1) == '.') {
                        $nome = substr($nome, 0, strlen($nome) - 1);
                    }
                    if (strlen(trim($nome)) > 0) {
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
        return "";
    }
}
