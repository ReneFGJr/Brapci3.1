<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Issues extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_issue';
    protected $primaryKey       = 'id_is';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_is',
        'is_source',
        'is_year',

        'is_vol',
        'is_vol_roman',

        'is_card',

        'is_nr',
        'is_place',
        'is_thema',

        'is_cover',
        'is_url_oai',

        'is_works',
        'is_source_issue',
        'is_oai_update',
        'is_oai_token',
    ];

    protected $typeFields    = [
        'hidden', 'sql:id_jnl:jnl_name:source_source order by jnl_name*', 'year*',
        'hidden', 'string', 'string',
        'string',
        '[1-199]', 'string', 'text',
        'string*', 'hidden', 'string',
        'hidden', 'string*', 'hidden',
        'hidden', 'hidden'
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
    var $path = '';
    var $path_back = '';
    var $id = 0;

    function index($act, $id)
    {
        $sx = '';
        switch ($act) {
            case 'listidentifiers':
                $jissue = get("id");
                if ($jissue != '') {
                    $sx .= $this->listidentifiers($jissue);
                } else {
                    $sx .= bsmessage('Error - No issue selected');
                }
                break;
            case 'edit':
                $jid = get("jid");
                if ($jid != 0) {
                    $_POST['is_source'] = $jid;
                }
                $sx = $this->edit($id);
                break;
            case 'harvesting':
                $id = get("id");
                if ($id > 0) {
                    $sx .= bsc($this->issue($id), 12);
                    $sx .= $this->harvesting($id);
                } else {
                    $sx .= bsmessage('ERRO: 580 - id not found', 3);
                }
                break;
            default:
                $id = get("id");
                $sx .= bsc($this->issue($id, true), 12);
                $sx .= '<hr>';
                $sx .= bsc($this->issue_section_works($id), 12);
                break;
        }
        return $sx;
    }

    function check_issues()
    {
        $sx = '';
        /************************************************* IssueProceeding */
        $RDFConcept = new \App\Models\Rdf\RDFConcept();
        $RDFData = new \App\Models\Rdf\RDFData();
        $dt = $RDFConcept->countClass('IssueProceeding');
        if ($dt['total'] > 0)
            {
                $RDFConcept->changeClass('IssueProceeding', 'Issue');
                $sx .= bsmessage('Trocado '.$dt['total'].' conceitos',3);
            } else {
                $sx .= bsmessage('Nenhuma classe identificada',1);
            }

        /************************************************* hasIssueProceeding */
        $ge = ['hasIssueOf', 'hasIssueProceeding', 'hasIssueProceedingOf'];
        foreach($ge as $prop)
            {
                $dt = $RDFData->countProp($prop);
                if ($dt['total'] > 0) {
                    $RDFData->changeProp($prop, 'hasIssue');
                    $sx .= bsmessage('Trocado ' . $dt['total'] . ' conceitos', 3);
                } else {
                    $sx .= bsmessage('Nenhuma propriedade <b>'.$prop. '</b> identificada', 1);
                }
            }

        /************************************************* Criar os Issue */
        $sx .= $this->checkIssues();

        return $sx;
    }

    function checkIssues()
        {
            $sx = '';
            $RDFConcept = new \App\Models\Rdf\RDFConcept();
            $RDF = new \App\Models\Rdf\RDF();
            $Class = 'Issue';
            $c1 = $RDF->getClass($Class);

            $dt = $RDFConcept
                ->select('id_cc, is_source_issue')
                ->join('source_issue', 'is_source_issue = id_cc','LEFT')
                ->where('cc_class',$c1)
                ->where('is_source_issue is NULL')
                ->findAll(10);
            foreach($dt as $id=>$line)
                {
                    $RSP = $this->getIssue($line['id_cc']);
                }
            if (count($dt) == 0)
                {
                    $sx .= bsmessage("Nenhum Issue Encontrado para cadastro",1);
                }
            return $sx;
        }

        function getIssue($id)
            {
                $sx = '';
                $RDF = new \App\Models\Rdf\RDF();
                $dt = $RDF->le($id);
                if ($dt['concept']['c_class'] == 'Issue')
                    {

                        $da = $this->getDadosIssue($dt);
                        $da['is_source_issue'] = $id;
                        $this->register($da);
                        $sx .= bsmessage("REGISTRADO ISSUE - $id");
                    } else {
                        /************************** ERRO DE ISSUE */
                        $sx .= bsmessage("ERRO DE CLASSO DE ISSUE - $id");
                    }
                return $sx;
            }

        function register($dt)
            {
                $this->check($dt);
                $dt = $this
                    ->set($dt)
                    ->where('is_source_issue',$dt['is_source_issue'])
                    ->first();
                if ($dt == '')
                    {
                        $this->set($dt)->insert();
                    } else {
                        $this
                            ->set($dt)
                            ->where('is_source_issue', $dt['is_source_issue'])
                            ->update();
                    }
                return "";
            }

        function check($dt)
            {
                $ck = ['is_year', 'is_source_issue', 'is_source'];
                foreach($ck as $fld)
                    {
                        if (!isset($dt[$fld])) {
                            echo "ERRO - is_source_issue não informado<br>";
                            pre($dt);
                            exit;
                        }

                    }
                return true;
            }

        function getDadosIssue($dt)
            {
                $RDF = new \App\Models\Rdf\RDF();
                $Metadata = new \App\Models\Base\Metadata();
                $prop = $dt['data'];
                $RSP = [];
                $w = [];
                foreach($prop as $id=>$line)
                    {
                        $prop = trim($line['c_class']);
                        $dd1 = $line['d_r1'];
                        $dd2 = $line['d_r2'];
                        $vv1 = $line['n_name'];
                        $vv2 = $line['n_name2'];
                        $lg1 = $line['n_lang'];
                        $lg2 = $line['n_lang2'];
                        //echo '<br>'.$prop . ' '.$dd1.' '.$dd2.' '.$vv1.' '. $vv2;
                        switch($prop)
                            {
                                case 'hasIssue':
                                    array_push($w,$dd2);
                                    break;
                                case 'hasPlace':
                                    $RSP['is_place'] = $vv2;
                                    break;
                                case 'hasPublicationNumber':
                                    $RSP['is_nr'] = $vv2;
                                    break;
                                case 'hasPublicationVolume':
                                    $RSP['is_vol'] = $vv2;
                                    break;
                                case 'dateOfPublication':
                                    $RSP['is_year'] = $vv2;
                                    break;
                            }
                    }

                    if ((!isset($RSP['is_source'])) and (count($w) > 0))
                        {
                            foreach($w as $idx=>$work)
                                {
                                    $da = $RDF->le($work);
                                    $dt = $Metadata->metadata($da);
                                    if (isset($dt['id_jnl']))
                                        {
                                            $RSP['is_source'] = $dt['id_jnl'];
                                            break;
                                        }
                                }
                        }
                return $RSP;
            }
}


/*
   function metadata_issue($id_issue, $loop = 0)
    {
        $Issue = new \App\Models\Base\Issues();
        $dt = $Issue->where('is_source_issue', $id_issue)->first();
        if ($dt != '') {
            $d['ID'] = $dt['is_source_issue'];
            $d['YEAR'] = $dt['is_year'];
            $d['VOL'] = $dt['is_vol'];
            $d['VOLR'] = $dt['is_vol_roman'];
            $d['NR'] = $dt['is_nr'];
            $d['PLACE'] = $dt['is_place'];
            $d['JOURNAL'] = $dt['is_source'];
            return ($d);
        } else {

            $dti = $Issue->getIssue($id_issue);

            if ($dti['ISSUE'] <= 0) {
                $RDF = new \App\Models\Rdf\RDF();
                $RDFdata = new \App\Models\Rdf\RDFData();
                $dt = $RDF->le($id);
                echo "<br>----METADATA ISSUE - NOT FOUND - $id<br>";

                if ($dt['concept']['c_class'] != 'Issue') {
                    echo "OOOOO";
                    $RDFdata->check_issue();
                    echo "<br>CLASSE INVÀLIDA PARA ISSUE<hr>";
                }

                $Is = $RDF->extract($dt, 'hasIssue');
                echo h('US=>' . $Is[0]);
                $dti = $Issue->getIssue($Is[0]);
                pre($dti);
            }

            $ISSUE = new \App\Models\Base\Issues();
            $ISSUE->register_issue($dti);
            /*************** REGISTRAR ISSUE */
            /*
            if ($loop == 0) {
                //$dt = $this->metadata_issue($id_issue);
            } else {
                echo "#######PROB";
                echo h($id_issue);
                exit;
            }
            return ($dt);
        }
    }
    */