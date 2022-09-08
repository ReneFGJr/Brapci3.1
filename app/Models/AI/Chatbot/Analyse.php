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

    function chat($q)
    {
        $VCterms = new \App\Models\AI\Skos\VCterms();
        $VCconcept = new \App\Models\AI\Skos\VCconcepts();

        $phrases = array();

        /********* Processe */
        $ri = 0;
        $loop = 0;

        while ($ri <= count($q)) {
            $word = '';
            $phrase = '';

            for ($r = $ri; $r < count($q); $r++) {
                $loop++;
                if ($loop > 150) {
                    echo bsmessage("OPS - LOOP OR TOO LONG", 3);
                    exit;
                }
                if (strlen($word) > 0) {
                    $word .= '_';
                }
                $word .= $q[$r];
                $dir = $VCconcept->directory($word, false);
                $file = $dir .= 'class.json';
                if (file_exists($file)) {
                    //echo '<span style="color: green">OK - ' . $file . '</span>';
                    $phrase = $word;
                    $ri = $r + 1;
                } else {
                    //echo "NO - " . $file;
                    //echo "OPS";
                }
            }
            if ($phrase == '') {
                $ri++;
            } else {
                array_push($phrases, $phrase);
            }
        }
        return ($phrases);
    }

    function analyse($t)
    {
        $sx = '';
        $VCterms = new \App\Models\AI\Skos\VCterms();
        $VClinks = new \App\Models\AI\Skos\VClinks();
        $sx .= h($t);
        $t = $VCterms->prepare($t);
        $sx .= $this->chat($t);
        return $sx;
    }

    function user_answers()
    {
        $VCterms = new \App\Models\AI\Skos\VCterms();
        $sx = '';
        $dt = $this
            ->select('m_message, count(*) as total')
            ->groupBy('m_message')
            ->findAll();
        $terms = array();
        for ($r = 0; $r < count($dt); $r++) {
            $msg = $dt[$r]['m_message'] . '<br>';

            $q = $VCterms->prepare($msg);
            $ph = $this->chat($q);

            $sx .= 'Analysed <b>' . $msg . '</b> messages';
            $sx .= ' and ' . count($ph) . ' terms';

            $sx .= '<pre>';
            $sx .= print_r($ph, true);
            $sx .= '</pre>';

        }
        return bs(bsc($sx, 12));
    }
}
