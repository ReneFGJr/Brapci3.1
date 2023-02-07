<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Titles extends Model
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
        $task = 'CHECK_TITLES';
        $limit = 500;
        $BOTS = new \App\Models\Bots\Index();
        $dt = $BOTS->task($task);

        if ($dt['task_status'] != 1)
        {
            $BOTS->task_remove($task);
            return "FIM";
        }

        $sx = '<hr>'.cr();
        $RDFLiteral = new \App\Models\Rdf\RDFLiteral();

        $RDF = new \App\Models\Rdf\RDF();
        $prop = $RDF->getClass('hasTitle');

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

        foreach ($dd as $row) {
            $update = false;
            $app = '';
            $title = trim($row->n_name);

            /************************************** Idioma */
            $lang = $row->n_lang;
            if (($lang != 'pt-BR') and ($lang != 'fr') and ($lang != 'en') and ($lang != 'es') and ($lang != 'es'))
                {
                    $update = false;
                    $app .= '[language]';
                    switch($lang)
                        {
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
            /******************************** Caixa Alta */
            $t = explode(' ', $row->n_name);
            $id = 0;
            if (count($t) < 2) {
                $idc = $row->d_r1;
                $title = ucfirst(mb_strtolower($row->n_name));
                if ($title != $row->n_name)
                    {
                        $sx .= 'SHORT: ' . $row->n_name . '<br>';
                        $app .= '[UPPER-SHORT]';
                        $update = true;
                    }
            } else {
                while (strlen($t[$id]) < 2) {
                    $id++;
                }
                /******************** CAIXA ALTA/BAIXA */
                $upper = uppercase($t[$id]);
                $so = sonumero($row->n_name);
                if (($upper == $t[$id]) and ($so == '')) {
                    //echo $upper.'=='.$t[$id].' = ';
                    $title_o = $row->n_name;
                    $title = ucfirst(mb_strtolower($row->n_name));
                    $app .= '[UPPER]';
                    $update = true;
                }
            }

            /*********************** ENTER */
            if (strpos($title,chr(13))) { $title = troca($title,chr(13),' ');
                $update = true; $app .= '[CR]'; }
            if (strpos($title, chr(10))) { $title = troca($title, chr(10), ' ');
                $update = true;
                $app .= '[LF]';}
            while (strpos($title,'  '))
                {
                    $title = troca($title,'  ',' ');
                    $update = true;
                }

            if ($update)
                {
                $da['n_name'] = trim($title);
                $da['n_lang'] = trim($lang);
                $RDFLiteral->set($da)->where('id_n', $row->id_n)->update();
                $sx .= $title.' '.$app.'<br>';
                }
        }
        $dt['task_offset'] = $dt['task_offset'] + $limit;
        if ($dt['task_offset'] > $total)
            {
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
