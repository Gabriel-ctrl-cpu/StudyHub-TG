<a href="<?= APP_URL ?>/" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</a>

<div class="auth-form-box">
    <h1 class="auth-title">Bem-vindo de volta</h1>
    <p class="auth-subtitle">Entre para continuar organizando seus estudos.</p>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/login" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required autofocus>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Lembrar de mim</label>
            </div>
            <a href="<?= APP_URL ?>/esqueci-senha" class="auth-link">Esqueci minha senha</a>
        </div>

        <button type="submit" class="btn btn-primary btn-auth w-100">Entrar</button>
    </form>

    <p class="auth-footer-text">
        Não tem uma conta? <a href="<?= APP_URL ?>/cadastro" class="auth-link">Criar conta</a>
    </p>
</div>

<script src="<?= APP_URL ?>/assets/js/auth.js"></script>
