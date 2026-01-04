<?= view('BrapciLabs/layout/header'); ?>
<!-- Sidebar -->
<?= view('BrapciLabs/layout/sidebar'); ?>
<!-- Conteúdo -->
<div class="content">

    <!-- Top -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <?= view('BrapciLabs/widget/projects/header') ?>
    </div>

    <!-- Cards -->
    <div class="row g-4 mb-4">
        <?= view("BrapciLabs/widget/cards/card_1", [
            'size'  => 4,
            'title' => 'Trabalhos <a href="' . base_url('labs/importRIS').'"
           class="ms-2 text-decoration-none"
           title="Importar trabalhos (RIS)">
            <i class="bi bi-file-earmark-arrow-up"></i>
        </a>',
            'info'  => $worksCount > 0
                ? '<a href="' . base_url('labs/project/works') . '" class="link">'
                . $worksCount . ' trabalhos</a>'
                : 'sem trabalhos'
        ]) ?>

        <!-- Autores Card -->
        <?= view("BrapciLabs/widget/cards/card_1", [
            'size'  => 4,
            'title' => 'Autores',
            'info'  => $authorsCount > 0
                ? '<a href="' . base_url('labs/project/authors') . '" class="link">'
                . $authorsCount . ' autores</a>'
                : 'sem autores'
        ]) ?>

        <!-- CodeBook Card -->
        <?= view("BrapciLabs/widget/cards/card_1", [
            'size'  => 4,
            'title' => 'CodeBook',
            'info'  => $codebookCount > 0
                ? '<a href="' . base_url('labs/project/codebook') . '" class="link">'
                . $codebookCount . ' anotações</a>'
                : 'sem codebook'
        ]) ?>

        <div class="col-md-4">
            <div class="card card-dashboard p-3">
                <h6>Expenses</h6>
                <h4>$1850.20</h4>
                <div class="fake-bars mt-3">
                    <?php for ($i = 0; $i < 10; $i++): ?>
                        <span class="active" style="height:<?= rand(20, 60) ?>px"></span>
                    <?php endfor ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dashboard p-3">
                <h6>Sales</h6>
                <h4>$5678</h4>
                <small class="text-muted">Chart preview</small>
            </div>
        </div>

    </div>

    <!-- Mensagens -->
    <div class="card card-dashboard p-4">
        <h5 class="mb-3">Recent Messages</h5>

        <div class="d-flex align-items-center mb-3">
            <div class="message-avatar me-3"></div>
            <div class="flex-grow-1">
                <strong>Leslie Alexander</strong><br>
                <small>How can I return package</small>
            </div>
            <span class="badge bg-success">Answered</span>
            <small class="ms-3 text-muted">12:45pm</small>
        </div>

        <div class="d-flex align-items-center mb-3">
            <div class="message-avatar me-3"></div>
            <div class="flex-grow-1">
                <strong>Robert Foxeriest</strong><br>
                <small>Question about the product</small>
            </div>
            <span class="badge bg-warning">Pending</span>
            <small class="ms-3 text-muted">3:45pm</small>
        </div>

        <div class="d-flex align-items-center">
            <div class="message-avatar me-3"></div>
            <div class="flex-grow-1">
                <strong>Brooklyn Simmons</strong><br>
                <small>Discount Code</small>
            </div>
            <span class="badge bg-warning">Pending</span>
            <small class="ms-3 text-muted">Yesterday</small>
        </div>

    </div>

</div>

</body>

</html>