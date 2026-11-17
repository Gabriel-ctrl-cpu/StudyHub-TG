# 📚 StudyHub

<h3 align="center">Organize seus estudos. Alcance seus objetivos.</h3>

<p align="center">
  Uma plataforma web para organização acadêmica, planejamento e acompanhamento da produtividade nos estudos.
</p>

---

## 📖 Sobre o projeto

O **StudyHub** é uma plataforma web de organização de estudos, desenvolvida como Trabalho de Conclusão de Curso (TCC) de **Análise e Desenvolvimento de Sistemas**.

O sistema foi projetado para ajudar estudantes a organizar sua rotina acadêmica em um único ambiente, permitindo gerenciar matérias, tarefas, compromissos, sessões de estudo, metas, anotações e avaliações.

Sua proposta é oferecer uma experiência individual, intuitiva e organizada, sem rankings, competição ou elementos de rede social.

**Filosofia do sistema:**

`Planejar → Estudar → Registrar → Acompanhar`

## 🎯 Objetivo

Oferecer uma ferramenta que permita ao estudante:

- Organizar sua rotina acadêmica.
- Gerenciar matérias, tarefas e compromissos.
- Registrar e acompanhar o tempo dedicado aos estudos.
- Estabelecer metas e monitorar seu progresso.
- Centralizar anotações e avaliações.
- Visualizar seu desempenho por meio de estatísticas e gráficos.

O foco é proporcionar uma visão clara da evolução individual, incentivando a organização e a constância nos estudos.

## 🛠️ Tecnologias utilizadas

**Front-end**
- HTML5
- CSS3
- JavaScript ES6+
- Bootstrap 5
- Font Awesome
- Chart.js
- FullCalendar

**Back-end**
- PHP 8.3+
- Arquitetura MVC sem framework
- PDO (PHP Data Objects)

**Banco de dados**
- MySQL 8+

**Ambiente de desenvolvimento**
- XAMPP
- Apache
- phpMyAdmin
- Git e GitHub

## ✨ Funcionalidades

### 📊 Dashboard
- Visão geral da rotina acadêmica.
- Cards com informações e indicadores.
- Progresso individual por matéria.
- Gráficos de horas estudadas.
- Próximas tarefas e avaliações.
- Resumo de metas e sessões recentes.
- Mini calendário.

### 📚 Gerenciamento de matérias
- Cadastro, edição e exclusão de matérias.
- Arquivamento de matérias.
- Acompanhamento automático do progresso.

### 📝 Gerenciamento de tarefas
- Cadastro e edição de tarefas.
- Associação com matérias.
- Filtros por período, status, prioridade e matéria.
- Pesquisa de tarefas.
- Conclusão rápida.
- Identificação automática de tarefas atrasadas e concluídas com atraso.

### 📅 Calendário acadêmico
- Visualização centralizada de tarefas, avaliações, sessões e compromissos.
- Integração com FullCalendar.
- Cadastro e gerenciamento de compromissos por modal e AJAX.

### ⏱️ Modo Foco
- Cronômetro baseado na técnica Pomodoro.
- Ciclos de 25 minutos de estudo e 5 minutos de descanso.
- Ciclos de 50 minutos de estudo e 10 minutos de descanso.
- Registro automático das sessões de estudo.

### 🎯 Metas
- Criação e gerenciamento de metas.
- Metas relacionadas a horas de estudo e tarefas concluídas com atualização automática.
- Metas personalizadas e de exercícios com atualização manual.
- Acompanhamento do progresso.

### 📒 Anotações
- Cadastro e gerenciamento de anotações.
- Pesquisa por texto e tags.
- Filtros por matéria.

### 📋 Avaliações
- Cadastro de provas, trabalhos, seminários e projetos.
- Registro de notas e pesos.
- Gerenciamento das avaliações acadêmicas.

### 📈 Estatísticas
- Horas estudadas hoje, na semana, no mês e no total.
- Acompanhamento de tarefas.
- Quatro gráficos desenvolvidos com Chart.js.
- Visualização da evolução da produtividade.

### 👤 Perfil e configurações
- Gerenciamento de dados pessoais.
- Upload de foto com validação de MIME.
- Alteração de senha.
- Tema claro e escuro.
- Preferências locais.
- Exclusão de conta.

### 🔔 Notificações
- Notificações geradas automaticamente pelo sistema.
- Alertas de metas concluídas.
- Avisos de tarefas atrasadas.
- Lembretes de avaliações próximas (até 3 dias).
- Contagem de notificações não lidas.

### 🔐 Segurança
- Autenticação e autorização por usuário.
- Proteção CSRF nos formulários.
- Senhas protegidas com `password_hash()` e `password_verify()`.
- Consultas SQL com PDO e Prepared Statements.
- Controle de sessões.
- Proteção de dados entre usuários.

## 🧱 Arquitetura do sistema

O StudyHub utiliza o padrão arquitetural **MVC (Model-View-Controller)**, implementado em PHP puro, sem utilização de frameworks no back-end.

A arquitetura separa as responsabilidades do sistema:

| Camada | Responsabilidade |
|---|---|
| Model | Comunicação com o banco de dados e regras relacionadas aos dados. |
| View | Apresentação das informações e interface do usuário. |
| Controller | Recebimento das requisições, validações e comunicação entre Models e Views. |
| Core | Componentes centrais, roteamento e funcionalidades compartilhadas. |

O sistema possui um roteador próprio, responsável por direcionar as requisições aos respectivos Controllers.

## 📁 Estrutura do projeto

```text
STUDYHUB/
│
├── app/
│   ├── controllers/
│   │   └── Controllers do sistema
│   │
│   ├── models/
│   │   └── Models e comunicação com o banco
│   │
│   ├── views/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── materias/
│   │   ├── tarefas/
│   │   └── layouts/
│   │
│   └── core/
│       ├── Router.php
│       ├── Controller.php
│       └── Model.php
│
├── config/
│   └── database.php
│
├── public/
│   ├── index.php
│   └── assets/
│
├── routes/
│   └── web.php
│
├── database/
│   ├── studyhub.sql
│   └── exemplos-alunos.sql
│
├── README.md
└── TESTES.md
```

## ⚙️ Instalação e configuração

### Requisitos

Antes de iniciar, certifique-se de possuir:

- XAMPP com Apache, PHP 8.3+ e MySQL 8+.
- Navegador atualizado.
- Git (opcional).

### 1. Clonar o repositório

```bash
git clone https://github.com/Gabriel-ctrl-cpu/StudyHub-TG.git
```

Ou baixe o projeto diretamente pelo GitHub.

### 2. Configurar a pasta do projeto

Copie a pasta `studyhub/` para o diretório `htdocs` do XAMPP.

**Windows:**
```text
C:\xampp\htdocs\studyhub
```

**Linux:**
```text
/opt/lampp/htdocs/studyhub
```

**macOS:**
```text
/Applications/XAMPP/htdocs/studyhub
```

### 3. Criar e importar o banco de dados

Abra o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Acesse a aba **Importar**, selecione o arquivo:

```text
database/studyhub.sql
```

Em seguida, execute a importação.

O script cria o banco `studyhub` e suas respectivas tabelas e dados de demonstração.

**Atenção:** o script principal contém `DROP DATABASE IF EXISTS studyhub`. Portanto, toda nova importação apaga o banco anterior e recria sua estrutura. Os dados cadastrados manualmente serão perdidos.

Também é possível importar pelo terminal:

```bash
mysql -u root -p < database/studyhub.sql
```

### 4. Configurar a conexão com o banco

Abra:

```text
config/database.php
```

Configure as credenciais conforme seu ambiente MySQL:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Confira também o endereço da aplicação:

```php
define('APP_URL', 'http://localhost/studyhub/public');
```

### 5. Iniciar o XAMPP

No painel de controle do XAMPP, inicie:

- Apache
- MySQL

### 6. Acessar o sistema

Abra o navegador e acesse:

```text
http://localhost/studyhub/public
```

## 👥 Usuários de demonstração

O banco de dados disponibiliza cinco usuários fictícios para testes.

**Senha padrão:** `senha123`

| Usuário | E-mail |
|---|---|
| Gabriel | gabriel@studyhub.com |
| Ana | ana@studyhub.com |
| Lucas | lucas@studyhub.com |
| Mariana | mariana@studyhub.com |
| Pedro | pedro@studyhub.com |

O usuário `gabriel@studyhub.com` possui uma massa de dados completa, incluindo matérias, tarefas, sessões, metas, anotações, eventos e avaliações.

### 📊 Dados comparativos de desempenho

O projeto também disponibiliza um script complementar para simular dois perfis acadêmicos diferentes.

Arquivo:

```text
database/exemplos-alunos.sql
```

O script adiciona dois usuários com dados referentes ao período de 01/08/2026 a 30/11/2026.

| Usuário | E-mail | Perfil |
|---|---|---|
| Beatriz | beatriz.organizada@studyhub.com | Bom desempenho |
| Carlos | carlos.dificuldade@studyhub.com | Desempenho em dificuldade |

**Beatriz — Perfil organizado**
- Aproximadamente 90% das tarefas concluídas.
- Sessões de estudo quase diárias.
- Aproximadamente 30 horas de estudo registradas no semestre até a data dos dados.
- Metas em dia.
- Boas notas.

**Carlos — Perfil em dificuldade**
- Baixa taxa de conclusão de tarefas.
- Diversas tarefas atrasadas.
- Poucas sessões de estudo, com queda acentuada após as duas primeiras semanas.
- Metas com pouco progresso.
- Notas baixas.

**Senha dos dois usuários:** `senha123`.

O script é aditivo e reimportável: remove e recria somente os dois usuários de exemplo, sem apagar os demais registros do banco.

Para utilizá-lo, abra a aba SQL do phpMyAdmin, cole o conteúdo do arquivo e execute.

## 🧪 Testes

O projeto possui um documento específico para orientar a validação de suas funcionalidades.

Consulte:

```text
TESTES.md
```

O roteiro contempla:

- Autenticação e cadastro.
- Controle de acesso.
- Gerenciamento de matérias e tarefas.
- Calendário e Modo Foco.
- Metas e anotações.
- Avaliações e estatísticas.
- Perfil e configurações.
- Validações de segurança.
- Responsividade.

### Teste rápido

1. Acesse a página inicial.
2. Crie uma conta ou entre com um usuário de demonstração.
3. Cadastre uma matéria.
4. Crie uma tarefa vinculada à matéria.
5. Inicie uma sessão no Modo Foco.
6. Configure uma meta de estudos.
7. Registre uma anotação.
8. Cadastre uma avaliação.
9. Consulte o Dashboard e as Estatísticas.

## 🔒 Detalhes de segurança

As principais medidas de segurança implementadas incluem:

| Recurso | Implementação |
|---|---|
| Proteção de senhas | `password_hash()` e `password_verify()` |
| Consultas SQL | PDO Prepared Statements |
| Proteção CSRF | Tokens nos formulários de autenticação |
| Gerenciamento de sessão | `session_regenerate_id()` no login e cadastro |
| Proteção contra XSS | `htmlspecialchars()` nas Views |
| Controle de acesso | Consultas vinculadas ao `user_id` da sessão |
| Upload de imagens | Validação real do tipo MIME |

## 📌 Status do projeto

**Status:** Funcionalmente completo conforme o roadmap original.

As funcionalidades principais foram implementadas e estão disponíveis para testes em ambiente local.

---

<p align="center">
  Desenvolvido como Trabalho de Conclusão de Curso
  <br>
  <strong>Análise e Desenvolvimento de Sistemas — FATEC Campinas</strong>
  <br><br>
  <i>StudyHub — Planejar. Estudar. Evoluir.</i>
</p>

---

<p align="center">
  <a href="https://github.com/Gabriel-ctrl-cpu">
    Gabriel Henrique Martins Gomes
  </a>
</p>
Desenvolvido como projeto de TCC — Análise e Desenvolvimento de Sistemas.
#   S t u d y H u b - T G 
 
 
