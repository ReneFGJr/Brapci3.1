<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class BooksSubmit extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'books_submit';
    protected $primaryKey       = 'id_bs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bs',
        'bs_post',
        'bs_status',
        'bs_title',
        'b_isbn',
        'bs_rdf',
        'bs_arquivo',
        'bs_email',
        'bs_json'
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

    function preview($id)
    {
        $BookSubmit = new \App\Models\Books\BooksSubmit();
        $dt = $BookSubmit->find($id);

        // Caminho do arquivo no servidor
        $file_path = '../.tmp/booksubmit/' . $dt['bs_arquivo'];

        // Verifica se o arquivo existe
        if (file_exists($file_path)) {
            // Define os cabeçalhos HTTP para download
            //header('Content-Description: File Transfer');
            //header('Content-Type: application/pdf'); // Define o tipo de arquivo
            //header('Content-Disposition: attachment; filename="' . basename($file_path) . '"'); // Nome do arquivo
            //header('Expires: 0');
            //header('Cache-Control: must-revalidate');
            //header('Pragma: public');
            //header('Content-Length: ' . filesize($file_path));

            // Define os cabeçalhos HTTP para visualizar no navegador
            header('Content-Type: application/pdf'); // Tipo de arquivo
            header('Content-Disposition: inline; filename="' . basename($file_path) . '"'); // Exibir no navegador
            header('Content-Length: ' . filesize($file_path));
            header('Accept-Ranges: bytes');
            // Envia o arquivo para o navegador
            readfile($file_path);
            exit;
        } else {
            pre($dt);
            echo "Arquivo não encontrado!";
        }
    }

    function catalogHarvesting($id)
        {
            $BooksModel = new \App\Models\Books\BookHarvesting();
            $dt = $BooksModel->find($id);
            echo "Catalogando registro de harvesting: " . $id . "<br>";

            /******************************************* */
            /*************************** Criar Livro (Conceito) */
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $isbn = $dt['ISBN'];

            if ($isbn == '') {
                $DOI = $dt['DOI'];
                if ($DOI != '') {
                    $pos = strpos($DOI, '978');
                    if ($pos !== false) {
                        $isbn = substr($DOI, $pos, 20);
                        $isbn = sonumero($isbn);
                    }
                } else {
                    echo "ISBN não localizado";
                    exit;
                }
            }

            /**************************** */
            $name = 'ISBN:' . $isbn;
            $class = 'Book';
            $lang = 'pt_BR';
            $value = $isbn;

            $idC = $RDFconcept->createConcept(['Class' => $class, 'Name' => $value, 'Lang' => $lang]);

            $dd = $this->where('b_isbn', $isbn)->first();
            if ($dd != []) {
                echo "Registro já catalogado: " . $dd['id_bs'] . "<br>";
            } else {
                echo "Registro não catalogado, criando registro de submissão...<br>";
                $dd['bs_title'] = $dt['title'];
                $dd['bs_post'] = json_encode($dt);
                $dd['bs_status'] = 7;
                $dd['b_isbn'] = $isbn;
                $dd['bs_rdf'] = $idC;
                $dd['bs_arquivo'] = '';
                $dd['bs_email'] = '';
                $dd['id_bs'] = $this->insert($dd);
            }

            $json = $this->generateBookJson($dt);
            $filename = preg_replace('/\D/', '', $dt['ISBN']);
            if ($filename == '') {
                $filename = preg_replace('/[^A-Za-z0-9]/', '', $dt['identifier']);
            }
            $dir = '../.tmp/booksubmit/';
            dircheck($dir);

            $filename = $dir . $filename;
            file_put_contents($filename . '.json', json_encode($json));
            $dd = [];
            $dd['bs_json'] = json_encode($json);
            $dd = $this->where('b_isbn', $isbn)->first();
            echo $this->getlastquery();
            $this->set($dd)->where('b_isbn', $isbn)->update();
            $this->process_json($idC, $filename . '.json');
            echo "FIM: ".$name;
            $dd = [];
            $dd['status'] = 2;
            $BooksModel->set($dd)->where('id', $id)->update();
            echo "<br>Registro de harvesting atualizado para catalogado.";
            exit;
        }

    /**
     * Gera o JSON padronizado de um livro
     */
    function generateBookJson(array $dt): string
    {
        // Idiomas
        $langMap = [
            'por' => 'pt',
            'pt'  => 'pt',
            'eng' => 'en',
            'en'  => 'en',
            'spa' => 'es',
            'es'  => 'es',
            'fra' => 'fr',
            'fr'  => 'fr'
        ];

        $language = strtolower(trim($dt['language'] ?? ''));
        $language = $langMap[$language] ?? $language;

        // ISBN
        $isbn = preg_replace('/\D/', '', $dt['ISBN'] ?? '');

        // Subjects
        $subjects = [];
        if (!empty($dt['subjects'])) {
            $subjects = is_array($dt['subjects'])
                ? $dt['subjects']
                : json_decode($dt['subjects'], true);
        }

        // Autores
        $authors = [];
        if (!empty($dt['creators'])) {

            $tmp = is_array($dt['creators'])
                ? $dt['creators']
                : json_decode($dt['creators'], true);

            foreach ($tmp as $a) {
                $parts = explode(';', $a);
                $authors[] = trim($parts[0]);
            }

            $authors = array_values(array_unique($authors));
        }

        // Organizadores
        $organizers = [];
        if (!empty($dt['organizators'])) {

            $tmp = is_array($dt['organizators'])
                ? $dt['organizators']
                : json_decode($dt['organizators'], true);

            foreach ($tmp as $a) {
                $parts = explode(';', $a);
                $organizers[] = trim($parts[0]);
            }
        }

        // Capítulos
        $chapters = [];

        if (!empty($dt['ChaptherBook'])) {

            $tmp = is_array($dt['ChaptherBook'])
                ? $dt['ChaptherBook']
                : json_decode($dt['ChaptherBook'], true);

            foreach ($tmp as $c) {

                $chapters[] = [
                    'hasTitle'      => $c['title'] ?? '',
                    'hasAuthor'     => array_map('trim', explode(';', $c['author'] ?? '')),
                    'hasAbstract'   => $c['abstract'] ?? null,
                    'hasSubject'    => $c['subjects'] ?? [],
                    'hasPageStart'  => $c['pageStart'] ?? null,
                    'hasPageEnd'    => $c['pageEnd'] ?? null,
                    'hasDOI'        => $c['doi'] ?? null
                ];
            }
        }

        $book = [

            'hasISBN' => $isbn,

            'hasTitle' => $dt['title'] ?? '',

            'hasAbstract' => trim($dt['description'] ?? ''),

            'hasLanguageExpression' => $language,

            'hasSubject' => $subjects,

            // opcional
            'hasKeywords' => $subjects,

            'hasAuthor' => $authors,

            'hasOrganizator' => $organizers,

            'hasPage' => $dt['pages'] ?? null,

            'isPlaceOfPublication' => $dt['place'] ?? null,

            'wasPublicationInDate' => substr($dt['dc_date'] ?? '', 0, 4),

            'isPublisher' => !empty($dt['publishers'])
                ? (is_array($dt['publishers'])
                    ? $dt['publishers']
                    : json_decode($dt['publishers'], true))
                : [],

            'hasDOI' => $dt['DOI'] ?? null,

            'hasBookChapter' => $chapters
        ];

        return json_encode(
            $book,
            JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
        );
    }

    function import_json($id)
    {
        $dt = $this->find($id);
        if ($dt == []) {
            return 'Registro não localizado ' . $id;
        }

        if (isset($_FILES['file']) && ($_FILES['file']['error'] === UPLOAD_ERR_OK)) {
            $tmp = $_FILES['file']['tmp_name'];
            $name = $_FILES['file']['name'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if ($ext != 'json') {
                return 'Envie um arquivo JSON válido.';
            }

            $dir = $this->directory();
            $filename = $dir . md5_file($tmp) . '.json';

            if (move_uploaded_file($tmp, $filename)) {
                return $this->process_json($id, $filename);
            }

            return 'Não foi possível salvar o arquivo enviado.';
        }

        $action = PATH . 'admin/book/change/' . $id . '/12';
        $sx = '';
        $sx .= '<form method="post" action="' . $action . '" enctype="multipart/form-data">';
        $sx .= '<div class="mb-3">';
        $sx .= '<label for="file" class="form-label">Arquivo JSON</label>';
        $sx .= '<input type="file" class="form-control" name="file" id="file" accept=".json,application/json" required>';
        $sx .= '</div>';
        $sx .= '<button type="submit" class="btn btn-primary">' . lang('brapci.import_json') . '</button>';
        $sx .= '</form>';
        $sx .= '<div class="mt-3 small text-muted">Envie um arquivo JSON para processar esta submissão.</div>';

        $txt = file_get_contents('../_Documments/_PROMPTS-IA/catalogacao-brapci-livros.txt');
        $sx .= '<hr>PROPOMPT ia';
        $sx .= '<textarea class="form-control mt-3" rows="10" readonly>' . $txt . '</textarea>';
        return $sx;
    }

    function process_json($id, $path_do_arquivo)
    {
        $dt = $this->find($id);
        if ($dt == []) {
            return 'Registro não localizado ' . $id;
        }

        if (!file_exists($path_do_arquivo)) {
            return 'Arquivo JSON não encontrado.';
        }

        $json = file_get_contents($path_do_arquivo);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return 'Arquivo JSON inválido.';
        }

        $dd = [];
        $dd['bs_json'] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (isset($data['b_titulo']) && trim($data['b_titulo']) != '') {
            $dd['bs_title'] = $data['b_titulo'];
        }

        if (isset($data['b_isbn']) && trim($data['b_isbn']) != '') {
            $dd['b_isbn'] = $data['b_isbn'];
        }

        $this->set($dd)->where('id_bs', $id)->update();

        $sx = '';
        $literal = ['hasTitle', 'hasAbstract'];
        $Classes = [
            'hasISBN' => 'ISBN',
            'hasAuthor' => 'Person',
            'hasPublisher' => 'Publisher',
            'hasLanguageExpression' => 'Language',
            'hasSubject' => 'Subject',
            'hasOrganizator' => 'Person',
            'hasPage' => 'Page',
            'isPlaceOfPublication' => 'Place',
            'wasPublicationInDate' => 'Date',
            'isPublisher' => 'Publisher',
            'hasEdition' => 'Edition',
            'hasKeywords' => 'Subject',
            'hasDOI' => 'DOI',
            'hasDate' => 'Date'
        ];
        if (!isset($data['hasISBN'])) {
            return 'Arquivo JSON inválido. Propriedade "hasISBN" não encontrada.';
        }
        $ISBN = $data['hasISBN'];
        if (is_array($ISBN)) {
            $ISBN = $ISBN[0];
        }
        $Chapter = 0;

        $IDCatalogador = $this->register_data($dt['bs_rdf'], 'hasCataloger', 'Cataloger', 'BrapciIA');

        foreach ($data as $key => $value) {
            /********************************** Chapter */
            if ($key == 'hasBookChapter') {
                $RdfID = $dt['bs_rdf'];

                foreach ($value as $k => $v) {
                    $Chapter++;
                    $ChapterID = 'ISBN:'.$ISBN.'_'.strzero($Chapter, 2);
                    $class = 'BookChapter';
                    $IDchapter = $this->register_data($dt['bs_rdf'], $key, $class, $ChapterID);

                    $IDCatalogador = $this->register_data($IDchapter, 'hasCataloger', 'Cataloger', 'BrapciIA');

                    echo $RdfID . '<br>';
                    echo $ChapterID.'<br>';
                    echo $IDchapter.'<br>';

                    $prop = 'hasChapterOf';
                    $this->register_link($RdfID, $prop, $IDchapter, 0);

                    /******************** Section */
                    $valueID = 344893;
                    $prop = 'hasSectionOf';
                    $this->register_link($IDchapter, $prop, $valueID, 0);

                    $prop = 'hasTitle';
                    $valueID = $v['hasTitle'];
                    $this->register_value($IDchapter, $prop, $valueID);

                    $prop = 'hasAbstract';
                    $valueID = $v['hasAbstract'];
                    $this->register_value($IDchapter, $prop, $valueID);

                    $prop = 'hasAuthor';
                    $valueID = $v['hasAuthor'];
                    foreach ($valueID as $kID => $va) {
                        $class = 'Person';
                        $this->register_data($IDchapter, $prop, $class, $va);
                    }

                    if (isset($v['hasPageStart'])) {
                        $prop = 'hasPageStart';
                        $valueID = $v['hasPageStart'];
                        $class = 'Page';
                        $this->register_data($IDchapter, $prop, $class, $valueID);
                    }

                    if (isset($v['hasPageEnd'])) {
                        $prop = 'hasPageEnd';
                        $valueID = $v['hasPageEnd'];
                        $class = 'Page';
                        $this->register_data($IDchapter, $prop, $class, $valueID);
                    }
                }
            } else {
                if (in_array($key, $literal)) {
                    print("<br>Registrando valor literal para a propriedade: $key");
                    $sx .= $this->register_value($dt['bs_rdf'], $key, $value);
                } else {
                    if ($key == 'hasKeyword') {
                        $key = 'hasSubject';
                    }
                    print("<br>Registrando valor de conceito para a propriedade: $key");
                    try {
                        if (isset($Classes[$key])) {

                            $class = $Classes[$key];

                            if (is_array($value)) {
                                foreach ($value as $k => $v) {
                                    $sx .= $this->register_data($dt['bs_rdf'], $key, $class, $v);
                                }
                            } else {
                                $sx .= $this->register_data($dt['bs_rdf'], $key, $class, $value);
                            }
                        } else {
                            echo "<br><span style=\"color:red\">Propriedade não mapeada: $key</span>";
                            exit;
                        }
                    } catch (\Exception $e) {

                        pre($key, false);
                        pre($value, false);

                        echo "<br><span class='text-danger'>Erro ao registrar a propriedade: $key - {$e->getMessage()}</span>";
                        exit;
                    }
                }
            }
        }
        $dd = [];
        $dd['bs_status'] = 2;
        $this->set($dd)->where('id_bs', $id)->update();
        return 'Arquivo JSON recebido e associado ao registro.';
    }

    function register_value($idRDF, $property, $value)
    {
        if ($idRDF == 0) {
            return 'Registro RDF não localizado.';
        }

        /*************** Criar conceito */
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFdata->register($idRDF, $property, 0, $value);
        return True;
    }

    function register_link($idRDF, $property, $value, $lang = 'pt')
    {
        if ($idRDF == 0) {
            return 'Registro RDF não localizado.';
        }

        /*************** Criar conceito */
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFdata->register($idRDF, $property, $value, 0);
        return True;
    }

    function register_data($idRDF, $property, $class, $value, $lang = 'pt')
    {
        if ($idRDF == 0) {
            return 'Registro RDF não localizado.';
        }

        /*************** Criar conceito */
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $idC = $RDFconcept->createConcept(['Class' => $class, 'Name' => $value, 'Lang' => $lang]);

        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFdata->register($idRDF, $property, $idC, 0);
        return $idC;
    }

    function discalimer($d2, $d3)
    {
        $id = $d2;
        $md5 = $d3;
        $dt = $this->where('id_bs', $id)->first();
        $dt['bs_status'] = 1;
        $this->set($dt)->where('id_bs', $id)->update();
        return $dt;
    }

    function registerPDF()
    {
        $tmp = $_FILES['file']['tmp_name'];
        $name = $_FILES['file']['name'];
        $md5 = md5_file($tmp);
        $dir = $this->directory();
        $filename = $dir . $md5 . '.pdf';
        $exist = file_exists($filename);
        move_uploaded_file($tmp, $filename);

        $dt = $this
            ->where('bs_arquivo', $md5)
            ->first();
        if ($dt == []) {
            $dd['bs_arquivo'] = $md5;
            $idc = $this->set($dd)->insert();
            $status = 0;
        } else {
            $idc = $dt['id_bs'];
            $status = $dt['bs_status'];
        }
        return [$md5, $exist, $idc, $status];
    }

    function directory()
    {
        $dir = '.tmp/books/';
        dircheck($dir);
        return $dir;
    }

    function view($id)
    {
        $sx = '';
        $dt = $this->find($id);

        if ($dt['bs_status'] == 2) {
            if ($dt['bs_rdf'] > 0) {
                $url = 'https://brapci.inf.br/admin/a/' . $dt['bs_rdf'];
                echo metarefresh($url, 0);
                exit;
            }
        }

        $sx .= bsc($this->action($dt), 12);

        if ($dt != []) {
            $js = (array)json_decode($dt['bs_post']);

            $sx .= bsc(msg('brapci.RDFID'), 3, 'small mt-2');
            $url = '<a href="https://brapci.inf.br/v/' . $dt['bs_rdf'].'" target="_blank">'. $dt['bs_rdf'].'</a>';
            $sx .= bsc($url, 3, 'small mt-2');

            $sx .= bsc(msg('brapci.hasTitle'), 3, 'small mt-2');
            $sx .= bsc('<b>'.$dt['bs_title'].'</b>', 9, 'small mt-2');

            foreach ($js as $key => $value) {
                $sx .= bsc(msg('brapci.' . $key), 3, 'small mt-2');
                $sx .= bsc($value . '&nbsp;', 9, 'border-top border-secondary');
            }
        } else {
            $sx .= 'Registro não localizado ' . $id;
        }
        $sx = bsc($sx, 5);
        $iframe = $this->show_pdf($dt);
        $sx .= bsc($iframe, 7);
        return bs($sx);
    }

    function chache_status($id, $sta)
    {
        $dd['bs_status'] = $sta;
        $this->set($dd)->where('id_bs', $id)->update();
        return True;
    }

    function action($dt)
    {
        $sx = '';
        $sta = $dt['bs_status'];
        $id = $dt['id_bs'];
        $btn = '<a href="' . PATH . 'admin/book/status/0" class="btn btn-outline-warning ms-2">' . lang('brapci.return') . '</a>';
        switch ($sta) {
            case '0':
                $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/1" class="btn btn-outline-primary">' . lang('brapci.accept') . '</a>';
                $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/9"  class="btn btn-outline-danger ms-2">' . lang('brapci.reject') . '</btn>';
                $sx .= $btn;
                break;
            case '1':
                $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/2" class="btn btn-outline-primary">' . lang('brapci.create_book') . '</a>';
                $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/9"  class="btn btn-outline-danger ms-2">' . lang('brapci.reject') . '</btn>';
                $sx .= $btn;
                break;
            case '7':
                $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/12"  class="btn btn-outline-danger ms-2">' . lang('brapci.import_json') . '</btn>';
                $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/13"  class="btn btn-outline-danger ms-2">' . lang('brapci.manual') . '</btn>';
                $sx .= $btn;
                break;
            default:
                $sx .= 'No actions';
                break;
        }
        return $sx;
    }

    function show_pdf($dt)
    {
        $html = PATH . 'admin/book/preview/' . $dt['id_bs'];
        $sx = $html . '
            <iframe src="' . $html . '" style="width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;">
                Your browser doesnt support iframes
            </iframe>';
        return $sx;
    }

    function savePDF($id) {}

    function list($sta)
    {
        $sx = '';
        $dt = $this
            ->where('bs_status', $sta)
            ->findAll();
        foreach ($dt as $id => $line) {
            $link = '<a href="' . PATH . 'admin/book/view/' . $line['id_bs'] . '">';
            $linka = '</a>';
            $js = (array)$line['bs_post'];
            $sx .= '<li>';
            $js = $js[0];
            $js = (array)json_decode($js);

            if (isset($js['b_titulo'])) {
                $sx .= '<b>';
                $sx .= $link . $line['bs_title'] . $linka;
                $sx .= '<br><i>' . $js['b_autor'] . '</i>';
                $sx .= '</b>';
            } else {
                $sx .= $line['created_at'];
                $sx .= '<br>';
                $sx .= '<b>';
                $sx .= $link . $line['bs_title'] . $linka;
                $sx .= '<br><i>' . 'sem autoria registrada' . '</i>';
                $sx .= '</b>';
            }

            $sx .= '</li>';
        }
        return $sx;
    }

    function resume()
    {
        $sx = '';
        $dt = $this
            ->select("count(*) as total, bs_status")
            ->where('bs_status', 1)
            ->ORwhere('bs_status', 2)
            ->ORwhere('bs_status > ', 2)
            ->groupBy('bs_status')
            ->orderBy('bs_status')
            ->findAll();
        foreach ($dt as $id => $line) {
            $link = '<a class="text-danger" href="' . PATH . 'admin/book/status/' . $line['bs_status'] . '">';
            $linka = '</a>';
            $sx .= '<li class="text-danger" style="font-size: 0.7em;">';
            $sx .= $link . lang('brapci.book_status_' . $line['bs_status']) . $linka;
            $sx .= ' <b>';
            $sx .= '(' . $line['total'] . ')';
            $sx .= '</b>';
            $sx .= '</li>';
        }
        $sx .= '<li class="text-danger" style="font-size: 0.7em;">';
        $sx .= '<a class="text-danger" href="' . PATH . 'admin/book/">';
        $sx .= lang('brapci.book_hasvestings_publisher');
        $sx .= '</a>';
        $sx .= '</li>';

        if ($sx != '') {
            $sx = '<b>Livros submetidos</b>' . $sx;
        }
        return $sx;
    }

    function sendEmail($id)
    {
        $dt = $this->where('id_bs', $id)->first();
        $email = $dt['bs_email'];
        $subject = 'Submissão de livro';
        $name = 'Rene Faustino Gabriel Junior';
        $to = [$email];

        $btn_concordancia = '<a href="https://brapci.inf.br/books/disclaimer/' . $id . '/' . md5($id . 'brapci_livros') . '" style="padding: 5px 10px; border:1px solid #000; border-radius: 10px;">Concordo com os termos</a>';

        /* Enviar e-mail */
        $txt = '';
        $txt .= '<table width="600" border=0>';
        $txt .= '<tr><td><img src="cid:$image1" style="width: 100%;"></td></tr>';
        $txt .= '<tr><td>';
        $txt .= 'Prezado autor ' . $name . ',<br>';
        $txt .= '<br>';
        $txt .= 'Sua submissão foi registrada e será analisada, porém é necessário que concorde com os termos.';
        $txt .= '<br><br>';
        $txt .=
            '<h2>Disclaimer - Brapci-Livros</h2><br>
            A Brapci-Livros tem como objetivo promover o acesso gratuito a livros e materiais educativos de domínio público ou disponibilizados sob licenças abertas.
            Todos os conteúdos disponíveis nesta plataforma foram selecionados para garantir que estejam em conformidade com as leis de direitos autorais e licenças aplicáveis.
            Não existe cobrança para registrar ou acessar as obras.
            <br><br>
            <b>Direitos Autorais e Licenças:</b>
            <br>
            Os livros e materiais disponíveis nesta base de dados são de domínio público ou licenciados sob termos que permitem sua livre distribuição.
            Podem também ser disponibilizados com as dividas autorizações dos autores e da editora.
            <br>
            No entanto, é responsabilidade dos usuários verificar a licença específica de cada obra antes de utilizá-la para fins comerciais ou redistribuição. Quaisquer usos fora do escopo permitido pela licença exigem a obtenção de permissão prévia do(s) titular(es) dos direitos autorais.
            <br><br>
            <b>Limitação de Responsabilidade:</b>
            <br>
            Embora nos esforcemos para garantir a precisão das informações e a conformidade legal dos materiais incluídos nesta base de dados, não nos responsabilizamos por eventuais erros, omissões, ou pela interpretação dos conteúdos pelos usuários. O uso dos materiais disponibilizados é de total responsabilidade do usuário.
            <br><br>
            <b>Atualizações e Alterações:</b>
            <br>
            Reservamo-nos o direito de atualizar ou remover qualquer material desta base de dados sem aviso prévio, a fim de garantir a conformidade com as leis de direitos autorais e as políticas da plataforma.
            <br><br>
            <b>Contato:</b>
            <br><br>
            Caso identifique qualquer material que não deva estar disponível na base de dados ou tenha dúvidas sobre os termos de uso, entre em contato conosco pelo e-mail brapcici@gmail.com.<br>
            <a href="https://brapci.inf.br/#/books">https://brapci.inf.br/#/books</a>
            <br><br>
            ' . $btn_concordancia . '
            ';
        $txt .= '</td></tr></table>';
        $subject = '[BRAPCI-LIVROS] ';
        $subject .= 'Termo de submissão';

        sendemail($email, $subject, $txt);
    }

    function register()
    {
        $PS = array_merge($_POST, $_GET);
        $PSj = json_encode($PS);
        $RSP = [];
        $dt = [];
        if (isset($PS['fileO'])) {
            $dt = $this->where('bs_arquivo', $PS['fileO'])->first();
            if ($dt == []) {
                $dt['bs_title'] = $PS['file'];
                $dt['bs_post'] = $PSj;
                $dt['bs_status'] = 0;
                $dt['bs_email'] = $PS['email'];
                $dt['bs_arquivo'] = $PS['fileO'];
                $dt['id_b'] = $this->insert($dt);
                $RSP['status'] = '200';
                $RSP['message'] = 'Registro efetuado com sucesso';
            } else {
                $RSP['status'] = '201';
                $RSP['message'] = 'Registro já existe na base de dados';
                $dt['id_b'] = $dt['id_bs'];
            }
            $this->sendEmail($dt['id_b']);
        } else {
            $RSP['status'] = '500';
            $RSP['message'] = 'Arquivo vazio';
            $RSP['post'] = $PSj;
        }

        return $RSP;
    }
}
