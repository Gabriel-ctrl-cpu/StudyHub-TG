<!-- ===================== HEADER ===================== -->
<nav class="navbar navbar-expand-lg landing-nav fixed-top">
    <div class="container">
        <a class="navbar-brand landing-brand" href="<?= APP_URL ?>/">
            <i class="fa-solid fa-book-open-reader"></i> StudyHub
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="#inicio">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="#funcionalidades">Funcionalidades</a></li>
                <li class="nav-item"><a class="nav-link" href="#como-funciona">Como funciona</a></li>
                <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
            </ul>
            <div class="d-flex gap-2">
                <a href="<?= APP_URL ?>/login" class="btn btn-outline-primary">Entrar</a>
                <a href="<?= APP_URL ?>/cadastro" class="btn btn-primary">Criar conta</a>
            </div>
        </div>
    </div>
</nav>

<!-- ===================== HERO ===================== -->
<header id="inicio" class="landing-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="landing-badge"><i class="fa-solid fa-sparkles"></i> Plataforma pessoal de estudos</span>
                <h1 class="landing-title">Organize seus estudos.<br>Alcance seus <span>objetivos</span>.</h1>
                <p class="landing-subtitle">O StudyHub reúne suas matérias, tarefas, metas, calendário e sessões de estudo em um único lugar.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="<?= APP_URL ?>/cadastro" class="btn btn-primary btn-lg">Começar agora <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    <a href="#funcionalidades" class="btn btn-outline-secondary btn-lg">Conhecer o sistema</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="landing-hero-card">
                    <div class="landing-hero-card-header">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Progresso geral</span>
                    </div>
                    <div class="landing-hero-progress">78%</div>
                    <p class="text-muted mb-0">Concluído esta semana</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- ===================== FUNCIONALIDADES ===================== -->
<section id="funcionalidades" class="landing-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Tudo que você precisa para estudar melhor</h2>
            <p class="section-subtitle">Ferramentas simples e diretas, sem distrações.</p>
        </div>
        <div class="row g-4">
            <?php
            $funcionalidades = [
                ['icon' => 'fa-book', 'title' => 'Matérias', 'text' => 'Organize seus conteúdos e disciplinas.'],
                ['icon' => 'fa-list-check', 'title' => 'Tarefas', 'text' => 'Controle tudo que precisa ser feito.'],
                ['icon' => 'fa-calendar-days', 'title' => 'Calendário', 'text' => 'Planeje sua rotina.'],
                ['icon' => 'fa-bullseye', 'title' => 'Metas', 'text' => 'Defina objetivos pessoais.'],
                ['icon' => 'fa-stopwatch', 'title' => 'Modo Foco', 'text' => 'Estude sem distrações.'],
                ['icon' => 'fa-chart-line', 'title' => 'Progresso', 'text' => 'Acompanhe sua evolução.'],
                ['icon' => 'fa-note-sticky', 'title' => 'Anotações', 'text' => 'Registre seus conhecimentos.'],
                ['icon' => 'fa-file-pen', 'title' => 'Avaliações', 'text' => 'Controle provas e trabalhos.'],
            ];
            foreach ($funcionalidades as $f): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid <?= $f['icon'] ?>"></i></div>
                        <h5><?= $f['title'] ?></h5>
                        <p><?= $f['text'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== COMO FUNCIONA ===================== -->
<section id="como-funciona" class="landing-section landing-section-alt">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Como funciona</h2>
            <p class="section-subtitle">Planejar → Estudar → Registrar → Acompanhar</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="step-circle">1</div>
                <h6 class="mt-3">Planejar</h6>
                <p class="text-muted">Cadastre matérias, tarefas e metas.</p>
            </div>
            <div class="col-md-3">
                <div class="step-circle">2</div>
                <h6 class="mt-3">Estudar</h6>
                <p class="text-muted">Use o Modo Foco para se concentrar.</p>
            </div>
            <div class="col-md-3">
                <div class="step-circle">3</div>
                <h6 class="mt-3">Registrar</h6>
                <p class="text-muted">Suas sessões são salvas automaticamente.</p>
            </div>
            <div class="col-md-3">
                <div class="step-circle">4</div>
                <h6 class="mt-3">Acompanhar</h6>
                <p class="text-muted">Veja gráficos do seu progresso real.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SOBRE ===================== -->
<section id="sobre" class="landing-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="section-title">Uma ferramenta só sua</h2>
                <p class="section-subtitle text-start">
                    O StudyHub não tem ranking, competição ou feed social. É um espaço individual para
                    organizar sua rotina acadêmica, no seu ritmo.
                </p>
                <ul class="landing-check-list">
                    <li><i class="fa-solid fa-check-circle"></i> Sem comparação entre usuários</li>
                    <li><i class="fa-solid fa-check-circle"></i> Seus dados são só seus</li>
                    <li><i class="fa-solid fa-check-circle"></i> Tema claro e escuro</li>
                </ul>
                <a href="<?= APP_URL ?>/cadastro" class="btn btn-primary btn-lg mt-3">Criar minha conta grátis</a>
            </div>
            <div class="col-lg-6">
                <div class="landing-quote-card">
                    <i class="fa-solid fa-quote-left"></i>
                    <p>"Disciplina é a ponte entre os seus objetivos e as suas conquistas."</p>
                    <span>— James Clear</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="landing-footer">
    <div class="container text-center">
        <div class="landing-brand mb-2"><i class="fa-solid fa-book-open-reader"></i> StudyHub</div>
        <p class="mb-1">Organize seus estudos. Alcance seus objetivos.</p>
        <p class="text-muted small mb-0">&copy; <?= date('Y') ?> StudyHub — Projeto de TCC (ADS)</p>
    </div>
</footer>
