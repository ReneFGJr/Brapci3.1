<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class IssuesWorks extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_issue_work';
    protected $primaryKey       = 'id_siw';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_siw', 'siw_journal', 'siw_issue', 'siw_author',
        'siw_title', 'siw_publish', 'siw_pag_ini', 'siw_pag_end',
        'siw_section', 'siw_work_rdf', 'update_at',
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

    function register($jnl,$issue,$work)
        {
            $dt = $this
                ->where('siw_journal',$jnl)
                ->where('siw_issue', $issue)
                ->where('siw_work_rdf', $work)
                ->first();
            if ($dt == null)
                {
                    $d = [];
                    $d['siw_journal'] = $jnl;
                    $d['siw_issue'] = $issue;
                    $d['siw_work_rdf'] = $work;
                    $this->set($d)->insert();
                }
        }

    function exclude($id)
    {
        $this->where('siw_work_rdf', $id)->delete();
        return true;
    }


    function issueWorks($id_rdf)
    {
        $dt = $this
            ->join('brapci_elastic.dataset', 'ID = siw_work_rdf')
            ->where('siw_issue', $id_rdf)
            ->orderBy('siw_order, siw_pag_ini')
            ->findAll();
        return $dt;
    }

    function show_issue_works($id_rdf)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $Mark = new \App\Models\Base\Mark();
        $Work = new \App\Models\Base\Work();
        $Keywords = new \App\Models\Base\Keywords();
        $Authors = new \App\Models\Base\Authors();
        $Sections = new \App\Models\Base\Sections();
        $Indexshow = new \App\Models\Base\IndexShow();

        $dt = $this->issueWorks($id_rdf);
        $sx = h(count($dt) . ' ' . lang('brapci.works'), 4, 'text-end');

        /******************************* */
        /* Index */
        $auth = array();
        $keys = array();
        $sect = array();

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            //$sx .= '<p>' . $Work->show_reference($line['siw_work_rdf']) . '</p>';
            $MK = $Mark->mark($line['siw_work_rdf']);
            $sx .= '<p>' . $MK . $RDF->c($line['siw_work_rdf']) . '</p>';

            $keys = $Keywords->index_keys($keys, $line['siw_work_rdf']);
            $auth = $Authors->index_auths($auth, $line['siw_work_rdf']);
            $sect = $Sections->index_sections($sect, $line['siw_work_rdf']);
        }

        $key_index = '';
        $key_index .= $Indexshow->show_index($auth, 'authors');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($sect, 'sections');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($keys, 'keyword');
        $sx = bs(
            bsc($key_index, 4, 'text_indexes') .
                bsc($sx, 8)
        );
        return $sx;
    }

    function saving($da)
    {
        $dt = $this->where('siw_work_rdf', $da['siw_work_rdf'])->findAll();
        if (count($dt) == 0) {
            $id = $this->insert($da);
            return 1;
        }
        return 0;
    }

    function getWorks($issue)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $Issues = new \App\Models\Base\Issues();
        $dt = $Issues->find($issue);

        if (isset($dt['is_source_issue'])) {
            $issue_rdf = $dt['is_source_issue'];
            $wk = $this->getWorksIssueRdf($issue_rdf);

            /* Não existe indexação */
            if (count($wk) == 0) {
                $this->check($dt);
                $wk = $this->getWorksIssueRdf($issue_rdf);

            }
        }
        return $wk;
    }

    function getWorksIssueRdf($id)
        {
        $wk = $this
            ->join('brapci_elastic.dataset', 'siw_work_rdf = ID')
            ->where('siw_issue', $id)
            ->orderby('siw_pag_ini, SESSION, siw_work_rdf')
            ->findAll();

        $wkk = [];

        $secs = [];
        $ct = -1;

        foreach($wk as $id=>$line)
            {
                $sec = $line['SESSION'];
                if (!isset($secs[$sec]))
                    {
                        $ct++;
                        $secs[$sec] = $ct;
                        $wkk[$ct]['name'] = $sec;
                        $wkk[$ct]['works'] = [];
                        $id = $ct;
                    } else {
                        $id = $secs[$sec];
                    }

                $wl = [];
                $json = (array)json_decode($line['json']);
                if (isset($json['Authors']))
                    {
                        $wl['ldl_authors'] = '';
                        foreach($json['Authors'] as $idc=>$name)
                            {
                                if ($wl['ldl_authors'] != '') {
                                    $wl['ldl_authors'] .= '; ';}
                                $wl['ldl_authors'] .= $name;
                            }
                    } else {
                        $wl['ldl_authors'] = '';
                    }
                /********************* TITULO */
                if (isset($json['Title'])) {
                    $json['Title'] = (array)$json['Title'];
                    if (isset($json['Title']['pt-BR']))
                        {
                            $wl['ldl_title'] = $json['Title']['pt-BR'];
                        } else {
                            if (isset($json['Title']['es'])) {
                                $wl['ldl_title'] = $json['Title']['es'];
                            } else {
                                if (isset($json['Title']['en'])) {
                                    $wl['ldl_title'] = $json['Title']['en'];
                                } else {
                                    foreach($json['Title'] as $lang=>$tit)
                                        {
                                            $wl['ldl_title'] = $tit;
                                            break;
                                        }
                                }
                            }
                        }

                } else {
                    $wl['ldl_title'] = '::sem título::';
                }
                $wl['siw_work_rdf'] = $line['siw_work_rdf'];
                $page = '';
                if ($line['siw_pag_ini'] != '')
                    {
                        $page = $line['siw_pag_ini'];
                    }
                if ($line['siw_pag_end'] != '') {
                    $page ='-'.$line['siw_pag_end'];
                }

                $wl['page'] = $page;

                array_push($wkk[$ct]['works'], $wl);


            }
        return $wkk;
        }

    function check($dd)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $idr = $dd['is_source_issue'];

        $dt = $RDF->le_data($idr);
        $dt = $dt['data'];

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $class = trim($line['c_class']);
            if ($class == 'hasIssue') {
                $da = array();
                $da['siw_work_rdf'] = $line['d_r2'];
                $da['siw_journal'] = $dd['is_source'];
                $da['siw_section'] = 0;
                $da['siw_issue'] = $idr;
                $this->saving($da);
            }

            if ($class == 'hasIssue') {
                $da = array();
                $da['siw_work_rdf'] = $line['d_r2'];
                $da['siw_journal'] = $dd['is_source'];
                $da['siw_section'] = 0;
                $da['siw_issue'] = $idr;
                $this->saving($da);
            }
        }
    }
}
