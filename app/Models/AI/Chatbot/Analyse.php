<?php

namespace App\Models\AI\Chatbot;

use CodeIgniter\Model;

class Analyse extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'brapci_chatbot.messages';
    protected $primaryKey           = 'id_m';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [];

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

    function analyse($t)
    {
        $VCterms = new \App\Models\AI\Skos\VCterms();
        $VClinks = new \App\Models\AI\Skos\VClinks();

        $t = $VCterms->prepare($t);
        $dd = array();

        /**************************************** Unitermos Termos */
        for ($i = 0; $i < count($t); $i++) {
            $ti = $t[$i];
            if (isset($terms[$ti])) {
                $id = $terms[$ti];
            } else {
                $id = $VCterms->term($ti);
                $terms[$ti] = $id;
            }
            array_push($dd, $id);
            $VClinks->where('lk_word_'.$i,$id);
        }
        $dt = $VClinks->findAll();
        echo $VClinks->getlastquery().'<br>';
        if (count($dt) > 0)
            {
                echo "XXXXXXXXXXXXX 1";
                pre($dt);
            }
    }

    function index()
    {
        $sx = '';
        $dt = $this
            ->select('m_message, count(*) as total')
            ->groupBy('m_message')
            ->findAll();
        $terms = array();
        for ($r = 0; $r < count($dt); $r++) {
            $msg = $dt[$r]['m_message'] . '<br>';
            $sx .= $msg.'<br>';
            $sx .= $this->analyse($msg);

            $sx .= 'Analysed ' . count($dt) . ' messages';
            $sx .= ' and ' . count($terms) . ' terms';

            return bs(bsc($sx, 12));
        }
    }
}
