<main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Cited by <?= count($works) ?></h1>
        <a href="<?= site_url('labs/thinkers') ?>" class="btn btn-outline-secondary">Pensadores</a>
    </div>
    <section class="card card-dashboard p-4 mb-4">
        <h2 class="h5">Obra citada #<?= esc($reference['id_ca']) ?></h2>
        <p class="mb-0"><?= esc($reference['ca_text']) ?></p>
    </section>
    <h2 class="h4">Trabalhos citantes</h2>
    <p class="text-muted">Do mais antigo ao mais recente. Trabalhos sem ano informado aparecem ao final. Cada trabalho é contado uma vez.</p>
    <?php if (!$works): ?>
        <div class="alert alert-info">Nenhum trabalho citante identificado para esta obra.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($works as $work): ?>
                <div class="col-12">
                    <article class="card card-dashboard p-4">
                        <span class="small text-muted mb-2"><?= esc($work['year'] ?? 'Ano não informado') ?></span>
                        <h3 class="h5"><a href="<?= base_url('v/' . (int) $work['ca_rdf']) ?>" target="_blank" rel="noopener noreferrer"><?= esc($work['title'] ?:  ('Registro #' . $work['ca_rdf'])) ?></a></h3>
                        <?php if (!empty($work['authors'])): ?>
                            <p class="text-muted"><?= esc($work['authors']) ?></p>
                        <?php endif; ?>
                        <a href="<?= site_url('labs/cited/work/' . (int) $work['ca_rdf']) ?>" class="align-self-start">Ver referências deste trabalho</a>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
