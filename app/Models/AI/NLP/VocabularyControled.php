<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class VocabularyControled extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'vocabularycontroleds';
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

    function text($t1,$bs)
        {
            $t1 = troca($t1,'-',' ');
            $t1 = troca($t1, chr(13), chr(13).' ');
            $t1 = troca($t1, chr(10), chr(10) . ' ');
            $t1 = troca($t1, ' Do ',' do ');
            $t1 = troca($t1, ' Da ', ' da ');
            while(strpos($t1,'  '))
                {
                    $t1 = troca($t1, '  ', ' ');
                }

            $t1 = troca($t1, ' De ', ' de ');
            $t1 = troca($t1, chr(10), chr(10) . ' ');
            $t1 = mb_strtoupper($t1);
            $vc = [];
            $ln = explode(chr(13),$bs);
            foreach($ln as $id=>$line)
                {
                    $line = troca($line,'"','');
                    $line = troca($line, chr(10), '');
                    $in = explode(";",$line);
                    if (count($in)==2)
                        {
                            $t = strzero(strlen($in[0]),4).$in[0];
                            $t = mb_strtoupper($t);
                            $vc[$t] = $in[1];
                        }
                }
            krsort($vc);

            foreach($vc as $bs=>$bt)
                {
                    $bs = trim(substr($bs,4,strlen($bs)));
                    if (strpos($t1,$bs))
                        {
                            $t1 = str_replace($bs, $bt, $t1);
                        }

                }

            $lst = '';
            $vi = [];
            foreach ($vc as $bs => $in) {
                $t = strzero(strlen($in), 4) . $in;
                $vi[$t] = 1;
            }
            krsort($vi);
            foreach ($vi as $bs => $id) {
                $bs = trim(substr($bs, 4, strlen($bs)));
                if (strpos($t1, $bs))
                {
                    $t1 = str_replace(' '.$bs, '[' . $bs . ']', $t1);
                }
            }
            return $t1;
        }
}
