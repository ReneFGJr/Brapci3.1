<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Resultado da Submissão OJS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-lg p-4 mb-4">
            <h2 class="mb-4">Resultado da Submissão</h2>
            <h5>Dados enviados:</h5>
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
                        <td><?= esc($csv['submit_id'] ?? '-') ?>/<?= esc($csv['Year'] ?? $csv['year'] ?? '-') ?></td>
                    </tr>
                </tbody>
            </table>
            <h5 class="mt-4">Resposta da API:</h5>
            <pre class="bg-light p-3 border rounded" style="max-height:300px;overflow:auto;">
<?= esc(print_r($response ?? $result ?? '-', true)) ?>
            </pre>
            <a href="<?= base_url('ojs/csv') ?>" class="btn btn-secondary mt-3">Voltar para lista</a>
        </div>
    </div>
</body>

</html>