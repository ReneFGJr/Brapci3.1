<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Abstracts extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'abstracts';
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

    function check_next()
    {
        $BUGS = new \App\Models\Functions\Bugs();
        $task = 'CHECK_ABSTRACT';
        $limit = 1000;
        $BOTS = new \App\Models\Bots\Index();
        $dt = $BOTS->task($task);

        if ($dt['task_status'] != 1) {
            $BOTS->task_remove($task);
            return "FIM";
        }

        $sx = '';
        $RDFLiteral = new \App\Models\Rdf\RDFLiteral();

        $RDF = new \App\Models\Rdf\RDF();
        $prop = $RDF->getClass('hasAbstract');

        $RDFData = new \App\Models\Rdf\RDFData();
        $dd = $RDFData
            ->select("count(*) as total")
            ->join('rdf_name', 'id_n = d_literal')
            ->where('d_p', $prop)
            ->findAll();
        $total = $dd[0]['total'];

        $dd = $RDFData
            ->join('rdf_name', 'id_n = d_literal')
            ->where('d_p', $prop)
            ->orderBy('id_d')
            ->get($limit, $dt['task_offset'])
            ->getResult();
        $upper = false;
        foreach ($dd as $row) {
            $app = '';
            $upper = false;
            $txt = $row->n_name;

            /************************************** Idioma */
            $lang = $row->n_lang;
            if (($lang != 'pt-BR') and ($lang != 'fr') and ($lang != 'it') and ($lang != 'en') and ($lang != 'es') and ($lang != 'es')) {
                $update = true;
                $app .= '[language]';
                switch ($lang) {
                    case 'NaN':
                        $lang = 'pt-BR';
                        break;
                    case 'it-IT':
                        $lang = 'it';
                        break;
                    case '':
                        $lang = 'pt-BR';
                        break;
                    case 'pt':
                        $lang = 'pt-BR';
                        break;
                    case '0':
                        $lang = 'pt-BR';
                        break;
                    case 'pt-PT':
                        $lang = 'pt-BR';
                        break;
                    case 'es-ES':
                        $lang = 'es';
                        break;
                    case 'ca-ES':
                        $lang = 'es';
                        break;
                    case 'fr-CA':
                        $lang = 'fr';
                        break;
                    case 'fr-FR':
                        $lang = 'fr';
                        break;
                    default:
                        pre($row);
                        break;
                }
            }

            if (strpos($txt,chr(13))) { $txt = troca($txt,chr(10),' '); $upper = true; $app .= '[CR]'; }

            /* ENTER */
            if (strpos($txt, chr(13))) {
                $txt = troca($txt, chr(10), ' ');
                $upper = true;
                $app .= '[CR]';
            }
            if (strpos($txt,chr(10).'Palavras-chave:'))
                {
                    $txt = troca($txt, $txt, chr(10) . 'Palavras-chave:','#Palavras-chave:');
                    $upper = true;
                    $app .= '[KEYWORDS]';
                }

            if (strpos($txt, chr(10) . 'Keywords:')) {
                $txt = troca($txt, $txt, chr(10) . 'Keywords:', '#Keywords:');
                $upper = true;
                $app .= '[KEYWORDS]';
            }
            if (strpos($txt,chr(10))) { $txt = troca($txt,chr(10),' '); $upper = true; $app .= '[LF]'; }
            }

        if ($upper)
            {
                $da['n_name'] = trim($txt);
                $da['n_lang'] = trim($lang);
                $RDFLiteral->set($da)->where('id_n', $row->id_n)->update();
                $sx .= $txt.$app.'<br>';
                pre($da,false);
            }
        $dt['task_offset'] = $dt['task_offset'] + $limit;
        if ($dt['task_offset'] > $total) {
            $dt['task_offset'] = 0;
            $dt['task_status'] = 0;
        }

        if (agent() == 1) {
            $perc = number_format($dt['task_offset'] / $total * 100, 1) . '%';
            $sx = '<br>Offset ' . $dt['task_offset'] . '/' . $total . ' ' . $perc . $sx;
            $sx .= metarefresh('', 1);
        } else {
            echo "OPS";
        }

        $BOTS
            ->set($dt)
            ->where('task_id', $task)
            ->update();
        return $sx;
    }
}
