<?= $this->extend('bookmarks/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h2><i class="bi bi-bookmarks"></i> Meus Favoritos</h2>

    <form method="get" action="/bookmarks/search" class="my-3">
        <input type="text" name="q" placeholder="Buscar..." class="form-control">
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Ícone</th>
                <th>Título</th>
                <th>URL</th>
                <th>Pasta</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookmarks as $b): ?>
                <tr>
                    <td><img src="<?= esc($b['favicon']) ?>" width="16"></td>
                    <td><?= esc($b['title']) ?></td>
                    <td><a href="<?= esc($b['url']) ?>" target="_blank"><?= esc($b['url']) ?></a></td>
                    <td><?= esc($b['folder']) ?></td>
                    <td><?= esc($b['date_added']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>