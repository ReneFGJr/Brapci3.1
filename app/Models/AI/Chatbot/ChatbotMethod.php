<?php

namespace App\Models\AI\Chatbot;

use CodeIgniter\Model;

class ChatbotMethod extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = '*';
    protected $primaryKey           = 'id';
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

    function longQuestionError()
        {
            $sx = lang('brapci.chatbot.long_question');
            $sx = bsmessage($sx,2);
            echo $sx;
            exit;
        }

    function index($q = '')
    {
        /*********************** Long Question */
        if (strlen($q) > 100) {
            $this->longQuestionError();
        }

        /*********************** Prepare Terms */
        $VCTerms = new \App\Models\AI\Skos\VCterms();
        $Analyse = new \App\Models\AI\Chatbot\Analyse();
        $q = $VCTerms->prepare($q);

        echo "########## METHOD 0 - TERM PROCESS" . '<br>';
        $terms = $Analyse->chat($q);
        pre($terms);

        echo "########## METHOD 1 - INTRODUCE".'<br>';
//        $ChatbotMethod_Introduction = new \App\Models\AI\Chatbot\ChatbotMethod_Introduction();
//        $ChatbotMethod_Introduction->index($w);

        echo "########## METHOD 2" . '<br>';

    }

    function terms($t)
        {
            $dir = '../.tmp';
            dircheck($dir);
            $dir .= '/AI';
            dircheck($dir);
            $dir .= '/Terms';
            dircheck($dir);

            $hash = md5($t);
            $file = $dir.'/'.$hash.'.json';
            if (file_exists($file))
                {
                    $data = file_get_contents($file);
                    $data = json_decode($data, true);
                    $data['count']++;
                    $data['last'] = date("Ymd");
                    $data = json_encode($data);
                    file_put_contents($file, $data);
                } else {
                    $data = array();
                    $data['term'] = $t;
                    $data['count'] = 1;
                    $data['last'] = date("Ymd");
                    $data = json_encode($data);
                    file_put_contents($file, $data);
                }
        }
}
