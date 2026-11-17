<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub — Organize seus estudos</title>
    <link rel="icon" href="<?= APP_URL ?>/assets/icons/favicon.svg" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-panel">
            <?php $content(); ?>
        </div>
        <div class="auth-hero d-none d-lg-flex">
            <div class="auth-hero-content">
                <div class="auth-logo mb-4">
                    <i class="fa-solid fa-book-open-reader"></i>
                    <span>StudyHub</span>
                </div>
                <h2>Organize seus estudos.<br>Alcance seus objetivos.</h2>
                <p>O StudyHub reúne suas matérias, tarefas, metas, calendário e sessões de estudo em um único lugar.</p>
                <ul class="auth-hero-list">
                    <li><i class="fa-solid fa-check"></i> Planejamento simples e visual</li>
                    <li><i class="fa-solid fa-check"></i> Modo Foco com cronômetro Pomodoro</li>
                    <li><i class="fa-solid fa-check"></i> Estatísticas do seu progresso</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
