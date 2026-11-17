-- =====================================================================
-- STUDYHUB - Banco de Dados
-- Plataforma pessoal de organização de estudos
-- MySQL 8+
-- =====================================================================

-- Garante que a conexão usada para importar este script trata tudo
-- como UTF-8, não importa a ferramenta usada (phpMyAdmin, linha de
-- comando, etc.) — evita caracteres acentuados corrompidos.
SET NAMES utf8mb4;

-- Recria o banco do zero a cada importação, evitando erros de
-- "tabela já existe" ao reimportar este script.
DROP DATABASE IF EXISTS studyhub;

CREATE DATABASE studyhub
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE studyhub;

-- =====================================================================
-- TABELA: users
-- =====================================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(150) DEFAULT NULL,
    institution VARCHAR(150) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    theme ENUM('light', 'dark') NOT NULL DEFAULT 'light',
    daily_goal_minutes INT UNSIGNED NOT NULL DEFAULT 120,
    remember_token VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: subjects (matérias)
-- =====================================================================
CREATE TABLE subjects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT DEFAULT NULL,
    color VARCHAR(7) NOT NULL DEFAULT '#2563eb',
    goal_description VARCHAR(255) DEFAULT NULL,
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    is_archived TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_subjects_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_subjects_user (user_id)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: tasks (tarefas)
-- =====================================================================
CREATE TABLE tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT DEFAULT NULL,
    priority ENUM('alta', 'media', 'baixa') NOT NULL DEFAULT 'media',
    status ENUM('pendente', 'em_andamento', 'concluida', 'cancelada') NOT NULL DEFAULT 'pendente',
    due_date DATE DEFAULT NULL,
    due_time TIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    INDEX idx_tasks_user (user_id),
    INDEX idx_tasks_subject (subject_id),
    INDEX idx_tasks_due_date (due_date),
    INDEX idx_tasks_status (status)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: study_sessions (sessões de estudo / modo foco)
-- =====================================================================
CREATE TABLE study_sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED DEFAULT NULL,
    duration_minutes INT UNSIGNED NOT NULL,
    session_type ENUM('foco', 'manual') NOT NULL DEFAULT 'foco',
    session_date DATE NOT NULL,
    started_at DATETIME DEFAULT NULL,
    finished_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_sessions_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    INDEX idx_sessions_user (user_id),
    INDEX idx_sessions_date (session_date),
    INDEX idx_sessions_subject (subject_id)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: goals (metas)
-- =====================================================================
CREATE TABLE goals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT DEFAULT NULL,
    goal_type ENUM('horas', 'tarefas', 'exercicios', 'personalizada') NOT NULL DEFAULT 'personalizada',
    target_value DECIMAL(10,2) NOT NULL DEFAULT 1,
    current_value DECIMAL(10,2) NOT NULL DEFAULT 0,
    deadline DATE DEFAULT NULL,
    is_completed TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_goals_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_goals_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    INDEX idx_goals_user (user_id)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: notes (anotações)
-- =====================================================================
CREATE TABLE notes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(180) NOT NULL,
    content TEXT NOT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_notes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_notes_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    INDEX idx_notes_user (user_id),
    FULLTEXT KEY ft_notes_content (title, content)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: events (compromissos do calendário — genéricos)
-- =====================================================================
CREATE TABLE events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT DEFAULT NULL,
    event_type ENUM('compromisso', 'lembrete', 'outro') NOT NULL DEFAULT 'compromisso',
    event_date DATE NOT NULL,
    start_time TIME DEFAULT NULL,
    end_time TIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_events_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    INDEX idx_events_user (user_id),
    INDEX idx_events_date (event_date)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: assessments (avaliações — provas/trabalhos)
-- =====================================================================
CREATE TABLE assessments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    assessment_type ENUM('prova', 'trabalho', 'seminario', 'projeto', 'outro') NOT NULL DEFAULT 'prova',
    assessment_date DATE NOT NULL,
    assessment_time TIME DEFAULT NULL,
    description TEXT DEFAULT NULL,
    weight DECIMAL(5,2) DEFAULT NULL,
    grade DECIMAL(5,2) DEFAULT NULL,
    status ENUM('agendado', 'realizado', 'corrigido') NOT NULL DEFAULT 'agendado',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_assessments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_assessments_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    INDEX idx_assessments_user (user_id),
    INDEX idx_assessments_date (assessment_date)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: notifications (notificações)
-- =====================================================================
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    message VARCHAR(255) NOT NULL,
    notification_type ENUM('tarefa', 'avaliacao', 'meta', 'sistema') NOT NULL DEFAULT 'sistema',
    related_type VARCHAR(20) DEFAULT NULL,
    related_id INT UNSIGNED DEFAULT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_read (is_read),
    UNIQUE KEY uq_notifications_related (user_id, related_type, related_id, notification_type)
) ENGINE=InnoDB;

-- =====================================================================
-- TABELA: sessions (sessões de login PHP - controle server-side)
-- =====================================================================
CREATE TABLE sessions (
    id VARCHAR(128) NOT NULL PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    payload TEXT DEFAULT NULL,
    last_activity INT UNSIGNED NOT NULL,
    CONSTRAINT fk_login_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sessions_user (user_id)
) ENGINE=InnoDB;

-- =====================================================================
-- DADOS DE DEMONSTRAÇÃO
-- Senha de todos os usuários fictícios: "senha123"
-- Hash gerado com password_hash('senha123', PASSWORD_BCRYPT)
-- =====================================================================

INSERT INTO users (name, email, password, course, institution, theme, daily_goal_minutes) VALUES
('Gabriel Henrique', 'gabriel@studyhub.com', '$2b$10$uYtf.YbCE15Ni62fkmx.h.UYECjc5gNpXaE1/oJ6QX3MtKOyr6mEe', 'Análise e Desenvolvimento de Sistemas', 'Faculdade Exemplo', 'light', 120),
('Ana Beatriz Souza', 'ana@studyhub.com', '$2b$10$uYtf.YbCE15Ni62fkmx.h.UYECjc5gNpXaE1/oJ6QX3MtKOyr6mEe', 'Engenharia de Software', 'Universidade Exemplo', 'light', 90),
('Lucas Martins', 'lucas@studyhub.com', '$2b$10$uYtf.YbCE15Ni62fkmx.h.UYECjc5gNpXaE1/oJ6QX3MtKOyr6mEe', 'Ciência da Computação', 'Universidade Exemplo', 'dark', 150),
('Mariana Alves', 'mariana@studyhub.com', '$2b$10$uYtf.YbCE15Ni62fkmx.h.UYECjc5gNpXaE1/oJ6QX3MtKOyr6mEe', 'Sistemas de Informação', 'Faculdade Exemplo', 'light', 60),
('Pedro Henrique Lima', 'pedro@studyhub.com', '$2b$10$uYtf.YbCE15Ni62fkmx.h.UYECjc5gNpXaE1/oJ6QX3MtKOyr6mEe', 'Análise e Desenvolvimento de Sistemas', 'Faculdade Exemplo', 'light', 120);

-- Matérias (todas do usuário 1, para consistência da demo principal)
INSERT INTO subjects (user_id, name, description, color, goal_description, start_date) VALUES
(1, 'JavaScript', 'Fundamentos e prática de JavaScript moderno (ES6+)', '#f7df1e', 'Concluir o módulo avançado', '2026-06-01'),
(1, 'Banco de Dados', 'Modelagem e SQL com MySQL', '#2563eb', 'Dominar consultas complexas e normalização', '2026-06-01'),
(1, 'Estatística', 'Estatística aplicada à computação', '#16a34a', 'Entender probabilidade e distribuições', '2026-07-01'),
(1, 'Python', 'Python para automação e dados', '#eab308', 'Concluir curso introdutório', '2026-07-10'),
(1, 'Desenvolvimento Web', 'HTML, CSS, PHP e arquitetura MVC', '#7c3aed', 'Construir o projeto de TCC', '2026-05-15'),
(1, 'Inglês Técnico', 'Leitura de documentação técnica em inglês', '#dc2626', 'Ler artigos técnicos sem tradução', '2026-06-20');

-- Tarefas
INSERT INTO tasks (user_id, subject_id, title, description, priority, status, due_date, due_time) VALUES
(1, 1, 'Estudar funções em JavaScript', 'Revisar arrow functions e closures', 'alta', 'pendente', '2026-09-01', '19:00:00'),
(1, 2, 'Fazer exercícios de SQL', 'Lista de exercícios de JOIN e subqueries', 'media', 'pendente', '2026-09-02', '14:00:00'),
(1, 3, 'Revisar Estatística', 'Revisão de distribuição normal', 'baixa', 'pendente', '2026-09-05', '10:00:00'),
(1, 4, 'Ler capítulo 4', 'Capítulo sobre estruturas de dados em Python', 'baixa', 'pendente', '2026-09-06', '16:00:00'),
(1, 1, 'Praticar manipulação de arrays', NULL, 'media', 'concluida', '2026-08-28', '18:00:00'),
(1, 5, 'Estruturar MVC do projeto', 'Organizar pastas do TCC', 'alta', 'em_andamento', '2026-09-03', '20:00:00'),
(1, 2, 'Modelar DER do banco', NULL, 'alta', 'concluida', '2026-08-25', '15:00:00'),
(1, 6, 'Traduzir artigo técnico', NULL, 'baixa', 'pendente', '2026-09-08', NULL),
(1, 3, 'Resolver lista de exercícios 2', NULL, 'media', 'pendente', '2026-09-10', NULL),
(1, 1, 'Assistir aula sobre Promises', NULL, 'media', 'pendente', '2026-09-04', '19:30:00'),
(1, 5, 'Criar wireframes das telas', NULL, 'media', 'concluida', '2026-08-20', NULL),
(1, 2, 'Estudar índices e performance', NULL, 'baixa', 'pendente', '2026-09-12', NULL),
(1, 4, 'Praticar laços de repetição', NULL, 'media', 'concluida', '2026-08-26', NULL),
(1, 6, 'Revisar vocabulário técnico', NULL, 'baixa', 'cancelada', '2026-08-30', NULL),
(1, 5, 'Configurar ambiente XAMPP', NULL, 'alta', 'concluida', '2026-08-15', NULL);

-- Sessões de estudo
INSERT INTO study_sessions (user_id, subject_id, duration_minutes, session_type, session_date) VALUES
(1, 1, 25, 'foco', '2026-08-29'),
(1, 2, 50, 'foco', '2026-08-28'),
(1, 3, 30, 'foco', '2026-08-27'),
(1, 4, 25, 'foco', '2026-08-26'),
(1, 1, 50, 'foco', '2026-08-25'),
(1, 5, 90, 'manual', '2026-08-24'),
(1, 2, 25, 'foco', '2026-08-23'),
(1, 6, 25, 'foco', '2026-08-22'),
(1, 3, 50, 'foco', '2026-08-21'),
(1, 1, 30, 'foco', '2026-08-20');

-- Metas
INSERT INTO goals (user_id, subject_id, title, description, goal_type, target_value, current_value, deadline, is_completed) VALUES
(1, NULL, 'Estudar 10 horas essa semana', NULL, 'horas', 10, 7.5, '2026-09-07', 0),
(1, 1, 'Concluir JavaScript', 'Terminar todos os tópicos do módulo', 'tarefas', 8, 6, '2026-09-30', 0),
(1, 2, 'Resolver 30 exercícios de SQL', NULL, 'exercicios', 30, 30, '2026-09-15', 1),
(1, NULL, 'Ler 2 livros esse mês', NULL, 'personalizada', 2, 1, '2026-09-30', 0),
(1, 5, 'Finalizar estrutura do TCC', 'Entregar primeira versão funcional', 'personalizada', 1, 0, '2026-10-01', 0);

-- Anotações
INSERT INTO notes (user_id, subject_id, title, content, tags) VALUES
(1, 1, 'Closures em JS', 'Uma closure é a combinação de uma função com o escopo léxico em que ela foi declarada.', 'javascript,funcoes'),
(1, 2, 'Normalização', 'Formas normais existem para reduzir redundância: 1FN, 2FN, 3FN.', 'sql,modelagem'),
(1, 3, 'Distribuição normal', 'Curva simétrica em torno da média, definida por média e desvio padrão.', 'estatistica'),
(1, 5, 'Padrão MVC', 'Separar Model, View e Controller melhora manutenção e organização do código.', 'arquitetura,mvc'),
(1, 4, 'Listas em Python', 'Listas são mutáveis; tuplas não. List comprehension é uma forma concisa de criar listas.', 'python'),
(1, 1, 'Promises', 'Uma Promise representa a eventual conclusão (ou falha) de uma operação assíncrona.', 'javascript,assincronismo'),
(1, 6, 'Vocabulário técnico', 'Termos comuns em documentação: deprecated, thread-safe, boilerplate.', 'ingles'),
(1, 2, 'Índices', 'Índices aceleram buscas, mas custam em escrita e espaço em disco.', 'sql,performance'),
(1, 5, 'Segurança básica', 'Sempre usar prepared statements e password_hash para evitar vulnerabilidades comuns.', 'seguranca'),
(1, 3, 'Probabilidade condicional', 'P(A|B) = P(A ∩ B) / P(B), desde que P(B) > 0.', 'estatistica');

-- Eventos
INSERT INTO events (user_id, subject_id, title, description, event_type, event_date, start_time, end_time) VALUES
(1, 1, 'Grupo de estudos JS', 'Encontro com colegas para revisão', 'compromisso', '2026-09-03', '19:00:00', '21:00:00'),
(1, NULL, 'Reunião de orientação do TCC', NULL, 'compromisso', '2026-09-05', '10:00:00', '11:00:00'),
(1, 2, 'Aula extra de SQL', NULL, 'compromisso', '2026-09-06', '14:00:00', '16:00:00'),
(1, NULL, 'Entrega parcial do projeto', 'Lembrete de entrega', 'lembrete', '2026-09-10', NULL, NULL),
(1, 5, 'Apresentação do protótipo', NULL, 'compromisso', '2026-09-12', '09:00:00', '10:00:00'),
(1, 3, 'Prova de Estatística', NULL, 'lembrete', '2026-09-20', '19:00:00', NULL),
(1, NULL, 'Revisão geral da semana', NULL, 'lembrete', '2026-09-07', '20:00:00', NULL),
(1, 4, 'Live sobre automação com Python', NULL, 'compromisso', '2026-09-15', '18:00:00', '19:30:00'),
(1, NULL, 'Feriado — sem aulas', NULL, 'outro', '2026-09-07', NULL, NULL),
(1, 6, 'Clube do livro técnico', NULL, 'compromisso', '2026-09-18', '17:00:00', '18:00:00');

-- Avaliações
INSERT INTO assessments (user_id, subject_id, title, assessment_type, assessment_date, assessment_time, description, weight, status) VALUES
(1, 2, 'Prova de Banco de Dados', 'prova', '2026-09-05', '19:00:00', 'Conteúdo: modelagem e SQL avançado', 10, 'agendado'),
(1, 1, 'Trabalho de JavaScript', 'trabalho', '2026-09-12', NULL, 'Projeto prático com manipulação de DOM', 8, 'agendado'),
(1, 3, 'Prova de Estatística', 'prova', '2026-09-20', '19:00:00', NULL, 10, 'agendado'),
(1, 5, 'Apresentação de Projeto', 'seminario', '2026-09-28', '14:00:00', 'Apresentação parcial do TCC', 15, 'agendado'),
(1, 4, 'Trabalho de Python', 'trabalho', '2026-08-20', NULL, NULL, 7, 'corrigido'),
(1, 2, 'Prova de SQL básico', 'prova', '2026-08-10', '19:00:00', NULL, 10, 'corrigido'),
(1, 6, 'Seminário de Inglês Técnico', 'seminario', '2026-08-15', NULL, NULL, 5, 'realizado'),
(1, 1, 'Exercício avaliativo de JS', 'outro', '2026-08-22', NULL, NULL, 3, 'corrigido');

UPDATE assessments SET grade = 8.5 WHERE id IN (5, 6, 8);
UPDATE assessments SET grade = 7.0 WHERE id = 7;

-- Notificações
INSERT INTO notifications (user_id, title, message, notification_type, is_read) VALUES
(1, 'Tarefas de hoje', 'Você possui 3 tarefas para hoje.', 'tarefa', 0),
(1, 'Prova se aproximando', 'Prova de Banco de Dados amanhã.', 'avaliacao', 0),
(1, 'Meta atingida!', 'Você concluiu a meta "Resolver 30 exercícios de SQL".', 'meta', 1),
(1, 'Tarefa concluída', 'Tarefa concluída com sucesso: Modelar DER do banco.', 'tarefa', 1),
(1, 'Bem-vindo ao StudyHub', 'Sua conta foi criada com sucesso. Bons estudos!', 'sistema', 1);
