<?php
/*
@category API
@package Brapci
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/brapci/services
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Brapci extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'finds';
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

    function index($d1,$d2,$d3)
        {
            header('Access-Control-Allow-Origin: *');
            $RSP = [];
            $RSP['status'] = '200';
            switch($d1)
                {
                    case 'get':
                        $RSP['result'] = $this->get($d2,$d3);
                        break;
                    case 'search':
                        $RSP['strategy'] = array_merge($_POST,$_GET);
                        $RSP['result'] = $this->search();
                        break;
                    default:
                        $RSP = $this->services($RSP);
                        $RSP['verb'] = $d1;
                        break;
                }
            echo json_encode($RSP);
            exit;
        }

        function get($v,$id=0)
            {
                $RDF = new \App\Models\Rdf\RDF();
                $dt = $RDF->le($id);

                $RSP = [];
                $RSP['id'] = $id;
                $RSP['title'] = '';
                $RSP['creator_author'] = [];
                $RSP['description'] = '';
                $RSP['resource_pdf'] = '';
                $RSP['resource_url'] = '';
                $RSP['section'] = [];
                $RSP['subject'] = [];
                $RSP['cover'] = '';

                $pg_ini = '';
                $pg_end = '';

                foreach($dt['data'] as $idx=>$desc)
                    {
                        $class = $desc['c_class'];
                        $vlr1 = $desc['n_name'];
                        $vlr2 = $desc['n_name2'];

                        $lk1 = $desc['d_r1'];
                        $lk2 = $desc['d_r2'];

                        $lang = troca($desc['n_lang'].$desc['n_lang2'],'-','_');
                        $vlr = trim($vlr1.$vlr2);

                        if ($lk2 == 0) { $lk2 = $lk1; }

                        switch($class)
                            {
                                case 'hasAbstract':
                                    $RSP['description'] = $vlr;
                                    break;
                                case 'hasTitle':
                                    $RSP['title'] = $vlr;
                                    break;
                                case 'hasUrl':
                                    $RSP['resource_url'] = $vlr;
                                    break;
                                case 'hasFileStorage':
                                    $RSP['resource_pdf'] = PATH. '/download/'.$id;
                                    break;
                                case 'hasPageStart':
                                    $pg_ini = $vlr;
                                    break;
                                case 'hasPageEnd':
                                    $pg_end = $vlr;
                                    break;
                                case 'publisher':
                                    $RSP['publisher'] = $vlr;
                                    break;
                                case 'isPubishIn':
                                    $journal = new \App\Models\Base\Sources();
                                    $dtj = $journal->where('jnl_frbr', $lk2)->first();
                                    $RSP['publisher'] = $vlr;
                                    $RSP['cover'] = URL . '/_repository/cover/cover_issue_'.strzero($dtj['id_jnl'],4).'.jpg';
                                    break;
                                case 'hasAuthor':
                                    $nome = nbr_author($vlr,7);
                                    array_push($RSP['creator_author'],['name'=>$nome,'id'=>$lk2]);
                                    break;
                                case 'hasSectionOf':
                                    $nome = nbr_title($vlr);
                                    array_push($RSP['section'], ['name' => $nome, 'id' => $lk2]);
                                    break;
                                case 'hasSubject':
                                    $nome = nbr_title($vlr);
                                    array_push($RSP['subject'], ['name' => $nome, 'id' => $lk2]);
                                    break;
                                default:
                                //echo '===>'.$class.'=='.$vlr.'<br>';
                            }

                    }

                if (($pg_ini.$pg_end) != '')
                    {
                        $pags = '';
                        if ($pg_ini != '')
                            {
                                $pags .= $pg_ini;
                            }
                        if ($pg_end != '')
                            {
                                $pags .= '-'.$pg_end;
                            }

                        $RSP['pagination'] = $pags;
                    }
                echo json_encode($RSP);
                exit;

            }

        function services($RSP)
            {
                $srv = [];
                $srv['livros'] = ['name' => 'Livros', 'link' => 'books', 'icone' => 'icone'];
                $RSP['services'] = $srv;
                return $RSP;
            }

        function search()
            {
                $term = get("q");
                if ($term != '')
                    {
                        $Elastic = new \App\Models\ElasticSearch\Search();
                        return $Elastic->searchFull($term);
                    } else {
                        return [];
                    }
            }
}
