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
        //$RDF->changePropriete('hasIssueProceeding', 'hasIssue');
        //$RDF->changePropriete('brapci:hasIssueProceeding', 'hasIssue');
        /************************************************* IssueProceeding */
        $RDFConcept = new \App\Models\Rdf\RDFConcept();
        $dt = $RDFConcept->countClass('IssueProceeding');
        if ($dt['total'] > 0)
            {
                $RDFConcept->changeClass('', 'Issue');
                $sx .= bsmessage('Trocado '.$dt['total'].' conceitos',3);
            } else {
                $sx .= bsmessage('Nenhuma classe identificada',1);
            }
        return $sx;
    }
}
