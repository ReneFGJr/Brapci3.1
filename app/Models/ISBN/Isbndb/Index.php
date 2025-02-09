<?php

namespace App\Models\ISBN\Isbndb;

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

    function search($isbn)
    {
        $url = 'https://api2.isbndb.com/book/' . $isbn;
        $restKey = getenv('isbndb.key');

        $headers = array(
            "Content-Type: application/json",
            "Authorization: " . $restKey
        );

        $dt = read_link($url, 'CURL', false, $headers);
        return $dt;
    }

    function form()
    {
        $isbn = sonumero(get("isbn"));
        $sx = '';
        $sx .= form_open();
        $sx .= form_input('isbn', $isbn, ['class' => 'form-control border border-secondary']);
        $sx .= form_submit('action', 'pesquisar', ['class' => 'btn btn-outline-primary mt-2']);
        $sx .= form_close();

        if ($isbn != '') {
            $sx .= '<hr>';
            $sx .= 'Buscando API';
            $sx .= '<hr>';
            $dt = $this->search($isbn);
            $dta = (array)json_decode($dt);

            if ($dta != '') {
                $Cataloging = new \App\Models\Find\Books\Cataloging();
                $data = (array)$this->convert($dta);
                $sx .= $Cataloging->import($data);
            }
        }
        return $sx;
    }

    function dimensoesEX($vlr)
    {
        $RSP['peso'] = 0;
        $RSP['largura'] = 0;
        $RSP['comprimento'] = 0;
        $RSP['altura'] = 0;
        $v = ['length', 'width', 'weight', 'height'];
        $n = [
            'length' => 'comprimento',
            'width' => 'largura',
            'weight' => 'peso',
            'height' => 'altura'
        ];
        foreach ($v as $var) {
            if (isset($vlr[$var])) {
                $va = (array)$vlr[$var];
                $un = 'cm';
                $valor = $va['value'];
                $unit = $va['unit'];

                if ($var == 'weight') {
                    $un = 'kg';
                }
                $va = $this->convertUnit($valor,$unit) .$un;
                $RSP[$n[$var]] = $va;
            }
        }
        return $RSP;
    }
    function convertUnit($v, $unit)
    {
        $dv = $v;
        switch ($unit) {
            case 'inches':
                $dv = round((float)$v * 254) / 100;
                break;
            case 'pounds':
                $dv = round((float)$v * 4.53592) / 10;
                break;
            default:
                $dv = '?'.$unit.'?';
                exit;
        }
        return $dv;
    }
    function dimensoes($vlr)
    {
        $dim = [];
        $dm = troca($vlr, ', ', ',');
        $dm = explode(',', $vlr);
        foreach ($dm as $idx => $dmv) {
            $dmv = explode(':', $dmv);
            if (isset($dmv[1])) {
                if (strpos($dmv[1], 'Pounds')) {
                    $dv = trim(troca($dmv[1], 'Inches', ''));
                    $dv = round((float)$dv * 453.592) / 1000;
                    $dmv[1] = $dv;
                }
                if (strpos($dmv[1], 'Inches')) {
                    $dv = trim(troca($dmv[1], 'Inches', ''));
                    $dv = round((float)$dv * 254) / 100;
                    $dmv[1] = $dv;
                } else {
                }
                switch (trim($dmv[0])) {
                    case 'Weight':
                        $dim['peso'] = $dmv[1];
                        break;
                    case 'Width':
                        $dim['largura'] = $dmv[1];
                        break;
                    case 'Length':
                        $dim['comprimento'] = $dmv[1];
                        break;
                    case 'Height':
                        $dim['altura'] = $dmv[1];
                        break;
                    default:
                        echo "OPS Dimmentions " . $dmv[0];
                        exit;
                }
            }
        }
        return $dim;
    }

    function convert($dta)
    {
        $ISBN = new \App\Models\ISBN\Index();
        $Language = new \App\Models\Language\Lang();

        $dt = [];
        $dt['authors'] = [];
        $dt['erros'] = '';

        $dta = (array)$dta;



        foreach ($dta as $ida => $line) {
            $line = (array)$line;
            foreach ($line as $prop => $vlr) {
                if ($prop == 'isbn13') {
                    $prop = 'isbn';
                }
                if ($prop == 'isbn10') {
                    $prop = 'isbn';
                }
                switch ($prop) {
                    case 'binding':
                        break;
                    case 'msrp':
                        break;
                    case 'date_published':
                        $dt['date'] = $vlr;
                        break;
                    case 'edition':
                        $dt['edicao'] = $vlr;
                        break;
                    case 'image':
                        $dt['cover'] = $vlr;
                        break;
                    case 'language':
                        $dt['language'] = $Language->code($vlr);
                        break;
                    case 'authors':
                        if (!is_array($vlr)) {
                            $vlr[0] = $vlr;
                        }
                        for ($r = 0; $r < count($vlr); $r++) {
                            $v = troca($vlr[$r], '_', '');
                            $v = trim($v);
                            if ($v != '') {
                                $v = nbr_author($v, 7);
                                array_push($dt['authors'], $v);
                            }
                        }
                        break;
                    case 'publisher':
                        if (is_array($vlr)) {
                            $dt['editora'] = $vlr;
                        } else {
                            $dt['editora'][0] = $vlr;
                        }
                        break;
                    case 'isbn':
                        $isbn = $vlr;
                        if (strlen($isbn) == 10) {
                            $dt['isbn13'] = $ISBN->isbn10to13($isbn);
                            $dt['isbn10'] = $isbn;
                        } elseif (strlen($isbn) == 13) {
                            $dt['isbn13'] = $isbn;
                            $dt['isbn10'] = $ISBN->isbn13to10($isbn);
                        } else {
                            echo "ISBN Inválido";
                        }
                        break;
                    case 'title':
                        if (strpos($vlr, '(') > 0) {
                            $vlr = substr($vlr, 0, strpos($vlr, '('));
                        }
                        $dt['title'] = nbr_title(trim($vlr));
                        break;
                    case 'pages':
                        $dt['pages'] = $vlr . ' p.';
                        break;
                    case 'title_long':
                        $vlr = troca($vlr, '(Em Portugues do Brasil)','');
                        $vlr = trim($vlr);
                        $dt['title_long'] = $vlr;
                        break;
                    case 'title_long':
                        $dt['title_long'] = $vlr;
                        break;
                    case 'subjects':
                        foreach ($vlr as $id => $subj) {
                            if (!isset($dt['subjects'])) {
                                $dt['subjects'] = [];
                            }
                            array_push($dt['subjects'], nbr_title($subj));
                        }

                        break;
                    case 'synopsis':
                        $dt['abstract'] = $vlr;
                        break;
                    case 'dimensions':
                        $dm = $this->dimensoes($vlr);
                        $dt['dimensoes'] = $dm;
                        break;
                    case 'dimensions_structured':
                        /***** Dimensoes */
                        $dm = $this->dimensoesEX((array)$vlr);
                        $dt['dimensoes_2'] = $dm;
                        break;
                    default:
                        $dt['erros'].= 'Não localizado a propriedade ['. $prop.']. ';
                        break;
                }
            }
        }
        return $dt;
    }
}
