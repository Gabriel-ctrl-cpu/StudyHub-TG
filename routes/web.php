<?php
/**
 * routes/web.php
 *
 * Definição de todas as rotas da aplicação: páginas públicas
 * (landing, autenticação) e todos os módulos da área autenticada
 * (dashboard, matérias, tarefas, calendário, modo foco, metas,
 * anotações, avaliações, estatísticas, configurações, perfil).
 */

/** @var Router $router */

// -------------------------------------------------------------
// Públicas
// -------------------------------------------------------------
$router->get('/', 'HomeController@index');

$router->get('login', 'AuthController@loginForm');
$router->post('login', 'AuthController@login');

$router->get('cadastro', 'AuthController@registerForm');
$router->post('cadastro', 'AuthController@register');

$router->get('esqueci-senha', 'AuthController@forgotForm');
$router->post('esqueci-senha', 'AuthController@forgot');

$router->get('logout', 'AuthController@logout');

// -------------------------------------------------------------
// Área autenticada
// -------------------------------------------------------------
$router->get('dashboard', 'DashboardController@index');

// Matérias
$router->get('materias', 'SubjectController@index');
$router->get('materias/nova', 'SubjectController@createForm');
$router->post('materias', 'SubjectController@store');
$router->get('materias/{id}/editar', 'SubjectController@editForm');
$router->post('materias/{id}', 'SubjectController@update');
$router->post('materias/{id}/excluir', 'SubjectController@destroy');
$router->post('materias/{id}/arquivar', 'SubjectController@archive');

// Tarefas
$router->get('tarefas', 'TaskController@index');
$router->get('tarefas/nova', 'TaskController@createForm');
$router->post('tarefas', 'TaskController@store');
$router->get('tarefas/{id}/editar', 'TaskController@editForm');
$router->post('tarefas/{id}', 'TaskController@update');
$router->post('tarefas/{id}/excluir', 'TaskController@destroy');
$router->post('tarefas/{id}/concluir', 'TaskController@toggleComplete');

// Calendário
$router->get('calendario', 'CalendarController@index');
$router->get('calendario/eventos', 'CalendarController@data');
$router->post('calendario/eventos', 'CalendarController@store');
$router->post('calendario/eventos/{id}', 'CalendarController@update');
$router->post('calendario/eventos/{id}/excluir', 'CalendarController@destroy');

// Modo Foco
$router->get('foco', 'FocusController@index');
$router->post('foco/sessao', 'FocusController@store');

// Metas
$router->get('metas', 'GoalController@index');
$router->get('metas/nova', 'GoalController@createForm');
$router->post('metas', 'GoalController@store');
$router->get('metas/{id}/editar', 'GoalController@editForm');
$router->post('metas/{id}', 'GoalController@update');
$router->post('metas/{id}/excluir', 'GoalController@destroy');
$router->post('metas/{id}/progresso', 'GoalController@updateProgress');

// Anotações
$router->get('anotacoes', 'NoteController@index');
$router->get('anotacoes/nova', 'NoteController@createForm');
$router->post('anotacoes', 'NoteController@store');
$router->get('anotacoes/{id}/editar', 'NoteController@editForm');
$router->post('anotacoes/{id}', 'NoteController@update');
$router->post('anotacoes/{id}/excluir', 'NoteController@destroy');

// Avaliações
$router->get('avaliacoes', 'AssessmentController@index');
$router->get('avaliacoes/nova', 'AssessmentController@createForm');
$router->post('avaliacoes', 'AssessmentController@store');
$router->get('avaliacoes/{id}/editar', 'AssessmentController@editForm');
$router->post('avaliacoes/{id}', 'AssessmentController@update');
$router->post('avaliacoes/{id}/excluir', 'AssessmentController@destroy');

// Estatísticas
$router->get('estatisticas', 'StatsController@index');

// Configurações
$router->get('configuracoes', 'SettingsController@index');
$router->post('configuracoes/tema', 'SettingsController@updateTheme');
$router->post('configuracoes/excluir', 'SettingsController@deleteAccount');

// Notificações
$router->get('notificacoes', 'NotificationController@index');
$router->post('notificacoes/marcar-lidas', 'NotificationController@markAllRead');

// Busca global
$router->get('busca', 'SearchController@index');


// Perfil
$router->get('perfil', 'ProfileController@index');
$router->post('perfil', 'ProfileController@update');
$router->post('perfil/foto', 'ProfileController@updatePhoto');
$router->post('perfil/senha', 'ProfileController@updatePassword');
