<?= $this->extend('bookmarks/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">

    <h2 class="mb-3">
        <i class="bi bi-bookmarks"></i> Meus Favoritos
    </h2>

    <!-- Busca -->
    <form method="get" action="/bookmarks/search" class="my-3">
        <input type="text" name="q" placeholder="Buscar..." class="form-control">
    </form>

    <style>
        .category-tree {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 14px;
            position: sticky;
            top: 20px;
        }

        .category-tree h5 {
            margin-bottom: 12px;
            font-weight: 700;
        }

        .category-tree ul {
            list-style: none;
            margin: 0;
            padding-left: 14px;
            border-left: 1px dashed #d8dee4;
        }

        .category-tree li {
            margin: 4px 0;
            line-height: 1.3;
        }

        .category-tree a {
            color: #0d6efd;
            text-decoration: none;
        }

        .category-tree a:hover {
            text-decoration: underline;
        }

        .category-badge {
            font-size: 0.72rem;
            vertical-align: middle;
        }

        .folder-card {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 12px;
            height: 100%;
        }

        .folder-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .folder-title i {
            margin-right: 8px;
            color: #c28f21;
        }

        .item {
            margin-left: 6px;
            margin-bottom: 8px;
        }

        .item a {
            text-decoration: none;
        }

        .favicon {
            width: 16px;
            margin-right: 6px;
        }

        .item-date {
            font-size: 0.8rem;
            color: #666;
        }
    </style>

    <?php
    $bookmarks = $bookmarks ?? [];
    $categories = $categories ?? [];

    /** Agrupa bookmarks por pasta para renderizar cards **/
    $folders = [];
    $countByFolder = [];
    foreach ($bookmarks as $b) {
        $folderName = trim($b['f_title'] ?? 'Sem categoria');
        $folderId = $b['folder_id'] ?? null;

        $folderKey = $folderId ?: $folderName;
        $folders[$folderKey]['name'] = $folderName;
        $folders[$folderKey]['id'] = $folderId;
        $folders[$folderKey]['items'][] = $b;
        $countByFolder[(int)$folderId] = ($countByFolder[(int)$folderId] ?? 0) + 1;
    }

    /** Monta árvore de diretórios com base em f_folder (path) **/
    $tree = [];

    $insertTreeNode = function (&$branch, array $segments, array $category, int $index = 0) use (&$insertTreeNode) {
        if (!isset($segments[$index])) {
            return;
        }

        $label = $segments[$index];
        if (!isset($branch[$label])) {
            $branch[$label] = [
                'label' => $label,
                'children' => [],
                'category' => null,
            ];
        }

        if ($index === count($segments) - 1) {
            $branch[$label]['category'] = $category;
            return;
        }

        $insertTreeNode($branch[$label]['children'], $segments, $category, $index + 1);
    };

    foreach ($categories as $category) {
        $path = trim((string) ($category['f_folder'] ?? ''));
        $fallbackTitle = trim((string) ($category['f_title'] ?? 'Sem categoria'));
        $source = $path !== '' ? $path : $fallbackTitle;
        $segments = array_values(array_filter(preg_split('~\s*[/\\\\>]+\s*~', $source)));

        if (empty($segments)) {
            $segments = [$fallbackTitle];
        }

        $insertTreeNode($tree, $segments, $category);
    }

    $renderTree = function (array $nodes) use (&$renderTree, $countByFolder) {
        if (empty($nodes)) {
            return;
        }

        ksort($nodes, SORT_NATURAL | SORT_FLAG_CASE);
        echo '<ul>';
        foreach ($nodes as $node) {
            echo '<li>';

            if (!empty($node['category'])) {
                $id = (int) ($node['category']['id_f'] ?? 0);
                $title = (string) ($node['category']['f_title'] ?? $node['label']);
                $count = (int) ($countByFolder[$id] ?? 0);

                echo '<a href="' . base_url('bookmarks/folders/view/' . $id) . '">';
                echo '<i class="bi bi-folder2 me-1"></i>' . esc($title);
                echo '</a>';
                echo ' <span class="badge text-bg-light border category-badge">' . $count . '</span>';
            } else {
                echo '<span><i class="bi bi-diagram-3 me-1 text-muted"></i>' . esc($node['label']) . '</span>';
            }

            $renderTree($node['children']);
            echo '</li>';
        }
        echo '</ul>';
    };
    ?>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-lg-4">
            <aside class="category-tree">
                <h5>
                    <i class="bi bi-diagram-3"></i> Categorias
                </h5>

                <?php if (!empty($tree)): ?>
                    <?php $renderTree($tree); ?>
                <?php else: ?>
                    <p class="text-muted mb-0">Nenhuma categoria encontrada.</p>
                <?php endif; ?>
            </aside>
        </div>

        <div class="col-12 col-lg-8">
            <div class="row g-4">
                <?php foreach ($folders as $folder): ?>
                    <div class="col-12 col-sm-6 col-xl-4">

                        <div class="folder-card">

                            <!-- Título da Pasta -->
                            <div class="folder-title">
                                <i class="bi bi-folder-fill"></i>
                                <?php if (!empty($folder['id'])): ?>
                                    <a href="<?= base_url('bookmarks/folders/view/' . (int)$folder['id']) ?>" class="text-decoration-none text-dark">
                                        <?= esc($folder['name']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= esc($folder['name']) ?>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <!-- Itens -->
                            <?php foreach ($folder['items'] as $b): ?>
                                <div class="item">
                                    <div>
                                        <img src="<?= esc($b['favicon']) ?>" class="favicon">

                                        <a href="<?= esc($b['url']) ?>" target="_blank">
                                            <?= esc($b['title']) ?>
                                        </a>
                                    </div>

                                    <div class="item-date">
                                        <i class="bi bi-clock-history"></i>
                                        <?= esc($b['date_added']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>