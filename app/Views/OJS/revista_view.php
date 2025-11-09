<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= esc($revista['name']['pt_BR']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">

    <div class="container">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="row g-0 align-items-center">
                <div class="col-md-4 text-center p-4">
                    <?php if (!empty($revista['journalThumbnail']['pt_BR'])): ?>
                        <img src="<?= esc($revista['url']) ?>/public/<?= esc($revista['journalThumbnail']['pt_BR']['uploadName']) ?>"
                            alt="<?= esc($revista['journalThumbnail']['pt_BR']['altText']) ?>"
                            class="img-fluid rounded mb-3"
                            style="max-width:250px;">
                    <?php endif; ?>
                </div>

                <div class="col-md-8 p-4">
                    <h2 class="text-primary fw-bold">
                        <?= esc($revista['name']['pt_BR']) ?>
                    </h2>
                    <h5 class="text-muted"><?= esc($revista['acronym']['pt_BR']) ?></h5>

                    <hr>

                    <p><?= $revista['description']['pt_BR'] ?></p>

                    <hr>

                    <p><strong>URL:</strong> <a href="<?= esc($revista['url']) ?>" target="_blank"><?= esc($revista['url']) ?></a></p>
                    <p><strong>Sigla:</strong> <?= esc($revista['abbreviation']['pt_BR']) ?></p>
                    <p><strong>ID:</strong> <?= esc($revista['id']) ?></p>
                    <p><strong>Issue atual:</strong> <?= esc($revista['currentIssueId']) ?></p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>