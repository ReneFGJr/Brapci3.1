<main class="bg-light pt-5">
        <div class="container-fluid px-3 px-lg-5 py-5">
            <?= view('OJS/partials/selected_journal', ['journal' => $journal]) ?>
            <div class="card shadow-lg p-4 mb-4">
                <h2 class="mb-4">OJS - Gerenciamento de Submissões</h2>
                <nav class="nav nav-pills flex-column flex-sm-row mb-4">
                    <a class="flex-sm-fill text-sm-center nav-link active" aria-current="page" href="<?= base_url('ojs') ?>">Página Inicial</a>
                    <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs/submissoes') ?>">Ver Submissões Ativas</a>
                    <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs/csv') ?>">Importar Submissões</a>
                    <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs/nova') ?>">Enviar Nova Submissão</a>
                    <a class="flex-sm-fill text-sm-center nav-link" href="<?= base_url('ojs/journals') ?>">Gerenciar Revistas</a>
                </nav>
                <div class="mt-4">
                    <h5>Bem-vindo ao sistema de submissões OJS!</h5>
                    <p>Utilize o menu acima para navegar entre as opções disponíveis.</p>
                    <?php if ($journal !== null): ?>
                        <a class="btn btn-primary btn-lg mt-2" href="<?= base_url('ojs/articles_to_submit') ?>">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>Artigos para Submeter
                        </a>
                        <a class="btn btn-outline-primary btn-lg mt-2" href="<?= base_url('ojs/submit') ?>">
                            <i class="bi bi-journal-text me-2"></i>Artigos
                        </a>                    <?php endif; ?>
                </div>
            </div>
        </div>
</main>
