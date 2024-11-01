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
            echo $sx;
            exit;
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
                    case 'clean':
                        $sx .= $this->clean();
                        break;
                    case 'thesa':
                        $sx .= $this->thesa();
                        break;
                    case 'Levenshtein':
                        $Levenshtein = new \App\Models\AI\NLP\Levenshtein();
                        $sx .= $Levenshtein->test();
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

    function thesa()
        {
            $sx = '';
            $sx .= h(lang('tools.clean_tools'),2);
            $Forms = new \App\Models\AI\Forms();
            $sx = '';
            $sa = '';
            $sa .= '<br>';
            if (get("thesa") == '') { $_POST['thesa']  = 8; }
            $sa .= 'Thesa ID:'.form_input('thesa',get("thesa")).'<br>';
            $sa .= form_checkbox('renew', '1') . ' ' . lang('tools.renew') . '<br>';
            $sa .= form_checkbox('clear', '1',get("clear")) . ' ' . lang('tools.clear_names') . '<br>';
            $sx .= bsc($Forms->textarea('', $sa), 8);

            $th = get("thesa");
            dircheck("../.tmp");
            dircheck("../.tmp/thesa");

            $file = "../.tmp/thesa/".strzero($th,6).'.txt';
            if ((!file_exists($file)) or (get("renew")))
                {
                    # Download
                    $url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/'.$th.'/csv';

                    $sx .= 'Download '.$url;

                    try {
                        $txt2 = file_get_contents($url);
                        $txt2 = utf8_encode($txt2);
                        file_put_contents($file,$txt2);
                    } catch (Exception $e) {
                        $sx .= msg('Exceção capturada: '.  $e->getMessage(),3);
                    }
                } else {
                    $txt2 = file_get_contents($file);
                }

            $txt = get("text");

            if ($txt != '')
                {
                    $VocabularyControled = new \App\Models\AI\NLP\VocabularyControled();
                    $rst = $VocabularyControled->text($txt,$txt2);
                    if (get("clear")=='1') {
                        $ln = explode(chr(13), $rst);
                        $rst = '';
                        foreach($ln as $id=>$line)
                            {
                                $lns = '';
                                $ins = [];
                                $ele = explode('[',$line);
                                foreach($ele as $id2=>$inst2)
                                    {
                                        if ($pos = strpos($inst2,']'))
                                            {
                                                $inst = substr($inst2,0,$pos);
                                                $ins[$inst] = 0;
                                            }
                                    }
                                foreach($ins as $name=>$if)
                                    {
                                        if ($lns != '') { $lns .= ';'; }
                                        $lns .= trim($name);
                                    }
                                if ($lns == '') { $lns = '[Sem instituição] - '.troca($line,chr(10),''); }
                                $rst .= $lns.chr(13);
                            }
                    }
                    $sx .= '<textarea class="full" rows=10>' . $rst . '</textarea>';
                }



            return $sx;
        }
    function clean()
        {
            $sx = '';
            $sx .= h(lang('tools.clean_tools'),2);
            $Forms = new \App\Models\AI\Forms();
            $sx = '';
            $sa = '';
            $sa .= '<br>';
            for ($r=1;$r <= 9;$r++)
                {
                    $sa .= form_checkbox('chk'.$r, '1', (get("chk".$r))) . ' ' . lang('tools.p'.$r) . '<br>';
                }

            $sx .= $Forms->textarea('',$sa);

            $txt = get("text");

            if (get("chk8")) {
                $sx .= '<li>' . lang('tools.p8') . ' - ' . date("H:i:s") . '</li>';
                $txt = mb_convert_encoding($txt, 'ISO-8859-1','UTF-8');
            }

            if (get("chk9")) {
                $sx .= '<li>' . lang('tools.p8') . ' - ' . date("H:i:s") . '</li>';
                $txt = mb_convert_encoding($txt, 'UTF-8', 'ISO-8859-1');
            }

            if (get("chk1"))
                {
                    $sx .= '<li>Removendo ' . lang('tools.p1') . ' - ' . date("H:i:s") . '</li>';
                    $Char = new \App\Models\AI\NLP\Charsets();
                    $txt = $Char->replace_char($txt,';',chr(13));
                }

            if (get("chk2")) {
                $sx .= '<li>Removendo '.lang('tools.p2').' - ' . date("H:i:s") . '</li>';
                $Char = new \App\Models\AI\NLP\Charsets();
                $txt = $Char->remove_space($txt);
            }

            if (get("chk7")) {
                $sx .= '<li>' . lang('tools.p7') . ' - ' . date("H:i:s") . '</li>';
                $Name = new \App\Models\AI\Person\Name();
                $txt = troca($txt,'"','');
                $txt = troca($txt, '(', '');
                $txt = troca($txt, ')', '');

                $txt = troca($txt, '[', '');
                $txt = troca($txt, ']', '');
            }

            if (get("chk3")) {
                $sx .= '<li>' . lang('tools.p3') . ' - ' . date("H:i:s") . '</li>';
                $Char = new \App\Models\AI\NLP\Charsets();
                $txt = $Char->groupBy($txt);
            }

            if (get("chk4")) {
                $sx .= '<li>' . lang('tools.p4') . ' - ' . date("H:i:s") . '</li>';
                $Char = new \App\Models\AI\NLP\Charsets();
                $txt = $Char->getTextLanguage($txt);
            }

            if (get("chk5")) {
                $sx .= '<li>' . lang('tools.p5') . ' - ' . date("H:i:s") . '</li>';
                $Genere = new \App\Models\AI\Person\Genere();
                $txt = $Genere->names($txt);
            }

            if (get("chk6")) {
                $sx .= '<li>' . lang('tools.p6') . ' - ' . date("H:i:s") . '</li>';
                $Name = new \App\Models\AI\Person\Name();
                $txt = $Name->nbr_author($txt);
            }

            $sb = '<hr>';
            $sb .= h(lang('tools.result'),4);
            $sb .= '<textarea class="full" rows=10>'.$txt.'</textarea>';

            $sx .= bsc($sb,12);

            return bs($sx);
        }
}
