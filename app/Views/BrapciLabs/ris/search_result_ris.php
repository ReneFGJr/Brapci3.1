<?php
/**
 * View: Lista de Trabalhos
 * Espera: array $Works
 */

// Função para destacar termos no texto
if (!function_exists('highlightTerms')) {
    function removeAccents($text) {
        $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return $normalized !== false ? $normalized : $text;
    }

    function buildAccentInsensitivePattern($term) {
        $accentMap = [
            'a' => 'aáàâãä',
            'e' => 'eéèêë',
            'i' => 'iíìîï',
            'o' => 'oóòôõö',
            'u' => 'uúùûü',
            'c' => 'cç',
            'n' => 'nñ',
            'y' => 'yýÿ'
        ];

        // Duas decomposicoes: termo original e termo sem acentos.
        $charsOriginal = preg_split('//u', $term, -1, PREG_SPLIT_NO_EMPTY);
        $charsNoAccent = preg_split('//u', mb_strtolower(removeAccents($term), 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
        $pattern = '';

        foreach ($charsOriginal as $i => $ch) {
            $base = $charsNoAccent[$i] ?? mb_strtolower($ch, 'UTF-8');

            if (isset($accentMap[$base])) {
                $variants = preg_quote($accentMap[$base], '/');
                $pattern .= '[' . $variants . ']';
            } else {
                $pattern .= preg_quote($ch, '/');
            }
        }

        return '/(' . $pattern . ')/iu';
    }

    function highlightTerms($text, $terms) {
        if (empty($terms)) return $text;

        // Prioriza termos maiores para reduzir sobreposição de destaques.
        $terms = array_values(array_filter($terms, fn($t) => !empty($t)));
        usort($terms, fn($a, $b) => mb_strlen($b, 'UTF-8') <=> mb_strlen($a, 'UTF-8'));

        foreach ($terms as $term) {
            $pattern = buildAccentInsensitivePattern($term);
            $text = preg_replace($pattern, '<mark style="background-color: #FFFF00; font-weight: bold;">$1</mark>', $text);
        }
        return $text;
    }
}
?>

<div class="container my-4">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-journal-text"></i> <?= $title; ?>
        </h2>
        <span class="badge bg-secondary">
            <?= count($Works) ?> registros
        </span>
    </div>

    <?php if (empty($Works)) : ?>
        <div class="alert alert-warning">
            Nenhum trabalho encontrado.
        </div>
    <?php else : ?>

        <div class="accordion" id="worksAccordion">

            <?php foreach ($Works as $i => $work) : ?>
                <div class="accordion-item mb-2 shadow-sm">

                    <!-- Cabeçalho -->
                    <h2 class="accordion-header" id="heading<?= $i ?>">
                        <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse<?= $i ?>"
                                aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>"
                                aria-controls="collapse<?= $i ?>">

                            <div>
                                <strong class="<?= $class; ?>"><?php
                                    $termosRP = $termosRP ?? [];
                                    echo highlightTerms(esc($work['title']), $termosRP);
                                ?></strong>
                                <div class="small text-muted <?= $class; ?>">
                                    <?= esc($work['authors']) ?> ·
                                    <?= esc($work['journal']) ?> (<?= esc($work['year']) ?>)
                                </div>
                            </div>
                        </button>
                    </h2>

                    <!-- Conteúdo -->
                    <div id="collapse<?= $i ?>"
                         class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>"
                         aria-labelledby="heading<?= $i ?>"
                         data-bs-parent="#worksAccordion">

                        <div class="accordion-body">

                            <!-- Metadados rápidos -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Tipo:</strong>
                                    <span class="badge bg-info text-dark">
                                        <?= esc($work['ris_type']) ?>
                                    </span>
                                </div>

                                <div class="col-md-4">
                                    <strong>Ano:</strong> <?= esc($work['year']) ?>
                                </div>

                                <div class="col-md-4">
                                    <strong>Status:</strong>
                                    <?= $work['status'] == 0
                                        ? '<span class="badge bg-success">Ativo</span>'
                                        : '<span class="badge bg-secondary">Inativo</span>' ?>
                                </div>
                            </div>

                            <!-- Resumo -->
                            <?php if (!empty($work['abstract'])) : ?>
                                <div class="mb-3">
                                    <h6 class="text-primary">
                                        <i class="bi bi-card-text"></i> Resumo
                                    </h6>
                                    <p class="text-justify">
                                        <?php
                                            $termosRP = $termosRP ?? [];
                                            echo highlightTerms(esc($work['abstract']), $termosRP);
                                        ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- Palavras-chave -->
                            <?php if (!empty($work['keywords'])) : ?>
                                <div class="mb-3">
                                    <h6 class="text-primary">
                                        <i class="bi bi-tags"></i> Palavras-chave
                                    </h6>

                                    <?php
                                    $keywords = array_map('trim', explode(';', $work['keywords']));
                                    $termosRP = $termosRP ?? [];
                                    ?>

                                    <?php foreach ($keywords as $kw) : ?>
                                        <?php
                                        $isHighlighted = in_array($kw, $termosRP);
                                        $bgClass = $isHighlighted ? 'bg-warning' : 'bg-light';
                                        $textClass = $isHighlighted ? 'text-dark' : 'text-dark';
                                        ?>
                                        <span class="badge <?= $bgClass ?> <?= $textClass ?> border me-1 mb-1">
                                            <?= esc($kw) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Ações -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    ID: <?= esc($work['id']) ?> ·
                                    Criado em <?= date('d/m/Y H:i', strtotime($work['created_at'])) ?>
                                </small>

                                <div class="btn-group">
                                    <?php if (!empty($work['url'])) : ?>
                                        <a href="<?= esc($work['url']) ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-box-arrow-up-right"></i> Ver na Brapci
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($work['doi'])) : ?>
                                        <a href="https://doi.org/<?= esc($work['doi']) ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-dark">
                                            DOI
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>
