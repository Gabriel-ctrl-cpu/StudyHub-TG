# StudyHub > **Organize seus estudos. Alcance seus objetivos.** ## 📖 Descrição O **StudyHub** é uma plataforma web pessoal de organização de estudos, desenvolvida como **Trabalho de Conclusão de Curso (TCC) de Análise e Desenvolvimento de Sistemas**. Permite que o usuário organize matérias, tarefas, calendário, sessões de estudo (Modo Foco), metas, anotações e avaliações em um único ambiente — sem ranking, competição ou qualquer elemento de rede social. **Filosofia do sistema:** Planejar → Estudar → Registrar → Acompanhar ## 🎯 Objetivo Oferecer uma ferramenta individual e intuitiva para que o estudante consiga visualizar sua rotina acadêmica, medir seu tempo de estudo e acompanhar seu progresso real, sem distrações ou comparação com outros usuários. ## 🚀 Tecnologias **Front-end:** HTML5, CSS3, JavaScript ES6+, Bootstrap 5, Font Awesome, Chart.js, FullCalendar (etapa do calendário) **Back-end:** PHP 8.3+ (MVC sem framework), PDO **Banco de dados:** MySQL 8+ ## 📦 Requisitos - XAMPP (Apache + PHP 8.3+ + MySQL 8+) — ou ambiente equivalente - Navegador atualizado ## 🔧 Instalação (ambiente local com XAMPP) ### 1. Onde colocar a pasta Copie a pasta studyhub/ inteira para dentro de htdocs do XAMPP: - Windows: C:\xampp\htdocs\studyhub - Linux: /opt/lampp/htdocs/studyhub - macOS: /Applications/XAMPP/htdocs/studyhub ### 2. Criar o banco de dados Abra o **phpMyAdmin** (http://localhost/phpmyadmin) e crie um novo banco chamado studyhub, ou simplesmente importe o script SQL — ele já cria o banco automaticamente (CREATE DATABASE IF NOT EXISTS). ### 3. Importar o SQL No phpMyAdmin: aba **SQL** (não precisa criar o banco antes — o script já cria) → escolha o arquivo database/studyhub.sql na aba **Importar** → **Executar**. Ou via terminal: ```bash mysql -u root -p < database/studyhub.sql ``` > ⚠️ O script começa com DROP DATABASE IF EXISTS studyhub, ou seja, ele **apaga e recria o banco do zero** a cada importação. Isso é proposital — permite reimportar quantas vezes precisar sem erros de "tabela já existe" — mas significa que qualquer dado que você tiver cadastrado manualmente será perdido a cada reimportação. ### 4. Configurar a conexão Edite config/database.php caso suas credenciais do MySQL sejam diferentes do padrão do XAMPP (usuário root, sem senha): ```php define('DB_HOST', 'localhost'); define('DB_NAME', 'studyhub'); define('DB_USER', 'root'); define('DB_PASS', ''); ``` Ajuste também APP_URL se a pasta do projeto tiver outro nome: ```php define('APP_URL', 'http://localhost/studyhub/public'); ``` ### 5. Iniciar o Apache No painel de controle do XAMPP, clique em **Start** ao lado de **Apache**. ### 6. Iniciar o MySQL No painel de controle do XAMPP, clique em **Start** ao lado de **MySQL**. ### 7. Acessar pelo navegador Abra: **http://localhost/studyhub/public** ## 👤 Usuário de teste O banco já vem com 5 usuários fictícios (todos com a senha senha123): | E-mail | Senha | |------------------------|-----------| | gabriel@studyhub.com | senha123 | | ana@studyhub.com | senha123 | | lucas@studyhub.com | senha123 | | mariana@studyhub.com | senha123 | | pedro@studyhub.com | senha123 | O usuário **gabriel@studyhub.com** possui a massa de dados completa (matérias, tarefas, sessões, metas, anotações, eventos e avaliações) para testar o dashboard. ### Alunos de exemplo (bom desempenho x desempenho fraco) O arquivo database/exemplos-alunos.sql acrescenta dois usuários adicionais, com um **semestre inteiro** de dados (01/08/2026 a 30/11/2026), pensados para comparar visualmente as Estatísticas de um perfil organizado com um perfil em dificuldade: | E-mail | Senha | Perfil | |---|---|---| | beatriz.organizada@studyhub.com | senha123 | ~90% de tarefas concluídas, sessões de estudo quase diárias (~30h no semestre até hoje), metas em dia, boas notas | | carlos.dificuldade@studyhub.com | senha123 | Baixa conclusão de tarefas (várias atrasadas), poucas sessões de estudo (caem drasticamente após as 2 primeiras semanas), metas travadas, notas baixas | Esse script é **aditivo e reimportável**: pode ser executado depois do studyhub.sql a qualquer momento (aba SQL do phpMyAdmin, colar o conteúdo e executar) sem apagar o restante do banco — ele remove e recria apenas esses dois usuários a cada execução. ## 📁 Estrutura do projeto ``` STUDYHUB/ ├── app/ │ ├── controllers/ Controllers (AuthController, DashboardController, HomeController...) │ ├── models/ Models (User, Subject, Task, StudySession, Goal, Assessment...) │ ├── views/ Views organizadas por módulo (auth, dashboard, materias, tarefas...) │ │ └── layouts/ Layouts reutilizáveis (landing, auth, app) │ └── core/ Núcleo do framework MVC (Router, Controller, Model) ├── config/ │ └── database.php Configuração de conexão PDO ├── public/ Document root do Apache │ ├── index.php Front controller (ponto de entrada único) │ └── assets/ CSS, JS, imagens e ícones ├── routes/ │ └── web.php Definição de todas as rotas ├── database/ │ └── studyhub.sql Script completo do banco (estrutura + dados de demonstração) └── README.md ``` ## 🧱 Arquitetura MVC - **Model** (app/models): acesso ao banco via PDO com *prepared statements*, sem lógica de apresentação. - **View** (app/views): apenas HTML/PHP para exibição, sem SQL nem regras de negócio. - **Controller** (app/controllers): recebe requisições, valida entrada, chama Models e renderiza Views. - **Core** (app/core): Router (mapeia rotas para Controllers), Controller base (helpers de sessão, CSRF, flash messages, redirecionamento) e Model base (CRUD genérico). O roteamento é feito por um **Router** próprio (sem framework), definido em routes/web.php e despachado por public/index.php. ## ✅ Funcionalidades implementadas - Landing page pública - Cadastro, login, logout e recuperação de senha (com CSRF e password_hash) - Dashboard com dados reais: cards de resumo, progresso por matéria, horas estudadas (Chart.js), mini calendário, próximas tarefas/avaliações, metas, sessões recentes - **Matérias** — CRUD completo, arquivamento, progresso automático - **Tarefas** — CRUD completo, filtros (hoje/semana/pendentes/concluídas/alta prioridade/matéria/busca), conclusão rápida, status "Atrasada"/"Concluída com atraso" calculados automaticamente a partir do prazo - **Calendário** — FullCalendar agregando tarefas, avaliações, sessões e compromissos; CRUD de compromissos via modal/AJAX - **Modo Foco** — cronômetro Pomodoro (25/5 e 50/10) com registro automático de sessões - **Metas** — CRUD completo; metas de horas/tarefas atualizam o progresso automaticamente com base na atividade real (sessões de estudo e tarefas concluídas); exercícios/personalizada continuam manuais - **Anotações** — CRUD completo com pesquisa por texto/tag e filtro por matéria - **Avaliações** — CRUD completo (provas/trabalhos/seminários/projetos), peso e nota - **Estatísticas** — horas estudadas (hoje/semana/mês/total), tarefas, 4 gráficos Chart.js - **Perfil** — dados pessoais, upload de foto (validação real de MIME), alteração de senha - **Configurações** — tema claro/escuro persistente, preferências locais, exclusão de conta - **Notificações reais** — geradas automaticamente a partir de eventos do sistema (meta concluída, tarefa atrasada, avaliação chegando nos próximos 3 dias), exibidas no sino do header com contagem de não lidas - Responsividade completa (desktop, tablet, celular) - Segurança: PDO prepared statements, CSRF, password_hash/password_verify, autorização por usuário em toda consulta O sistema está **funcionalmente completo** conforme o roadmap original. Veja TESTES.md para o roteiro de testes manuais. ## 🧪 Testes Consulte **TESTES.md** para o roteiro completo de testes manuais (autenticação, controle de acesso, cada módulo do sistema e responsividade). ## 🔐 Segurança implementada - Senhas com password_hash() / password_verify() (bcrypt) - 100% das queries via PDO com *prepared statements* (sem concatenação de SQL) - Tokens CSRF em todos os formulários de autenticação - Sessões PHP regeneradas no login/cadastro (session_regenerate_id) - Saída sempre tratada com htmlspecialchars nas views - Autorização por usuário: todas as consultas filtram por user_id da sessão ## 🧪 Como testar (visão geral) 1. Acesse a landing page e clique em **Criar conta**. 2. Preencha o formulário de cadastro com um e-mail novo — deve redirecionar direto para o Dashboard. 3. Explore os módulos pelo menu lateral: crie uma matéria, depois uma tarefa vinculada a ela, inicie uma sessão no Modo Foco, defina uma meta, escreva uma anotação, cadastre uma avaliação e confira as Estatísticas. 4. Para o roteiro completo, com casos de borda de segurança (acesso sem login, dados de outro usuário, formulários inválidos etc.), veja **TESTES.md**. --- Desenvolvido como projeto de TCC — Análise e Desenvolvimento de Sistemas. # StudyHub-TG arruma pra mim
…
Vou organizar seu README do StudyHub para ficar com aparência de projeto profissional de portfólio, adequado para apresentar a recrutadores e também para a banca do TCC.

Vou manter as funcionalidades e tecnologias que você descreveu, mas melhorar:

Organização e hierarquia das seções.

Linguagem mais profissional e objetiva.

Formatação das tabelas e instruções.

Estrutura de instalação e configuração.

Destaque para arquitetura MVC, segurança e funcionalidades.

Remoção de informações repetidas.

A ideia é apresentar o StudyHub como um sistema completo, e não apenas como um trabalho acadêmico.

README.md — StudyHub
📚 StudyHub
<h3 align="center">Organize seus estudos. Alcance seus objetivos.</h3>

<p align="center">
  Uma plataforma web para organização acadêmica, planejamento e acompanhamento da produtividade nos estudos.
</p>

📖 Sobre o projeto
O StudyHub é uma plataforma web de organização de estudos, desenvolvida como Trabalho de Conclusão de Curso (TCC) de Análise e Desenvolvimento de Sistemas.

O sistema foi projetado para ajudar estudantes a organizar sua rotina acadêmica em um único ambiente, permitindo gerenciar matérias, tarefas, compromissos, sessões de estudo, metas, anotações e avaliações.

Sua proposta é oferecer uma experiência individual, intuitiva e organizada, sem rankings, competição ou elementos de rede social.

Filosofia do sistema:

Planejar → Estudar → Registrar → Acompanhar

🎯 Objetivo
Oferecer uma ferramenta que permita ao estudante:

Organizar sua rotina acadêmica.

Gerenciar matérias, tarefas e compromissos.

Registrar e acompanhar o tempo dedicado aos estudos.

Estabelecer metas e monitorar seu progresso.

Centralizar anotações e avaliações.

Visualizar seu desempenho por meio de estatísticas e gráficos.

O foco é proporcionar uma visão clara da evolução individual, incentivando a organização e a constância nos estudos.

🛠️ Tecnologias utilizadas
Front-end

HTML5

CSS3

JavaScript ES6+

Bootstrap 5

Font Awesome

Chart.js

FullCalendar

Back-end

PHP 8.3+

Arquitetura MVC sem framework

PDO (PHP Data Objects)

Banco de dados

MySQL 8+

Ambiente de desenvolvimento

XAMPP

Apache

phpMyAdmin

Git e GitHub

✨ Funcionalidades
📊 Dashboard
Visão geral da rotina acadêmica.

Cards com informações e indicadores.

Progresso individual por matéria.

Gráficos de horas estudadas.

Próximas tarefas e avaliações.

Resumo de metas e sessões recentes.

Mini calendário.

📚 Gerenciamento de matérias
Cadastro, edição e exclusão de matérias.

Arquivamento de matérias.

Acompanhamento automático do progresso.

📝 Gerenciamento de tarefas
Cadastro e edição de tarefas.

Associação com matérias.

Filtros por período, status, prioridade e matéria.

Pesquisa de tarefas.

Conclusão rápida.

Identificação automática de tarefas atrasadas e concluídas com atraso.

📅 Calendário acadêmico
Visualização centralizada de tarefas, avaliações, sessões e compromissos.

Integração com FullCalendar.

Cadastro e gerenciamento de compromissos por modal e AJAX.

⏱️ Modo Foco
Cronômetro baseado na técnica Pomodoro.

Ciclos de 25 minutos de estudo e 5 minutos de descanso.

Ciclos de 50 minutos de estudo e 10 minutos de descanso.

Registro automático das sessões de estudo.

🎯 Metas
Criação e gerenciamento de metas.

Metas relacionadas a horas de estudo e tarefas concluídas com atualização automática.

Metas personalizadas e de exercícios com atualização manual.

Acompanhamento do progresso.

📒 Anotações
Cadastro e gerenciamento de anotações.

Pesquisa por texto e tags.

Filtros por matéria.

📋 Avaliações
Cadastro de provas, trabalhos, seminários e projetos.

Registro de notas e pesos.

Gerenciamento das avaliações acadêmicas.

📈 Estatísticas
Horas estudadas hoje, na semana, no mês e no total.

Acompanhamento de tarefas.

Quatro gráficos desenvolvidos com Chart.js.

Visualização da evolução da produtividade.

👤 Perfil e configurações
Gerenciamento de dados pessoais.

Upload de foto com validação de MIME.

Alteração de senha.

Tema claro e escuro.

Preferências locais.

Exclusão de conta.

🔔 Notificações
Notificações geradas automaticamente pelo sistema.

Alertas de metas concluídas.

Avisos de tarefas atrasadas.

Lembretes de avaliações próximas (até 3 dias).

Contagem de notificações não lidas.

🔐 Segurança
Autenticação e autorização por usuário.

Proteção CSRF nos formulários.

Senhas protegidas com password_hash() e password_verify().

Consultas SQL com PDO e Prepared Statements.

Controle de sessões.

Proteção de dados entre usuários.

🧱 Arquitetura do sistema
O StudyHub utiliza o padrão arquitetural MVC (Model-View-Controller), implementado em PHP puro, sem utilização de frameworks no back-end.

A arquitetura separa as responsabilidades do sistema:

Camada	Responsabilidade
Model	Comunicação com o banco de dados e regras relacionadas aos dados.
View	Apresentação das informações e interface do usuário.
Controller	Recebimento das requisições, validações e comunicação entre Models e Views.
Core	Componentes centrais, roteamento e funcionalidades compartilhadas.
O sistema possui um roteador próprio, responsável por direcionar as requisições aos respectivos Controllers.

📁 Estrutura do projeto
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
⚙️ Instalação e configuração
Requisitos
Antes de iniciar, certifique-se de possuir:

XAMPP com Apache, PHP 8.3+ e MySQL 8+.

Navegador atualizado.

Git (opcional).

1. Clonar o repositório
git clone https://github.com/Gabriel-ctrl-cpu/StudyHub-TG.git
Ou baixe o projeto diretamente pelo GitHub.

2. Configurar a pasta do projeto
Copie a pasta studyhub/ para o diretório htdocs do XAMPP.

Windows:

C:\xampp\htdocs\studyhub
Linux:

/opt/lampp/htdocs/studyhub
macOS:

/Applications/XAMPP/htdocs/studyhub
3. Criar e importar o banco de dados
Abra o phpMyAdmin:

http://localhost/phpmyadmin
Acesse a aba Importar, selecione o arquivo:

database/studyhub.sql
Em seguida, execute a importação.

O script cria o banco studyhub e suas respectivas tabelas e dados de demonstração.

Atenção: o script principal contém DROP DATABASE IF EXISTS studyhub. Portanto, toda nova importação apaga o banco anterior e recria sua estrutura. Os dados cadastrados manualmente serão perdidos.

Também é possível importar pelo terminal:

mysql -u root -p < database/studyhub.sql
4. Configurar a conexão com o banco
Abra:

config/database.php
Configure as credenciais conforme seu ambiente MySQL:

define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub');
define('DB_USER', 'root');
define('DB_PASS', '');
Confira também o endereço da aplicação:

define('APP_URL', 'http://localhost/studyhub/public');
5. Iniciar o XAMPP
No painel de controle do XAMPP, inicie:

Apache

MySQL

6. Acessar o sistema
Abra o navegador e acesse:

http://localhost/studyhub/public
👥 Usuários de demonstração
O banco de dados disponibiliza cinco usuários fictícios para testes.

Senha padrão: senha123

Usuário	E-mail
Gabriel	gabriel@studyhub.com
Ana	ana@studyhub.com
Lucas	lucas@studyhub.com
Mariana	mariana@studyhub.com
Pedro	pedro@studyhub.com
O usuário gabriel@studyhub.com possui uma massa de dados completa, incluindo matérias, tarefas, sessões, metas, anotações, eventos e avaliações.

📊 Dados comparativos de desempenho
O projeto também disponibiliza um script complementar para simular dois perfis acadêmicos diferentes.

Arquivo:

database/exemplos-alunos.sql
O script adiciona dois usuários com dados referentes ao período de 01/08/2026 a 30/11/2026.

Usuário	E-mail	Perfil
Beatriz	beatriz.organizada@studyhub.com	Bom desempenho
Carlos	carlos.dificuldade@studyhub.com	Desempenho em dificuldade
Beatriz — Perfil organizado

Aproximadamente 90% das tarefas concluídas.

Sessões de estudo quase diárias.

Aproximadamente 30 horas de estudo registradas no semestre até a data dos dados.

Metas em dia.

Boas notas.

Carlos — Perfil em dificuldade

Baixa taxa de conclusão de tarefas.

Diversas tarefas atrasadas.

Poucas sessões de estudo, com queda acentuada após as duas primeiras semanas.

Metas com pouco progresso.

Notas baixas.

Senha dos dois usuários: senha123.

O script é aditivo e reimportável: remove e recria somente os dois usuários de exemplo, sem apagar os demais registros do banco.

Para utilizá-lo, abra a aba SQL do phpMyAdmin, cole o conteúdo do arquivo e execute.

🧪 Testes
O projeto possui um documento específico para orientar a validação de suas funcionalidades.

Consulte:

TESTES.md
O roteiro contempla:

Autenticação e cadastro.

Controle de acesso.

Gerenciamento de matérias e tarefas.

Calendário e Modo Foco.

Metas e anotações.

Avaliações e estatísticas.

Perfil e configurações.

Validações de segurança.

Responsividade.

Teste rápido
Acesse a página inicial.

Crie uma conta ou entre com um usuário de demonstração.

Cadastre uma matéria.

Crie uma tarefa vinculada à matéria.

Inicie uma sessão no Modo Foco.

Configure uma meta de estudos.

Registre uma anotação.

Cadastre uma avaliação.

Consulte o Dashboard e as Estatísticas.

🔒 Detalhes de segurança
As principais medidas de segurança implementadas incluem:

Recurso	Implementação
Proteção de senhas	password_hash() e password_verify()
Consultas SQL	PDO Prepared Statements
Proteção CSRF	Tokens nos formulários de autenticação
Gerenciamento de sessão	session_regenerate_id() no login e cadastro
Proteção contra XSS	htmlspecialchars() nas Views
Controle de acesso	Consultas vinculadas ao user_id da sessão
Upload de imagens	Validação real do tipo MIME
📌 Status do projeto
Status: Funcionalmente completo conforme o roadmap original.

As funcionalidades principais foram implementadas e estão disponíveis para testes em ambiente local.

<p align="center">
  Desenvolvido como Trabalho de Conclusão de Curso
  <br>
  <strong>Análise e Desenvolvimento de Sistemas — FATEC Campinas</strong>
  <br><br>
  <i>StudyHub — Planejar. Estudar. Evoluir.</i>
</p>

<p align="center">
  <a href="https://github.com/Gabriel-ctrl-cpu">
    Gabriel Henrique Martins Gomes
  </a>
</p>

Algumas recomendações antes de publicar
Confirme o nome do repositório: usei StudyHub-TG no comando de clonagem, conforme o nome que você colocou no final do texto.

Confira a pasta database: o README considera que os dois arquivos SQL estão presentes.

Inclua imagens do sistema: recomendo adicionar capturas do Dashboard, Modo Foco e Estatísticas. Isso valoriza muito o projeto no GitHub.

Cuidado com os usuários de demonstração: deixe claro que são contas fictícias e que as senhas são apenas para ambiente de teste.

Uma coisa que considero especialmente importante: o StudyHub tem bastante conteúdo técnico para demonstrar em uma entrevista de estágio, principalmente MVC próprio em PHP, PDO, segurança, gerenciamento de sessões e integração com MySQL. Eu daria bastante destaque a esses pontos no seu portfólio.

Anexo do usuário

Pensou por 11s

Agora entendi o que aconteceu. O problema não é o conteúdo do StudyHub, mas a formatação do arquivo README.md.

Na imagem, o GitHub está interpretando várias seções como um único parágrafo. Por isso, aparecem coisas como --- ## 📖 Sobre o projeto no meio do texto, em vez de títulos e divisórias.

Também vou corrigir um problema da minha versão anterior: alguns trechos de código tinham identificadores extras dentro dos blocos, que não deveriam fazer parte do README.

Vou deixar o arquivo em Markdown puro, sem HTML desnecessário, com títulos, listas, tabelas e blocos de código corretamente separados.

README corrigido e pronto para publicar

README_StudyHub.md

Markdown puro • UTF-8 • Estrutura organizada

Como colocar no GitHub
Baixe o arquivo acima.

Abra seu repositório StudyHub-TG.

Substitua o conteúdo do README.md pelo conteúdo do arquivo corrigido (ou renomeie o arquivo para README.md).

Clique em Commit changes.

Importante: mantive as informações técnicas que você forneceu, sem inventar funcionalidades novas. Depois disso, o GitHub deverá exibir os títulos, tabelas, listas e divisórias corretamente.

Você atingiu o limite de anexos por enquanto. Faça upgrade do seu plano para ter mais ou tente novamente amanhã após 09:28.




📚 StudyHub
Organize seus estudos. Alcance seus objetivos.

Uma plataforma web para organização acadêmica, planejamento e acompanhamento da produtividade nos estudos.

📖 Sobre o projeto
O StudyHub é uma plataforma web de organização de estudos, desenvolvida como Trabalho de Conclusão de Curso (TCC) de Análise e Desenvolvimento de Sistemas.

O sistema foi projetado para ajudar estudantes a organizar sua rotina acadêmica em um único ambiente, permitindo gerenciar matérias, tarefas, compromissos, sessões de estudo, metas, anotações e avaliações.

Sua proposta é oferecer uma experiência individual, intuitiva e organizada, sem rankings, competição ou elementos de rede social.

Filosofia do sistema:

Planejar → Estudar → Registrar → Acompanhar

🎯 Objetivo
Oferecer uma ferramenta que permita ao estudante:

Organizar sua rotina acadêmica.
Gerenciar matérias, tarefas e compromissos.
Registrar e acompanhar o tempo dedicado aos estudos.
Estabelecer metas e monitorar seu progresso.
Centralizar anotações e avaliações.
Visualizar seu desempenho por meio de estatísticas e gráficos.
O foco é proporcionar uma visão clara da evolução individual, incentivando a organização e a constância nos estudos.

🛠️ Tecnologias utilizadas
Front-end
HTML5
CSS3
JavaScript ES6+
Bootstrap 5
Font Awesome
Chart.js
FullCalendar
Back-end
PHP 8.3+
Arquitetura MVC sem framework
PDO (PHP Data Objects)
Banco de dados
MySQL 8+
Ambiente de desenvolvimento
XAMPP
Apache
phpMyAdmin
Git e GitHub
✨ Funcionalidades
📊 Dashboard
Visão geral da rotina acadêmica.
Cards com informações e indicadores.
Progresso individual por matéria.
Gráficos de horas estudadas.
Próximas tarefas e avaliações.
Resumo de metas e sessões recentes.
Mini calendário.
📚 Gerenciamento de matérias
Cadastro, edição e exclusão de matérias.
Arquivamento de matérias.
Acompanhamento automático do progresso.
📝 Gerenciamento de tarefas
Cadastro e edição de tarefas.
Associação com matérias.
Filtros por período, status, prioridade e matéria.
Pesquisa de tarefas.
Conclusão rápida.
Identificação automática de tarefas atrasadas e concluídas com atraso.
📅 Calendário acadêmico
Visualização centralizada de tarefas, avaliações, sessões e compromissos.
Integração com FullCalendar.
Cadastro e gerenciamento de compromissos por modal e AJAX.
⏱️ Modo Foco
Cronômetro baseado na técnica Pomodoro.
Ciclos de 25 minutos de estudo e 5 minutos de descanso.
Ciclos de 50 minutos de estudo e 10 minutos de descanso.
Registro automático das sessões de estudo.
🎯 Metas
Criação e gerenciamento de metas.
Metas relacionadas a horas de estudo e tarefas concluídas com atualização automática.
Metas personalizadas e de exercícios com atualização manual.
Acompanhamento do progresso.
📒 Anotações
Cadastro e gerenciamento de anotações.
Pesquisa por texto e tags.
Filtros por matéria.
📋 Avaliações
Cadastro de provas, trabalhos, seminários e projetos.
Registro de notas e pesos.
Gerenciamento das avaliações acadêmicas.
📈 Estatísticas
Horas estudadas hoje, na semana, no mês e no total.
Acompanhamento de tarefas.
Quatro gráficos desenvolvidos com Chart.js.
Visualização da evolução da produtividade.
👤 Perfil e configurações
Gerenciamento de dados pessoais.
Upload de foto com validação de MIME.
Alteração de senha.
Tema claro e escuro.
Preferências locais.
Exclusão de conta.
🔔 Notificações
Notificações geradas automaticamente pelo sistema.
Alertas de metas concluídas.
Avisos de tarefas atrasadas.
Lembretes de avaliações próximas (até 3 dias).
Contagem de notificações não lidas.
🧱 Arquitetura do sistema
O StudyHub utiliza o padrão arquitetural MVC (Model-View-Controller), implementado em PHP puro, sem utilização de frameworks no back-end.

A arquitetura separa as responsabilidades do sistema:

Camada	Responsabilidade
Model	Comunicação com o banco de dados e regras relacionadas aos dados.
View	Apresentação das informações e interface do usuário.
Controller	Recebimento das requisições, validações e comunicação entre Models e Views.
Core	Componentes centrais, roteamento e funcionalidades compartilhadas.
O sistema possui um roteador próprio, responsável por direcionar as requisições aos respectivos Controllers.

📁 Estrutura do projeto
STUDYHUB/
├── app/
│   ├── controllers/
│   │   └── Controllers do sistema
│   ├── models/
│   │   └── Models e comunicação com o banco
│   ├── views/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── materias/
│   │   ├── tarefas/
│   │   └── layouts/
│   └── core/
│       ├── Router.php
│       ├── Controller.php
│       └── Model.php
├── config/
│   └── database.php
├── public/
│   ├── index.php
│   └── assets/
├── routes/
│   └── web.php
├── database/
│   ├── studyhub.sql
│   └── exemplos-alunos.sql
├── README.md
└── TESTES.md
⚙️ Instalação e configuração
Requisitos
Antes de iniciar, certifique-se de possuir:

XAMPP com Apache, PHP 8.3+ e MySQL 8+.
Navegador atualizado.
Git (opcional).
1. Clonar o repositório
git clone https://github.com/Gabriel-ctrl-cpu/StudyHub-TG.git
Ou baixe o projeto diretamente pelo GitHub.

2. Configurar a pasta do projeto
Copie a pasta do projeto para o diretório htdocs do XAMPP.

Windows:

C:\xampp\htdocs\studyhub
Linux:

/opt/lampp/htdocs/studyhub
macOS:

/Applications/XAMPP/htdocs/studyhub
3. Criar e importar o banco de dados
Abra o phpMyAdmin:

http://localhost/phpmyadmin

Acesse a aba Importar, selecione o arquivo:

database/studyhub.sql

Em seguida, execute a importação.

O script cria o banco studyhub, suas tabelas e os dados de demonstração.

⚠️ Atenção: o script principal contém DROP DATABASE IF EXISTS studyhub. Portanto, cada nova importação apaga o banco anterior e recria sua estrutura. Os dados cadastrados manualmente serão perdidos.

Também é possível importar pelo terminal:

mysql -u root -p < database/studyhub.sql
4. Configurar a conexão com o banco
Abra o arquivo:

config/database.php

Configure as credenciais conforme seu ambiente MySQL:

define('DB_HOST', 'localhost');
define('DB_NAME', 'studyhub');
define('DB_USER', 'root');
define('DB_PASS', '');
Confira também o endereço da aplicação:

define('APP_URL', 'http://localhost/studyhub/public');
5. Iniciar o XAMPP
No painel de controle do XAMPP, inicie:

Apache
MySQL
6. Acessar o sistema
Abra o navegador e acesse:

http://localhost/studyhub/public

👥 Usuários de demonstração
O banco de dados disponibiliza cinco usuários fictícios para testes.

Senha padrão: senha123

Usuário	E-mail
Gabriel	gabriel@studyhub.com
Ana	ana@studyhub.com
Lucas	lucas@studyhub.com
Mariana	mariana@studyhub.com
Pedro	pedro@studyhub.com
O usuário gabriel@studyhub.com possui uma massa de dados completa, incluindo matérias, tarefas, sessões, metas, anotações, eventos e avaliações.

📊 Dados comparativos de desempenho
O projeto também disponibiliza um script complementar para simular dois perfis acadêmicos diferentes.

Arquivo: database/exemplos-alunos.sql

O script adiciona dois usuários com dados referentes ao período de 01/08/2026 a 30/11/2026.

Usuário	E-mail	Perfil
Beatriz	beatriz.organizada@studyhub.com	Bom desempenho
Carlos	carlos.dificuldade@studyhub.com	Desempenho em dificuldade
Beatriz — Perfil organizado

Aproximadamente 90% das tarefas concluídas.
Sessões de estudo quase diárias.
Aproximadamente 30 horas de estudo registradas no semestre até a data dos dados.
Metas em dia.
Boas notas.
Carlos — Perfil em dificuldade

Baixa taxa de conclusão de tarefas.
Diversas tarefas atrasadas.
Poucas sessões de estudo, com queda acentuada após as duas primeiras semanas.
Metas com pouco progresso.
Notas baixas.
Senha dos dois usuários: senha123.

O script é aditivo e reimportável: remove e recria somente os dois usuários de exemplo, sem apagar os demais registros do banco.

Para utilizá-lo, abra a aba SQL do phpMyAdmin, cole o conteúdo do arquivo e execute.

🧪 Testes
O projeto possui um documento específico para orientar a validação de suas funcionalidades.

Consulte o arquivo TESTES.md.

O roteiro contempla:

Autenticação e cadastro.
Controle de acesso.
Gerenciamento de matérias e tarefas.
Calendário e Modo Foco.
Metas e anotações.
Avaliações e estatísticas.
Perfil e configurações.
Validações de segurança.
Responsividade.
Teste rápido
Acesse a página inicial.
Crie uma conta ou entre com um usuário de demonstração.
Cadastre uma matéria.
Crie uma tarefa vinculada à matéria.
Inicie uma sessão no Modo Foco.
Configure uma meta de estudos.
Registre uma anotação.
Cadastre uma avaliação.
Consulte o Dashboard e as Estatísticas.
🔐 Segurança
As principais medidas de segurança implementadas incluem:

Recurso	Implementação
Proteção de senhas	password_hash() e password_verify()
Consultas SQL	PDO Prepared Statements
Proteção CSRF	Tokens nos formulários de autenticação
Gerenciamento de sessão	session_regenerate_id() no login e cadastro
Proteção contra XSS	htmlspecialchars() nas Views
Controle de acesso	Consultas vinculadas ao user_id da sessão
Upload de imagens	Validação real do tipo MIME
📌 Status do projeto
Status: Funcionalmente completo conforme o roadmap original.

As funcionalidades principais foram implementadas e estão disponíveis para testes em ambiente local.

<p align="center"> Desenvolvido como Trabalho de Conclusão de Curso <br> <strong>Análise e Desenvolvimento de Sistemas — FATEC Campinas</strong> <br><br> <em>StudyHub — Planejar. Estudar. Evoluir.</em> </p><p align="center"> <a href="https://github.com/Gabriel-ctrl-cpu"> Gabriel Henrique Martins Gomes </a> </p>
⋮⋮⋮⋮
