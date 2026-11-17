<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
    <div>
        <h1>Avaliações</h1>
        <p>Controle suas provas, trabalhos e seminários.</p>
    </div>
    <a href="<?= APP_URL ?>/avaliacoes/nova" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nova avaliação
    </a>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>

<div class="dash-card p-0">
    <div class="table-responsive">
        <table class="table assessments-table align-middle mb-0">
            <thead>
                <tr>
                    <th>Avaliação</th>
                    <th>Matéria</th>
                    <th>Tipo</th>
                    <th>Data</th>
                    <th>Peso</th>
                    <th>Nota</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rotulosTipo = ['prova' => 'Prova', 'trabalho' => 'Trabalho', 'seminario' => 'Seminário', 'projeto' => 'Projeto', 'outro' => 'Outro'];
                $rotulosSituacao = ['agendado' => 'Agendado', 'realizado' => 'Realizado', 'corrigido' => 'Corrigido'];
                foreach ($avaliacoes as $a):
                    $jaPassou = strtotime($a['assessment_date']) < strtotime(date('Y-m-d'));
                ?>
                    <tr class="<?= $jaPassou && $a['status'] === 'agendado' ? 'text-muted' : '' ?>">
                        <td>
                            <div class="fw-medium"><?= htmlspecialchars($a['title']) ?></div>
                            <?php if (!empty($a['description'])): ?>
                                <div class="small text-muted"><?= htmlspecialchars($a['description']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="dot" style="background: <?= htmlspecialchars($a['subject_color']) ?>; width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px;"></span>
                            <?= htmlspecialchars($a['subject_name']) ?>
                        </td>
                        <td><?= $rotulosTipo[$a['assessment_type']] ?? $a['assessment_type'] ?></td>
                        <td>
                            <?= date('d/m/Y', strtotime($a['assessment_date'])) ?>
                            <?php if ($a['assessment_time']): ?><br><small class="text-muted"><?= date('H:i', strtotime($a['assessment_time'])) ?></small><?php endif; ?>
                        </td>
                        <td><?= $a['weight'] !== null ? number_format((float) $a['weight'], 1, ',', '.') : '—' ?></td>
                        <td><?= $a['grade'] !== null ? number_format((float) $a['grade'], 1, ',', '.') : '—' ?></td>
                        <td><span class="badge status-badge assessment-status-<?= $a['status'] ?>"><?= $rotulosSituacao[$a['status']] ?? $a['status'] ?></span></td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/avaliacoes/<?= $a['id'] ?>/editar"><i class="fa-solid fa-pen me-2"></i>Editar</a></li>
                                    <li>
                                        <form method="POST" action="<?= APP_URL ?>/avaliacoes/<?= $a['id'] ?>/excluir" class="form-confirm-delete" data-confirm="Excluir a avaliação &quot;<?= htmlspecialchars($a['title'], ENT_QUOTES) ?>&quot;?">
                                            <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">
                                            <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i>Excluir</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($avaliacoes)): ?>
                    <tr><td colspan="8" class="p-0">
                        <div class="empty-state border-0">
                            <i class="fa-solid fa-file-pen"></i>
                            <p>Nenhuma avaliação cadastrada.</p>
                            <a href="<?= APP_URL ?>/avaliacoes/nova" class="btn btn-primary btn-sm">Cadastrar primeira avaliação</a>
                        </div>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?= APP_URL ?>/assets/js/confirm-delete.js"></script>
