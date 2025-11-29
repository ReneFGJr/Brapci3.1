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
    /** Agrupa bookmarks por pasta **/
    $folders = [];
    foreach ($bookmarks as $b) {
        $folders[$b['title']][] = $b;
    }
    ?>

    <!-- GRID 4 COLUNAS -->
    <div class="row g-4">

        <?php foreach ($folders as $folder => $items): ?>
            <div class="col-12 col-sm-6 col-lg-3">

                <div class="folder-card">

                    <!-- Título da Pasta -->
                    <div class="folder-title">
                        <i class="bi bi-folder-fill"></i> <?= esc($folder) ?>
                    </div>

                    <hr>

                    <!-- Itens -->
                    <?php foreach ($items as $b): ?>
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

<?= $this->endSection() ?>