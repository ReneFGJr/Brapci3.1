<?php

namespace App\Models\CDU;

use CodeIgniter\Model;
use CodeIgniter\Files\File;
use SimpleXMLElement;

class QuizMoodleQuestion extends Model
{
    protected $DBGroup          = 'CDU';
    protected $table            = 'moodle_question';
    protected $primaryKey       = 'id_q';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_q',
        'q_type',
        'q_text',
        'q_questiontext',
        'q_generalfeedback',
        'q_penalty',
        'q_defaultgrade',
        'q_hidden',
        'q_usecase'
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

    public function exportMoodleQuiz()
    {
        $QuizMoodleCategory = new \App\Models\CDU\QuizMoodleCategory();
        $QuizMoodleQuestion = new \App\Models\CDU\QuizMoodleQuestion();
        $QuizMoodleQuestionAnswer = new \App\Models\CDU\QuizMoodleQuestionAnswer();

        // 1) Busca categorias
        $cats = $QuizMoodleCategory
            ->select('c_name, c_format')
            ->orderBy('id_c')
            ->get()
            ->getResult();

        // 2) Busca todas as questões (sem filtro de categoria, pois não há FK)
        $qs = $QuizMoodleQuestion
            ->select('id_q, q_type, q_text, q_questiontext, q_generalfeedback, q_defaultgrade, q_penalty, q_hidden, q_usecase')
            ->orderBy('id_q')
            ->get()
            ->getResult();

        // 3) Busca todas as respostas
        $answers = $QuizMoodleQuestionAnswer
            ->select('id_qa, qa_question, qa_fraction, qa_format, qa_text, qa_feedback')
            ->orderBy('id_qa')
            ->get()
            ->getResult();

        // 4) Cria o objeto XML
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><quiz></quiz>');

        // 5) Adiciona as categorias
        foreach ($cats as $cat) {
            $q = $xml->addChild('question');
            $q->addAttribute('type', 'category');
            $catNode = $q->addChild('category');
            // Moodle espera algo como <text>$categoria</text>
            $catNode->addChild('text', htmlspecialchars($cat->c_name, ENT_XML1));
        }

        // 6) Adiciona as questões e suas respostas
        foreach ($qs as $qRow) {
            $q = $xml->addChild('question');
            $q->addAttribute('type', $qRow->q_type);

            // nome
            $name = $q->addChild('name');
            $name->addChild('text', htmlspecialchars(substr($qRow->q_text, 0, 255), ENT_XML1));

            // texto da questão
            $qt = $q->addChild('questiontext');
            $qt->addAttribute('format', 'html');
            $qt->addChild('text', "$qRow->q_questiontext");

            // feedback geral
            $gf = $q->addChild('generalfeedback');
            $gf->addAttribute('format', 'html');
            $gf->addChild('text', "$qRow->q_generalfeedback>");

            // defaultgrade e penalty
            $q->addChild('defaultgrade', $qRow->q_defaultgrade);
            $q->addChild('penalty', $qRow->q_penalty);

            // hidden e usecase
            $q->addChild('hidden', $qRow->q_hidden);
            $q->addChild('usecase', $qRow->q_usecase);

            // respostas que pertencem a esta questão
            foreach ($answers as $ans) {
                if ($ans->qa_question != $qRow->id_q) continue;

                $a = $q->addChild('answer');
                $a->addAttribute('fraction', $ans->qa_fraction);
                $a->addAttribute('format', $ans->qa_format);

                $a->addChild('text', "$ans->qa_text");

                $fb = $a->addChild('feedback');
                $fb->addAttribute('format', 'html');
                $fb->addChild('text', "$ans->qa_feedback>");
            }
        }

        // 5) Converte para string
        $xmlString = $xml->asXML();

        return $xmlString;
    }

    public function importMoodleQuiz($fileInput): array
    {
        // Se veio um objeto File/UploadedFile, mova-o para uploads e obtenha o path real
        if ($fileInput instanceof File) {
            if (! $fileInput->isReadable() || $fileInput->getSize() === 0) {
                throw new \Exception("Arquivo inválido ou vazio.");
            }
            $newName = $fileInput->getRandomName();
            $fileInput->move(WRITEPATH . 'uploads', $newName);
            $filePath = WRITEPATH . 'uploads/' . $newName;
        }
        // Caso seja string, consideramos que já é um caminho no FS
        elseif (is_string($fileInput)) {
            $filePath = $fileInput;
            if (! is_file($filePath) || ! is_readable($filePath)) {
                throw new \Exception("Arquivo não encontrado ou inacessível: {$filePath}");
            }
        } else {
            throw new \Exception("Tipo de entrada inválido para importMoodleQuiz()");
        }

        // Lê e limpa o XML
        $xmlRaw   = file_get_contents($filePath);
        $xmlClean = preg_replace('/^\x{FEFF}/u', '', $xmlRaw);
        $pos      = mb_strpos($xmlClean, '<');
        if ($pos !== false && $pos > 0) {
            $xmlClean = mb_substr($xmlClean, $pos);
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlClean);
        if ($xml === false) {
            $errs = libxml_get_errors();
            libxml_clear_errors();
            $msg  = "Falha ao parsear XML:";
            foreach ($errs as $e) {
                $msg .= "\n Linha {$e->line}, Coluna {$e->column}: {$e->message}";
            }
            throw new \Exception($msg);
        }

        // Importação propriamente dita
        $count = ['categories' => 0, 'questions' => 0, 'answers' => 0];
        $QuizMoodleCategory = new \App\Models\CDU\QuizMoodleCategory();

        foreach ($xml->question as $q) {
            // categoria
            if ((string)$q['type'] === 'category') {
                $dt = $QuizMoodleCategory->where('c_name', (string)$q->category->text)->first();
                if (!$dt) {

                    $QuizMoodleCategory->set([
                        'c_type'   => 'category',
                        'c_name'   => (string)$q->category->text,
                        'c_format' => (string)($q->info['format'] ?? 'moodle_auto_format'),
                    ])->insert();
                }
                $count['categories']++;
                continue;
            }
            // questão
            $this->db->table('moodle_question')->insert([
                'q_type'           => (string)$q['type'],
                'q_text'           => (string)$q->name->text,
                'q_questiontext'   => (string)$q->questiontext->text,
                'q_generalfeedback' => (string)$q->generalfeedback->text,
                'q_defaultgrade'   => (float)$q->defaultgrade,
                'q_penalty'        => (float)$q->penalty,
                'q_hidden'         => (int)$q->hidden,
                'q_usecase'        => (int)$q->usecase,
            ]);
            $qid = $this->db->insertID();
            $count['questions']++;

            // respostas
            $QuizMoodleQuestionAnswer = new \App\Models\CDU\QuizMoodleQuestionAnswer();
            foreach ($q->answer as $ans) {
                $QuizMoodleQuestionAnswer->set([
                    'qa_question' => $qid,
                    'qa_fraction' => (float)$ans['fraction'],
                    'qa_format'   => (string)$ans['format'],
                    'qa_text'     => (string)$ans->text,
                    'qa_feedback' => isset($ans->feedback->text) ? (string)$ans->feedback->text : '',
                ])->insert();
                $count['answers']++;
            }
        }

        return $count;
    }
}
