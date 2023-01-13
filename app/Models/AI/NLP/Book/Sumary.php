<?php

namespace App\Models\AI\NLP\Book;

use CodeIgniter\Model;

class Sumary extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sumaries';
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
            $sx .= form_textarea(array('name'=>'text','value'=>$txt,'class'=>'form-control-lm','style'=>'width: 100%;'));
            $sx .= form_submit(array('name'=>'actiom','value'=>lang('brapci.save')));
            $sx .= form_close();

            $sx .= 'TITULO - [autor1;autor2]';

            $sx .= '<div>'.pre(json_decode($this->json),false).'</div>';
            return $sx;
        }

    function process($txt)
        {
            $txt = troca($txt,'-','.');
            while($pos = strpos($txt,'..'))
                {
                    $txt = troca($txt,'..','.');
                }
            $txt = troca($txt,'. ','.');
            $txt = troca($txt, ' .', '.');
            $txt = troca($txt,chr(10),chr(13));
            $ln = explode(chr(13),$txt);

            $lr = array();

            foreach($ln as $line)
                {
                    if (strlen(trim($line)) > 0)
                    {
                    $first = trim(substr($line,0,strpos($line,' ')));

                    /*************************************** Elimina Números */
                    if ($first == sonumero($first))
                        {
                            $line = trim(substr($line,strlen($first),strlen($line)));
                        }

                    /*********************** Insere o número da pagina inicial */
                    for ($a=0;$a < 10;$a++)
                        {
                            $line = troca($line,'.'.$a,'{p.'.$a);
                        }


                    /*********************** Guada dados em arquivo ************/
                    if (strlen($line) > 0)
                        {
                            array_push($lr, $line);
                        }
                    }
                }

            $w = array();
            $xpag = 0;
            foreach($lr as $ln)
                {
                    /*************************** PAGINA */
                    $pag = 0;
                    if ($pos = strpos($ln,'{p.'))
                        {
                            $pag = sonumero(trim(substr($ln,$pos+3,4)));
                        }
                     /************************** TITULO */
                    if ($pos = strpos($ln, '{p.')) {
                        $titulo = substr($ln,0,$pos);
                    } else {
                        if ($pos = strpos($ln,'['))
                            {
                                $titulo = substr($ln, 0, $pos);
                            } else {
                                $titulo = $ln;
                            }
                    }

                    /************************** AUTORES */
                    $autor = array();
                    if ($pos = strpos($ln,' ['))
                        {
                            $at = trim(substr($ln,$pos+1,strlen($ln)));
                            $at = troca($at,'[','');
                            $at = troca($at,']','');

                            $au = explode(';',$at);
                            foreach($au as $id=>$nome)
                                {
                                    $nome = nbr_author($nome,1);
                                    $au[$id] = $nome;
                                }
                            $autor = $au;
                        }

                    $work = array();
                    $work['title'] = $titulo;
                    $work['autor'] = $autor;
                    $work['pag'] = $pag;
                    if (count($w) > 0)
                        {
                            $n = count($w)-1;
                            $w[$n]['pagf'] = $pag-1;
                        }

                    array_push($w,$work);
                }
                $this->json = json_encode($w);
            return $txt;
        }
}