<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Bookmarks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('/bookmarks') ?>">
                <i class="bi bi-bookmarks"></i> Bookmark Manager
            </a>

            <a class="text-light" href="<?= base_url('/bookmarks/folder') ?>">
                <i class="bi bi-folder"></i>
            </a>
        </div>
    </nav>

    <main class="container mt-4">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="text-center text-muted py-3">
        <small>© <?= date('Y') ?> - Sistema de Bookmarks em CI4</small>
    </footer>

</body>

</html>