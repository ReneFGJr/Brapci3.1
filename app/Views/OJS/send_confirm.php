<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Confirmar Submissão OJS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Navbar OJS -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('ojs') ?>">OJS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarOJS" aria-controls="navbarOJS" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarOJS">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('ojs') ?>">Início</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="card shadow-lg p-4 mb-4">
            <h2 class="mb-4">Confirmar Submissão</h2>
            <table class="table table-bordered bg-white" style="width:100%">
                <tbody>
                    <tr>
                        <th style="width:30%">Title</th>
                        <td><?= esc($csv['Title'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Author</th>
                        <td><?= esc($csv['Authors'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>ID de Submissão</th>
                        <td><?= esc($csv['submit_id'] ?? '-') ?>/<?= esc($csv['Year'] ?? $csv['year'] ?? '-') ?>/ ID:<?= esc($csv['ID'] ?? '-') ?></td>
                    </tr>
                </tbody>
            </table>
            <form method="post" action="<?= base_url('ojs/send/' . esc($csv['status'] ?? '')) ?>">
                <?php foreach ($csv as $k => $v): ?>
                    <input type="hidden" name="csv[<?= esc($k) ?>]" value="<?= esc($v) ?>">
                <?php endforeach; ?>
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="btn btn-primary">Confirmar Submissão</button>
                <a href="<?= base_url('ojs/csv') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>

</html>