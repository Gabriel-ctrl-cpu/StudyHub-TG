<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="<?= $_SESSION['theme'] ?? 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub — Dashboard</title>
    <link rel="icon" href="<?= APP_URL ?>/assets/icons/favicon.svg" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/materias.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/tarefas.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/calendario.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/foco.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/metas.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/anotacoes.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/avaliacoes.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/estatisticas.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/perfil.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/configuracoes.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/responsive.css">
</head>
<body class="app-body">
    <div class="app-shell">
        <!-- ===================== SIDEBAR ===================== -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-book-open-reader"></i>
                <div>
                    <span class="brand-name">StudyHub</span>
                    <span class="brand-slogan">Organize seus estudos.<br>Alcance seus objetivos.</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php
                $caminhoAtual = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
                $menu = [
                    ['label' => 'Início',         'icon' => 'fa-house',          'path' => 'dashboard'],
                    ['label' => 'Matérias',       'icon' => 'fa-book',           'path' => 'materias'],
                    ['label' => 'Tarefas',        'icon' => 'fa-list-check',     'path' => 'tarefas'],
                    ['label' => 'Calendário',     'icon' => 'fa-calendar-days',  'path' => 'calendario'],
                    ['label' => 'Modo Foco',      'icon' => 'fa-stopwatch',      'path' => 'foco'],
                    ['label' => 'Metas',          'icon' => 'fa-bullseye',       'path' => 'metas'],
                    ['label' => 'Anotações',      'icon' => 'fa-note-sticky',    'path' => 'anotacoes'],
                    ['label' => 'Avaliações',     'icon' => 'fa-file-pen',       'path' => 'avaliacoes'],
                    ['label' => 'Estatísticas',   'icon' => 'fa-chart-column',   'path' => 'estatisticas'],
                    ['label' => 'Perfil',         'icon' => 'fa-user',           'path' => 'perfil'],
                    ['label' => 'Configurações',  'icon' => 'fa-gear',           'path' => 'configuracoes'],
                ];
                foreach ($menu as $item):
                    $estaAtivo = $caminhoAtual === $item['path'] || ($item['path'] === 'dashboard' && $caminhoAtual === '');
                ?>
                    <a href="<?= APP_URL ?>/<?= $item['path'] ?>" class="sidebar-link <?= $estaAtivo ? 'active' : '' ?>">
                        <i class="fa-solid <?= $item['icon'] ?>"></i>
                        <span><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-tip">
                <i class="fa-solid fa-seedling"></i>
                <p>Grandes conquistas começam com pequenos hábitos diários.</p>
            </div>
        </aside>

        <!-- ===================== MAIN ===================== -->
        <div class="app-main">
            <header class="app-header">
                <button class="sidebar-toggle d-lg-none" id="sidebarToggle" aria-label="Abrir menu">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="app-search" id="globalSearch">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" id="globalSearchInput" placeholder="Buscar matérias, tarefas, anotações, avaliações..." aria-label="Buscar" autocomplete="off">
                    <div class="global-search-results d-none" id="globalSearchResults"></div>
                </div>

                <div class="app-header-actions">
                    <button class="header-icon-btn" id="themeToggle" aria-label="Alternar tema">
                        <i class="fa-solid fa-sun theme-icon-light"></i>
                        <i class="fa-solid fa-moon theme-icon-dark"></i>
                    </button>
                    <div class="app-user-menu dropdown">
                        <button class="header-icon-btn position-relative dropdown-toggle no-caret" id="notifButton" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notificações">
                            <i class="fa-regular fa-bell"></i>
                            <span class="notif-badge d-none" id="notifBadge">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end notif-dropdown">
                            <div class="notif-dropdown-header">
                                <span>Notificações</span>
                                <button type="button" class="btn btn-link btn-sm p-0" id="notifMarkAllRead">Marcar todas como lidas</button>
                            </div>
                            <div class="notif-dropdown-list" id="notifList">
                                <p class="text-muted small text-center py-3 mb-0">Carregando...</p>
                            </div>
                        </div>
                    </div>
                    <div class="app-user-menu dropdown">
                        <button class="app-user-btn dropdown-toggle" data-bs-toggle="dropdown">
                            <?php if (!empty($_SESSION['user_avatar'])): ?>
                                <img src="<?= APP_URL ?>/assets/images/uploads/<?= htmlspecialchars($_SESSION['user_avatar']) ?>" alt="Foto de perfil" class="app-user-avatar-img">
                            <?php else: ?>
                                <span class="app-user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></span>
                            <?php endif; ?>
                            <span class="app-user-info d-none d-md-flex">
                                <strong><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></strong>
                                <small>Estudante</small>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/perfil"><i class="fa-solid fa-user me-2"></i>Perfil</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/configuracoes"><i class="fa-solid fa-gear me-2"></i>Configurações</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/logout"><i class="fa-solid fa-right-from-bracket me-2"></i>Sair</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="app-content">
                <?php $content(); ?>
            </main>
        </div>
    </div>

    <script>
        window.APP_URL = '<?= APP_URL ?>';
        window.CSRF_TOKEN = '<?= $this->csrfToken() ?>';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/theme.js"></script>
    <script src="<?= APP_URL ?>/assets/js/notifications.js"></script>
    <script src="<?= APP_URL ?>/assets/js/search.js"></script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
