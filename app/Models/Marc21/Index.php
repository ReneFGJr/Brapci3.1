<?php

namespace App\Models\Marc21;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'liked';
    protected $table            = 'likes';
    protected $primaryKey       = 'id_lk';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lk',
        'lk_user',
        'lk_id',
        'lk_status',
        'lk_update'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function process($tx)
        {
            pre($tx);
        }

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '', $d6 = '')
    {
        if (empty($d1)) {
            $RSP = [];
            $RSP['status'] = '200';
            $RSP['message'] = 'Marc21 API is running';
            $RSP['data'] = [];
            return $RSP;
        }
        switch ($d1) {
            case 'index':
                $RSP = $this->prompt();
                break;
            case 'importChapter':
                if ($d2 == '') {
                    $RSP['status'] = '400';
                    $RSP['message'] = 'Book ID is required';
                    return $RSP;
                }
                if ($d3 == '') {
                    $RSP['status'] = '400';
                    $RSP['message'] = 'Chapter data is required';
                    return $RSP;
                }
                $data = $this->import();
                $RSP = $this->saveImportChapter($d2,$data,$d3);
                break;
            case 'sample':
                $sx = $this->sample();
                break;
            default:
                return 'Invalid command';
        }
        return $RSP;
    }

    function trataTitle($title)
    {
        if (substr($title, -1) == ',') {
            $title = substr($title, 0, -1);
        }
        if (substr($title, -1) == '.') {
            $title = substr($title, 0, -1);
        }

        $title = troca($title, ': :', ':');
        $title = troca($title, ' :', ':');
        $title = troca($title, '[', '');
        $title = troca($title, ']', '');
        $title = troca($title, '/', '');
        $title = troca($title, '\\', '');
        $title = troca($title, '*', '');
        $title = troca($title, '?', '');
        $title = troca($title, '"', '');
        $title = troca($title, '<', '');
        $title = troca($title, '>', '');
        $title = troca($title, '|', '');
        $title = trim($title);
        return $title;
    }


    function trataData($data)
    {
        $data = sonumero($data);
        if (strlen($data) == 4) {
            return $data;
        }
        if (strlen($data) == 8) {
            return substr($data, 0, 4);
        }
        if (strlen($data) == 6) {
            return substr($data, 0, 4);
        }
        if (strlen($data) > 4) {
            return substr($data, -4);
        }
        return '0';
    }

    function marc21($txt,$isbn)
        {
            $txt = utf8_encode($txt);
            $marcArr = $this->marc21_to_array($txt);
            $meta    = $this->marc_extract_metadata($marcArr);

            $dt = [];
            $dt['title'] = $this->trataTitle($meta['title'] ?? '[Sem título]');
            $dt['place'] = $this->trataTitle($meta['publication']['place'] ?? '[Sem Local]');
            $dt['publisher'] = $this->trataTitle($meta['publication']['publisher'] ?? '[Sem Editora]');
            $dt['date'] = $this->trataData($meta['publication']['date'] ?? '');
            $dt['isbn13'] = sonumero($meta['isbn'] ?? '');
            $dt['language'] = $meta['publication']['language'] ?? 'pt_BR';
            $dt['authors'] = $meta['authors'] ?? ['[Sem Autor]'];
            $dt['status'] = '1';
            foreach ($dt['authors'] as $k => $v) {
                $dt['authors'][$k] = nbr_author($v, 7);
            }


            pre($dt,false);
            //pre($marcArr);
            pre($meta);
        }

    function marc21_to_array(string $marcRaw): array
    {
        $marc = trim(preg_replace('/\s+/', ' ', $marcRaw)); // normaliza espaços

        // 1) Extrair leader (tudo antes da 1ª tag no padrão dddII|)
        $leader = '';
        $rest   = $marc;
        if (preg_match('/^(.*?)(?=\d{3}[0-9_]{2}\|)/s', $marc, $m)) {
            $leader = trim($m[1]);
            $rest   = substr($marc, strlen($m[0]));
        }

        // 2) Quebrar por campos (lookahead mantém o início de cada campo)
        $fieldsRaw = preg_split('/(?=\d{3}[0-9_]{2}\|)/', $rest, -1, PREG_SPLIT_NO_EMPTY);

        $fields = [];
        foreach ($fieldsRaw as $chunk) {
            $chunk = trim($chunk);

            // Tag (3), indicadores (2), depois vem "|"
            if (!preg_match('/^(?<tag>\d{3})(?<ind>[0-9_]{2})\|(?<subs>.*)$/', $chunk, $mm)) {
                // fallback: às vezes pode vir controle sem subcampos (raro nesse formato)
                if (preg_match('/^(?<tag>\d{3})(?<ind>[0-9_]{2})(?<data>.+)$/', $chunk, $mc)) {
                    $tag = $mc['tag'];
                    $ind = $mc['ind'];
                    $fields[$tag][] = [
                        'ind1'      => $ind[0] ?? ' ',
                        'ind2'      => $ind[1] ?? ' ',
                        'subfields' => [],
                        'control'   => trim($mc['data']),
                        'raw'       => $chunk,
                    ];
                }
                continue;
            }

            $tag = $mm['tag'];
            $ind = $mm['ind'];
            $sub = $mm['subs'];

            // 3) Parse de subcampos: "|aValor A|bValor B|cValor C"
            $subfields = [];
            // Garante que começa com '|'
            if ($sub !== '' && $sub[0] !== '|') {
                $sub = '|' . $sub;
            }
            // Divide por '|', ignorando o 1º vazio
            $parts = array_values(array_filter(explode('|', $sub), fn($v) => $v !== ''));

            foreach ($parts as $p) {
                $code = substr($p, 0, 1);
                $val  = trim(substr($p, 1));
                if ($code === false || $val === false) {
                    continue;
                }
                // Agrupa múltiplos subcampos com a mesma letra
                $subfields[$code][] = $val;
            }

            $fields[$tag][] = [
                'ind1'      => $ind[0] ?? ' ',
                'ind2'      => $ind[1] ?? ' ',
                'subfields' => $subfields,
                'raw'       => $chunk,
            ];
        }

        return [
            'leader' => $leader,
            'fields' => $fields,
        ];
    }

    /**
     * Helper: extrai metadados comuns a partir do array do MARC.
     * Ajuste conforme necessidade (outros campos/subcampos).
     */
    function marc_extract_metadata(array $marc): array
    {
        $f = $marc['fields'] ?? [];

        $getFirst = function ($tag, $code) use ($f) {
            if (!isset($f[$tag])) return null;
            foreach ($f[$tag] as $occ) {
                if (!empty($occ['subfields'][$code][0])) {
                    return $occ['subfields'][$code][0];
                }
            }
            return null;
        };

        $getAll = function ($tag, $code) use ($f) {
            $out = [];
            if (!isset($f[$tag])) return $out;
            foreach ($f[$tag] as $occ) {
                if (!empty($occ['subfields'][$code])) {
                    foreach ($occ['subfields'][$code] as $v) {
                        $out[] = $v;
                    }
                }
            }
            return $out;
        };

        // Título (245 a, b) e responsabilidade (245 c)
        $title      = trim(($getFirst('245', 'a') ?? '') . (($b = $getFirst('245', 'b')) ? ' : ' . $b : ''));
        $statement  = $getFirst('245', 'c'); // responsabilidade
        // Publicação (260 a: lugar; b: editora; c: data)
        $pub_place  = $getFirst('260', 'a');
        $publisher  = $getFirst('260', 'b');
        $pub_date   = $getFirst('260', 'c');
        // Descrição física (300 a, c)
        $extent     = $getFirst('300', 'a');
        $dim        = $getFirst('300', 'c');
        // ISBN (020 a)
        $isbn       = $getFirst('020', 'a');
        // Classificação (082 a)
        $ddc        = $getFirst('082', 'a');
        // Assuntos (650 a, x)
        $subjects_a = $getAll('650', 'a');
        $subjects_x = $getAll('650', 'x');
        $subjects   = array_values(array_filter(array_merge($subjects_a, $subjects_x)));

        // Autores (em alguns dumps vêm em 100/700; no seu exemplo, autores podem estar em outros campos)
        $mainAuthor = $getFirst('100', 'a');             // autor principal, se existir
        $others     = $getAll('700', 'a');               // autores secundários
        $authors    = array_values(array_filter(array_merge([$mainAuthor], $others)));

        return [
            'leader'        => $marc['leader'] ?? '',
            'title'         => $title ?: null,
            'responsibility' => $statement,
            'publication'   => [
                'place'     => $pub_place,
                'publisher' => $publisher,
                'date'      => $pub_date,
            ],
            'physical'      => [
                'extent'    => $extent,
                'dimensions' => $dim,
            ],
            'isbn'          => $isbn,
            'ddc'           => $ddc,
            'subjects'      => $subjects,
            'authors'       => $authors,
            'raw_fields'    => $marc['fields'] ?? [],
        ];
    }

    function saveImportChapter($book, $data,$nr)
    {
        // Logic to save the imported chapter data
        // This function should contain the logic to process and save the chapter data
        // Implementation details would depend on the specific requirements.
        if (empty($data)) {
            return false;
        }
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $Book = $RDF->le($book);

        $ISBN = $RDF->extract($Book, 'hasISBN');
        if (empty($ISBN)) {
         $RSP['status'] = '404';
         $RSP['message'] = 'ISBN not found';
         return $RSP;
        }

        $Label = $ISBN.'_'.strzero($nr, 2);
        $dt = [];
        $dt['Name'] = $Label;
        $dt['Class'] = 'BookChapter';
        $dt['Lang'] = 'nn';
        $IDch = $RDFconcept->createConcept($dt);

        $RDFdata->register($book, 'hasBookChapter', $IDch,0);

        /* Title */
        $title = $this->get245($data);
        if (empty($title)) {
            $RSP['status'] = '404';
            $RSP['message'] = 'Title not found';
            return $RSP;
        }
        $RDFdata->register($IDch, 'hasTitle', 0, $title);

        /* Authors */
        $authors = $this->get700($data);
        foreach ($authors as $author) {
            if (empty($author)) {
                continue;
            }
            $IDa = $RDFconcept->createConcept(['Name' => $author, 'Class' => 'Person', 'Lang' => 'nn']);
            $RDFdata->register($IDch, 'hasAuthor', $IDa, 0);
        }

        /* Resumo */
        $resumo = $this->get520($data);
        if (empty($resumo)) {
            $RSP['status'] = '404';
            $RSP['message'] = 'Resumo not found';
            return $RSP;
        }
        $RDFdata->register($IDch, 'hasAbstract', 0, $resumo);

        /* Keywords */
        $keywords = $this->get650($data);
        foreach ($keywords as $keyword) {
            if (empty($keyword)) {
                continue;
            }
            $IDk = $RDFconcept->createConcept(['Name' => $keyword, 'Class' => 'Subject', 'Lang' => 'pt']);
            $RDFdata->register($IDch, 'hasSubject', $IDk, 0);
        }


        /* Pagination */
        $RDFliteral = new \App\Models\RDF2\RDFliteral();
        $pagination = $this->get300($data);
        if ($pagination[0] != '') {
            $pag = $RDFliteral->register($pagination[0]);
            $RDFdata->register($IDch, 'hasPageStart', 0, $pag);
        }
        if ($pagination[1] != '') {
            $pag = $RDFliteral->register($pagination[1]);
            $RDFdata->register($IDch, 'hasPageEnd', 0, $pag);
        }

        $IDchap = $RDFconcept->createConcept(['Name' => 'Capítulo de livro', 'Class' => 'Section', 'Lang' => 'pt']);
        $RDFdata->register($IDch, 'hasSectionOf', $IDchap, 0);

        $RSP['status'] = '200';
        $RSP['message'] = 'Chapter imported successfully';
        $RSP['data'] = [
            'book' => $book,
            'chapter' => $IDch,
            'title' => $title,
            'authors' => $authors,
            'resumo' => $resumo,
            'keywords' => $keywords,
            'pagination' => $pagination
        ];
        return $RSP;
    }

    function get300($data)
    {
        $pagination = '';
        if (isset($data['300'])) {
            $pagination = $data['300'][0];
        }
        if (isset($data['=300'])) {
            $pagination = $data['=300'][0];
        }
        $t = explode('$', $pagination);
        foreach ($t as $v) {
            if (strpos($v, 'a') === 0) {
                $pagination = substr($v, 1);
                break;
            }
        }
        $pagination = troca($pagination,'p.','');
        if (strpos($pagination, '-') !== false) {
            $pagination = explode('-', $pagination);
            if ($pagination[0] > $pagination[1]) {
                $pagx = $pagination[0];
                $pagination[0] = $pagination[1];
                $pagination[1] = $pagx;
            }
        } else {
            $pagination = [$pagination, ''];
        }
        return $pagination;
    }

    function get650($data)
    {
        $keywords = [];
        foreach ($data as $key => $value) {
            if ($key == '=650') {
                $key = '650';
            }
            if (strpos($key, '650') === 0) {
                foreach ($value as $keyword) {
                    if (strpos($keyword, '$a')) {
                        $keyword = substr($keyword, strpos($keyword, '$a') + 2);
                        $keyword = strtolower($keyword);
                    }
                    $keywords[] = nbr_title($keyword, 7);
                }
            }
        }
        return $keywords;
    }

    function get520($data)
    {
        $resumo = '';
        if (isset($data['520'])) {
            $resumo = $data['520'][0];
        }
        if (isset($data['=520'])) {
            $resumo = $data['=520'][0];
        }
        $t = explode('$', $resumo);
        foreach ($t as $v) {
            if (strpos($v, 'a') === 0) {
                $resumo = substr($v, 1);
                break;
            }
        }
        $resumo = trim($resumo);
        return $resumo;
    }

    function get700($data)
    {
        $authors = [];
        foreach ($data as $key => $value) {
            if ($key == '=700') { $key = '700'; }
            if (strpos($key, '700') === 0) {
                foreach ($value as $author) {
                    if (strpos($author, '$a')) {
                        $author = substr($author, strpos($author, '$a') + 2);
                    }
                    $authors[] = nbr_author($author,7);
                }
            }
        }
        return $authors;
    }
    /*********** GET TÌTULO */
    function get245($data)
    {
        $title = '';
        if (isset($data['245'])) {
            $title = $data['245'][0];
        }
        if (isset($data['=245'])) {
            $title = $data['=245'][0];
        }
        $t = explode('$', $title);
        foreach ($t as $v) {
            if (strpos($v, 'a') === 0) {
                $title = substr($v, 1);
                break;
            }
        }
        $title = nbr_title($title);
        $title = trim($title);
        return $title;
    }

    function prompt()
    {
        return ['prompt'=>
'Catalogue em formato marc21 todos os 11 capítulos, gere uma abstract (com 250 palavras) e cinco palavras-chave.
Coloque os autores no campo 700, separe os marcadores
Insira a paginação.
Gere e mostre o código MARC para cada um dos capitulos, com todos os campos
'];

$prop = ['Gere um resumo do capitulo 3 com 250 palavras e identifique 5 palavras-chave
Gere o código MARC21 para esse capítulo com os campos como no exemplo

245  10$aAcesso aberto: um ensaio sobre suas dinâmicas e derivações
300  ##$a58-77 p.
520  ##$aDiscute o desenvolvimento do acesso aberto, suas motivações, modelos econômicos e desafios. Analisa o papel da tecnologia e da política no avanço desse movimento.
650  #7$aAcesso aberto
650  #7$aComunicação científica
650  #7$aPublicações
650  #7$aModelos de negócios
650  #7$aTecnologia
700  1#$aSarita Albagli
700  1#$aMarcos Sfair Sunye
'];

    }

    function sample()
        {
            $txt = '
245  10$aRevisão por pares aberta
300  ##$a111-130 p.
520  ##$aO capítulo apresenta a revisão por pares aberta como uma prática que promove a transparência e a colaboração na avaliação científica. Discute suas modalidades, benefícios, desafios e exemplos práticos, destacando sua relevância no contexto da ciência aberta.
650  #7$aRevisão por pares aberta
650  #7$aAvaliação científica
650  #7$aTransparência
650  #7$aCiência aberta
650  #7$aComunicação científica
700  1#$aPatricia Pedri

            ';
            return $txt;
        }

    function import()
    {
        // Import logic for Marc21 data
        // This function should contain the logic to import Marc21 records
        // from an external source or file into the database.
        // Implementation details would depend on the specific requirements.
        $txt = get("marc21");
        $txt = $this->sample();
        if (empty($txt)) {
            return false;
        }
        $txt = str_replace("\r\n", "\n", $txt);
        $txt = str_replace("\r", "\n", $txt);
        $lines = explode("\n", $txt);
        $data = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $parts = explode(' ', $line, 2);
                if (count($parts) == 2) {
                    $tag = $parts[0];
                    $value = $parts[1];
                    if (!isset($data[$tag])) {
                        $data[$tag] = [];
                    }
                    $data[$tag][] = $value;
                }
            }
        }
        return $data;
    }
}
