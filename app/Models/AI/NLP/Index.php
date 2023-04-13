<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
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

    function index($d1='',$d2='',$d3='')
        {
            $sx = h(lang('tools.NLP'));
            switch($d1)
                {
                    case 'bot_affiliations':
                        $Strings = new \App\Models\AI\NLP\Strings();
                        $sx .= $Strings->check_next('CorporateBody');
                        echo $sx;
                        break;
                    case 'bot_titles':
                        $Titles = new \App\Models\AI\NLP\Titles();
                        $sx .= $Titles->check_next();
                        echo $sx;
                        break;
                    case 'bot_abstracts':
                        $Abstracts = new \App\Models\AI\NLP\Abstracts();
                        $sx .= $Abstracts->check_next();
                        echo $sx;
                        break;
                    case 'language':
                        $LANGUAGE = new \App\Models\AI\NLP\Language();
                        $sx .= $LANGUAGE->train();
                        break;
                    case 'fulltext':
                        $API = new \App\Models\AI\NLP\Fulltext();;
                        $sx = $API->index($d1,$d2,$d3);
                        break;
                    case 'book_sumary':
                        $API = new \App\Models\AI\NLP\Book\Sumary();;
                        $sx = $API->show_form();
                        break;
                    case 'text_speech':
                        $API = new \App\Models\AI\NLP\TextToSpeech();;
                        $sx = $API->index($d1, $d2, $d3);
                        break;
                    case 'email':
                        $sx .= $this->email();
                        break;
                    default:
                        $sx .= $this->menu();
                        $sx .= $d1;
                        break;
                }
            return $sx;
        }

    function menu()
        {
            $menu = array();
            $menu['#'.lang('tools.text_tools')] = '';
            $menu[PATH.'tools/nlp/email'] = lang('tools.extract_email_from_text');
            $menu[PATH . 'tools/nlp/book_sumary'] = lang('tools.extract_book_sumary');
            $menu[PATH . 'tools/nlp/fulltext'] = lang('tools.extract_fulltext');


            $menu['#' . lang('tools.languages')] = '';
            $menu[PATH . 'ai/nlp/language/train'] = lang('tools.language_train');
            $menu[PATH . 'ai/synthesize'] = lang('tools.synthesize');
            return menu($menu);
        }

    function email()
        {
            $Forms = new \App\Models\AI\Forms();
            $AI = new \App\Models\AI\NLP\Scraping();
            $sx = '';
            $sx .= $Forms->textarea();

            $sa = '';
            $sb = '';

            $text = get("text");
            if ($text != '')
                {
                    $email_list = $AI->get_email($text);
                    for ($r=0;$r < count($email_list);$r++)
                        {
                            $email = $email_list[$r];
                            $sa .= $email.'<br>';
                        }
                    $sb = h(lang('tools.email_found'),2);
                    $sb .= h(lang('tools.total').': '.count($email_list),4);

                }
            $sx .= bs(bsc($sa,6,'mt-5').bsc($sb,6, 'mt-5'));
            return $sx;
        }
}
