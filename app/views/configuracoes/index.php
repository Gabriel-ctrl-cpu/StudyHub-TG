<div class="page-header">
    <h1>Configurações</h1>
    <p>Personalize sua experiência no StudyHub.</p>
</div>

<?php if (!empty($sucesso)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
<?php endif; ?>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-8">
        <!-- ===================== APARÊNCIA ===================== -->
        <div class="dash-card mb-3">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-palette me-2"></i>Aparência</h6>
            <div class="settings-row">
                <div>
                    <div class="settings-row-title">Tema escuro</div>
                    <div class="settings-row-desc">Alterna entre o tema claro e escuro em toda a plataforma.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="darkThemeSwitch"
                           <?= ($perfil['theme'] ?? 'light') === 'dark' ? 'checked' : '' ?>>
                </div>
            </div>
        </div>

        <!-- ===================== NOTIFICAÇÕES ===================== -->
        <div class="dash-card mb-3">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-bell me-2"></i>Notificações</h6>
            <div class="settings-row">
                <div>
                    <div class="settings-row-title">Tarefas do dia</div>
                    <div class="settings-row-desc">Avisar sobre tarefas com prazo para hoje.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input pref-toggle" type="checkbox" role="switch" data-pref="notif_tasks" checked>
                </div>
            </div>
            <div class="settings-row">
                <div>
                    <div class="settings-row-title">Avaliações próximas</div>
                    <div class="settings-row-desc">Avisar quando uma prova ou trabalho estiver se aproximando.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input pref-toggle" type="checkbox" role="switch" data-pref="notif_assessments" checked>
                </div>
            </div>
            <div class="settings-row">
                <div>
                    <div class="settings-row-title">Metas concluídas</div>
                    <div class="settings-row-desc">Comemorar quando uma meta for atingida.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input pref-toggle" type="checkbox" role="switch" data-pref="notif_goals" checked>
                </div>
            </div>
            <p class="settings-note">Essas preferências ficam salvas neste navegador.</p>
        </div>

        <!-- ===================== PREFERÊNCIAS ===================== -->
        <div class="dash-card mb-3">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-sliders me-2"></i>Preferências</h6>
            <div class="settings-row">
                <div>
                    <div class="settings-row-title">Iniciar semana na segunda-feira</div>
                    <div class="settings-row-desc">Afeta a exibição do calendário e dos resumos semanais.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input pref-toggle" type="checkbox" role="switch" data-pref="week_starts_monday" checked>
                </div>
            </div>
            <div class="settings-row">
                <div>
                    <div class="settings-row-title">Sons no Modo Foco</div>
                    <div class="settings-row-desc">Tocar um som ao final de cada ciclo de foco/pausa.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input pref-toggle" type="checkbox" role="switch" data-pref="focus_sound" checked>
                </div>
            </div>
        </div>

        <!-- ===================== PRIVACIDADE ===================== -->
        <div class="dash-card">
            <h6 class="dash-card-title mb-3"><i class="fa-solid fa-shield-halved me-2"></i>Privacidade</h6>
            <p class="settings-row-desc mb-0">
                O StudyHub é uma ferramenta individual: seus dados de estudo, tarefas, metas e anotações
                nunca são exibidos para outros usuários e não existe nenhum tipo de ranking ou comparação
                entre contas.
            </p>
        </div>
    </div>

    <!-- ===================== CONTA / ZONA DE PERIGO ===================== -->
    <div class="col-lg-4">
        <div class="dash-card danger-zone">
            <h6 class="dash-card-title mb-2 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Excluir conta</h6>
            <p class="settings-row-desc">Essa ação é permanente. Todos os seus dados — matérias, tarefas, metas, anotações, avaliações e sessões — serão apagados e não poderão ser recuperados.</p>
            <button type="button" class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                Excluir minha conta
            </button>
        </div>
    </div>
</div>

<!-- ===================== MODAL: excluir conta ===================== -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= APP_URL ?>/configuracoes/excluir">
                <input type="hidden" name="csrf_token" value="<?= $tokenCsrf ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Excluir conta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Digite sua senha para confirmar a exclusão permanente da sua conta.</p>
                    <input type="password" class="form-control" name="password" placeholder="Sua senha" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir permanentemente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= APP_URL ?>/assets/js/configuracoes.js"></script>
