-- =====================================================================
-- STUDYHUB — Alunos de exemplo para comparação (semestre completo)
-- =====================================================================
-- Script ADITIVO e reimportável: remove (se existirem) e recria os
-- dois usuários de exemplo abaixo, sem afetar o restante do banco.
--
-- Senha dos dois usuários: senha123
--
-- Perfil 1 — Beatriz Organizada: aluna organizada, ~90% de conclusão
--            de tarefas, sessões de estudo quase diárias, metas em dia,
--            boas notas. Semestre de 01/08/2026 a 30/11/2026.
-- Perfil 2 — Carlos Dificuldade: várias tarefas atrasadas, poucas
--            sessões de estudo (interesse caindo ao longo do tempo),
--            metas travadas, notas baixas. Mesmo período de semestre.
-- =====================================================================

SET NAMES utf8mb4;

USE studyhub;

-- Remove os dois usuários de exemplo, se já existirem (reimportação segura).
-- Os dados relacionados (matérias, tarefas, sessões, metas, notas,
-- avaliações) são removidos automaticamente via ON DELETE CASCADE.
DELETE FROM users WHERE email IN ('beatriz.organizada@studyhub.com', 'carlos.dificuldade@studyhub.com');

-- ---------------------------------------------------------------------
-- USUÁRIOS
-- ---------------------------------------------------------------------
INSERT INTO users (name, email, password, course, institution, theme, daily_goal_minutes, created_at) VALUES
('Beatriz Organizada', 'beatriz.organizada@studyhub.com', '$2b$10$uYtf.YbCE15Ni62fkmx.h.UYECjc5gNpXaE1/oJ6QX3MtKOyr6mEe', 'Análise e Desenvolvimento de Sistemas', 'Faculdade Exemplo', 'light', 120, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '08:00:00')),
('Carlos Dificuldade', 'carlos.dificuldade@studyhub.com', '$2b$10$uYtf.YbCE15Ni62fkmx.h.UYECjc5gNpXaE1/oJ6QX3MtKOyr6mEe', 'Análise e Desenvolvimento de Sistemas', 'Faculdade Exemplo', 'light', 120, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '08:00:00'));

SET @beatriz_id = (SELECT id FROM users WHERE email = 'beatriz.organizada@studyhub.com');
SET @carlos_id  = (SELECT id FROM users WHERE email = 'carlos.dificuldade@studyhub.com');

-- =====================================================================
-- PERFIL 1 — BEATRIZ ORGANIZADA (exemplo bom)
-- =====================================================================

-- Matérias
INSERT INTO subjects (user_id, name, description, color, goal_description, start_date, end_date) VALUES
(@beatriz_id, 'Programação Web', 'PHP, MVC e desenvolvimento de sistemas web', '#2563eb', 'Dominar arquitetura MVC', DATE_ADD(CURDATE(), INTERVAL -37 DAY), DATE_ADD(CURDATE(), INTERVAL 84 DAY));
SET @b_prog = LAST_INSERT_ID();

INSERT INTO subjects (user_id, name, description, color, goal_description, start_date, end_date) VALUES
(@beatriz_id, 'Estruturas de Dados', 'Listas, pilhas, filas e árvores', '#16a34a', 'Implementar as estruturas do zero', DATE_ADD(CURDATE(), INTERVAL -37 DAY), DATE_ADD(CURDATE(), INTERVAL 84 DAY));
SET @b_estr = LAST_INSERT_ID();

INSERT INTO subjects (user_id, name, description, color, goal_description, start_date, end_date) VALUES
(@beatriz_id, 'Redes de Computadores', 'Protocolos e arquitetura de redes', '#7c3aed', 'Entender o modelo OSI a fundo', DATE_ADD(CURDATE(), INTERVAL -37 DAY), DATE_ADD(CURDATE(), INTERVAL 84 DAY));
SET @b_redes = LAST_INSERT_ID();

INSERT INTO subjects (user_id, name, description, color, goal_description, start_date, end_date) VALUES
(@beatriz_id, 'Banco de Dados', 'Modelagem relacional e SQL avançado', '#0d9488', 'Dominar consultas complexas', DATE_ADD(CURDATE(), INTERVAL -37 DAY), DATE_ADD(CURDATE(), INTERVAL 84 DAY));
SET @b_bd = LAST_INSERT_ID();

-- Tarefas (semestre inteiro; ~90% de conclusão no período já decorrido)
INSERT INTO tasks (user_id, subject_id, title, priority, status, due_date, completed_at) VALUES
(@beatriz_id, @b_prog, 'Estudar Composer e autoload', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -37 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -35 DAY), '19:00:00')),
(@beatriz_id, @b_prog, 'Revisar PDO e prepared statements', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -30 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -30 DAY), '19:00:00')),
(@beatriz_id, @b_prog, 'Implementar paginação', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -25 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -25 DAY), '19:00:00')),
(@beatriz_id, @b_prog, 'Praticar validação de formulários', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -20 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -20 DAY), '19:00:00')),
(@beatriz_id, @b_prog, 'Implementar CRUD de exemplo', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -13 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -13 DAY), '19:00:00')),
(@beatriz_id, @b_prog, 'Estudar deploy de aplicações', 'baixa', 'concluida', DATE_ADD(CURDATE(), INTERVAL -7 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -7 DAY), '19:00:00')),
(@beatriz_id, @b_prog, 'Estudar cache', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -2 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -2 DAY), '19:00:00')),
(@beatriz_id, @b_prog, 'Praticar consultas com JOIN', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 3 DAY), NULL),
(@beatriz_id, @b_prog, 'Revisar sessões PHP', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 8 DAY), NULL),
(@beatriz_id, @b_prog, 'Ler sobre proteção CSRF', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 15 DAY), NULL),
(@beatriz_id, @b_prog, 'Praticar refatoração', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 20 DAY), NULL),
(@beatriz_id, @b_prog, 'Estudar API REST', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 27 DAY), NULL),
(@beatriz_id, @b_prog, 'Revisar arquitetura em camadas', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 34 DAY), NULL),
(@beatriz_id, @b_prog, 'Estudar padrões de projeto', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 41 DAY), NULL),
(@beatriz_id, @b_prog, 'Revisar testes automatizados', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 46 DAY), NULL),
(@beatriz_id, @b_prog, 'Implementar upload de arquivos', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 53 DAY), NULL),
(@beatriz_id, @b_prog, 'Estudar roteamento MVC', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 59 DAY), NULL),
(@beatriz_id, @b_prog, 'Estudar Composer e autoload', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 64 DAY), NULL),
(@beatriz_id, @b_prog, 'Revisar PDO e prepared statements', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 69 DAY), NULL),
(@beatriz_id, @b_prog, 'Implementar paginação', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 74 DAY), NULL),
(@beatriz_id, @b_prog, 'Praticar validação de formulários', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 79 DAY), NULL),
(@beatriz_id, @b_prog, 'Implementar CRUD de exemplo', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 84 DAY), NULL),
(@beatriz_id, @b_estr, 'Estudar heaps', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -37 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -35 DAY), '19:00:00')),
(@beatriz_id, @b_estr, 'Resolver desafios de algoritmos', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -30 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -30 DAY), '19:00:00')),
(@beatriz_id, @b_estr, 'Implementar busca em profundidade', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -23 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -23 DAY), '19:00:00')),
(@beatriz_id, @b_estr, 'Estudar grafos', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -16 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -16 DAY), '19:00:00')),
(@beatriz_id, @b_estr, 'Estudar árvores binárias', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -11 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -11 DAY), '19:00:00')),
(@beatriz_id, @b_estr, 'Revisar complexidade Big O', 'baixa', 'concluida', DATE_ADD(CURDATE(), INTERVAL -4 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -4 DAY), '19:00:00')),
(@beatriz_id, @b_estr, 'Implementar busca em largura', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 3 DAY), NULL),
(@beatriz_id, @b_estr, 'Resolver lista de exercícios', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 8 DAY), NULL),
(@beatriz_id, @b_estr, 'Revisão geral', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 15 DAY), NULL),
(@beatriz_id, @b_estr, 'Estudar árvores AVL', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 22 DAY), NULL),
(@beatriz_id, @b_estr, 'Estudar tabelas hash', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 27 DAY), NULL),
(@beatriz_id, @b_estr, 'Implementar lista encadeada', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 32 DAY), NULL),
(@beatriz_id, @b_estr, 'Estudar algoritmos de ordenação', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 38 DAY), NULL),
(@beatriz_id, @b_estr, 'Implementar pilha em PHP', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 44 DAY), NULL),
(@beatriz_id, @b_estr, 'Implementar quicksort', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 50 DAY), NULL),
(@beatriz_id, @b_estr, 'Praticar recursão', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 57 DAY), NULL),
(@beatriz_id, @b_estr, 'Implementar fila em PHP', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 64 DAY), NULL),
(@beatriz_id, @b_estr, 'Estudar heaps', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 71 DAY), NULL),
(@beatriz_id, @b_estr, 'Resolver desafios de algoritmos', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 76 DAY), NULL),
(@beatriz_id, @b_estr, 'Implementar busca em profundidade', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 83 DAY), NULL),
(@beatriz_id, @b_redes, 'Revisão geral', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -37 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '19:00:00')),
(@beatriz_id, @b_redes, 'Ler sobre segurança em redes', 'baixa', 'concluida', DATE_ADD(CURDATE(), INTERVAL -32 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -32 DAY), '19:00:00')),
(@beatriz_id, @b_redes, 'Estudar protocolo TCP/IP', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -25 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -25 DAY), '19:00:00')),
(@beatriz_id, @b_redes, 'Estudar switches e VLANs', 'baixa', 'concluida', DATE_ADD(CURDATE(), INTERVAL -18 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -18 DAY), '19:00:00')),
(@beatriz_id, @b_redes, 'Praticar subnetting', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -12 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -12 DAY), '19:00:00')),
(@beatriz_id, @b_redes, 'Estudar redes sem fio', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -5 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -5 DAY), '19:00:00')),
(@beatriz_id, @b_redes, 'Revisar para a prova', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 1 DAY), NULL),
(@beatriz_id, @b_redes, 'Estudar protocolo HTTP/HTTPS', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 6 DAY), NULL),
(@beatriz_id, @b_redes, 'Estudar modelo OSI', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 12 DAY), NULL),
(@beatriz_id, @b_redes, 'Estudar roteamento', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 18 DAY), NULL),
(@beatriz_id, @b_redes, 'Estudar VPN', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 23 DAY), NULL),
(@beatriz_id, @b_redes, 'Praticar comandos de rede', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 29 DAY), NULL),
(@beatriz_id, @b_redes, 'Revisar camada de transporte', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 34 DAY), NULL),
(@beatriz_id, @b_redes, 'Estudar DNS', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 41 DAY), NULL),
(@beatriz_id, @b_redes, 'Configurar rede local de teste', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 48 DAY), NULL),
(@beatriz_id, @b_redes, 'Estudar DHCP', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 54 DAY), NULL),
(@beatriz_id, @b_redes, 'Configurar firewall de teste', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 61 DAY), NULL),
(@beatriz_id, @b_redes, 'Revisão geral', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 66 DAY), NULL),
(@beatriz_id, @b_redes, 'Ler sobre segurança em redes', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 73 DAY), NULL),
(@beatriz_id, @b_redes, 'Estudar protocolo TCP/IP', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 78 DAY), NULL),
(@beatriz_id, @b_bd, 'Praticar consultas agregadas', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL -37 DAY), NULL),
(@beatriz_id, @b_bd, 'Estudar normalização', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -30 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -30 DAY), '19:00:00')),
(@beatriz_id, @b_bd, 'Revisão para a prova', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -23 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -23 DAY), '19:00:00')),
(@beatriz_id, @b_bd, 'Revisão geral', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -18 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -18 DAY), '19:00:00')),
(@beatriz_id, @b_bd, 'Praticar stored procedures', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -11 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -11 DAY), '19:00:00')),
(@beatriz_id, @b_bd, 'Revisar backup e restore', 'alta', 'concluida', DATE_ADD(CURDATE(), INTERVAL -6 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -6 DAY), '19:00:00')),
(@beatriz_id, @b_bd, 'Revisar chaves estrangeiras', 'baixa', 'concluida', DATE_ADD(CURDATE(), INTERVAL -1 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -1 DAY), '19:00:00')),
(@beatriz_id, @b_bd, 'Praticar otimização de queries', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 4 DAY), NULL),
(@beatriz_id, @b_bd, 'Estudar views', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 11 DAY), NULL),
(@beatriz_id, @b_bd, 'Praticar modelagem ER', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 18 DAY), NULL),
(@beatriz_id, @b_bd, 'Estudar particionamento', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 23 DAY), NULL),
(@beatriz_id, @b_bd, 'Estudar transações', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 28 DAY), NULL),
(@beatriz_id, @b_bd, 'Revisar triggers', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 33 DAY), NULL),
(@beatriz_id, @b_bd, 'Estudar NoSQL vs relacional', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 38 DAY), NULL),
(@beatriz_id, @b_bd, 'Praticar subqueries', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 44 DAY), NULL),
(@beatriz_id, @b_bd, 'Praticar DER completo', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 49 DAY), NULL),
(@beatriz_id, @b_bd, 'Estudar índices e performance', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 56 DAY), NULL),
(@beatriz_id, @b_bd, 'Praticar consultas agregadas', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 61 DAY), NULL),
(@beatriz_id, @b_bd, 'Estudar normalização', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 67 DAY), NULL),
(@beatriz_id, @b_bd, 'Revisão para a prova', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 74 DAY), NULL),
(@beatriz_id, @b_bd, 'Revisão geral', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 80 DAY), NULL);

-- Sessões de estudo (quase diárias, do início do semestre até hoje)
INSERT INTO study_sessions (user_id, subject_id, duration_minutes, session_type, session_date, created_at) VALUES
(@beatriz_id, @b_bd, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -36 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -36 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -36 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -36 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -33 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -33 DAY), '19:30:00')),
(@beatriz_id, @b_redes, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -33 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -33 DAY), '19:30:00')),
(@beatriz_id, @b_redes, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -31 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -31 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -30 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -30 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -28 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -28 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -27 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -27 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -26 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -26 DAY), '19:30:00')),
(@beatriz_id, @b_redes, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -26 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -26 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -25 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -25 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -25 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -25 DAY), '19:30:00')),
(@beatriz_id, @b_redes, 60, 'foco', DATE_ADD(CURDATE(), INTERVAL -24 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -24 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -22 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -22 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -22 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -22 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -21 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -21 DAY), '19:30:00')),
(@beatriz_id, @b_redes, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -21 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -21 DAY), '19:30:00')),
(@beatriz_id, @b_redes, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -20 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -20 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -20 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -20 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -19 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -19 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -18 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -18 DAY), '19:30:00')),
(@beatriz_id, @b_redes, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -18 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -18 DAY), '19:30:00')),
(@beatriz_id, @b_redes, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -17 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -17 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -16 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -16 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 60, 'foco', DATE_ADD(CURDATE(), INTERVAL -16 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -16 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -15 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -15 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -13 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -13 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -12 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -12 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 45, 'foco', DATE_ADD(CURDATE(), INTERVAL -11 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -11 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -10 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -10 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -9 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -9 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -8 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -8 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -8 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -8 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 45, 'foco', DATE_ADD(CURDATE(), INTERVAL -7 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -7 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -7 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -7 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 60, 'foco', DATE_ADD(CURDATE(), INTERVAL -6 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -6 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -5 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -5 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 60, 'foco', DATE_ADD(CURDATE(), INTERVAL -5 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -5 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 60, 'foco', DATE_ADD(CURDATE(), INTERVAL -4 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -4 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 30, 'foco', DATE_ADD(CURDATE(), INTERVAL -3 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -3 DAY), '19:30:00')),
(@beatriz_id, @b_estr, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -2 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -2 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 50, 'foco', DATE_ADD(CURDATE(), INTERVAL -2 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -2 DAY), '19:30:00')),
(@beatriz_id, @b_prog, 60, 'foco', DATE_ADD(CURDATE(), INTERVAL -1 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -1 DAY), '19:30:00')),
(@beatriz_id, @b_bd, 50, 'foco', CURDATE(), TIMESTAMP(CURDATE(), '19:30:00')),
(@beatriz_id, @b_redes, 25, 'foco', CURDATE(), TIMESTAMP(CURDATE(), '19:30:00'));

-- Metas (created_at fixado no início do semestre, para o progresso
-- automático de horas/tarefas contar a atividade toda)
INSERT INTO goals (user_id, subject_id, title, goal_type, target_value, current_value, deadline, is_completed, created_at) VALUES
(@beatriz_id, NULL, 'Estudar 40 horas neste semestre', 'horas', 40, 0, DATE_ADD(CURDATE(), INTERVAL 84 DAY), 0, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '08:00:00')),
(@beatriz_id, @b_estr, 'Concluir todas as tarefas de Estruturas de Dados', 'tarefas', 6, 0, DATE_ADD(CURDATE(), INTERVAL 84 DAY), 0, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '08:00:00')),
(@beatriz_id, @b_prog, 'Resolver 10 exercícios extras de Programação Web', 'exercicios', 10, 7, DATE_ADD(CURDATE(), INTERVAL 38 DAY), 0, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '08:00:00'));

-- Avaliações (passadas já corrigidas com notas boas; futuras agendadas)
INSERT INTO assessments (user_id, subject_id, title, assessment_type, assessment_date, weight, grade, status) VALUES
(@beatriz_id, @b_redes, 'Prova de Redes de Computadores', 'prova', DATE_ADD(CURDATE(), INTERVAL -16 DAY), 10, 9.0, 'corrigido'),
(@beatriz_id, @b_prog, 'Trabalho de Programação Web', 'trabalho', DATE_ADD(CURDATE(), INTERVAL -9 DAY), 8, 8.5, 'corrigido'),
(@beatriz_id, @b_estr, 'Prova de Estruturas de Dados', 'prova', DATE_ADD(CURDATE(), INTERVAL -4 DAY), 10, 9.5, 'corrigido'),
(@beatriz_id, @b_bd, 'Trabalho de modelagem de Banco de Dados', 'trabalho', DATE_ADD(CURDATE(), INTERVAL 12 DAY), 8, NULL, 'agendado'),
(@beatriz_id, @b_prog, 'Seminário de arquitetura MVC', 'seminario', DATE_ADD(CURDATE(), INTERVAL 26 DAY), 6, NULL, 'agendado'),
(@beatriz_id, @b_redes, 'Prova final de Redes', 'prova', DATE_ADD(CURDATE(), INTERVAL 47 DAY), 10, NULL, 'agendado'),
(@beatriz_id, @b_bd, 'Prova de Banco de Dados', 'prova', DATE_ADD(CURDATE(), INTERVAL 61 DAY), 10, NULL, 'agendado'),
(@beatriz_id, @b_estr, 'Projeto final de Estruturas de Dados', 'projeto', DATE_ADD(CURDATE(), INTERVAL 75 DAY), 12, NULL, 'agendado');

-- Anotações
INSERT INTO notes (user_id, subject_id, title, content, tags) VALUES
(@beatriz_id, @b_estr, 'Complexidade de pilhas e filas', 'Push/pop e enqueue/dequeue são O(1). Buscar um elemento é O(n).', 'estruturas,complexidade'),
(@beatriz_id, @b_redes, 'Camadas do modelo OSI', 'Física, enlace, rede, transporte, sessão, apresentação e aplicação — 7 camadas ao todo.', 'redes,osi'),
(@beatriz_id, @b_prog, 'Padrão MVC', 'Separar Model, View e Controller melhora manutenção e organização do código.', 'arquitetura,mvc'),
(@beatriz_id, @b_bd, 'Normalização', 'Formas normais existem para reduzir redundância: 1FN, 2FN, 3FN.', 'sql,modelagem'),
(@beatriz_id, @b_prog, 'Segurança básica', 'Sempre usar prepared statements e password_hash para evitar vulnerabilidades comuns.', 'seguranca'),
(@beatriz_id, @b_estr, 'Árvores AVL', 'Árvore binária de busca balanceada — rotações simples e duplas mantêm o balanceamento.', 'estruturas,arvores'),
(@beatriz_id, @b_redes, 'TCP vs UDP', 'TCP é orientado a conexão e confiável; UDP é mais rápido mas não garante entrega.', 'redes,protocolos'),
(@beatriz_id, @b_bd, 'Índices', 'Índices aceleram buscas, mas custam em escrita e espaço em disco.', 'sql,performance');

-- =====================================================================
-- PERFIL 2 — CARLOS DIFICULDADE (exemplo ruim)
-- =====================================================================

-- Matérias
INSERT INTO subjects (user_id, name, description, color, goal_description, start_date, end_date) VALUES
(@carlos_id, 'Cálculo I', 'Limites, derivadas e integrais', '#dc2626', 'Passar na disciplina', DATE_ADD(CURDATE(), INTERVAL -37 DAY), DATE_ADD(CURDATE(), INTERVAL 84 DAY));
SET @c_calc = LAST_INSERT_ID();

INSERT INTO subjects (user_id, name, description, color, goal_description, start_date, end_date) VALUES
(@carlos_id, 'Programação I', 'Lógica de programação e estruturas básicas', '#eab308', 'Entender laços e funções', DATE_ADD(CURDATE(), INTERVAL -37 DAY), DATE_ADD(CURDATE(), INTERVAL 84 DAY));
SET @c_prog = LAST_INSERT_ID();

INSERT INTO subjects (user_id, name, description, color, goal_description, start_date, end_date) VALUES
(@carlos_id, 'Física I', 'Mecânica clássica e cinemática', '#64748b', 'Acompanhar o conteúdo das aulas', DATE_ADD(CURDATE(), INTERVAL -37 DAY), DATE_ADD(CURDATE(), INTERVAL 84 DAY));
SET @c_fis = LAST_INSERT_ID();

-- Tarefas (semestre inteiro; baixa conclusão e várias atrasadas no período já decorrido)
INSERT INTO tasks (user_id, subject_id, title, priority, status, due_date, completed_at) VALUES
(@carlos_id, @c_calc, 'Resolver lista de derivadas', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL -37 DAY), NULL),
(@carlos_id, @c_calc, 'Revisar derivadas parciais', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL -32 DAY), NULL),
(@carlos_id, @c_calc, 'Praticar otimização', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL -26 DAY), NULL),
(@carlos_id, @c_calc, 'Fazer exercícios de revisão', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL -21 DAY), NULL),
(@carlos_id, @c_calc, 'Revisar regra da cadeia', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL -14 DAY), NULL),
(@carlos_id, @c_calc, 'Revisão final', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL -8 DAY), NULL),
(@carlos_id, @c_calc, 'Praticar mais exercícios', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL -3 DAY), NULL),
(@carlos_id, @c_calc, 'Estudar limites no infinito', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 2 DAY), NULL),
(@carlos_id, @c_calc, 'Preparar para a prova', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 9 DAY), NULL),
(@carlos_id, @c_calc, 'Estudar integrais indefinidas', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 15 DAY), NULL),
(@carlos_id, @c_calc, 'Estudar séries', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 21 DAY), NULL),
(@carlos_id, @c_calc, 'Revisar teorema fundamental do cálculo', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 28 DAY), NULL),
(@carlos_id, @c_calc, 'Estudar aplicações de derivadas', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 34 DAY), NULL),
(@carlos_id, @c_calc, 'Fazer lista de exercícios 4', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 40 DAY), NULL),
(@carlos_id, @c_calc, 'Estudar integrais definidas', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 47 DAY), NULL),
(@carlos_id, @c_calc, 'Estudar limites', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 54 DAY), NULL),
(@carlos_id, @c_calc, 'Revisão geral', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 59 DAY), NULL),
(@carlos_id, @c_calc, 'Resolver lista de derivadas', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 65 DAY), NULL),
(@carlos_id, @c_calc, 'Revisar derivadas parciais', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 72 DAY), NULL),
(@carlos_id, @c_calc, 'Praticar otimização', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 77 DAY), NULL),
(@carlos_id, @c_calc, 'Fazer exercícios de revisão', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 83 DAY), NULL),
(@carlos_id, @c_prog, 'Praticar exercícios de lógica', 'baixa', 'concluida', DATE_ADD(CURDATE(), INTERVAL -37 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '22:00:00')),
(@carlos_id, @c_prog, 'Estudar mais um pouco', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL -30 DAY), NULL),
(@carlos_id, @c_prog, 'Fazer lista de exercícios 5', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL -23 DAY), NULL),
(@carlos_id, @c_prog, 'Estudar structs', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL -17 DAY), NULL),
(@carlos_id, @c_prog, 'Estudar recursão básica', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL -12 DAY), NULL),
(@carlos_id, @c_prog, 'Praticar strings', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL -7 DAY), NULL),
(@carlos_id, @c_prog, 'Revisar algoritmos simples', 'baixa', 'pendente', CURDATE(), NULL),
(@carlos_id, @c_prog, 'Estudar entrada e saída', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 6 DAY), NULL),
(@carlos_id, @c_prog, 'Praticar depuração', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 11 DAY), NULL),
(@carlos_id, @c_prog, 'Praticar exercícios de funções', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 16 DAY), NULL),
(@carlos_id, @c_prog, 'Estudar laços de repetição', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 21 DAY), NULL),
(@carlos_id, @c_prog, 'Estudar ordenação básica', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 26 DAY), NULL),
(@carlos_id, @c_prog, 'Revisar vetores e matrizes', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 31 DAY), NULL),
(@carlos_id, @c_prog, 'Terminar lista de exercícios 3', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 36 DAY), NULL),
(@carlos_id, @c_prog, 'Revisar ponteiros', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 42 DAY), NULL),
(@carlos_id, @c_prog, 'Revisão para a prova', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 49 DAY), NULL),
(@carlos_id, @c_prog, 'Revisão geral', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 54 DAY), NULL),
(@carlos_id, @c_prog, 'Praticar exercícios de lógica', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 60 DAY), NULL),
(@carlos_id, @c_prog, 'Estudar mais um pouco', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 66 DAY), NULL),
(@carlos_id, @c_prog, 'Fazer lista de exercícios 5', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 73 DAY), NULL),
(@carlos_id, @c_prog, 'Estudar structs', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 80 DAY), NULL),
(@carlos_id, @c_fis, 'Revisão geral', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL -37 DAY), NULL),
(@carlos_id, @c_fis, 'Fazer exercícios de revisão', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL -32 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar mais', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL -27 DAY), NULL),
(@carlos_id, @c_fis, 'Revisar unidades de medida', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL -22 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar leis de Newton', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL -17 DAY), NULL),
(@carlos_id, @c_fis, 'Resolver exercícios de MRU', 'media', 'concluida', DATE_ADD(CURDATE(), INTERVAL -11 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -11 DAY), '22:00:00')),
(@carlos_id, @c_fis, 'Revisão para a prova', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL -4 DAY), NULL),
(@carlos_id, @c_fis, 'Praticar exercícios de força', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 1 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar gravitação', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 8 DAY), NULL),
(@carlos_id, @c_fis, 'Praticar problemas de física', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 13 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar quantidade de movimento', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 18 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar energia cinética', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 24 DAY), NULL),
(@carlos_id, @c_fis, 'Revisar trabalho e energia', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 30 DAY), NULL),
(@carlos_id, @c_fis, 'Revisão final', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 36 DAY), NULL),
(@carlos_id, @c_fis, 'Fazer lista de exercícios 2', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 43 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar movimento circular', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 49 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar cinemática', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 55 DAY), NULL),
(@carlos_id, @c_fis, 'Revisão geral', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 60 DAY), NULL),
(@carlos_id, @c_fis, 'Fazer exercícios de revisão', 'media', 'pendente', DATE_ADD(CURDATE(), INTERVAL 65 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar mais', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 71 DAY), NULL),
(@carlos_id, @c_fis, 'Revisar unidades de medida', 'baixa', 'pendente', DATE_ADD(CURDATE(), INTERVAL 77 DAY), NULL),
(@carlos_id, @c_fis, 'Estudar leis de Newton', 'alta', 'pendente', DATE_ADD(CURDATE(), INTERVAL 84 DAY), NULL);

-- Sessões de estudo (frequentes só nas primeiras semanas, praticamente somem depois)
INSERT INTO study_sessions (user_id, subject_id, duration_minutes, session_type, session_date, created_at) VALUES
(@carlos_id, @c_fis, 10, 'foco', DATE_ADD(CURDATE(), INTERVAL -37 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '22:30:00')),
(@carlos_id, @c_calc, 25, 'foco', DATE_ADD(CURDATE(), INTERVAL -34 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -34 DAY), '22:30:00')),
(@carlos_id, @c_calc, 20, 'foco', DATE_ADD(CURDATE(), INTERVAL -33 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -33 DAY), '22:30:00')),
(@carlos_id, @c_fis, 15, 'foco', DATE_ADD(CURDATE(), INTERVAL -30 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -30 DAY), '22:30:00')),
(@carlos_id, @c_fis, 10, 'foco', DATE_ADD(CURDATE(), INTERVAL -28 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -28 DAY), '22:30:00')),
(@carlos_id, @c_prog, 10, 'foco', DATE_ADD(CURDATE(), INTERVAL -26 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -26 DAY), '22:30:00')),
(@carlos_id, @c_fis, 20, 'foco', DATE_ADD(CURDATE(), INTERVAL -9 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -9 DAY), '22:30:00')),
(@carlos_id, @c_prog, 20, 'foco', DATE_ADD(CURDATE(), INTERVAL -5 DAY), TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -5 DAY), '22:30:00'));

-- Metas (progresso automático vai refletir a baixa atividade real)
INSERT INTO goals (user_id, subject_id, title, goal_type, target_value, current_value, deadline, is_completed, created_at) VALUES
(@carlos_id, NULL, 'Estudar 30 horas neste semestre', 'horas', 30, 0, DATE_ADD(CURDATE(), INTERVAL 84 DAY), 0, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '08:00:00')),
(@carlos_id, @c_calc, 'Concluir as tarefas de Cálculo I', 'tarefas', 6, 0, DATE_ADD(CURDATE(), INTERVAL 84 DAY), 0, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '08:00:00')),
(@carlos_id, @c_prog, 'Resolver 10 exercícios extras de Programação I', 'exercicios', 10, 1, DATE_ADD(CURDATE(), INTERVAL 38 DAY), 0, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -37 DAY), '08:00:00'));

-- Avaliações (notas baixas nas passadas; futuras agendadas sem preparo)
INSERT INTO assessments (user_id, subject_id, title, assessment_type, assessment_date, weight, grade, status) VALUES
(@carlos_id, @c_calc, 'Prova de Cálculo I', 'prova', DATE_ADD(CURDATE(), INTERVAL -18 DAY), 10, 4.0, 'corrigido'),
(@carlos_id, @c_prog, 'Trabalho de Programação I', 'trabalho', DATE_ADD(CURDATE(), INTERVAL -11 DAY), 8, 5.5, 'corrigido'),
(@carlos_id, @c_fis, 'Prova de Física I', 'prova', DATE_ADD(CURDATE(), INTERVAL -5 DAY), 10, 3.5, 'corrigido'),
(@carlos_id, @c_calc, 'Prova final de Cálculo I', 'prova', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 10, NULL, 'agendado'),
(@carlos_id, @c_prog, 'Trabalho final de Programação I', 'trabalho', DATE_ADD(CURDATE(), INTERVAL 33 DAY), 8, NULL, 'agendado'),
(@carlos_id, @c_fis, 'Prova final de Física I', 'prova', DATE_ADD(CURDATE(), INTERVAL 58 DAY), 10, NULL, 'agendado');

-- Anotações (poucas)
INSERT INTO notes (user_id, subject_id, title, content, tags) VALUES
(@carlos_id, @c_calc, 'Dúvida sobre limites', 'Preciso revisar limites, não entendi direito ainda.', 'duvida'),
(@carlos_id, @c_fis, 'Anotação da aula', 'Fórmulas de MRU e MRUV, preciso praticar mais exercícios.', 'fisica,duvida');

