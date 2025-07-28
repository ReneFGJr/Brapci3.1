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

        $IDch = $RDFconcept->createConcept(['Name' => 'Capítulo de livro', 'Class' => 'Section', 'Lang' => 'pt']);
        $RDFdata->register($IDch, 'hasSectionOf', $IDch, 0);

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
245  10$aPesquisa reprodutível aberta: desafios, práticas e tecnologias
300  ##$a121-140 p.
520  ##$aAborda a importância da reprodutibilidade na ciência aberta, distinguindo-a da replicabilidade. Discute os desafios técnicos, culturais e institucionais, e apresenta ferramentas e políticas que promovem práticas científicas mais transparentes e confiáveis.
650  #7$aPesquisa reprodutível
650  #7$aCiência aberta
650  #7$aCompartilhamento de dados
650  #7$aTecnologia científica
650  #7$aTransparência científica
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
