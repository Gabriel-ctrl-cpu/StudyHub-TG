<a href="<?= APP_URL ?>/" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</a>

<div class="auth-form-box">
    <h1 class="auth-title">Crie sua conta</h1>
    <p class="auth-subtitle">Comece a organizar seus estudos hoje mesmo.</p>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/cadastro" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Nome completo</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $this->old('name') ?>" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $this->old('email') ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" minlength="6" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="confirm_password" class="form-label">Confirmar senha</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="course" class="form-label">Curso</label>
                <input type="text" class="form-control" id="course" name="course" value="<?= $this->old('course') ?>" placeholder="Ex: Análise e Desenvolvimento de Sistemas">
            </div>
            <div class="col-md-6 mb-3">
                <label for="institution" class="form-label">Instituição de ensino</label>
                <input type="text" class="form-control" id="institution" name="institution" value="<?= $this->old('institution') ?>" placeholder="Ex: Faculdade Exemplo">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-auth w-100 mt-2">Criar conta</button>
    </form>

    <p class="auth-footer-text">
        Já tem uma conta? <a href="<?= APP_URL ?>/login" class="auth-link">Entrar</a>
    </p>
</div>
