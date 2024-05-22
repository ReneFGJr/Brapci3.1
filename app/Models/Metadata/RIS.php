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
        array_push($RSP, 'TY - ' . $TYPE);

        /* Autores */
        foreach ($ln['Authors'] as $ida => $author) {
            $name = nbr_author($author, 2);
            array_push($RSP, 'AU - ' . $name);
        }

        /* Title */
        $Title = (array)$ln['Title'];
        if (isset($Title['pt'])) {
            array_push($RSP, 'TI - ' . $Title['pt'][0]);
        } elseif (isset($Title['es'])) {
            array_push($RSP, 'TI - ' . $Title['es'][0]);
        } elseif (isset($Title['en'])) {
            array_push($RSP, 'TI - ' . $Title['en'][0]);
        } elseif (isset($Title['fr'])) {
            array_push($RSP, 'TI - ' . $Title['fr'][0]);
        }

        /* Ano */
        array_push($RSP, 'PY - ' . $ln['YEAR']);



        /* Issue */
        if (isset($ln['Issue']))
            {
                $Issue = (array)$ln['Issue'];

                /* Publicação */
                array_push($RSP, 'PB - ' . $Issue['journal']);

                /* Volume */
                if ((isset($Issue['vol'])) and ($Issue['vol'] != ''))
                    {
                        array_push($RSP, 'VL - ' . sonumero($Issue['vol']));
                    }
                /* Number */
                if ((isset($Issue['nr'])) and ($Issue['nr'] != '')) {
                    array_push($RSP, 'IS - ' . sonumero($Issue['nr']));
                }
            }

        /* Subjects */
        if (isset($ln['Subject']))
        {
            foreach($ln['Subject'] as $lg=>$line)
                {
                    if (is_array($line))
                        {
                            foreach ($line as $idx => $word) {
                                array_push($RSP, 'KW - ' . $word);
                            }
                        } else {
                            array_push($RSP, 'KW - ' . $line);
                        }
                }
        }

        array_push($RSP, 'DB - ' . 'BRAPCI');
        array_push($RSP, 'M3 - ' . $ln['Class']);
        array_push($RSP, 'LA - ' . 'Portuguese');
        //array_push($RSP, 'N1 - ' . 'Portuguese');

        /* Fim */
        array_push($RSP, 'ER - ');
        $sx = '';
        foreach($RSP as $ln=>$content)
            {
                $sx .= $content.chr(13);
            }
        $sx .= chr(13);
        return $sx;
    }
}
