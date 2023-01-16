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
                    case 'language':
                        $LANGUAGE = new \App\Models\AI\NLP\Language();
                        $sx .= $LANGUAGE->train();
                        break;
                    case 'book_sumary':
                        $API = new \App\Models\AI\NLP\Book\Sumary();;
                        $sx = $API->show_form();
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

            $menu['#' . lang('tools.languages')] = '';
            $menu[PATH . 'ai/nlp/language/train'] = lang('tools.language_train');
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
