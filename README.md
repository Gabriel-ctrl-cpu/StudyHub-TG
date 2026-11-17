# StudyHub

> **Organize seus estudos. Alcance seus objetivos.**

## 📖 Descrição

O **StudyHub** é uma plataforma web pessoal de organização de estudos, desenvolvida como **Trabalho de Conclusão de Curso (TCC) de Análise e Desenvolvimento de Sistemas**. Permite que o usuário organize matérias, tarefas, calendário, sessões de estudo (Modo Foco), metas, anotações e avaliações em um único ambiente — sem ranking, competição ou qualquer elemento de rede social.

**Filosofia do sistema:** Planejar → Estudar → Registrar → Acompanhar

## 🎯 Objetivo

Oferecer uma ferramenta individual e intuitiva para que o estudante consiga visualizar sua rotina acadêmica, medir seu tempo de estudo e acompanhar seu progresso real, sem distrações ou comparação com outros usuários.

## 🚀 Tecnologias

**Front-end:** HTML5, CSS3, JavaScript ES6+, Bootstrap 5, Font Awesome, Chart.js, FullCalendar (etapa do calendário)
**Back-end:** PHP 8.3+ (MVC sem framework), PDO
**Banco de dados:** MySQL 8+

## 📦 Requisitos

- XAMPP (Apache + PHP 8.3+ + MySQL 8+) — ou ambiente equivalente
- Navegador atualizado

## 🔧 Instalação (ambiente local com XAMPP)

### 1. Onde colocar a pasta

Copie a pasta `studyhub/` inteira para dentro de `htdocs` do XAMPP:

- Windows: `C:\xampp\htdocs\studyhub`
- Linux: `/opt/lampp/htdocs/studyhub`
- macOS: `/Applications/XAMPP/htdocs/studyhub`

### 2. Criar o banco de dados

Abra o **phpMyAdmin** (`http://localhost/phpmyadmin`) e crie um novo banco chamado `studyhub`, ou simplesmente importe o script SQL — ele já cria o banco automaticamente (`CREATE DATABASE IF NOT EXISTS`).

### 3. Importar o SQL

No phpMyAdmin: aba **SQL** (não precisa criar o banco antes — o script já cria) → escolha o arquivo `database/studyhub.sql` na aba **Importar** → **Executar**.

Ou via terminal:

```bash
mysql -u root -p < database/studyhub.sql
```

> ⚠️ O script começa com `DROP DATABASE IF EXISTS studyhub`, ou seja, ele **apaga e recria o banco do zero** a cada importação. Isso é proposital — permite reimportar quantas vezes precisar sem erros de "tabela já existe" — mas significa que qualquer dado que você tiver cadastrado manualmente será perdido a cada reimportação.

### 4. Configurar a conexão

Edite `config/database.php` caso suas credenciais do MySQL sejam diferentes do padrão do XAMPP (usuário `root`, sem senha):

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Ajuste também `APP_URL` se a pasta do projeto tiver outro nome:

```php
define('APP_URL', 'http://localhost/studyhub/public');
```

### 5. Iniciar o Apache

No painel de controle do XAMPP, clique em **Start** ao lado de **Apache**.

### 6. Iniciar o MySQL

No painel de controle do XAMPP, clique em **Start** ao lado de **MySQL**.

### 7. Acessar pelo navegador

Abra: **http://localhost/studyhub/public**

## 👤 Usuário de teste

O banco já vem com 5 usuários fictícios (todos com a senha `senha123`):

| E-mail                | Senha      |
|------------------------|-----------|
| gabriel@studyhub.com   | senha123  |
| ana@studyhub.com       | senha123  |
| lucas@studyhub.com     | senha123  |
| mariana@studyhub.com   | senha123  |
| pedro@studyhub.com     | senha123  |

O usuário **gabriel@studyhub.com** possui a massa de dados completa (matérias, tarefas, sessões, metas, anotações, eventos e avaliações) para testar o dashboard.

### Alunos de exemplo (bom desempenho x desempenho fraco)

O arquivo `database/exemplos-alunos.sql` acrescenta dois usuários adicionais, com um **semestre inteiro** de dados (01/08/2026 a 30/11/2026), pensados para comparar visualmente as Estatísticas de um perfil organizado com um perfil em dificuldade:

| E-mail | Senha | Perfil |
|---|---|---|
| beatriz.organizada@studyhub.com | senha123 | ~90% de tarefas concluídas, sessões de estudo quase diárias (~30h no semestre até hoje), metas em dia, boas notas |
| carlos.dificuldade@studyhub.com | senha123 | Baixa conclusão de tarefas (várias atrasadas), poucas sessões de estudo (caem drasticamente após as 2 primeiras semanas), metas travadas, notas baixas |

Esse script é **aditivo e reimportável**: pode ser executado depois do `studyhub.sql` a qualquer momento (aba SQL do phpMyAdmin, colar o conteúdo e executar) sem apagar o restante do banco — ele remove e recria apenas esses dois usuários a cada execução.

## 📁 Estrutura do projeto

```
STUDYHUB/
├── app/
│   ├── controllers/       Controllers (AuthController, DashboardController, HomeController...)
│   ├── models/             Models (User, Subject, Task, StudySession, Goal, Assessment...)
│   ├── views/               Views organizadas por módulo (auth, dashboard, materias, tarefas...)
│   │   └── layouts/          Layouts reutilizáveis (landing, auth, app)
│   └── core/                 Núcleo do framework MVC (Router, Controller, Model)
├── config/
│   └── database.php         Configuração de conexão PDO
├── public/                  Document root do Apache
│   ├── index.php             Front controller (ponto de entrada único)
│   └── assets/                CSS, JS, imagens e ícones
├── routes/
│   └── web.php               Definição de todas as rotas
├── database/
│   └── studyhub.sql          Script completo do banco (estrutura + dados de demonstração)
└── README.md
```

## 🧱 Arquitetura MVC

- **Model** (`app/models`): acesso ao banco via PDO com *prepared statements*, sem lógica de apresentação.
- **View** (`app/views`): apenas HTML/PHP para exibição, sem SQL nem regras de negócio.
- **Controller** (`app/controllers`): recebe requisições, valida entrada, chama Models e renderiza Views.
- **Core** (`app/core`): `Router` (mapeia rotas para Controllers), `Controller` base (helpers de sessão, CSRF, flash messages, redirecionamento) e `Model` base (CRUD genérico).

O roteamento é feito por um **Router** próprio (sem framework), definido em `routes/web.php` e despachado por `public/index.php`.

## ✅ Funcionalidades implementadas

- Landing page pública
- Cadastro, login, logout e recuperação de senha (com CSRF e `password_hash`)
- Dashboard com dados reais: cards de resumo, progresso por matéria, horas estudadas (Chart.js), mini calendário, próximas tarefas/avaliações, metas, sessões recentes
- **Matérias** — CRUD completo, arquivamento, progresso automático
- **Tarefas** — CRUD completo, filtros (hoje/semana/pendentes/concluídas/alta prioridade/matéria/busca), conclusão rápida, status "Atrasada"/"Concluída com atraso" calculados automaticamente a partir do prazo
- **Calendário** — FullCalendar agregando tarefas, avaliações, sessões e compromissos; CRUD de compromissos via modal/AJAX
- **Modo Foco** — cronômetro Pomodoro (25/5 e 50/10) com registro automático de sessões
- **Metas** — CRUD completo; metas de horas/tarefas atualizam o progresso automaticamente com base na atividade real (sessões de estudo e tarefas concluídas); exercícios/personalizada continuam manuais
- **Anotações** — CRUD completo com pesquisa por texto/tag e filtro por matéria
- **Avaliações** — CRUD completo (provas/trabalhos/seminários/projetos), peso e nota
- **Estatísticas** — horas estudadas (hoje/semana/mês/total), tarefas, 4 gráficos Chart.js
- **Perfil** — dados pessoais, upload de foto (validação real de MIME), alteração de senha
- **Configurações** — tema claro/escuro persistente, preferências locais, exclusão de conta
- **Notificações reais** — geradas automaticamente a partir de eventos do sistema (meta concluída, tarefa atrasada, avaliação chegando nos próximos 3 dias), exibidas no sino do header com contagem de não lidas
- Responsividade completa (desktop, tablet, celular)
- Segurança: PDO prepared statements, CSRF, `password_hash`/`password_verify`, autorização por usuário em toda consulta

O sistema está **funcionalmente completo** conforme o roadmap original. Veja `TESTES.md` para o roteiro de testes manuais.

## 🧪 Testes 

Consulte **`TESTES.md`** para o roteiro completo de testes manuais (autenticação, controle de acesso, cada módulo do sistema e responsividade).

## 🔐 Segurança implementada

- Senhas com `password_hash()` / `password_verify()` (bcrypt)
- 100% das queries via PDO com *prepared statements* (sem concatenação de SQL)
- Tokens CSRF em todos os formulários de autenticação
- Sessões PHP regeneradas no login/cadastro (`session_regenerate_id`)
- Saída sempre tratada com `htmlspecialchars` nas views
- Autorização por usuário: todas as consultas filtram por `user_id` da sessão

## 🧪 Como testar (visão geral)

1. Acesse a landing page e clique em **Criar conta**.
2. Preencha o formulário de cadastro com um e-mail novo — deve redirecionar direto para o Dashboard.
3. Explore os módulos pelo menu lateral: crie uma matéria, depois uma tarefa vinculada a ela, inicie uma sessão no Modo Foco, defina uma meta, escreva uma anotação, cadastre uma avaliação e confira as Estatísticas.
4. Para o roteiro completo, com casos de borda de segurança (acesso sem login, dados de outro usuário, formulários inválidos etc.), veja **`TESTES.md`**.

---

Desenvolvido como projeto de TCC — Análise e Desenvolvimento de Sistemas.
#   S t u d y H u b - T G 
 
 
