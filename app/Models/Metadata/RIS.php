<?php

namespace App\Models\Metadata;

use CodeIgniter\Model;

class RIS extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'ris';
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

    function short($ln)
    {
        $TYPE = 'JOUR';

        $RSP = [];
        array_push($RSP, 'TY  - ' . $TYPE);

        /* Autores */
        if (isset($ln['Authors'])) {
            foreach ($ln['Authors'] as $ida => $author) {
                $name = nbr_author($author, 2);
                array_push($RSP, 'AU  - ' . $name);
            }
        }

        /* Title */
        $Title = (array)$ln['Title'];
        if (isset($Title['pt'])) {
            array_push($RSP, 'TI  - ' . $Title['pt'][0]);
        } elseif (isset($Title['es'])) {
            array_push($RSP, 'TI  - ' . $Title['es'][0]);
        } elseif (isset($Title['en'])) {
            array_push($RSP, 'TI  - ' . $Title['en'][0]);
        } elseif (isset($Title['fr'])) {
            array_push($RSP, 'TI  - ' . $Title['fr'][0]);
        }

        /* Ano */
        array_push($RSP, 'PY  - ' . $ln['YEAR']);

        /* DOI */
        //pre($ln);
        /*
        array_push($RSP, 'DO  - ' . $ln['YEAR']);
        array_push($RSP, 'DI  - ' . $ln['YEAR']);
        */



        /* Issue */
        if (isset($ln['Issue'])) {
            $Issue = (array)$ln['Issue'];

            /* Publicação */
            array_push($RSP, 'PB  - ' . $Issue['journal']);
            array_push($RSP, 'JO  - ' . $Issue['journal']);

            //array_push($RSP, 'UR  - ' . 'https://brapci.inf.br/#v/' . $ln['ID']);
            array_push($RSP, 'UR  - ' . 'https://handle.net/20.500.11959/brapci/' . $ln['ID']);

            /* Volume */
            if ((isset($Issue['vol'])) and ($Issue['vol'] != '')) {
                array_push($RSP, 'VL  - ' . sonumero($Issue['vol']));
            }
            /* Number */
            if ((isset($Issue['nr'])) and ($Issue['nr'] != '')) {
                array_push($RSP, 'IS  - ' . sonumero($Issue['nr']));
            }
        }

        /* Abstract */
        if (isset($ln['Abstract'])) {
            /* Title */
            $Title = (array)$ln['Abstract'];
            if (isset($Title['pt'])) {
                array_push($RSP, 'AB  - ' . troca(troca($Title['pt'][0], chr(13), ' '), chr(10), ' '));
            } elseif (isset($Title['es'])) {
                array_push($RSP, 'AB  - ' . troca(troca($Title['es'][0], chr(13), ' '), chr(10), ' '));
            } elseif (isset($Title['en'])) {
                array_push($RSP, 'AB  - ' . troca(troca($Title['en'][0], chr(13), ' '), chr(10), ' '));
            } elseif (isset($Title['fr'])) {
                array_push($RSP, 'AB  - ' . troca(troca($Title['fr'][0], chr(13), ' '), chr(10), ' '));
            }
        }
        /* Subjects */
        if (isset($ln['Subject'])) {
            foreach ($ln['Subject'] as $lg => $line) {
                if (is_array($line)) {
                    foreach ($line as $idx => $word) {
                        array_push($RSP, 'KW  - ' . $word);
                    }
                } else {
                    array_push($RSP, 'KW  - ' . $line);
                }
            }
        }

        array_push($RSP, 'DB  - ' . 'BRAPCI');
        array_push($RSP, 'M3  - ' . $ln['Class']);
        array_push($RSP, 'LA  - ' . 'por');
        //array_push($RSP, 'N1  - ' . 'Portuguese');

        /* Fim */
        array_push($RSP, 'ER  - ');
        $sx = '';
        foreach ($RSP as $ln => $content) {
            $sx .= $content . chr(13);
        }
        $sx .= chr(13);
        return $sx;
    }

    function risToMarc21($risData)
    {

        // Separa o arquivo RIS em linhas
        $risLines = explode("\n", $risData);

        // Inicializa uma string para o formato MARC21
        $marc21 = '';

        // Mapeamento simples de campos RIS para MARC21
        $risToMarc21Map = [
            'TY' => 'LDR',   // Tipo de referência
            'AU' => '100',   // Autor principal
            'TI' => '245',   // Título
            'PY' => '260',   // Data de publicação
            'JO' => '440',   // Nome do periódico
            'SN' => '022',   // ISSN
            'VL' => '300',   // Volume
            'IS' => '362',   // Número
            'SP' => '300',   // Páginas de início
            'EP' => '300',   // Páginas de término
            'PB' => '260',   // Editora
            'CY' => '260',   // Local de publicação
            'KW' => '650',   // Palavras-chave
            'AB' => '520',   // Resumo
            'UR' => '856',   // URL
            'DO' => '024',   // DOI
        ];

        // Loop pelas linhas RIS e converte para MARC21
        foreach ($risLines as $line) {
            $tag = substr($line, 0, 2); // Primeiros dois caracteres são a tag RIS
            $value = substr($line, 6);  // O valor começa a partir do sexto caractere

            echo $tag.' ';

            if (isset($risToMarc21Map[$tag])) {

                switch ($tag) {
                    case '245':
                        $value = nbr_title($value,2);
                        break;
                    case '650':
                        $value = explode(';',$value);
                        break;
                }

                $marc21Tag = $risToMarc21Map[$tag];
                // Formato básico MARC21: "campo $a valor"
                if (is_array($value))
                    {
                        foreach($value as $id=>$valueA)
                            {
                                $valueA = trim($valueA);
                                if ($valueA != '')
                                    {
                                        $marc21 .= "{$marc21Tag}  \$a {$valueA}\n";
                                    }

                            }

                    } else {
                        $marc21 .= "{$marc21Tag}  \$a {$value}\n";
                    }


                if ($marc21Tag == '100') {
                    $risToMarc21Map['AU'] = '700';
                }
            }
        }

        return $marc21;
    }
}
