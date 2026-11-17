<div class="page-header">
    <h1>Perfil</h1>
    <p>Gerencie suas informações pessoais e sua conta.</p>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="row g-3">
    <!-- ===================== COLUNA ESQUERDA: FOTO + STATS ===================== -->
    <div class="col-lg-4">
        <div class="dash-card text-center mb-3">
            <div class="profile-avatar-wrap">
                <?php if (!empty($usuario['avatar'])): ?>
                    <img src="<?= APP_URL ?>/assets/images/uploads/<?= htmlspecialchars($usuario['avatar']) ?>" alt="Foto de perfil" class="profile-avatar-img">
                <?php else: ?>
                    <div class="profile-avatar-placeholder"><?= strtoupper(substr($usuario['name'], 0, 1)) ?></div>
                <?php endif; ?>
            </div>
            <h5 class="mt-3 mb-0"><?= htmlspecialchars($usuario['name']) ?></h5>
            <p class="text-muted small"><?= htmlspecialchars($usuario['email']) ?></p>

            <form method="POST" action="<?= APP_URL ?>/perfil/foto" enctype="multipart/form-data" class="mt-3">
                <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">
                <label for="avatarInput" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-camera me-1"></i> Alterar foto
                </label>
                <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp" class="d-none" onchange="this.form.submit()">
            </form>
            <p class="text-muted small mt-2 mb-0">JPG, PNG ou WEBP — até 2MB</p>
        </div>

        <div class="dash-card">
            <h6 class="dash-card-title mb-3">Estatísticas pessoais</h6>
            <ul class="list-unstyled profile-stats-list">
                <li><i class="fa-solid fa-book"></i> <?= $estatisticas['subjects'] ?> matérias cadastradas</li>
                <li><i class="fa-regular fa-clock"></i> <?= intdiv($estatisticas['minutes'], 60) ?>h <?= $estatisticas['minutes'] % 60 ?>min estudadas no total</li>
                <li><i class="fa-solid fa-check"></i> <?= $estatisticas['tasksDone'] ?> tarefas concluídas</li>
                <li><i class="fa-regular fa-calendar"></i> Desde <?= date('d/m/Y', strtotime($usuario['created_at'])) ?></li>
            </ul>
        </div>
    </div>

    <!-- ===================== COLUNA DIREITA: FORMULÁRIOS ===================== -->
    <div class="col-lg-8">
        <div class="dash-card mb-3">
            <h6 class="dash-card-title mb-3">Dados pessoais</h6>
            <form method="POST" action="<?= APP_URL ?>/perfil">
                <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

                <div class="mb-3">
                    <label class="form-label">Nome completo</label>
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($usuario['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
                    <small class="text-muted">O e-mail não pode ser alterado.</small>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Curso</label>
                        <input type="text" class="form-control" name="course" value="<?= htmlspecialchars($usuario['course'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Instituição de ensino</label>
                        <input type="text" class="form-control" name="institution" value="<?= htmlspecialchars($usuario['institution'] ?? '') ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </form>
        </div>

        <div class="dash-card">
            <h6 class="dash-card-title mb-3">Alterar senha</h6>
            <form method="POST" action="<?= APP_URL ?>/perfil/senha">
                <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

                <div class="mb-3">
                    <label class="form-label">Senha atual</label>
                    <input type="password" class="form-control" name="current_password" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nova senha</label>
                        <input type="password" class="form-control" name="new_password" minlength="6" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-primary">Alterar senha</button>
            </form>
        </div>
    </div>
</div>
