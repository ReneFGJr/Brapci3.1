<?php

namespace App\Models\AI\Chatbot;

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
        'vc_name','id_vc'
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
            $q = mb_strtolower($q) . ' ';
            $chars = array('!', '?', '.', ',', ';');
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

    function term($term)
    {
        /* Query */
        $dt = $this->select('id_vc')
            ->where('vc_name', $term)
            ->findAll();

        /* Result */
        if (count($dt) == 0) {
            $dd['vc_name']  = $term;
            $this->insert($dd);
            $id = $this->getInsertID();

        } else {
            $id = $dt[0]['id_vc'];
        }
        return $id;
    }
}
