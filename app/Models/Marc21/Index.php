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
        $sx = '';
        if (empty($d1)) {
            return 'API Marc21';
        }
        switch ($d1) {
            case 'index':
                $sx = $this->prompt();
                break;
            case 'importChapter':
                $data = $this->import();
                $RSP = $this->saveImportChapter($d2,$data);
                break;
            case 'sample':
                $sx = $this->sample();
                break;
            default:
                return 'Invalid command';
        }
        return $sx;
    }

    function saveImportChapter($book, $data)
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

        $Label = $ISBN.'_03';
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
        echo "Title: $title\n";

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
        pre($data);



        echo "Chapter ID: $IDch\n";
        pre($data);

        return true;
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
        return 'Catalogue em formato marc21 todos os 11 capítulos, gere uma abstract e palavras-chave.
Coloque os autores no campo 700, separe os marcadores
Insira a paginação';
    }

    function sample()
        {
            $txt = '
=245  10$aAcesso aberto: um ensaio sobre suas dinâmicas e derivações
=300  ##$a58-77 p.
=520  ##$aDiscute o desenvolvimento do acesso aberto, suas motivações, modelos econômicos e desafios. Analisa o papel da tecnologia e da política no avanço desse movimento.
=650  #7$aAcesso aberto
=650  #7$aComunicação científica
=650  #7$aPublicações
=650  #7$aModelos de negócios
=650  #7$aTecnologia
=700  1#$aSarita Albagli
=700  1#$aMarcos Sfair Sunye
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
