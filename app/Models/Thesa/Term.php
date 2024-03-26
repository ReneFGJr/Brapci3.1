<?php

namespace App\Models\Thesa;

use CodeIgniter\Model;

class Term extends Model
{
    protected $DBGroup          = 'thesa';
    protected $table            = 'thesa_terms';
    protected $primaryKey       = 'id_term';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_term','term_name','term_lang'
    ];

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

    public function getID($id)
    {
        $dt = $this
            ->where('id_term', $id)
            ->first();
        return $dt;
    }

    function add($term,$lang,$th)
        {
            $Language = new \App\Models\Thesa\Language();
            $TermTh = new \App\Models\Thesa\TermTh();

            $idL = $Language->getId($lang);

            $term = mb_strtolower($term);

            $ltr1 = substr($term,0,1);
            $ltr2 = substr(ascii($term),0,1);

            if ($ltr1 != $ltr2)
                {
                    $RSP = [];
                    $RSP['erro'] = 'Erro de conversão de caracter';
                    return $RSP;
                }

            $RSP = [];
            $dt = $this
                ->where('term_name',$term)
                ->where('term_lang', $idL)
                ->first();
            if ($dt == '')
                {
                    $term = UpperCase($ltr1) . substr($term,1,strlen($term));
                    $dd = [];
                    $dd['term_name'] = $term;
                    $dd['term_lang'] = $idL;
                    $id = $this->set($dd)->insert();
                    $RSP['term_name'] = $term;
                    $RSP['new'] = True;
                } else {
                    $id = $dt['id_term'];
                    $RSP['term_name'] = $dt['term_name'];
                    $RSP['new'] = False;
                }

            /**************************** Associate TH/TERM */
            $TermTh->asign($th,$id);
            $RSP['id'] = $id;
            return $RSP;
        }
}
