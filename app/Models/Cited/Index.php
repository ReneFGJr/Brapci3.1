<?php

namespace App\Models\Cited;

use CodeIgniter\Model;

class Index extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'brapci_cited.cited_article';
	protected $primaryKey           = 'id_ca';
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

    function process($txt,$ID)
        {
            $ln = explode(chr(13),$txt);
            pre($ln);
        }

	function show($id)
		{
			$dt = $this->where('ca_rdf',$id)->findAll();
		}

	function zera()
        {
            $sql = "update ".$this->table." set ca_status = 0, ca_tipo = 0 WHERE ca_status <> 0";
            $this->db->query($sql);
        }

    function citation_by_author($ida,$type='')
        {
                $rdf = new rdf;
                $dt = $rdf->le_data($ida);
                $ob = $rdf->extract_id($dt,'hasAuthor',$ida);
                return($ob);
        }

    function api_citation($suba)
        {
            $id = get("id");
            if ($id <= 0)
                {
                    return($this->api_brapci->error('id not informed'));
                }
            switch($suba)
                {
                    case 'author':
                        $this->load->model('ias');
                        $this->load->model('ias_cited');
                        $dt = $this->citation($id,'author');
                    break;

                    default:
                        $dt = $this->citation($id,'author');
                    break;
                }
            return($dt);
        }

    function citation_total($id)
        {
            $dt = $this
                ->select('count(*) as total')
                ->where('ca_rdf',$id)
                ->findAll();

            if (count($dt) == 0)
                {
                    $total = $dt[0]['total'];
                } else {
                    $total = 0;
                }
            $sx = '';
            $sx .= '<div class="btn btn-outline-primary mt-2" style="width: 100%;">';
            $sx .= '<table width="100%">';
            $sx .= '<tr><td>';
            if ($total == 0)
                {
                    $sx .= lang('brapci.no_citations');
                } else {
                    $sx .= $total;
                    $sx .= lang('brapci.citations');
                }

            $sx .= '</td><td>';
            $sx .= '</table>';
            $sx .= '</div>';
            return $sx;
        }

    function citation($id=0,$type='jnl')
        {
            $cp = 'ca_rdf, ca_year, ca_journal, cj_name_asc as cj_name, ';
            $cp .= 'ca_year_origem, ca_vol, ct_type, ca_tipo, ca_status, id_ca, ';
            $cp .= 'ca_nr, ca_pag, ';
            $cp .= ' "" as 1st, "" as 2nd, "" as 3th, ';
            $cp .= 'ca_text, jnl_name, jnl_name_abrev, ';
            $cp .= 'concat(\''.PATH.COLLECTION .'/v/'.'\',jnl_frbr) as jnl_url';
            //$cp = '*';
            switch($type)
                {
                    case 'author':
                    $pb = $this->citation_by_author($id);
                    $wh = '';
                    for ($r=0;$r < count($pb);$r++)
                        {
                            if (strlen($wh) > 0)
                                {
                                    $wh .= ' OR ';
                                }
                            $wh .= '(ca_rdf = '.$pb[$r].') ';
                        }
                        $sql = "select $cp from ".$this->table."
                            left join cited_journal ON ca_journal = id_cj
                            left join source_source ON ca_journal_origem = id_jnl
                            left join cited_type ON id_ct = ca_tipo
                            where ($wh) or (1=2)
                            order by ca_text, ca_year desc";
                    break;

                    default:
                    $sql = "select $cp from ".$this->baseCited."cited_article
                        inner join ".$this->baseCited."cited_journal ON ca_journal = id_cj
                        left join source_source ON ca_journal_origem = id_jnl
                        left join ".$this->baseCited."cited_type ON id_ct = ca_tipo
                        where cj_journal = $jnl
                        order by ca_year desc, ca_text";
                    break;
                }
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $apos = array('1st','2nd','3th');
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $a = array();
                    $auth = $this->add_authors($line['ca_text'],$a);
                    $id = 0;
                    foreach($auth as $name=>$total)
                        {
                            if (($id <= 2) and (strlen($name) > 0))
                                {
                                    $fld = $apos[$id];
                                    $line[$fld] = $name;
                                    $id++;
                                }
                        }
                    $rlt[$r] = $line;
                }
            return($rlt);
        }

   function refs_group($ob,$tp)
        {

            //$this->zera();
            $wh = '';
            for ($r=0;$r < count($ob);$r++)
                {
                    if (strlen($wh) > 0) { $wh .= ' or '; }
                    $wh .= '( ca_rdf = '.$ob[$r].') ';
                }

            $sql = "SELECT * FROM ".$this->base."cited_article where $wh ORDER BY ca_text";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            $sx = 'id,ca_journal_origem,ca_journal,ca_year,ca_year_origem,ref'.cr();
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    if (strlen($line['ca_text']) > 10)
                    {

                        $t  = $line['ca_text'];
                        $t = troca($t,chr(13),' ');
                        $t = troca($t,chr(10),' ');
                        $t = troca($t,'"','`');
                        $sx .= trim($line['ca_rdf']);
                        $sx .= ',';
                        $sx .= trim($line['ca_journal_origem']);
                        $sx .= ',';
                        $sx .= trim($line['ca_journal']);
                        $sx .= ',';
                        $sx .= trim($line['ca_year']);
                        $sx .= ',';
                        $sx .= trim($line['ca_year_origem']);
                        $sx .= ',';
                        $sx .= ',"'.trim($t).'"'.cr();
                    }
                }
            return($sx);
        }

    function show_ref($id)
        {
            $Socials = new \App\Models\Socials();
            $sx = '';
            $sql = "select * from ".$this->table.'
                    where ca_rdf = '.round($id).'
                    order by ca_ordem';
            $dt =
                $this
                    ->where("ca_rdf",round($id))
                    ->orderBy('ca_ordem')
                    ->findAll();

            if (count($dt) > 0)
            {
            $sx = '<a name="CITED"></a>';
            $sx .= '<h4>'.msg('References').'</h4>';
            $sx .= '<ul>';
            for ($r=0;$r < count($dt);$r++)
                {
                    $l = $dt[$r];
                    if ($Socials->getAccess("#ADM"))
                    {
                        $st = '';
                        if ($l['ca_tipo'] == 99) { $st = ' style="color: red;"'; }
                        $link = '<span onclick="newxy(\''.base_url(PATH.'ia/cited/ed/'.$l['id_ca']).'?nocab=true\',800,600);" style="cursor: pointer;">';
                        $linka = '</span>';
                        $txt = trim($l['ca_text']);
                        if (strlen($txt) == 0)
                            { $txt = msg('erro'); }
                        $sx .= '<li '.$st.'>'.$link.$txt.$linka;
                        $sx .= ' '.$this->cited_type($l);
                        $sx .= '</li>';
                    } else {
                        $sx .= '<li>'.$l['ca_text'];
                    }
                    $sx .= '</li>';
                }
            $sx .= '</ul>';
            }
            return($sx);
        }

        function export_citeds($id)
        {
            $file = 'c/'.$id.'/cited.';
            $sql = "
                    select * from ".$this->base."cited_article
                    where ca_rdf = $id
                    order by ca_ordem";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $tot = 0;
            $ref = "";
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $ref .= $line['ca_text'].cr();
                    $tot++;
                }

            file_put_contents($file.'nm',$ref);
            file_put_contents($file.'total',$tot);
        }

    function cited_type($l)
        {
            $AICited = new \App\Models\AI\Cited\Index();
            $type = $l['ca_tipo'];
            $status = $l['ca_status'];
            $id = $l['id_ca'];

            if (($type == 0) and ($status == 0))
            {
                $type = $AICited->neuro_type_source($l['ca_text'],$l['id_ca']);
                if ($type != 0)
                    {
                        $this->cited_type_update($id,1,$type);
                    }
            }
            $sx = $this->type($type);

            return($sx);
        }

    function type($type)
        {
            switch($type)
                {
                    case '1':
                    $sx = '<span class="type-journal radius5">&nbsp;'.msg('journal').'&nbsp;</span>';
                    break;

                    case '2':
                    $sx = '<span class="type-book radius5">&nbsp;'.msg('book').'&nbsp;</span>';
                    break;

                    case '3':
                    $sx = '<span class="type-bookchapter radius5">&nbsp;'.msg('book.cap').'&nbsp;</span>';
                    break;

                    case '5':
                    $sx = '<span class="type-proceeding radius5">&nbsp;'.msg('events').'&nbsp;</span>';
                    break;

                    case '7':
                    $sx = '<span class="type-these radius5">&nbsp;'.msg('these').'&nbsp;</span>';
                    break;

                    case '8':
                    $sx = '<span class="type-dissertation radius5">&nbsp;'.msg('dissertation').'&nbsp;</span>';
                    break;

                    case '9':
                    $sx = '<span class="type-tcc radius5">&nbsp;'.msg('TCC').'&nbsp;</span>';
                    break;

                    case '15':
                        $sx = '<span class="type-link radius5">&nbsp;'.msg('LINK').'&nbsp;</span>';
                        break;

                    case '20':
                    $sx = '<span class="type-law radius5">&nbsp;'.msg('LAW').'&nbsp;</span>';
                    break;

                    case '21':
                    $sx = '<span class="type-report radius5">&nbsp;'.msg('REPORT').'&nbsp;</span>';
                    break;

                    case '22':
                    $sx = '<span class="type-standard radius5">&nbsp;'.msg('STANDARD').'&nbsp;</span>';
                    break;

                    case '29':
                    $sx = '<span class="type-interview radius5">&nbsp;'.msg('INTERVIEW').'&nbsp;</span>';
                    break;

                    case '30':
                    $sx = '<span class="type-software radius5">&nbsp;'.msg('SOFTWARE').'&nbsp;</span>';
                    break;

                    case '31':
                    $sx = '<span class="type-patent radius5">&nbsp;'.msg('PATENT').'&nbsp;</span>';
                    break;

                    default:
                    $sx = '<span class="type-none radius5">&nbsp;'.msg('none').$type.'&nbsp;</span>';
                }
            return($sx);
        }

    function cited_type_update($id,$status,$type)
        {
            $date = date("Y-m-d");
            $sql = "update ".$this->base."cited_article
                    set ca_tipo = $type,
                    ca_status = $status,
                    ca_update_at = '$date'
                    where id_ca = $id";
            $this->db->query($sql);
            return(1);
        }

    function show_icone($id)
        {
            $sx = '';
            $file = 'c/'.$id.'/cited.total';
            if (file_exists(($file)))
            {
                $total = file_get_contents($file);
            $sx = '
            <div class="infobox" style="width: 100px;">
                <div class="infobox_name" style="background-color: #e0e0ff; float: left; width: 70%; padding: 0px 5px;">
                '.msg("Refs").'
                </div>
                <div class="infobox_version text-center" style="float: left; background-color: #e0ffe0; width: 30%; padding: 0px 2px;">
                <a href="#CITED">'.$total.'</a>
                </div>
            </div>
            ';
            }
            return($sx);
        }


    function save_ref($id)
        {
            $ref = get("dd1");
            $ref = $this->ias->to_line($ref);
            if (count($ref) > 0)
                {
                    $this->delete_ref($id);
                    for ($item=0;$item < count($ref);$item++)
                        {
                            $l = $ref[$item];
                            $this->save_ref_item($l,$id,$item+1);
                        }
                }
            $this->export_citeds($id);
            redirect(base_url(PATH.'/v/'.$id));
        }
    function delete_ref($id)
        {
            $sql = "delete from ".$this->base.'cited_article where ca_rdf = '.round($id);
            $this->db->query($sql);
        }
    function save_ref_item($l,$id,$item)
        {
            $sql = "insert into ".$this->base."cited_article ";
            $sql .= "(ca_rdf, ca_journal, ca_year,
                       	ca_vol, ca_nr, ca_pag,
                        ca_tipo, ca_text, ca_status,
                        ca_ordem)";
            $sql .= " values ";
            $sql .= "($id,0,0,
                        '','','',
                        0,'$l',0,
                        $item)";
            $rlt = $this->db->query($sql);
        }
    function add_authors($txt,$auth)
        {
            $a = $this->ias_cited->cited_analyse($txt);
            for ($r=0;$r < count($a);$r++)
                {
                    $w = $a[$r];
                    if (isset($auth[$w]))
                        {
                            $auth[$w] = $auth[$w] + 1;
                        } else {
                            $auth[$w] = 1;
                        }
                }
            return($auth);
        }
}