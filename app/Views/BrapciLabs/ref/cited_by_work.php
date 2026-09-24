<div class="content">
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h4 class="mb-1">Referências do registro #<?= (int) $rdf ?></h4>
                <div class="text-muted">Visualize e edite todas as referências vinculadas a este ID.</div>
            </div>
            <a href="<?= base_url('/v/' . (int) $rdf) ?>" target="_blank" rel="noopener noreferrer"
                class="btn btn-outline-secondary btn-sm">
                Ver registro
            </a>
        </div>

        <?= view('BrapciLabs/widget/authors/brapci_details_cited', ['data' => $data]) ?>
    </div>
</div>
