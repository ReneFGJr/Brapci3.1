<?php

namespace App\Models\MetricStudy;

use CodeIgniter\Model;

class Analyse extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'analyses';
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

    function index()
    {
        $Keywords = new \App\Models\Base\Keywords();
        $Authors = new \App\Models\Base\Authors();
        $Sections = new \App\Models\Base\Sections();
        $Sections = new \App\Models\Base\Sections();
        $SourceType = new \App\Models\Base\SourceType();
        $EMI_Year = new \App\Models\MetricStudy\Analyse\Year();
        $Work = new \App\Models\Base\Work();

        $sel = $Work->getWorkMark();
        $dd['works'] = count($sel);

        /* Index */
        $auth = array();
        $keys = array();
        $sect = array();
        $source = array();
        $year = array();


        foreach ($sel as $id => $selected) {
            if ($selected == 1) {
                if (substr($id, 0, 1) == 'w') {
                    $id = round(substr($id, 1, 20));
                    $keys = $Keywords->index_keys($keys, $id);
                    $auth = $Authors->index_auths($auth, $id);
                    $sect = $Sections->index_sections($sect, $id);
                    $source = $SourceType->index_sourcers($source, $id);
                    $year = $EMI_Year->totalizer($year, $id,'year.nm');
                }
            }
        }

        /********************************************************** TYPE */
        /******************************************************* AUTHORS */
        /******************************************************* SOURCES */
        /******************************************************* SUBJECT */
        $dd['header'] = load_grapho_script();
        $dd['authors_total'] = count($auth);
        $dd['keys_total'] = count($keys);
        $dd['sections_total'] = count($sect);
        $dd['types_total'] = count($source);

        ksort($year);
        $dd['year_production'] = graph($year, 'column', lang('brapci.DocumentYear'), 'DocumentYear', 30, 0);


        arsort($source);
        $dd['types_total'] = pie($source, 'pie', lang('brapci.DocumentType'), 'DocumentType', 10, 1);

        arsort($auth);
        $dd['authors_total'] = graph($auth, 'bar', lang('brapci.authors'), 'authTotal',10,1);

        $sx = view('MetricStudy/DrashBoard', $dd);


        $sx .= $this->table($auth,'auth');

        return $sx;
    }

    function table($data,$type='auth')
        {
            $limit = round(sqrt(count($data)));
            $ct = 0;

            $sx = '';
            $sx .= '<table class="table" style="width: 100%">';
            switch($type)
                {
                    case 'auth':
                        $sx .= '<tr>';
                        $sx .= '<th>' . lang('brapci.hash') . '</th>';
                        $sx .= '<th>'.lang('brapci.author').'</th>';
                        $sx .= '<th>' . lang('brapci.works') . '</th>';
                        $sx .= '<th>' . lang('brapci.works_percente') . '</th>';
                        $sx .= '<th>' . lang('brapci.works_acumulate') . '</th>';
                        $sx .= '<th>' . lang('brapci.works_acumulate_percente') . '</th>';
                        $sx .= '</tr>';
                }
            $totm = 0;
            foreach ($data as $name => $total)
                {
                    $totm = $totm + $total;
                }
            $sx .= 'Limit: ' . $limit . ' ' . lang('brapci.of') . ' ' . count($data);
            $sx .= ' - Total '.$totm;
            /******************************** */
            $totac = 0;
            foreach($data as $name=>$total)
                {
                    $ct++;
                    $totac = $totac + $total;
                    $name_full = substr($name,0,strpos($name,';'));
                    $sx .= '<tr>';
                    $sx .= '<td>'.($ct).'</td>';
                    $sx .= '<td>'.$name_full. '</td>';
                    $sx .= '<td class="text-center">' . $total . '</td>';
                    $sx .= '<td class="text-center">' . number_format($total / $totm * 100, 1, ',', '.') . '%</td>';
                    $sx .= '<td class="text-center">' . $totac . '</td>';
                    $sx .= '<td class="text-center">' . number_format($totac / $totm * 100, 1, ',', '.') . '%</td>';
                    $sx .= '</tr>';
                    if ($ct > $limit)
                        {
                            $total = ($totm - $totac);
                            $sx .= '<tr>';
                            $sx .= '<td></td>';
                            $sx .= '<td>' . lang('brapci.others') . '</td>';
                            $sx .= '<td class="text-center">' . $total . '</td>';
                            $sx .= '<td class="text-center">' . number_format($total / $totm * 100, 1, ',', '.') . '%</td>';
                            $sx .= '<td class="text-center">' . $totac . '</td>';
                            $sx .= '<td class="text-center">' . number_format(100, 1, ',', '.') . '%</td>';
                            $sx .= '</tr>';
                            break;
                        }
                }
            $sx .= '</table>';
            $sx = bs(bsc($sx));
            return $sx;
        }

    function a()
    {

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $sx .= '<p>' . $Work->show_reference($id) . '</p>';

            $keys = $Keywords->index_keys($keys, $id);
            $auth = $Authors->index_auths($auth, $id);
            $sect = $Sections->index_sections($sect, $id);
        }
        $key_index = $Indexshow->show_index($auth, 'authors');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($sect, 'sections');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($keys, 'keyword');
    }
}
