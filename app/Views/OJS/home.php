<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>OJS - Submissões</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-lg p-4 mb-4">
            <h2 class="mb-4">OJS - Gerenciamento de Submissões</h2>
            <nav class="nav nav-pills flex-column flex-sm-row mb-4">
                <a class="flex-sm-fill text-sm-center nav-link active" aria-current="page" href="<?= base_url('ojs') ?>">Página Inicial</a>
                <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs/submissoes') ?>">Ver Submissões Ativas</a>
                <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs/csv') ?>">Importar Submissões</a>
                <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs/nova') ?>">Enviar Nova Submissão</a>
            </nav>
            <div class="mt-4">
                <h5>Bem-vindo ao sistema de submissões OJS!</h5>
                <p>Utilize o menu acima para navegar entre as opções disponíveis.</p>
            </div>
        </div>
    </div>
</body>

</html>