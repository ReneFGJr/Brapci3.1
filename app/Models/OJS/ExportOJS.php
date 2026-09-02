<?php

namespace App\Models\OJS;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Model;
use RuntimeException;

/**
 * Exporta registros da tabela article no formato XML nativo do OJS.
 */
class ExportOJS extends Model
{
    protected $DBGroup = 'ojs_import';
    protected $table = 'article';
    protected $primaryKey = 'idR';
    protected $returnType = 'array';

    private BaseConnection $dbOjsImport;

    public function __construct()
    {
        parent::__construct();
        $this->dbOjsImport = \Config\Database::connect('ojs_import');
    }

    /**
     * Lê um artigo do banco e devolve seu XML nativo do OJS.
     *
     * O journalId é opcional, mas pode ser informado para garantir que o artigo
     * pertence à revista selecionada.
     */
    public function exportArticle(int $articleId, ?int $journalId = null): string
    {
        $builder = $this->dbOjsImport->table($this->table)
            ->where($this->primaryKey, $articleId);

        if ($journalId !== null) {
            $builder->where('journal_id', $journalId);
        }

        $article = $builder->get()->getRowArray();
        if ($article === null) {
            throw new RuntimeException('Artigo não encontrado para exportação: ' . $articleId);
        }

        return $this->generateXml($article);
    }

    /**
     * Gera o XML a partir de um registro da tabela article.
     */
    public function generateXml(array $article): string
    {
        $articleId = (int) ($article['idR'] ?? 0);
        if ($articleId <= 0) {
            throw new RuntimeException('O registro do artigo não possui um idR válido.');
        }

        $title = trim((string) ($article['Title'] ?? ''));
        if ($title === '') {
            throw new RuntimeException('O artigo não possui título para exportação.');
        }

        $year = $this->normalizeYear($article['Year'] ?? null);
        $number = trim((string) ($article['Num'] ?? '')) ?: '1';
        $issueTitle = trim((string) ($article['Vol'] ?? ''));
        $datePublished = $year . '-12-31';
        $publicationId = ($articleId * 10) + 1;
        $issueId = ($articleId * 10) + 2;
        $pages = $this->formatPages($article);
        $authors = $this->parseAuthors((string) ($article['Authors'] ?? ''));

        $xml = [
            '<?xml version="1.0" encoding="utf-8"?>',
            '<issues xmlns="http://pkp.sfu.ca" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pkp.sfu.ca native.xsd">',
            '  <issue published="1" access_status="1">',
            '    <id type="internal" advice="ignore">' . $issueId . '</id>',
            '    <issue_identification>',
            '      <number>' . $this->escape($number) . '</number>',
            '      <year>' . $year . '</year>',
        ];

        if ($issueTitle !== '') {
            $xml[] = '      <title locale="pt_BR">' . $this->escape($issueTitle) . '</title>';
        }

        array_push(
            $xml,
            '    </issue_identification>',
            '    <date_published>' . $datePublished . '</date_published>',
            '    <sections>',
            '      <section ref="ART" seq="1" editor_restricted="0" meta_indexed="1" meta_reviewed="0" abstracts_not_required="1" hide_title="0" hide_author="0" abstract_word_count="0">',
            '        <abbrev locale="pt_BR">ART</abbrev>',
            '        <title locale="pt_BR">Artigos</title>',
            '      </section>',
            '    </sections>',
            '    <articles>',
            '      <article locale="pt_BR" current_publication_id="' . $publicationId . '" stage="production">',
            '        <id type="internal" advice="ignore">' . $articleId . '</id>',
            '        <publication version="1" status="3" seq="1" section_ref="ART" access_status="1" date_published="' . $datePublished . '">',
            '          <id type="internal" advice="ignore">' . $publicationId . '</id>',
            '          <title locale="pt_BR">' . $this->escape($title) . '</title>'
        );

        if ($authors !== []) {
            $xml[] = '          <authors>';
            foreach ($authors as $sequence => $author) {
                $authorId = ($articleId * 100) + $sequence + 1;
                $primary = $sequence === 0 ? 'true' : 'false';
                $xml[] = '            <author primary_contact="' . $primary . '" user_group_ref="Author" include_in_browse="true" seq="' . ($sequence + 1) . '" id="' . $authorId . '">';
                $xml[] = '              <givenname locale="pt_BR">' . $this->escape($author['givenName']) . '</givenname>';
                $xml[] = '              <familyname locale="pt_BR">' . $this->escape($author['familyName']) . '</familyname>';
                $xml[] = '              <email />';
                $xml[] = '            </author>';
            }
            $xml[] = '          </authors>';
        }

        array_push(
            $xml,
            '          <issue_identification>',
            '            <number>' . $this->escape($number) . '</number>',
            '            <year>' . $year . '</year>'
        );

        if ($issueTitle !== '') {
            $xml[] = '            <title locale="pt_BR">' . $this->escape($issueTitle) . '</title>';
        }

        $xml[] = '          </issue_identification>';
        if ($pages !== '') {
            $xml[] = '          <pages>' . $this->escape($pages) . '</pages>';
        }

        array_push(
            $xml,
            '        </publication>',
            '      </article>',
            '    </articles>',
            '  </issue>',
            '</issues>'
        );

        return implode("\n", $xml) . "\n";
    }

    private function normalizeYear($year): string
    {
        $year = trim((string) $year);
        if (!preg_match('/^\d{4}$/', $year)) {
            throw new RuntimeException('O artigo não possui um ano válido para exportação.');
        }

        return $year;
    }

    private function formatPages(array $article): string
    {
        $start = trim((string) ($article['PagINI'] ?? ''));
        $end = trim((string) ($article['PagEND'] ?? ''));

        if ($start !== '' && $end !== '') {
            return $start === $end ? $start : $start . '-' . $end;
        }

        return $start !== '' ? $start : $end;
    }

    /**
     * Na base Article os nomes são armazenados como "Sobrenome Prenomes".
     * Autores podem ser separados por vírgula ou ponto e vírgula.
     */
    private function parseAuthors(string $authors): array
    {
        $names = preg_split('/\s*[;,]\s*/u', trim($authors), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $result = [];

        foreach ($names as $name) {
            $parts = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            if ($parts === []) {
                continue;
            }

            $familyName = (string) array_shift($parts);
            $givenName = trim(implode(' ', $parts));
            if ($givenName === '') {
                $givenName = $familyName;
            }

            $result[] = [
                'givenName' => $givenName,
                'familyName' => $familyName,
            ];
        }

        return $result;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
