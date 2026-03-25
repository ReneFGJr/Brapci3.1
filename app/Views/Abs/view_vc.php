<?= view('BrapciLabs/layout/header'); ?>
<?= view('BrapciLabs/layout/sidebar'); ?>

<style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --line: #d8dee9;
            --text: #1d2939;
            --muted: #667085;
            --accent: #0f766e;
        }

        body {
            background: radial-gradient(circle at 0% 0%, #e6f4ff 0%, var(--bg) 55%);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .tree ul {
            list-style: none;
            margin: 0;
            padding-left: 1.25rem;
            border-left: 1px dashed var(--line);
        }

        .tree > ul {
            border-left: 0;
            padding-left: 0;
        }

        .tree li {
            margin: .35rem 0;
            padding-left: .75rem;
            position: relative;
        }

        .tree li::before {
            content: "";
            position: absolute;
            top: .9rem;
            left: 0;
            width: .55rem;
            border-top: 1px dashed var(--line);
        }

        .node-card {
            display: inline-block;
            background: var(--card);
            border: 1px solid #e4e7ec;
            border-radius: 10px;
            padding: .45rem .7rem;
            box-shadow: 0 2px 8px rgba(16, 24, 40, 0.05);
        }

        .node-id {
            color: var(--accent);
            font-weight: 700;
            margin-right: .35rem;
        }

        .node-label {
            font-weight: 500;
            font-size: 1.3rem;
        }

        .muted {
            color: var(--muted);
            font-size: .86rem;
        }
</style>

<div class="content">
    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                            <h1 class="h3 mb-0">Visualizador de Hierarquia VC <?= esc($vocabularyId) ?></h1>
                            <a href="<?= esc(base_url('labs/viewVC/' . $vocabularyId)) ?>" class="btn btn-outline-secondary btn-sm">Recarregar</a>
                        </div>

                    <p class="text-secondary mb-3">
                        Estrutura lida do arquivo <strong><?= esc(basename($netPath)) ?></strong> com rótulos do
                        <strong><?= esc(basename($termsPath)) ?></strong>.
                    </p>

                    <div class="row g-2 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded border bg-light">
                                <div class="small text-muted">Nós</div>
                                <div class="fw-bold"><?= count($nodes) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded border bg-light">
                                <div class="small text-muted">Raízes</div>
                                <div class="fw-bold"><?= count($roots) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded border bg-light">
                                <div class="small text-muted">Conceitos com termos</div>
                                <div class="fw-bold"><?= count($termsByConcept) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded border bg-light">
                                <div class="small text-muted">Arestas</div>
                                <div class="fw-bold"><?= array_sum(array_map('count', $children)) ?></div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $renderNode = function ($node) use (&$renderNode, $nodes, $termsByConcept) {
                        $nodeId = (int)($node['id'] ?? 0);
                        $rawLabel = $nodes[$nodeId] ?? ('Termo ' . $nodeId);

                        $concept = 0;
                        if (preg_match('/Termo\s+(\d+)/i', $rawLabel, $m)) {
                            $concept = (int)$m[1];
                        }

                        $termInfo = $termsByConcept[$concept] ?? null;
                        $preferred = $termInfo['preferred'] ?? '';
                        $allTerms = $termInfo['terms'] ?? [];

                        echo '<li>';
                        echo '<div class="node-card">';

                        if ($preferred !== '') {
                            echo '<span class="node-label">' . esc($preferred) . '</span>';
                        } else {
                            echo '<span class="node-label text-muted">sem termo associado</span>';
                        }
                        echo '<sup>' . esc(str_replace("Termo ","",$rawLabel)) . '</sup>';

                        if (!empty($node['cycle'])) {
                            echo ' <span class="badge text-bg-warning">ciclo</span>';
                        }

                        if (!empty($allTerms)) {
                            echo '<div class="muted mt-1">' . esc(implode(' | ', $allTerms)) . '</div>';
                        }

                        echo '</div>';

                        if (!empty($node['children'])) {
                            echo '<ul>';
                            foreach ($node['children'] as $child) {
                                $renderNode($child);
                            }
                            echo '</ul>';
                        }

                        echo '</li>';
                    };
                    ?>

                        <div class="tree">
                            <ul>
                                <?php foreach ($hierarchy as $rootNode): ?>
                                    <?php $renderNode($rootNode); ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('BrapciLabs/layout/footer'); ?>
</body>
</html>
