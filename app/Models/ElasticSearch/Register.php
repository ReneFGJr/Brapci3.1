<?php

namespace App\Models\ElasticSearch;

use CodeIgniter\Model;

class Register extends Model
{
    protected $DBGroup          = 'elastic';
    protected $table            = 'dataset';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ds', 'ID', 'json', 'CLASS','COVER', 'COLLECTION',
        'JOURNAL', 'ISSUE', 'YEAR', 'KEYWORD', 'ABSTRACT', 'KEYWORDS','ABSTRACTS',
        'PDF', 'updated_at', 'status', 'AUTHORS', 'TITLE', 'SESSION', 'PUBLICATION',
        'LEGEND','new','use','URL',
        'KEYWORD_EN',
        'KEYWORD_ES',
        'KEYWORD_FR',
        'OAI_ID','DOI'
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

    public function update_index()
    {
        $Source = new \App\Models\Base\Sources();
        $API    = new \App\Models\ElasticSearch\API();

        $type   = 'prod';
        $limit  = 100;
        $offset = (int) (get('offset') ?? 0);

        // Builder base com os MESMOS filtros usados na busca
        $base = $this->where('new', 1)->where('use', 0);

        // Total com os mesmos filtros
        // Em CI4, countAllResults($reset=false) evita resetar o builder
        $dtt = $base->countAllResults(false);

        // Busca paginada e ordenada
        $dta = $base->orderBy('ID', 'DESC')->findAll($limit, $offset);

        // Cabeçalho de status
        $percent = ($dtt > 0) ? ($offset / max($dtt, 1) * 100) : 100;
        $sx  = 'Export ElasticSearch v2.2 - ' . $offset . ' of ' . $dtt;
        $sx .= ' (' . number_format($percent, 1) . '%)';
        $sx .= '<hr>';

        // Helpers internos
        $norm = static function ($s) {
            $s = is_string($s) ? trim($s) : '';
            if ($s === '') return '';
            return mb_strtolower(ascii($s));
        };

        $collect = static function (array $DT, string $field) use ($norm): array {
            $out = [];
            $bag = (array)($DT[$field] ?? []);
            foreach ($bag as $lang => $items) {
                foreach ((array)$items as $term) {
                    $t = $norm($term);
                    if ($t !== '') {
                        $out[] = $t;
                    }
                }
            }
            return $out;
        };

        $collectAuthors = static function (array $DT, &$full) use ($norm): array {
            $authors = [];
            $seen = [];
            $bag = (array)($DT['Authors'] ?? []);
            foreach ($bag as $group) {
                foreach ((array)$group as $raw) {
                    $raw = (string)$raw;
                    $rawAscii = ascii($raw);
                    $key = $norm($rawAscii);
                    if ($key === '' || isset($seen[$key])) {
                        continue;
                    }

                    // manter no texto completo (formas ascii e normalizada)
                    $full .= $rawAscii . ' ' . $key . ' ';

                    // Padroniza exibição do autor
                    $display = UpperCase($rawAscii);
                    $display = nbr_author($display, 7);
                    $authors[] = $display;

                    $seen[$key] = true;
                }
            }
            return $authors;
        };

        foreach ($dta as $line) {
            $DT   = (array) json_decode($line['json'], true);
            $full = '';

            // TITLE
            $atit = $collect($DT, 'Title');
            foreach ($atit as $t) {
                $full .= $t . ' ';
            }

            // SECTIONS
            $asec = $collect($DT, 'Sections');

            // KEYWORDS
            $akey = $collect($DT, 'Subject');
            foreach ($akey as $k) {
                $full .= $k . ' ';
            }

            // ABSTRACT
            $aabs = $collect($DT, 'Abstract');
            foreach ($aabs as $a) {
                $full .= $a . ' ';
            }

            // AUTHORS (dedup + normalização + adiciona no full)
            $aaut = $collectAuthors($DT, $full);

            // Montagem do documento
            $dt = [
                'id'       => (int)$line['ID'],
                'keyword'  => $akey,
                'abstract' => $aabs,
                'authors'  => $aaut,
                'title'    => $atit,
                'journal'  => $line['JOURNAL'] ?? '',
                'year'     => $line['YEAR'] ?? '',
                'type'     => $line['CLASS'] ?? '',
                'section'  => $asec,
                'language' => $DT['Idioma'] ?? [],
                'full'     => trim($full),
            ];

            // Collection (Issue->id_jnl -> collection) + mapeamento por tipo
            $dt['collection'] = 'ER'; // default
            if (!empty($DT['Issue'])) {
                $Issue = (array)$DT['Issue'];
                if (!empty($Issue['id_jnl'])) {
                    $DTS = $Source->find((int)$Issue['id_jnl']);
                    if (!empty($DTS['jnl_collection'])) {
                        $dt['collection'] = $DTS['jnl_collection'];
                    }
                }
            }

            switch ($line['CLASS'] ?? '') {
                case 'Book':
                    $dt['collection'] = 'BK';
                    break;
                case 'BookChapter':
                    $dt['collection'] = 'BC';
                    break;
                case 'Proceeding':
                    $dt['collection'] = 'EV';
                    break;
            }

            // DOI / URL
            if (!empty($DT['DOI'])) {
                $doi = (string)$DT['DOI'];
                // Se não vier com http, presume DOI e monta link resolvido
                $href = (stripos($doi, 'http') === 0) ? $doi : ('https://doi.org/' . ltrim($doi, '/'));
                $dt['DOI'] = $doi;
                $dt['URL'] = '<a href="' . $href . '" target="_blank">' . $href . '</a>';
            } else {
                $dt['DOI'] = '';
                $dt['URL'] = 'https://hdl.handle.net/20.500.11959/brapci/' . $dt['id'];
            }

            // Envio ao ES
            // Servidor 2
            $API->server = 'http://143.54.112.91:9200/';
            $rst = $API->call('brapci3.3/' . $type . '/' . $dt['id'], 'POST', $dt);

            $sx .= $dt['id'] . ' => ' . ($rst['result'] ?? '-') .
                ' v.' . ($rst['_version'] ?? '-') .
                ' (' . $dt['collection'] . ')<br>';

            // Marca como exportado / atualiza flags
            $this->exported($dt['id'], 0);
            $this->set(['new' => 0])->where('id_ds', $line['id_ds'])->update();
        }

        // Próxima página ou fim
        if (count($dta) === $limit) {
            $sx .= metarefresh(PATH . '/elasticsearch/update_index?offset=' . ($offset + $limit), 1);
        } else {
            $sx = bsmessage('Elastic Search Exported', 1);
        }

        return bs(bsc($sx, 12));
    }


    function update_index_old()
    {
        $full = '';

        /****************************** Biblioteca */
        $Source = new \App\Models\Base\Sources();
        $JNL = $Source->getCollections();

        $limit = 500;
        $limit = 100;
        $offset = get('offset');
        if ($offset == '') {
            $offset = 0;
        }
        $dtt = $this->countAllResults();
        //$dtt = $this->countAllNews();

        $dta = $this
            ->where('new',1)
            ->where('use',0)
            ->orderBy('ID desc')
            ->FindAll($limit);

        $type = 'prod';


        $API = new \App\Models\ElasticSearch\API();
        $sx = 'Export ElasticSearch v2.2 - ';
        $sx .= $offset . ' of ' . $dtt;
        if ($dtt > 0) {
            $percent = ($offset / $dtt * 100);
        } else {
            $percent = 100;
        }

        $sx .= ' (' . number_format($percent, 1) . '%)';
        $sx .= '<hr>';

        foreach ($dta as $id => $line) {
            $dt = [];
            $DT = json_decode($line['json']);
            $full = '';

            $abs = '';
            $key = '';
            $aut = '';
            $tit = '';
            $aaut = [];
            $akey = [];
            $atit = [];
            $aabs = [];
            $asec = [];


            $DT = (array)$DT;

            /*********************************************** TITLE */
            if (isset($DT['Title'])) {
                $keys = (array)$DT['Title'];
                foreach ($keys as $lang => $ks) {
                    $ks = (array)$ks;
                    foreach ($ks as $idk => $term) {
                        if (trim($term) != '') {
                            array_push($atit, mb_strtolower(ascii($term)));
                            $full .= mb_strtolower(ascii($term)) . ' ';
                        }
                    }
                }
            }

            /*********************************************** SECTIONS */
            if (isset($DT['Sections'])) {
                $keys = (array)$DT['Sections'];
                foreach ($keys as $lang => $ks) {
                    $ks = (array)$ks;
                    foreach ($ks as $idk => $term) {
                        if (trim($term) != '') {
                            array_push($asec, trim(mb_strtolower(ascii($term))));
                        }
                    }
                }
            }

            /*********************************************** Authors */
            $idaa = [];
            if (isset($DT['Authors'])) {
                $keys = (array)$DT['Authors'];
                foreach ($keys as $id => $ks) {
                    $ks = (array)$ks;
                    foreach ($ks as $idk => $term) {
                        if (!isset($idaa[$term])) {
                            if (trim($term) != '') {
                                $term = ascii($term);
                                $full .= $term.' ';
                                $term = UpperCase($term);
                                $term = nbr_author($term,7);
                                array_push($aaut, $term);
                                $full .= ' '. mb_strtolower(ascii($term)) . ' ';
                            }
                            $idaa[$idk] = 1;
                        }
                    }
                }
            }

            /*********************************************** KEYWORDS */
            if (isset($DT['Subject'])) {
                $keys = (array)$DT['Subject'];
                foreach ($keys as $lang => $ks) {
                    $ks = (array)$ks;
                    foreach ($ks as $idk=>$term) {
                        if (trim($term) != '') {
                            array_push($akey, mb_strtolower(ascii($term)));
                            $full .= mb_strtolower(ascii($term)) . ' ';
                        }
                    }
                }
            }

            /*********************************************** ABSTRACT */
            if (isset($DT['Abstract'])) {
                $keys = (array)$DT['Abstract'];
                foreach ($keys as $lang => $ks) {
                    $ks = (array)$ks;
                    foreach ($ks as $idk => $term) {
                        if (trim($term) != '') {
                            array_push($aabs, mb_strtolower(ascii($term)));
                            $full .= mb_strtolower(ascii($term)) . ' ';
                        }
                    }
                }
            }

            $dt['id'] = $line['ID'];
            $dt['full'] = '';
            $dt['full'] = strip_tags($dt['full']);
            $dt['keyword'] = $akey;
            $dt['abstract'] = $aabs;
            $dt['authors'] = $aaut;
            $dt['title'] = $atit;
            $dt['journal'] = $line['JOURNAL'];


            if (isset($DT['Issue'])) {
                $Issue = (array)$DT['Issue'];
                $idj = $Issue['id_jnl'];
                $DTS = $Source->find($idj);

                if (isset($DTS['jnl_collection'])) {
                    $dt['collection'] = $DTS['jnl_collection'];
                } else {
                    $dt['collection'] = 'ER';
                }
            }

            switch ($line['CLASS'])
                {
                    case 'Book':
                        $dt['collection'] = 'BK';
                        break;
                    case 'BookChapter':
                        $dt['collection'] = 'BC';
                        break;
                    case 'Proceeding':
                        $dt['collection'] = 'EV';
                        break;
                }
            /**************************************************** */
            //$dt['collection']

            $dt['year'] = $line['YEAR'];
            $dt['type'] = $line['CLASS'];


            if (isset($DT['Idioma'])) {
                $dt['language'] = $DT['Idioma'];
            } else {
                $dt['language'] = [];
            }

            $dt['section'] = $asec;
            if (isset($DT['DOI'])) {
                $dt['DOI'] = $DT['DOI'];
                $dt['URL'] = '<a href="' . $dt['DOI'] . '" target="_blank">' . $dt['DOI'] . '</a>';
            } else {
                $dt['DOI'] = '';
                $dt['URL'] = 'https://hdl.handle.net/20.500.11959/brapci/' . $dt['id'];
            }

            $dt['full'] = $full;

            $id = $dt['id'];
            //$rst = $API->call('brapci3.3/' . $type . '/' . $id, 'POST', $dt);

            /* Second Server */
            $API->server = 'http://143.54.112.91:9200/';
            $rst = $API->call('brapci3.3/' . $type . '/' . $id, 'POST', $dt);

            $sx .= $id .= ' => ' .
                $rst['result'] . ' v.' .
                $rst['_version'] .
                ' (' . $dt['collection'] . ')<br>';
            $this->exported($id,0);
            $dq = [];
            $dq['new'] = 0;
            $this->set($dq)->where('id_ds',$line['id_ds'])->update();
        }

        /****************************************************************************** LOOP */
        if (count($dta) == $limit) {
            $sx .= metarefresh(PATH . '/elasticsearch/update_index?offset=' . ($offset + $limit), 1);
        } else {
            $sx = bsmessage('Elastic Search Exported', 1);
        }
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    function getWorksSource($ID)
        {
            $dt = $this->select("ID")->where('JOURNAL ', $ID)->findAll();
            return $dt;
        }
    function exported($id,$new)
        {
            $dta = [];
            $dta['new'] = $new;
            $this->set($dta)->where('ID', $id)->update();
        }

    function set_status($id, $dta)
    {
        $dt = $this->where('ID', $id)->first();
        if ($dt != '') {
            $this->set($dta)->where('ID', $id)->update();
        }
    }

    function show($id)
    {
        $sx = '';
        $dt = $this->where('ID', $id)->first();
        if ($dt == '') {
            return '';
        }
        $dt['json'] = 'JSON';
        $sx .= '<ul>';
        foreach ($dt as $name => $v) {
            $sx .= '<li>';
            $sx .= '(' . $name . ') = "' . $v . '"';
            $sx .= '</li>';
        }

        if ($dt['status'] >= 0) {
            $sx .= '<a href="' . PATH . '/v/' . $id . '?reindex=1">Reindex</a>';
            $sx .= '</ul>';
            if (get("reindex") == '1') {
                $d = [];
                $d['status'] = -1;
                $this->set($d)->where('ID', $id)->update();

                $BOTS = new \App\Models\Bots\Index();
                $BOTS->task('EXPORT_SELECTED');
            }
        }

        return bs(bsc($sx, 12));
    }

    function whithout_year()
    {
        $sx = '';
        $sx .= '<a href="' . PATH . 'admin/dataset/year_without/?t=-1" title="Reprocessar todos os 0">';
        $sx .= '<a href="' . PATH . 'admin/dataset/year_without/?t=0" title="Volta todos os 0">';
        $sx .= bsicone('reload');
        $sx .= '</a>';
        $sx .= '<ul>';

        /******************************* Reprocessar */
        if (get("t") != "") {
            $BOTS = new \App\Models\Bots\Index();
            $BOTS->task('EXPORT_SELECTED');
            $d['status'] = -1;
            $this->set($d)
                ->where('YEAR > 9000')
                ->Orwhere('YEAR < 1950')
                ->update();
            return metarefresh(PATH . 'admin/dataset/year_without');
            exit;
        }
        $dt = $this
            ->where('YEAR > 2050')
            ->Orwhere('YEAR < 1950')
            ->orderBy('YEAR, ID')->findAll();

        $sx .= h('Total ' . count($dt), 5);
        foreach ($dt as $k => $line) {
            $da = (array)json_decode($line['json']);
            $sx .= '<li>';
            $link = '<a href="' . PATH . '/v/' . $line['ID'] . '" target="_blank">';
            $linka = '</a>';
            $sx .= $link . $line['ID'] . ' (' . $line['YEAR'] . ')' . $linka;
            if ($line['status'] == -1) {
                $sx .= '*';
            }
            $sx .= '</li>';
        }
        $sx .= '</ul>';
        return $sx;
    }


    function resume()
    {
        $tot = 0;
        $sx = h(lang('brapci.ElasticSearch'), 4);
        $sa = '';

        $dt = $this
            ->select('count(*) as total, CLASS')
            ->groupBy('CLASS')
            ->findAll();

        $sa .= '<ul style="font-size: 0.7em;">';
        foreach ($dt as $line) {
            $sa .= '<li>' . lang('brapci.' . $line['CLASS']) . ' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
            $tot = $tot + $line['total'];
        }
        $sa .= '</ul>';
        /********* Total */
        $sx .= '<b style="font-size: 0.7em;">Total ' . number_format($tot, 0, ',', '.') . '</b>';
        /********* Result (alterar ordem) */
        $sx .= $sa;

        $sx .= '<ul style="font-size: 0.7em;">';

        /***************************************** PDF */
        $dt = $this
            ->select('count(*) as total, pdf')
            ->where('pdf', 0)
            ->groupBy('pdf')
            ->findAll();

        foreach ($dt as $line) {
            $link = '<a href="' . PATH . '/admin/dataset/erros/pdf' . '">';
            $linka = '</a>';
            $sx .= '<li>' . $link . lang('brapci.pdf.' . $line['pdf']) . $linka . ' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
        }


        /***************************************** KEYWORDS */
        $dt = $this
            ->select('count(*) as total, KEYWORD')
            ->where('KEYWORD is NULL')
            ->groupBy('KEYWORD')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.keywords_without') . ' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
        }

        /***************************************** ABSTRACT */
        $dt = $this
            ->select('count(*) as total, ABSTRACT')
            ->where('ABSTRACT is NULL')
            ->Orwhere('ABSTRACT', '')
            ->groupBy('ABSTRACT')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.abstract_without') . ' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
        }

        /***************************************** YEAR */
        $dt = $this
            ->select('count(*) as total')
            ->where('year is NULL')
            ->Orwhere('year', '')
            ->Orwhere('year < 1950')
            ->Orwhere('year > 9000')
            ->findAll();

        foreach ($dt as $line) {
            $link = '<a href="' . PATH . '/admin/dataset/year_without">';
            $linka = '</a>';
            $sx .= '<li>' . $link . lang('brapci.year_without') . $linka . ' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
        }

        $sx .= '</ul>';


        return $sx;
    }

    function data_convert_elastic($data)
    {

        $da = array();
        $da['ID'] = $data['ID'];
        $da['json'] = json_encode($data);
        $da['CLASS'] = $data['Class'];
        $da['COVER'] = PATH. '/img/books/no_cover.png';
        $da['OAI_ID'] = $data['OAI_ID'];

        $da['COLLECTION'] = $data['COLLECTION'];

        $da['KEYWORDS'] = $data['KEYWORDS'];
        $da['ABSTRACTS'] = $data['ABSTRACTS'];
        $da['PUBLICATION'] = $data['PUBLICATION'];
        $da['SESSION'] = $data['SESSION'];
        $da['URL'] = $data['URL'];
        $da['DOI'] = $data['DOI'];

        /* verifica se tem o ISSUE */
        if (isset($data['Issue']['ID'])) {
            $da['ISSUE'] = $data['Issue']['issue'];
            $da['YEAR'] = $data['Issue']['year'];
            $da['JOURNAL'] = $data['Issue']['thema'];
            $da['VOL'] = $data['Issue']['vol'];
            $da['NR'] = $data['Issue']['nr'];
        }

        if ((isset($data['YEAR'])) and ($data['YEAR'] != '')) {
            $da['YEAR'] = $data['YEAR'];
        }

        if ((isset($data['JOURNAL'])) and ($data['JOURNAL'] != '')) {
            $da['JOURNAL'] = $data['JOURNAL'];
        }
        /**************************** KEYWORDS */
        if (isset($data['Keywords'])) {
            $da['KEYWORD'] = 1;
        } else {
            $da['KEYWORD'] = 0;
        }

        /**************************** ABSTRACT */
        if (isset($data['Abstract'])) {
            $da['ABSTRACT'] = 1;
        } else {
            $da['ABSTRACT'] = 0;
        }

        if (!isset($da['YEAR'])) {
            $da['YEAR'] = '2000';
        }
        if ($da['YEAR'] == '') {
            $da['year'] = '2000';
        }

        if (isset($data['PDF'])) {
            $da['PDF'] = $data['PDF'];
        } else {
            $data['PDF'] = 0;
        }
        if (isset($data['Issue']['JOURNAL']))
            {
                $da['JOURNAL'] = $data['Issue']['JOURNAL'];
            }

        if (isset($data['id_jnl'])) {
            if ($data['id_jnl'] > 0)
                {
                    $da['JOURNAL'] = $data['id_jnl'];
                }
        }

        if (isset($data['Title']))
            {
                $tit = '';
                $tit2 = '';
                foreach($data['Title'] as $lang=>$line)
                    {
                        if ($tit2 == '') { $tit2 = $line[0]; }
                        if ($lang == 'pt'){
                            $tit = $line[0];
                        }
                    }
                if ($tit == '') { $tit = $tit2; }
                $da['TITLE'] = $tit;
            } else {
                $da['TITLE'] = ':: Sem titulo ::';
            }

        /********************************************** COVER */
        if (isset($data['COVER']))
            {
                $da['COVER'] = $data['COVER'];
            }

        /***************************************************** */
        $da['AUTHORS'] = '';
        if (isset($data['Authors'])) {
            $au = [];
            foreach ($data['Authors'] as $ida => $name) {
                if ($ida == sonumero($ida))
                    {
                        array_push($au,$name);
                    } else {
                        if (is_array($name))
                            {
                                foreach($name as $idb=>$nameb)
                                    {
                                        array_push($au, $nameb);
                                    }
                            }
                    }
            }
            foreach ($au as $ida => $name) {
                if ($da['AUTHORS'] != '') {
                    $da['AUTHORS'] .= '; ';
                }
                $da['AUTHORS'] .= nbr_author($name, 7);
            }

        }
        if (isset($data['Organizer'])) {
            foreach ($data['Organizer'] as $ida => $name) {
                if ($da['AUTHORS'] != '') {
                    $da['AUTHORS'] .= '; ';
                }
                $da['AUTHORS'] .= nbr_author($name, 7).' (Org.)';
            }
        }


        /***************************************************** */
        switch($data['Class'])
            {
                /**************************************** Legend Article */
                case 'Article':
                    if (!is_array($data['Issue']))
                        {
                            echo h("OPS, Issue is string, LEGEND");
                            PRE($data);
                        }
                    $da['LEGEND'] = $data['Issue']['journal'];

                    if ($data['Issue']['vol'] != '') { $da['LEGEND'] .= ', v. '. $data['Issue']['vol']; }
                    if ($data['Issue']['nr'] != '') { $da['LEGEND'] .= ', n. '. $data['Issue']['nr']; }
                    if ($data['Issue']['year'] != '') { $da['LEGEND'] .= ', '. $data['Issue']['year']; }
                    $da['JOURNAL'] = $data['Issue']['id_jnl'];
                    $da['ISSUE'] = $data['Issue']['issue'];
                break;

                /**************************************** Legend Proceeding */
                case 'Proceeding':
                    $da['LEGEND'] = $da['TITLE'];
                    if ($data['Issue']['year'] != '') {
                        $da['LEGEND'] .= ', ' . $data['Issue']['year'];
                    }
                    $da['JOURNAL'] = $data['Issue']['id_jnl'];
                    $da['PUBLICATION'] = $data['Issue']['journal'];
                    $da['ISSUE'] = $data['Issue']['issue'];
                    break;

                /**************************************** Legend Book */
                case 'Book':
                    $da['LEGEND'] = $da['TITLE'];
                    if ($data['Issue']['year'] != '') {
                        $da['LEGEND'] .= ', ' . $data['Issue']['year'];
                    }
                    $da['JOURNAL'] = 0;
                    $da['ISSUE'] = 0;
                    $da['PUBLICATION'] = 'LIVRO';
                    break;

                case 'BookChapter':
                    $da['LEGEND'] = 'Capítulo de livro - '.$da['TITLE'];
                    if ($data['Issue']['year'] != '') {
                        $da['LEGEND'] .= ', ' . $data['Issue']['year'];
                    }
                    $da['JOURNAL'] = 0;
                    $da['ISSUE'] = 0;
                    $da['PUBLICATION'] = 'CAPITULO DE LIVRO';
                    break;

                default:
                    echo "Class Legend not found ".$data['Class'];
                    exit;
            }
        $da['updated_at'] = date("Y-m-d H:i:s");
        $da['new'] = 1;

        return $da;
    }

    function check($dt, $stop, $id = 0)
    {
        $sx = '';
        switch ($dt['CLASS']) {
            case 'Article':
                $sx .= $this->checkIssue($dt, $id);
                $sx .= $this->checkYear($dt, $id);
                $sx .= $this->checkJournal($dt, $id);
        }
        if (($stop == True) and ($sx != '')) {
            echo h("ERROS", 1);
            echo '<a href="' . (PATH . '/v/' . $id) . '" target="_blank">ID: ' . $id . '</a>';
            echo '<hr>';
            echo $sx;
            exit;
        }
    }
    function checkJournal($dt)
    {
        if (!isset($dt['JOURNAL']) or ($dt['JOURNAL'] < 1)) {
            $RDF = new \App\Models\Rdf\RDF();
            $RDF->exclude($dt['ID']);
            return "";
            return "JOURNAL not set<br>";
        } else {
            return "";
        }
    }
    function checkYear($dt)
    {
        if (!isset($dt['YEAR'])) {
            return "YEAR not set<br>";
        } else {
            return "";
        }
    }
    function checkIssue($dt)
    {
        if (!isset($dt['ISSUE'])) {
            $dt['json'] = '';
            return "ISSUE not set<br>";
        } else {
            return "";
        }
    }


    function data($id, $xdata)
    {
        $dt = $this->where('ID', round($id))->first();
        if (count($xdata) == 0) {
            echo '======================== A001 ==';
            $sx = lang('brapci.skip') . ' deleted';
            return $sx;
        }

        /*********************** CONVERT DADOS */
        $data = $this->data_convert_elastic($xdata);
        $this->check($data, true, $id);

        /* NOVO REGISTRO */

        if ($dt==[]) {
            if (count($data) > 0) {
                $data['status'] = 1;
                $this->set($data)->insert();
                $sx = lang('brapci.inserted');
            } else {
                $sx = lang('brapci.deleted');
            }
        } else {
            if (count($data) > 0) {
                $data['status'] = 1;
                $this->set($data)
                    ->where('ID', $id)
                    ->update();
                $sx = lang('brapci.updated');
            } else {
                $this->where('ID', $id)->delete();
                $sx = lang('brapci.deleted');
            }
        }
        return $sx;
    }
}
