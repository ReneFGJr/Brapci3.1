<?php
$citations = array_values(array_filter(
    $data['cited'] ?? [],
    static fn ($citation): bool => is_array($citation)
));

$sortText = static function (array $citation): string {
    $text = trim((string) ($citation['ca_text'] ?? ''));
    $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

    return mb_strtolower($normalized !== false ? $normalized : $text, 'UTF-8');
};

usort($citations, static function (array $left, array $right) use ($sortText): int {
    return strnatcasecmp($sortText($left), $sortText($right));
});
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h4 class="mb-0">Análise de Citação</h4>
    <span class="badge bg-secondary"><?= count($citations) ?> citações</span>
</div>

<?php if ($citations === []): ?>
    <p class="text-muted mb-0">Nenhuma citação encontrada para este autor.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col" style="width: 60px;">#</th>
                    <th scope="col">Citação</th>
                    <th scope="col" style="width: 100px;">Ano</th>
                    <th scope="col" style="width: 180px;">DOI</th>
                    <th scope="col" style="width: 100px;">Origem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($citations as $index => $citation): ?>
                    <?php
                    $text = trim((string) ($citation['ca_text'] ?? ''));
                    $year = trim((string) ($citation['ca_year'] ?? ''));
                    $doi = trim((string) ($citation['ca_doi'] ?? ''));
                    $rdf = (int) ($citation['ca_rdf'] ?? 0);
                    $doiUrl = preg_match('~^https?://~i', $doi)
                        ? $doi
                        : 'https://doi.org/' . ltrim($doi, '/');
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($text !== '' ? $text : 'Citação sem texto') ?></td>
                        <td><?= esc($year !== '' ? $year : '-') ?></td>
                        <td>
                            <?php if ($doi !== ''): ?>
                                <a href="<?= esc($doiUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer">
                                    <?= esc($doi) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($rdf > 0): ?>
                                <a href="<?= base_url('/v/' . $rdf) ?>" target="_blank" rel="noopener noreferrer">
                                    <?= $rdf ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
