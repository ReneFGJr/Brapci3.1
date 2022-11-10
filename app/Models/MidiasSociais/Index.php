<?php

namespace App\Models\MidiasSociais;

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

    function sharing($dt)
    {
        //pre($dt,false);
        $d['id_cc'] = $dt['id_cc'];
        $dt['http'] = 'https://cip.brapci.inf.br/benancib/v/'.$dt['id_cc'];
        $dt['doi'] = '';

        $title = $dt['Title'];
        $tt = '';
        foreach($title as $id=>$title)
            {
                $title = troca($title,chr(13),'');
                $title = troca($title,chr(10),'');
                $tt .= $title.'. ';
            }
        $dt['title'] = $tt;
        
        $sx = '<i>Compartilhe</i><br>';
        //$sx .= $this->twitter($dt);
        $sx .= $this->facebook($dt);
        $sx .= '<script>'.cr();
        $sx .= ' function newwin2(url) {  NewWindow=window.open(url,\'newwin\',\'scrollbars=yes,resizable=yes,width=690,height=450,top=10,left=10\');  NewWindow.focus(); void(0);}';
        $sx .= '</script>'.cr();
        return $sx;
    }
    
    function facebook($d) {
        $url = $d['http'];
        if ($d['doi'] != '')
            {
                $url .= ' DOI: '.$d['doi'];
            }
        $nm = $d['title'];
        $mn = troca($nm,'"','');
        $mn = troca($nm,"'",'');
        $nm = urlencode($nm);
        //https://www.facebook.com/dialog/share?app_id=140586622674265&display=popup&href=http%3A%2F%2Fseer.ufrgs.br%2Findex.php%2FEmQuestao%2Farticle%2Fview%2F56837%23.W3NwkU7trRU.facebook&picture=&title=Empoderamento%20das%20mulheres%20quilombolas%3A%20contribui%C3%A7%C3%B5es%20das%20pr%C3%A1ticas%20mediacionais%20desenvolvidas%20na%20Ci%C3%AAncia%20da%20Informa%C3%A7%C3%A3o&description=&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html
        $url = 'https://www.facebook.com/dialog/share?app_id=140586622674265'.'&display=popup&href=' . $url . '&picture=&title=Divulgação Científica: '.$nm;
        $link = '<span onclick="newwin2(\'' . $url . '\',800,400);" id="tw' . date("Ymdhis") . '" style="cursor: pointer;">';
        $link .= '<img src="' . base_url('img/nets/icone_facebook.png') . '" class="icone_nets">';
        $link .= '</span>' . cr();
        return ($link);
    }

    function twitter($d) {
        //echo '<pre>';
        //print_r($d);
        //echo '</pre>';
        $url = trim($d['http']);
        if ($d['doi'] != '')
            {
                $url .= ' DOI: '.$d['doi'];
            }

        $nm = $d['title'] . '. ' .  troca($d['authors'],'$','; ') . '. ' . $url . ' #BRAPCI';
        $nm = $url;
        $nm = urlencode($nm);
        $url = 'https://twitter.com/intent/tweet?url=' . $nm;
        $url .= '&text='.$d['title'];
        $link = '<span onclick="newwin2(\'' . $url . '\',800,400);" id="tw' . date("Ymdhis") . '" style="cursor: pointer;">';
        $link .= '<img src="' . base_url('img/nets/icone_twitter.png') . '" class="icone_nets">';
        $link .= '</span>' . cr();
        return ($link);
    }     
}
