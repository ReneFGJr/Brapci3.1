<div class="content">

    <h4 class="mb-4">
        <i class="bi bi-funnel"></i> Classificação da Literatura
    </h4>

    <div class="table-responsive">
        <table class="table table-hover align-middle">

            <thead class="table-light">
                <tr>
                    <th>Título</th>
                    <th>Ano</th>
                    <th>Decisão</th>
                    <th>Critérios</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($works as $w): ?>
                    <tr>
                        <td>
                            <strong><?= esc($w['title']) ?></strong><br>
                            <small class="text-muted"><?= esc($w['authors']) ?></small>
                        </td>

                        <td><?= esc($w['year']) ?></td>

                        <td>
                            <select class="form-select form-select-sm">
                                <option value="pending">Em análise</option>
                                <option value="include">Incluir</option>
                                <option value="exclude">Excluir</option>
                            </select>
                        </td>

                        <td>
                            <?php foreach ($criteria as $c): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox">
                                    <label class="form-check-label small">
                                        <strong><?= esc($c['code']) ?></strong> –
                                        <?= esc($c['description']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </td>

                        <td>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-save"></i> Salvar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>