<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Envio automático para OJS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4 bg-light">
    <div class="container">
        <div class="card shadow-lg p-4">
            <h2 class="mb-3">Submeter artigo automaticamente (OJS API)</h2>
            <form action="<?= base_url('ojs/send') ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control"
                        value="Título de teste" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Resumo</label>
                    <textarea name="resumo" class="form-control" rows="3" required>
                    Teste
                    </textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Autor</label>
                    <input type="text" name="autor"
                        value="Rene Faustino Gabriel Junior"
                        class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email"
                        value="renefgj@gmail.com"
                        name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Arquivo (PDF)</label>
                    <input type="file" name="arquivo" class="form-control" accept="application/pdf" required>
                </div>
                <button type="submit" class="btn btn-primary">Enviar para OJS</button>
            </form>
        </div>
    </div>
</body>

</html>