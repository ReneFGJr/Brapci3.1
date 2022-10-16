<?php

namespace App\Models\ScientificCommunication;

use CodeIgniter\Model;

class Reference extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'references';
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

    function index($d1,$d2,$d3,$d4)
        {
            $sx = '';
            $sx .= $this->form();
            $sx = bs(bsc($sx,12));
            $ref = get('ref');
            if ($ref != '')
                {
                    $ref = $this->prepare($ref);
                    $sx .= $this->view($ref);

                    $sx .= $this->analyse($ref);
                }
            $sx = bs($sx);
            return $sx;
        }

    function prepare($ref)
        {
            $rref = array();
            $tref = explode(chr(13), $ref);

            for ($r=0;$r < count($tref);$r++)
                {
                    $line = trim($tref[$r]);
                    if (strlen($line) > 0)
                        {
                            $rref[] = $line;
                        }
                }
            return $rref;
        }

    function analyse($ref)
        {
            $sx = h('peer.bibliometric_analysis',4);
            $sx .= '<p>'.$this->analyse_reference_total($ref). '</p>';
            $sx .= '<p>'.$this->analyse_year($ref). '</p>';
            $sx = bsc($sx,12);
            return $sx;
        }

    function analyse_year($ref)
        {
            $year = array();
            $tot = 0;
            $max = 0;
            $min = 9999;
            $anof = date("Y") + 1;
            for ($r = 0;$r < count($ref);$r++)
                {
                    $l = $ref[$r];
                    $l = mb_strtolower($l);
                    if (strpos($l,'http') > 0)
                        {
                            $l = substr($l,0,strpos($l,'http'));
                        }
                    if (strpos($l, 'doi:') > 0) {
                        $l = substr($l, 0, strpos($l, 'doi:'));
                    }
                    if (strpos($l, 'http') > 0) {
                        $l = substr($l, 0, strpos($l, 'http'));
                    }
                    $y = 0;

                    for ($y=$anof;$y > 1900; $y--)
                        {
                            $yr = strzero($y,4);
                            $pos = strpos($l, $yr);
                            if ($pos > 0)
                                {
                                    $tot++;
                                    if ($y > $max) { $max = $y; }
                                    if ($y < $min) { $min = $y; }
                                    $ya = substr($l,$pos,4);
                                    if (isset($year[$ya]))
                                        {
                                            $year[$ya]++;
                                        } else {
                                            $year[$ya] = 1;
                                        }
                                    break;
                                }
                        }
                }
                $sx = 'Foram identidicadas '.$tot.' referências com ano de publicação entre 1900 e '.$anof.', sendo a mais antiga de '.$min.' e a mais recente de '.$max.'.';
                /** Moda */
                $meia_vida = 0;
                $meia_vida_total = 0;
                ksort($year);
                $max = 0;
                foreach($year as $key => $value)
                    {
                        if ($value > $max)
                            {
                                $max = $value;
                                $meia_vida = $meia_vida + $key*$value;
                                $meia_vida_total = $meia_vida_total + $value;
                            }
                    }

                /** Mediana */
                $meia_vida = round($meia_vida / $meia_vida_total*10)/10;
                $moda = '';
                foreach ($year as $key => $value) {
                    if ($value == $max) {
                        if (strlen($moda) > 0) { $moda .= ', '; }
                        $moda .= $key;
                    }
                }
                if (strpos($moda,',') > 0)
                    {
                        $sx .= ' Os anos mais frequentes são '.$moda;
                    } else {
                        $sx .= ' O ano mais frequente é '.$moda;
                    }
                $sx .= ' com ' . $max . ' referências.';

                /**************** Meia vida */
                $mv = date('Y') - $meia_vida;
                $sx .= 'A media dos anos citados é do ano de '. round($meia_vida).'';
                $sx .= ', o que corresponde a uma meia vida da literatura de '.$mv.' anos.';

                if ($mv <= 5)
                    {
                        $sx .= ' A literatura é muito recente a atualizada.';
                    } else {
                        if ($mv > 10)
                            {
                                if ($mv < 12)
                                    {
                                        $sx .= ' A literatura é um pouco antiga. Recomenda-se incorporar novas referências mais recentes.';
                                    } else {
                                        $sx .= ' Desta forma a literatura é considerada desatualizada.. Recomenda-se a atualização com estudos mais recente sobre o tema.';
                                        $sx .= ' Resgatando a literatura clássica e os trabalhos mais recente, pode-se obter uma visão mais atualizada do tema.';
                                    }
                            } else {
                                $sx .= ' A literatura é atualizada.';
                            }
                    }
                return $sx;
        }

    function analyse_reference_total($ref)
        {
            $total = count($ref);
            $sx = '';
            $sx .= 'Na bibliografia são listadas '.$total.' fontes citadas. ';
            if ($total < 10)
                {
                    $sx .= ' O número de referências é muito baixo, é recomendado que sejam citadas pelo menos 10 fontes.';
                } else {
                    if ($total < 20)
                        {
                            $sx .= ' O número de referências é adequado. Porém poderia ser aumentado.';
                        } else {
                            if ($total > 31) {
                                $sx .= ' O número de referências é muito grande, sendo recomendado citar fontes relevantes para a pesquisa.';
                            } else {
                                $sx .= ' O número de referências é muito bom, adequado a pesquisa';
                            }
                        }
                }
                return $sx;
        }

    function view($ref)
        {
            $sx = '<ol>';
            for ($r=0;$r < count($ref);$r++)
                {
                    $line = $ref[$r];
                    $sx .= '<li>'.$line.'</li>';
                }
            $sx .= '</ol>';
            return $sx;
        }

    function form()
        {
            $sx = '';
            $sx .= '<h1>Referências</h1>';
            $sx .= form_open();
            $sx .= form_textarea(array('name'=>'ref','class'=>'form-control','rows'=>20));
            $sx .= form_submit(array('name'=>'submit','class'=>'btn btn-primary'),lang('peer.analyse'));
            $sx .= form_close();
            return $sx;
        }
}
