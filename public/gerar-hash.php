<?php
/**
 * gerar-hash.php
 *
 * Ferramenta simples para gerar o hash bcrypt de uma senha, para uso
 * ao inserir usuários manualmente no banco (via phpMyAdmin), já que
 * o sistema exige senhas com password_hash() — nunca texto puro.
 *
 * COMO USAR:
 * 1. Coloque este arquivo dentro da pasta public/ do projeto
 *    (ex: C:\xampp\htdocs\studyhub\public\gerar-hash.php)
 * 2. Acesse http://localhost/studyhub/public/gerar-hash.php
 * 3. Digite a senha desejada e clique em "Gerar hash"
 * 4. Copie o hash gerado e use no campo `password` do INSERT no phpMyAdmin
 * 5. IMPORTANTE: apague este arquivo depois de usar — ele não faz parte
 *    do sistema e não deve ficar disponível em produção.
 */

$hash = null;
$senha = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'] ?? '';
    if ($senha !== '') {
        $hash = password_hash($senha, PASSWORD_BCRYPT);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerador de hash de senha — StudyHub</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 560px; margin: 60px auto; padding: 0 20px; color: #1e2432; }
        h1 { font-size: 1.3rem; }
        input[type="text"] { width: 100%; padding: 10px; font-size: 1rem; box-sizing: border-box; margin-bottom: 10px; }
        button { padding: 10px 18px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        .hash-box { background: #f4f6fb; border: 1px solid #e6e9f2; border-radius: 8px; padding: 14px; margin-top: 20px; word-break: break-all; font-family: monospace; }
        .warning { background: #fef2e0; border: 1px solid #eab308; border-radius: 8px; padding: 12px; margin-top: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>Gerador de hash de senha (uso manual/teste)</h1>
    <form method="POST">
        <label>Senha desejada:</label>
        <input type="text" name="senha" value="<?= htmlspecialchars($senha) ?>" placeholder="Ex: 123456" required>
        <button type="submit">Gerar hash</button>
    </form>

    <?php if ($hash): ?>
        <p>Hash gerado para a senha "<strong><?= htmlspecialchars($senha) ?></strong>":</p>
        <div class="hash-box"><?= htmlspecialchars($hash) ?></div>
        <p>Copie esse valor e cole no campo <code>password</code> ao inserir o usuário no phpMyAdmin.</p>
    <?php endif; ?>

    <div class="warning">
        ⚠️ Este arquivo é só uma ferramenta de apoio para desenvolvimento/testes.
        Apague-o da pasta <code>public/</code> depois de usar.
    </div>
</body>
</html>
