<?php

namespace App\Models\AI\Skos;

use CodeIgniter\Model;

class VCterms extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'brapci_chatbot.vc_word';
    protected $primaryKey           = 'id_vc';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [
        'id_vc', 'vc_prefLabel'
    ];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    function prepare($q)
        {
            $q = mb_strtolower(ascii($q)) . ' ';
            $chars = array('!', '?', '.', ',', ';','-',':','(',')','[',']','{','}','"','\'','/','\\','|','_','+','=','*','&','^','%','$','#','@','~','`','<','>');
            for ($r = 0; $r < count($chars); $r++) {
                $q = troca($q, $chars[$r], ' ' . $chars[$r] . ' ');
            }
            $t = explode(' ', $q);
            $w = array();
            foreach($t as $id=>$word)
                {
                    if (trim($word) != '')
                        {
                            $w[] = $word;
                        }
                }
        return $w;
        }

    function terms_skos($t,$skos)
        {
            $VClinks = new \App\Models\AI\Skos\VClinks();
            $da = $this->terms($t);
            $da['lk_skos'] = $skos;
            $VClinks->link($da);
        }

    function terms($t)
        {
            $nr = 0;
            $ta = array();
            $t = $this->prepare($t);
            for ($r=0;$r < count($t);$r++)
                {
                    $ta['lk_word_'.$r] = $this->term($t[$r]);
                    $nr++;
                }
            return $ta;
        }

    function term($term)
    {
        /* Query */
        $dt = $this->select('id_vc')
            ->where('vc_prefLabel', $term)
            ->findAll();

        /* Result */
        if (count($dt) == 0) {
            $dd['vc_prefLabel']  = $term;
            $this->insert($dd);
            $id = $this->getInsertID();
        } else {
            $id = $dt[0]['id_vc'];
        }
        return $id;
    }
}
