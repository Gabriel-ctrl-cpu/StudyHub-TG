<a href="<?= APP_URL ?>/login" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Voltar ao login</a>

<div class="auth-form-box">
    <h1 class="auth-title">Esqueci minha senha</h1>
    <p class="auth-subtitle">Informe seu e-mail para receber instruções de redefinição.</p>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/esqueci-senha" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">

        <div class="mb-4">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary btn-auth w-100">Enviar instruções</button>
    </form>
</div>
