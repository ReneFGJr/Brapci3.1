<?php

namespace App\Models\Lattes;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = '*';
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

    /*

    */

    function robot()
    {
        set_time_limit(1500);
        $t = array("direito autoral", "direitos autorais", "ciencia aberta", "recursos educacionais abertos", "gestao de dados", "open access", "repositorios institucionais", "repositorio digital", "acesso a informaçao", "educaçao aberta", "propriedade intelectual", "politicas públicas", "bibliotecas digitais", "preservaçao digital", "ciencia cidada", "abertura de dados", "open data", "web semântica", "via verde", "gestao da informaçao", "dados de pesquisa", "repositorios digitais", "bibliometria", "acceso abierto", "open source", "politica de acesso aberto", "periodicos cientificos", "repositorios", "transparencia", "arquivo aberto", "dados governamentais abertos", "autoarquivamento", "curadoria de dados", "arca", "curadoria digital", "periodico cientifico", "fiocruz", "computaçao em nuvem", "gestao de dados de pesquisa", "acessibilidade", "revistas cientificas", "repositorio tematico", "repositorio", "governo aberto", "lei de acesso a informaçao", "principios fair", "publicaçao de acesso aberto", "dados cientificos", "acesso aberto a informaçao", "arquivos abertos", "politica pública", "via dourada", "repositorio institucional da fiocruz", "universidade aberta", "ciencia de dados", "e-science", "politica de dados abertos", "datos abiertos", "tecnologias digitais", "elearning", "periodico cientifico em acesso aberto", "confoa", "redes sociais cientificas", "ciencia aberta na fiocruz", "interoperabilidad", "dados para pesquisa", "fundaçao oswaldo cruz", "repositorios de dados", "praticas de ciencia aberta", "repositorio aberto", "gerenciamento de dados", "repositorios de dados de pesquisa", "ambientes virtuais compartilhados", "arca dados", "dados de investigaçao", "politica de ciencia aberta", "repositorio institucional", "revisao por pares aberta", "inovaçao aberta", "repositorios tematicos", "software libre", "altmetria", "rcaap", "budapest open access initiative", "metricas alternativas", "responsabilidade social", "ciclo de vida dos dados", "dados governamentais", "plano de gestao de dados", "politicas editoriais", "acceso libre", "armazenamento de dados", "compartilhamento de dados de pesquisa", "compartilhamento e abertura de dados", "produçao colaborativa", "rede sudeste de repositorios institucionais", "repositorio de dados cientificos", "repositorios cientificos", "acesso a aberto", "fair principles", "preprints", "rede colaborativa", "repositorio institucional arca", "repositorios digitais confiaveis", "software aberto", "go fair", "open access movement", "revisao por pares", "avaliaçao aberta", "cris", "encontro da rede sudeste de repositorios institucionais", "gestao de repositorios", "identificadores persistentes", "naacs", "politica editorial", "politicas de ciencia aberta da fiocruz", "dados fair", "abertura de dados de pesquisa", "dados abertos de pesquisa", "dados de pesquisa abertos", "direitos de propriedade intelectual", "indicadores de desempenho", "indicadores de produçao cientifica", "open notebook science", "postprint", "preprint", "produtividade cientifica", "repositorio institucional de dados para pesquisa da fiocruz", "scientific collaboration", "tecnologia social", "agregador", "avaliaçao pelos pares", "caderno de laboratorio aberto", "conhecimento aberto", "cultura de colaboraçao", "dados cientificos de pesquisa", "fair", "fator de impacto", "foster", "laboratorio cidadao", "open journal systems", "politica de repositorio", "promoçao da ciencia aberta", "repositorio de dados para pesquisa da fiocruz", "revistas academicas", "acesso ao conhecimento cientifico", "caderno aberto de laboratorio", "ciencia colaborativa", "compartir datos", "comunicaçao cientifica aberta", "cultura participatoria", "dados abiertos", "dados cientificos abertos", "dados de investigaçao abertos", "dados de pesquisas", "data journals", "digital data curation", "engajamento público na ciencia", "infraestrutura da ciencia aberta", "modelo de publicaçao", "objetivos do desenvolvimento sustentavel", "parceria para governo aberto", "politica de ciencia e tecnologia", "politicas de auto arquivo", "politicas de ciencia aberta na fiocruz", "projeto blimunda", "projeto rcaap", "re3data", "repositorio comum", "reutilizacion", "sustainable development goals", "tecnologias livres", "a mudança de paradigma na publicaçao cientifica", "abertura e compartilhamento de dados de pesquisa", "academia aberta (projetos)", "aula aberta", "autoralidade colaborativa", "avaliaçao aberta por pares", "avaliaçao participativa", "avaliaçao por pares aberta", "boas praticas e inovações na gestao de revistas cientificas", "buen conocer", "caderno aberto de laboratorio cidadao", "cadernos cientificos abertos", "cadernos de pesquisa abertos", "ciencia aberta (movimento)", "ciencia aberta e qualidade de revistas cientificas", "compartilhamento de artigos cientificos", "compartilhamento e abertura de dados para pesquisa da fiocruz", "conhecimento compartilhado", "democratizacion de la ciencia", "diretorio de repositorios", "diretorio de repositorios digitais", "diretrizes de ciencia aberta", "diretrizes para repositorio de dados abert", "ediçao em livre acesso", "editoraçao academica", "editoraçao de publicações cientificas", "etica na avaliaçao por pares", "fair data", "fiopgd", "fit4rri", "flok society", "free and open source hardware", "gestao fair", "gestor de repositorio", "grupo de trabalho de ciencia aberta (gtca)", "hardware aberto", "indicador de colaboraçao", "instrumentos da ciencia aberta", "investigaçao colaborativa", "investigacion abierta", "laboratorio ciencia aberta (open science laboratory)", "laboratorio de ciencia aberta", "metricas em repositorios", "modelo de governança", "open access model", "openaire connect", "openaire provide", "opendata", "orcid/ciencia id", "parceria para governo aberto", "periodicos tecnico-cientificos", "pesquisa aberta", "pesquisa e inovaçao responsavel", "plano de açao nacional para o governo aberto", "plataforma aberta da web", "politica de ciencia aberta na fiocruz", "politica de dados para pesquisa", "politica de governança de dados", "politica nacional de ciencia aberta", "politicas de acesso aberto ao conhecimento", "politicas e diretrizes de ciencia aberta da fiocruz", "praticas de cidadania e comunidade", "pre-publicaçao", "produçao aberta", "projeto aberto", "projeto fairsfair", "pros e contra a praticas de ciencia aberta", "pubfair", "quarto paradigma cientifico", "rede go fair", "rede norte de repositorios institucionais", "redes cooperativas", "repositorio de dados abertos", "repositorio institucional da ufra", "reúso de dados de pesquisa", "revisao aberta", "revisao aberta por pares", "revisao de dados", "transparencia cientifica");

        for ($r = 0; $r < count($t); $r++) {
            $term = $t[$r];
            echo '===>' . $term . cr() . '<br>';
            sleep(5);
            $this->lattes_search_result($term);
        }
        exit;
    }

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $sx = "";
        $sx .= h(lang('tools.Lattes_tools'), 2);
        switch ($d1) {
            case 'in':
                $sx = 'FORM';
                $lattes = get("lattes");
                if (strlen($lattes) != '') {
                    $Projects = new \App\Models\Tools\Projects();
                    $prj = $Projects->selected();

                    $ProjectsHarvestingXml = new \App\Models\Tools\ProjectsHarvestingXml();
                    $sx .= $ProjectsHarvestingXml->inport($lattes, $prj);
                }
                $sx .= $this->form($d2);
                break;
            case 'export':
                $ok = 0;
                switch($d3)
                    {
                        case 'openaier':
                            $sx .= $this->exportOpenAire($d2);
                            break;
                        case '1':
                            $LattesProducao = new \App\Models\LattesExtrator\LattesProducao();
                            $txt = $LattesProducao->csv($d2);
                        break;

                        case '2':
                            $LattesFormacao = new \App\Models\LattesExtrator\LattesFormacao();
                            $txt = $LattesFormacao->csv($d2);
                        break;

                        case '3':
                            $LattesProducaoEvento = new \App\Models\LattesExtrator\LattesProducaoEvento();
                            $txt = $LattesProducaoEvento->csv($d2);
                            break;
                    }
                if ($ok==1)
                    {
                        exit;
                    }
                break;
            case 'robot':
                $sx .= $this->robot();
                break;
            case 'search':
                $sx .= $this->lattes_search_form($d2, $d3, $d4);
                $sx .= $this->lattes_search_result($d2, $d3, $d4);
                break;
            case 'viewid':
                $sx .= $this->viewid($d2);
                break;
            default:
                $Projects = new \App\Models\Tools\Projects();
                $prj = $Projects->selected();

                $API = new \App\Models\Api\Lattes\Index();
                $ProjectsHarvestingXml = new \App\Models\Tools\ProjectsHarvestingXml();
                $dt = array();
                $dt['title'] = array('Resume', 'Curriculos', 'Harvesting', 'Export');
                $dt['tab'] = array('Resume', 'Curriculos', 'Harvesting', 'Export');
                $dt['tab'][0] = $ProjectsHarvestingXml->resume($prj);
                $dt['tab'][1] = $this->btn_lattes_add($prj) . $ProjectsHarvestingXml->list($prj);
                $dt['tab'][2] = $this->btn_lattes_harvesting($prj);
                $dt['tab'][3] = $this->btn_export_lattes($prj);
                $sx .= bs(view('Tools/Project/pills', $dt));
                break;
        }
        return $sx;
    }

    function exportOpenaire($d2)
        {
                $LattesProducao = new \App\Models\LattesExtrator\LattesProducao();
                $doi = $LattesProducao->doi($d2);

                $OpenAire = new \App\Models\Tools\Openaire\Index();
                $sx = $OpenAire->import_doi($d2,$doi);
                return $sx;
        }

    function link($id)
    {
        $link = '<a href="http://lattes.cnpq.br/' . $id . '" target="_blank">';
        $linka = '</a>';
        $sx = $link . '<img src="' . URL . '/img/icons/logo_lattes_mini.png" style="height: 24px;">' . $linka;
        return $sx;
    }
    function checkID($code)
    {
        $dig = substr($code, 15, 1);
        $code = substr($code, 0, 15);

        $weightflag = true;
        $sum = 0;
        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightflag ? 3 : 1);
            $weightflag = !$weightflag;
        }
        $ver = (10 - ($sum % 10)) % 10;
        if ($ver == $dig) {
            return 1;
        } else {
            return 0;
        }
    }

    function viewid($id)
    {
        //$sx = $this->viewid(ida);
        $id_brapci = 0;

        if ($id == '') {
            return redirect('tools:index');
        }
        $Genene = new \App\Models\Authority\Genere();
        $LattesDados = new \App\Models\LattesExtrator\LattesDados();
        $LattesProducao = new \App\Models\LattesExtrator\LattesProducao();
        $LattesProducaoEvento = new \App\Models\LattesExtrator\LattesProducaoEvento();
        $LattesProducaoLivro = new \App\Models\LattesExtrator\LattesProducaoLivro();
        $LattesProducaoPatent = new \App\Models\LattesExtrator\LattesPatent();
        $LattesProducaoSoftware = new \App\Models\LattesExtrator\LattesSoftware();
        $LattesProducaoCapitulo = new \App\Models\LattesExtrator\LattesProducaoCapitulo();
        $LattesProducaoArtistica = new \App\Models\LattesExtrator\LattesProducaoArtistica();
        $LattesInstituicao = new \App\Models\LattesExtrator\LattesInstituicao();
        $LattesOrientacao = new \App\Models\LattesExtrator\LattesOrientacao();
        $LattesExtrator = new \App\Models\LattesExtrator\Index();
        $LattesFormacao = new \App\Models\LattesExtrator\LattesFormacao();
        $LattesEndereco = new \App\Models\LattesExtrator\LattesEndereco();
        $PQ = new \App\Models\PQ\Bolsas();

        $dtl = $LattesDados->where('lt_id', $id)->first();
        if ($dtl == '') {
            $LattesExtrator = new \App\Models\LattesExtrator\Index();
            $LattesExtrator->harvesting($id);
            $dtl = $LattesDados->where('lt_id', $id)->first();
            if ($dtl == '') {
                return "ERRO LATTES ID";
            }

        }

        $dtl['bs_image'] = $Genene->image("X");
        $dtl['bs_nome'] = $dtl['lt_name'];
        $dtl['bs_content'] = $this->link($id);
        $dtl['bs_content'] .= $LattesExtrator->btn_coletor($id);
        $dtl['bs_content'] .= $PQ->btn_new($id);
        $dtl['bs_brapci'] = '';

        $sa = view('Lattes/pesquisador', $dtl);

        $sa .= $LattesEndereco->resume($id);
        $sa .= $LattesFormacao->resume($id);

        $sb = '';
        /***** */
        $p3 = $LattesOrientacao->resume($id);
        $p1 = $LattesProducao->resume($id,'C');
        $p7 = $LattesProducao->resume($id, 'R');
        $p4 = $LattesProducaoCapitulo->resume($id);
        $p5 = $LattesProducaoLivro->resume($id);
        $p6 = $LattesProducaoEvento->resume($id);
        $pq = count($PQ->bolsas_pesquisador($id));
        $p8 = $LattesProducaoSoftware->resume($id);
        $p9 = $LattesProducaoPatent->resume($id);
        $pA = $LattesProducaoArtistica->resume($id);

        $p2 = 0;
        $sc = '';
        $sc .= bsc('Produção Científica', 12);
        $sc .= bsc($LattesProducao->selo($p1, 'ARTIGOS'), 3);
        $sc .= bsc($LattesProducao->selo($p7, 'ARTIGOS RESUMO'), 3);
        $sc .= bsc($LattesProducao->selo($p5, 'LIVROS'), 3);
        $sc .= bsc($LattesProducao->selo($p4, 'CAPÍTULOS'), 3);

        $sc .= bsc($LattesProducao->selo($p6, 'ANAIS'), 3);
        $sc .= bsc('Produção Tecnológica', 12);
        $sc .= bsc($LattesProducao->selo($p9, 'PATENTES'), 3);
        $sc .= bsc($LattesProducao->selo($p8, 'SOFTWARES'), 3);
        $sc .= bsc('', 6);
        $sc .= bsc($LattesProducao->selo($pq, 'BOLSAS PQ'), 3);
        $sc .= bsc('Orientações', 12);
        $sc .= bsc($LattesProducao->selo($p3[0], 'GRADUAÇÃO'), 2);
        $sc .= bsc($LattesProducao->selo($p3[1], 'IC/IT'), 2);
        $sc .= bsc($LattesProducao->selo($p3[2], 'MESTRADO'), 2);
        $sc .= bsc($LattesProducao->selo($p3[3], 'DOUTORADO'), 2);
        $sc .= bsc($LattesProducao->selo($p3[4], 'POS-DOUT.'), 2);
        $sc .= bsc($LattesProducao->selo($p3[5], 'OUTROS'), 2);
        $sc .= bsc('Produção Artistica', 12);
        $sc .= $LattesProducaoArtistica->selo($pA);


        $sb = '<div class="m-4">';
        $sb .= '<ul class="nav nav-tabs" id="myTab">';

        $sbi = ['Article','ArticleResume'];
        if ($p5 > 0) { array_push($sbi, 'Books'); }
        if ($p4 > 0) { array_push($sbi, 'Chapter'); }
        if ($p6 > 0) { array_push($sbi, 'Proceedings'); }
        if ($p8 > 0) { array_push($sbi, 'softwares'); }
        if ($p9 > 0) { array_push($sbi, 'patentes'); }
        if ($pq > 0) { array_push($sbi, 'BolsasPQ'); }
        if ($pA > 0) { array_push($sbi, 'Artistic'); }
        $active = 'active';
        $show = 'show';
        $sbd = '';
        foreach($sbi as $id=>$link)
            {
            $sb .= '<li class="nav-item '.$show.'"><a href="#'.$link.'" class="nav-link " data-bs-toggle="tab">'.lang("brapci.".$link).'</a></li>'.cr();
            $sbd .= '<div class="tab-pane fade '.$show.' " id="'.$link.'">';
            $sbd .= h(lang('brapci.'.$link),4);
            switch($link)
                {
                    case 'Artistic':
                        $sbd .= $LattesProducaoArtistica->producao($dtl['lt_id'], 'C');
                        break;
                    case 'Article':
                        $sbd .= $LattesProducao->producao($dtl['lt_id'], 'C');
                        break;
                    case 'ArticleResume':
                        $sbd .= $LattesProducao->producao($dtl['lt_id'], 'R');
                        break;
                    case 'Books':
                        $sbd .= $LattesProducaoLivro->producao($dtl['lt_id']);
                        break;
                    case 'Chapter':
                        $sbd .= $LattesProducaoCapitulo->producao($dtl['lt_id']);
                        break;
                    case 'Proceedings':
                        $sbd .= $LattesProducaoEvento->producao($dtl['lt_id']);
                        break;
                    case 'patentes':
                        $sbd .= $LattesProducaoPatent->producao($dtl['lt_id']);
                        break;
                    case 'softwares':
                        $sbd .= $LattesProducaoSoftware->producao($dtl['lt_id']);
                        break;
                    case 'BolsasPQ':
                        $sbd .= $PQ->historic_researcher($dtl['lt_id']);
                        break;
                    default:
                        $sbd .= '<br>==>'.$link;
                        $show = '';
                        break;
                }
            $sbd .= '</div>';
        }
        $sb .= '</ul>'.cr();

        $sb .= bs($sc);
        $sb .= '<div class="tab-content">' . $sbd . '</div>';

        $sx = bs(bsc($sa, 4) . bsc($sb, 9));


        //$sx .= '<style> div { border: 1px solid #000;"} </style>';
        $sx .= '</div>';

        return $sx;
    }

    function form($id)
    {
        $submit = lang('brapci.save');
        $lattes = get("lattes");
        $sx = form_open();
        $sx .= '<label>Insira o número dos Lattes (16 digitos, ex: 1234567890123456</label>';
        $sx .= form_textarea(array('name' => 'lattes', 'value' => $lattes, 'id' => 'lattes', 'rows' => 10, 'style' => 'width: 100%;'));
        $sx .= form_submit(array('name' => 'action', 'value' => $submit));
        $sx .= form_close();

        return $sx;
    }

    function btn_export_lattes($prj)
        {
            $sx = h(lang('brapci.export'),3);
            $sx .= '<ul>';
            $sx .= '<li>'.anchor(PATH.'tools/project/api/'.$prj.'/lattes/export/1',lang('tools.export').' '.lang('tools.lattes_articles')).' (csv)</li>';
            $sx .= '<li>' . anchor(PATH . 'tools/project/api/' . $prj . '/lattes/export/3', lang('tools.export') . ' ' . lang('tools.lattes_events')) . ' (csv)</li>';
            $sx .= '<li>' . anchor(PATH . 'tools/project/api/' . $prj . '/lattes/export/2', lang('tools.export') . ' ' . lang('tools.lattes_formacao')) . ' (csv)</li>';
            $sx .= '<li>' . anchor(PATH . 'tools/project/api/' . $prj . '/lattes/export/openaier', lang('tools.export') . ' ' . lang('tools.openaier')) . ' (api)</li>';
            $sx .= '</ul>';
            return $sx;
        }

    function btn_lattes_add($prj)
    {
        $sx = '<span onclick="download(\'' . PATH . COLLECTION .
            '/project/api/2/lattes/in\');"
                    class="btn btn-outline-primary">' . lang('brapci.add') . '</span>';
        return $sx;
    }

    function btn_lattes_harvesting($prj)
    {
        $sx = '<span onclick="download(\'' . PATH . '/bots/lattes/' . $prj . '\');"
                    class="btn btn-outline-primary">' . lang('brapci.harvesting') . '</span>';
        return $sx;
    }

    function lattes_search_extrai_nome($txt, $term = '')
    {
        $key = '';
        $sx = '';
        $txt = substr($txt, strpos($txt, '(por Assunto)'), strlen($txt));
        $txt_ref = '<a href="javascript:abreDetalhe(';
        //$txt = troca($txt,$txt_ref,'[ID]');

        preg_match_all("|<[^>]+>(.*)</[^>]+>|U", $txt, $matches);
        $search1 = 'javascript:abreDetalhe(';
        $search2 = "class='porcentagem'";
        $match = $matches[0];
        $rst = array();

        //pre($match);

        $next = false;
        for ($r = 0; $r < count($match); $r++) {
            $line = $match[$r];

            if ($next == true) {
                $line = strip_tags($line);
                if (strlen($line) == 0) {
                    $next = false;
                } else {
                    $rst[$key]['desc'] .= ascii(utf8_encode($line));
                    //pre($line, false);
                }
            }

            if (strpos($line, $search2)) {
                $percent = strip_tags($line);
            }

            if (strpos($line, $search1)) {
                $line = substr($line, strpos($line, $search1) + strlen($search1), strlen($line));
                $line = substr($line, 0, strpos($line, '</a'));
                $line = troca($line, ')">', '');
                $line = troca($line, "'", '');

                $names = explode(',', $line);
                if (!isset($rst[$names[0]])) {
                    $rst[$names[0]] = array();
                    $rst[$names[0]]['name'] = $names[3];
                    $rst[$names[0]]['name_asc'] = $names[1];
                    $rst[$names[0]]['ids'] = $names[2];
                    $rst[$names[0]]['desc'] = '';
                    $rst[$names[0]]['term'] = get("term");
                    $rst[$names[0]]['perc'] = $percent;
                    $key = $names[0];
                    $names[1] = '';
                }
                $next = true;
            }
        }
        $file = 'lattes_' . date("Ymd_His") . '.csv';

        $sr = '';
        foreach ($rst as $id => $line) {
            $sr .= $id . ';';
            $sr .= $line['name'] . ';';
            $sr .= $line['name_asc'] . ';';
            $sr .= $line['ids'] . ';';
            $sr .= $line['desc'] . ';';
            $sr .= $line['perc'] . ';';
            $sr .= $term . ';';
            $sr .= cr();
        }
        dircheck('.tmp/');
        dircheck('.tmp/Lattes/');
        dircheck('.tmp/Lattes/Export/');
        $file  = '.tmp/Lattes/Export/' . $file;
        file_put_contents($file, ($sr));

        $sx .= h('total: ' . count($rst), 3);

        $sx .= '<a href="' . URL . '/' . $file . '" class="btn btn-primary">' . msg('brapci.export') . '</a>';
        $sx .= $sr;

        return $sx;
    }

    function lattes_search_result($t)
    {
        $term = get("term") . $t;
        $sx = '';

        /*********************  */
        if ($term != '') {
            $termURL = '""' . ascii(urlencode($term)) . '""';
            $url = '';
            $url .= 'https://buscatextual.cnpq.br/buscatextual/busca.do?';
            $url .= 'metodo=forwardPaginaResultados';
            $url .= '&registros=0;5000';
            $url .= '&query=%28%2Bidx_assunto%3A%28%22' . $termURL . '%22%29++%2Bidx_nacionalidade%3Ae%29+or+%28%2Bidx_assunto%3A%28%22' . $termURL . '%22%29++%2Bidx_nacionalidade%3Ab+%5E500+%29';
            $url .= '&analise=cv';
            $url .= '&tipoOrdenacao=null';
            $url .= '&paginaOrigem=index.do';
            $url .= '&mostrarScore=true';
            $url .= '&mostrarBandeira=true&modoIndAdhoc=null';
            $sx .= '<hr>' . urldecode($url);
            $sx .= '<hr>' . '<textarea style="width:100%; height:190px;">' . $url . '</textarea>';

            $file = md5($url) . '.txt';
            dircheck('.tmp/');
            dircheck('.tmp/Lattes/');
            dircheck('.tmp/Lattes/Inport/');
            $file  = '.tmp/Lattes/Inport/' . $file;

            if (file_exists($file)) {
                $txt = file_get_contents($file);
            } else {
                $txt = file_get_contents($url);
                file_put_contents($file, $txt);
            }
            $sx = $this->lattes_search_extrai_nome($txt, $term);
        }


        return $sx;
    }

    function lattes_search_form()
    {
        $sx = '';
        $sx .= form_open();
        $sx .= '<label>' . lang('brapci.subject') . '</label>';
        $sx .= form_input(array('name' => 'term', 'class' => 'form-control-mb full', 'value' => get("term")));
        $sx .= form_submit('submit', lang('brapci.search'), 'class="btn btn-outline-primary"');
        $sx .= form_close();
        return $sx;
    }
}
