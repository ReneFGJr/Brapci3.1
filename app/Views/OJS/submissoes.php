<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Submissões Ativas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-lg p-4 mb-4">
            <h2 class="mb-4">Submissões Ativas</h2>
            <nav class="nav nav-pills flex-column flex-sm-row mb-4">
                <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs') ?>">Página Inicial</a>
                <a class="flex-sm-fill text-sm-center nav-link active" aria-current="page" href="<?= base_url('ojs/submissoes') ?>">Ver Submissões Ativas</a>
                <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs/nova') ?>">Enviar Nova Submissão</a>
            </nav>
            <div class="mt-4">
                <?php if (!empty($submissoes) && is_array($submissoes)): ?>
                    <table class="table table-bordered table-hover bg-white">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Autor(es)</th>
                                <th>Status</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($submissoes as $sub): ?>
                            <tr>
                                <td><?= esc($sub->id ?? '-') ?></td>
                                <td>
                                    <?php
                                    // Tenta pegar o título completo da primeira publicação
                                    $titulo = '-';
                                    if (isset($sub->publications) && is_array($sub->publications) && count($sub->publications) > 0) {
                                        $titulo = $sub->publications[0]->fullTitle->pt_BR ?? '-';
                                    }
                                    echo esc($titulo);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // Busca o nome do autor na primeira publicação
                                    $autor = '-';
                                    if (isset($sub->publications) && is_array($sub->publications) && count($sub->publications) > 0) {
                                        $autor = $sub->publications[0]->authorsString ?? '-';
                                    }
                                    echo esc($autor);
                                    ?>
                                </td>
                                <td><?= esc($sub->status ?? '-') ?></td>
                                <td><?= esc($sub->dateSubmitted ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Nenhuma submissão ativa encontrada.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
