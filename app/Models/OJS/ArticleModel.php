<?php

namespace App\Models\OJS;

use CodeIgniter\Model;

use CodeIgniter\Database\BaseConnection;

class ArticleModel extends Model
{
    protected $DBGroup = 'ojs_import'; // Conexão com o banco ojs_import
    protected $table = 'inma'; // nome da tabela no banco
    protected $primaryKey = 'idR';
    protected $useAutoIncrement = true;

    // Conexão com o banco ojs_import
    protected $dbOjsImport;

    public function __construct()
    {
        parent::__construct();
        // Conecta ao banco ojs_import
        $this->dbOjsImport = \Config\Database::connect('ojs_import');
    }

    public function createDraftSubmission(int $journalId, array $article): array
    {
    }
    public function getArticlesForSubmission(int $journalId, array $articleIds): array
    {
    }
    public function updateSubmittedArticle(int $journalId, int $articleId, array $data): bool
    {
    }
    public function markArticleAsSubmitted(int $journalId, int $articleId): bool
    {
    }
    public function markArticleAsInEvaluation(int $journalId, int $articleId): bool
    {
    }
    public function markArticleAsSentToCopyediting(int $journalId, int $articleId): bool
    {
    }
    public function markArticleAsInProduction(int $journalId, int $articleId): bool
    {
    }
    public function markArticleAsScheduled(int $journalId, int $articleId): bool
    {
    }
    public function getSubmittedArticle(int $journalId, int $articleId): ?array
    {
    }

    public function getArticleForJournal(int $journalId, int $articleId): ?array
    {
    }
    public function updateOjsSubmissionFromArticle(int $journalId, array $article): array
    {
    }

    private function syncOjsContributors(int $submissionId, int $publicationId, string $authors, string $affiliation): array
    {
    }
    private function formatContributorError(int $index, int $status, array $result): string
    {
    }
    public function updateArticleFromOjs(int $journalId, array $article): array
    {
    }
    public function getSubmissionDetails(int $journalId, int $submissionId, bool $useEditorApiKey = false): array
    {
    }
    public function getArticlesForStatusSummary(int $journalId): array
    {
    }
    public function getArticlesForStatus(int $journalId, int $status, ?string $year = null): array
    {
        $builder = $this->dbOjsImport->table('article')
            ->select('article.*, COALESCE(journal_submit_id, submit_id) AS journal_submit_id', false)
            ->where('journal_id', $journalId)
            ->where('status', $status);

        if ($year !== null) {
            $builder->where('Year', $year);
        }

        return $builder
            ->orderBy('Year', 'DESC')
            ->orderBy('idR', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getSubmittedArticles(int $journalId, ?string $year = null): array
    {
    }

}
