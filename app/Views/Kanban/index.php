<?= $this->extend('Kanban/header') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="text-center">📌 Kanban - Projeto</h2>
    <div class="row">

        <?php
        $columns = [
            'a_fazer' => 'A Fazer',
            'fazendo' => 'Fazendo',
            'revisar' => 'Revisar'
        ];

        foreach ($columns as $colKey => $colName): ?>
            <div class="col-md-4">
                <h4 class="text-center"><?= $colName ?></h4>
                <div class="kanban-column p-2 bg-light rounded" style="min-height:400px;">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] == $colKey): ?>
<div class="postit mb-3 shadow-sm p-3 rounded
    <?php if ($task['priority']=='urgente') echo 'bg-danger text-white';
          elseif ($task['priority']=='normal') echo 'bg-warning';
          else echo 'bg-success text-white'; ?>">

    <h5><?= esc($task['title']) ?></h5>
    <p><?= esc($task['description']) ?></p>
    <small>Deadline: <?= esc($task['deadline']) ?></small><br>

    <!-- Botões -->
    <div class="d-flex justify-content-between mt-2">
        <a href="#" class="btn btn-sm btn-outline-dark" data-bs-toggle="offcanvas"
           data-bs-target="#commentPanel<?= $task['id'] ?>">💬 Comentários</a>

        <a href="#" class="btn btn-sm btn-outline-light" data-bs-toggle="offcanvas"
           data-bs-target="#editTaskPanel<?= $task['id'] ?>">✏️ Editar</a>
    </div>
</div>

                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Botão flutuante -->
<button class="btn btn-primary rounded-circle shadow-lg"
    style="position: fixed; bottom: 20px; right: 20px; width:60px; height:60px;"
    data-bs-toggle="offcanvas" data-bs-target="#newTaskPanel">
    <i class="bi bi-plus-circle-fill fs-3"></i>
</button>

<!-- Panel lateral -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="newTaskPanel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">➕ Nova Tarefa</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form method="post" action="<?= base_url('/kanban/store') ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input type="text" class="form-control" name="title" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea class="form-control" name="description"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="a_fazer">A Fazer</option>
                    <option value="fazendo">Fazendo</option>
                    <option value="revisar">Revisar</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Prioridade</label>
                <select class="form-select" name="priority">
                    <option value="urgente">Urgente</option>
                    <option value="normal" selected>Normal</option>
                    <option value="sem_pressa">Sem pressa</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Atribuir a</label>
                <input type="number" class="form-control" name="assigned_to" placeholder="ID do usuário">
            </div>

            <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" name="deadline">
            </div>

            <button type="submit" class="btn btn-success w-100">Salvar</button>
        </form>
    </div>
</div>

<?php foreach ($tasks as $task): ?>
<!-- Panel Editar -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editTaskPanel<?= $task['id'] ?>">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">✏️ Editar Tarefa</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form method="post" action="<?= base_url('/kanban/update/'.$task['id']) ?>">
      <div class="mb-3">
        <label class="form-label">Título</label>
        <input type="text" class="form-control" name="title" value="<?= esc($task['title']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea class="form-control" name="description"><?= esc($task['description']) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="a_fazer" <?= $task['status']=='a_fazer'?'selected':'' ?>>A Fazer</option>
          <option value="fazendo" <?= $task['status']=='fazendo'?'selected':'' ?>>Fazendo</option>
          <option value="revisar" <?= $task['status']=='revisar'?'selected':'' ?>>Revisar</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Prioridade</label>
        <select class="form-select" name="priority">
          <option value="urgente" <?= $task['priority']=='urgente'?'selected':'' ?>>Urgente</option>
          <option value="normal" <?= $task['priority']=='normal'?'selected':'' ?>>Normal</option>
          <option value="sem_pressa" <?= $task['priority']=='sem_pressa'?'selected':'' ?>>Sem pressa</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Atribuir a (ID)</label>
        <input type="number" class="form-control" name="assigned_to" value="<?= esc($task['assigned_to']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Deadline</label>
        <input type="date" class="form-control" name="deadline" value="<?= esc($task['deadline']) ?>">
      </div>

      <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
    </form>
  </div>
</div>
<?php endforeach; ?>


<style>
    .postit {
        border: 1px solid #ccc;
        transform: rotate(-1deg);
    }
</style>

<?= $this->endSection() ?>