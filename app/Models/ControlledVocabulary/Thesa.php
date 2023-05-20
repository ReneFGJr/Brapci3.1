<?php

namespace App\Models\ControlledVocabulary;

use CodeIgniter\Model;

class Thesa extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'thesas';
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

    function process($xml,$th)
        {
            $sx = '';
            try {
                $C = (array)$xml['Concept'];
                foreach($C as $id=>$line)
                    {
                        $concp = '';
                        foreach ($line->attributes() as $a => $b) {
                            if ($a == 'about') { $concp = troca($b,'#','');}
                        }

                        $sx .= '<li>'.$concp.'</li>'.cr();

                        $pref = $line->prefLabel;
                        $type = 'P';
                        $this->process_terms($pref, $type, $concp,$th);

                        $pref = $line->altLabel;
                        $type = 'A';
                        $this->process_terms($pref, $type, $concp, $th);

                        $pref = $line->hiddenLabel;
                        $type = 'H';
                        $this->process_terms($pref, $type, $concp, $th);
                    }
            } catch (Exception $e) {
                $msg = $e->getTraceAsString();
                throw $e;
                $sx = bsmessage('Erro no processamento Thesa - ' . $msg, 3);
            }
            return $sx;
        }

    function process_terms($pref, $type,$concp, $th)
        {
            $ThesaurusDescriptors = new \App\Models\ControlledVocabulary\ThesaurusDescriptors();
            $ThesaurusDescriptorsTh = new \App\Models\ControlledVocabulary\ThesaurusDescriptorsTh();
            foreach ($pref as $idt => $term) {
                $termo = $term;
                $lang = '';
                foreach ($term->attributes() as $a => $b) {
                    if ($a == 'lang') {
                        $lang = $b;
                    }
                }
                $idt = $ThesaurusDescriptors->register($termo);
                $idt = $ThesaurusDescriptorsTh->register($th, $idt, $lang, $type, $concp);
            }
        }

    function import($id)
        {
            $sx = '';
            $sx .= '<ul>';
            $Thesaurus = new \App\Models\ControlledVocabulary\Thesaurus();
            $dt = $Thesaurus->find($id);
            $url = $dt['th_url'];

            try {
                    $xml = read_link($url);
                    $pref = ['xml', 'rdf'];
                    foreach($pref as $id=>$pre)
                        {
                            $xml = troca($xml, $pre.':', '');
                        }
                    $xml = simplexml_load_string($xml);
                    $xml = (array)$xml;
                    $sx .= $this->process($xml,$id);

            } catch (Exception $e) {
                $msg = $e->getTraceAsString();
                throw $e;
                $sx = bsmessage('Erro de importação Thesa - '. $msg, 3);
                return $sx;
            }
            finally {

            }
            $sx = '</ul>';
            return $sx;

        }
}
