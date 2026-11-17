# StudyHub — Documentação Técnica Detalhada

> Documentação de apoio ao TCC (Análise e Desenvolvimento de Sistemas). Explica a arquitetura do sistema e o funcionamento de cada bloco de código, módulo por módulo.

---

## Sumário

1. [Visão geral](#1-visão-geral)
2. [Arquitetura MVC](#2-arquitetura-mvc)
3. [Estrutura de pastas](#3-estrutura-de-pastas)
4. [Banco de dados](#4-banco-de-dados)
5. [Núcleo do framework (`app/core`)](#5-núcleo-do-framework-appcore)
6. [Configuração (`config/database.php`)](#6-configuração-configdatabasephp)
7. [Front controller (`public/index.php`)](#7-front-controller-publicindexphp)
8. [Rotas (`routes/web.php`)](#8-rotas-routeswebphp)
9. [Módulo: Autenticação](#9-módulo-autenticação)
10. [Módulo: Dashboard](#10-módulo-dashboard)
11. [Módulo: Matérias](#11-módulo-matérias)
12. [Módulo: Tarefas](#12-módulo-tarefas)
13. [Módulo: Calendário](#13-módulo-calendário)
14. [Módulo: Modo Foco](#14-módulo-modo-foco)
15. [Módulo: Metas](#15-módulo-metas)
16. [Módulo: Anotações](#16-módulo-anotações)
17. [Módulo: Avaliações](#17-módulo-avaliações)
18. [Módulo: Estatísticas](#18-módulo-estatísticas)
19. [Módulo: Perfil](#19-módulo-perfil)
20. [Módulo: Configurações](#20-módulo-configurações)
21. [Front-end: CSS e JavaScript](#21-front-end-css-e-javascript)
22. [Segurança — resumo consolidado](#22-segurança--resumo-consolidado)
23. [Sugestões para a documentação acadêmica (TCC)](#23-sugestões-para-a-documentação-acadêmica-tcc)

---

## 1. Visão geral

O StudyHub é uma aplicação web que permite a um estudante organizar toda a sua rotina acadêmica: matérias, tarefas, calendário, sessões de estudo (Modo Foco), metas, anotações e avaliações. O sistema é **individual** — não existe nenhuma forma de ranking, comparação ou interação entre usuários. Toda consulta ao banco de dados é filtrada pelo `user_id` da sessão autenticada.

**Pilha tecnológica:**

| Camada | Tecnologia |
|---|---|
| Front-end | HTML5, CSS3, JavaScript ES6+ (vanilla, sem frameworks) |
| Componentes visuais | Bootstrap 5, Font Awesome |
| Gráficos | Chart.js |
| Calendário | FullCalendar 6 |
| Back-end | PHP 8.3+, sem framework, PDO |
| Banco de dados | MySQL 8+ |

**Por que sem framework?** A escolha de não usar Laravel (ou similar) foi proposital: o objetivo é que cada camada do MVC seja escrita à mão e totalmente compreensível, facilitando a explicação da arquitetura na banca do TCC.

---

## 2. Arquitetura MVC

O sistema segue o padrão **Model-View-Controller**, implementado manualmente (sem framework):

```
Requisição HTTP
      │
      ▼
public/index.php  (front controller)
      │
      ▼
app/core/Router.php  (decide qual Controller/método chamar)
      │
      ▼
app/controllers/*.php  (recebe a requisição, valida, chama o Model)
      │
      ▼
app/models/*.php  (executa a query no banco via PDO)
      │
      ▼
app/controllers/*.php  (recebe os dados e manda renderizar a View)
      │
      ▼
app/views/*.php  (gera o HTML de resposta, dentro de um layout)
      │
      ▼
Resposta HTML (ou JSON, em endpoints AJAX) enviada ao navegador
```

**Responsabilidade de cada camada:**

- **Model** — a única camada que "conversa" com o banco de dados. Todo acesso é feito via PDO com *prepared statements*. Nunca contém HTML.
- **View** — a única camada que gera HTML. Recebe dados já prontos do Controller (por meio de `extract()`) e apenas os exibe. Não faz consultas SQL nem contém regras de negócio complexas.
- **Controller** — a "cola" entre as duas camadas acima: interpreta a requisição (`$_GET`/`$_POST`), valida dados, decide o que fazer, chama o Model, e manda a View correta ser renderizada.

---

## 3. Estrutura de pastas

```
studyhub/
├── app/
│   ├── controllers/   → 13 controllers (um por módulo)
│   ├── models/         → 8 models (um por tabela principal)
│   ├── views/            → HTML/PHP das telas, organizadas por módulo
│   │   └── layouts/       → 3 "moldes" reutilizáveis (landing, auth, app)
│   └── core/               → Router, Controller base e Model base
├── config/
│   └── database.php        → conexão PDO e configurações gerais
├── public/                  → document root do Apache (única pasta exposta)
│   ├── index.php             → front controller
│   └── assets/                 → CSS, JS, imagens, ícones
├── routes/
│   └── web.php                 → mapa de todas as rotas do sistema
├── database/
│   └── studyhub.sql             → schema completo + dados de demonstração
├── README.md
└── TESTES.md
```

Só a pasta `public/` fica acessível diretamente pelo navegador (é o "document root" configurado no Apache/XAMPP). Todo o resto (`app/`, `config/`, `routes/`, `database/`) fica fora do alcance direto de uma URL — isso é uma prática de segurança: mesmo que alguém tente acessar `http://localhost/studyhub/config/database.php` diretamente, o Apache não vai encontrar esse caminho dentro do document root.

---

## 4. Banco de dados

O arquivo `database/studyhub.sql` cria o banco `studyhub` e 10 tabelas. Abaixo, a explicação de cada uma:

### 4.1. `users`
Armazena os dados de cadastro: nome, e-mail (único), senha (**hash**, nunca texto puro), curso, instituição, avatar, tema preferido (`light`/`dark`) e meta diária de minutos. O campo `remember_token` é usado tanto para o "lembrar de mim" do login quanto para o fluxo de recuperação de senha.

### 4.2. `subjects` (matérias)
Cada matéria pertence a um `user_id` (chave estrangeira com `ON DELETE CASCADE` — se o usuário for excluído, suas matérias somem junto). Guarda nome, descrição, cor (usada em quase toda a interface para identificar visualmente a matéria), objetivo, datas de início/conclusão e uma flag `is_archived`.

### 4.3. `tasks` (tarefas)
Vinculada a um usuário e, opcionalmente, a uma matéria (`ON DELETE SET NULL` — se a matéria for excluída, a tarefa não desaparece, só perde o vínculo). Campos de destaque: `priority` (enum alta/media/baixa), `status` (enum pendente/em_andamento/concluida/cancelada), `due_date`/`due_time` (prazo) e `completed_at` (preenchido automaticamente ao concluir).

### 4.4. `study_sessions` (sessões de estudo)
Registra cada sessão do Modo Foco (ou lançada manualmente): duração em minutos, tipo (`foco`/`manual`), data e horários de início/fim. É a tabela-base para quase todos os gráficos de Estatísticas.

### 4.5. `goals` (metas)
`target_value` (valor alvo) e `current_value` (progresso atual) são `DECIMAL`, permitindo metas fracionárias (ex: 7,5 horas). `is_completed` é recalculado automaticamente sempre que o progresso é atualizado.

### 4.6. `notes` (anotações)
Tem um índice `FULLTEXT` em `(title, content)` — usado para a pesquisa full-text, embora a implementação atual da busca use `LIKE` por simplicidade e compatibilidade; o índice fica disponível para uma evolução futura com `MATCH() AGAINST()`.

### 4.7. `events` (compromissos)
Eventos de calendário criados manualmente pelo usuário (reuniões, lembretes). Diferente de tarefas/avaliações, que também aparecem no calendário mas vêm de suas próprias tabelas.

### 4.8. `assessments` (avaliações)
Diferente das outras, tem `subject_id` como `NOT NULL` — uma avaliação **sempre** pertence a uma matéria (regra de negócio validada também no Controller). Guarda tipo, data/horário, peso, nota e status.

### 4.9. `notifications`
Estrutura pronta para um sistema de notificações (título, mensagem, tipo, lida/não lida). A tabela existe e tem dados de demonstração, mas a interface de notificações no header é hoje estática — fica registrado aqui como ponto de evolução natural do projeto.

### 4.10. `sessions`
Tabela de apoio para, futuramente, migrar as sessões PHP do sistema de arquivos para o banco de dados (útil em ambientes com múltiplos servidores). Não é usada pelo `session_start()` padrão do PHP nesta versão — é uma preparação de infraestrutura.

### Relacionamentos

```
users (1) ──< subjects (N)
users (1) ──< tasks (N)  ────> subjects (N) [opcional]
users (1) ──< study_sessions (N) ────> subjects (N) [opcional]
users (1) ──< goals (N) ────> subjects (N) [opcional]
users (1) ──< notes (N) ────> subjects (N) [opcional]
users (1) ──< events (N) ────> subjects (N) [opcional]
users (1) ──< assessments (N) ────> subjects (1) [obrigatório]
users (1) ──< notifications (N)
```

Todas as chaves estrangeiras para `users` usam `ON DELETE CASCADE` (exclui os dados junto com a conta). As chaves estrangeiras para `subjects` (exceto em `assessments`) usam `ON DELETE SET NULL` (o registro permanece, só perde o vínculo com a matéria excluída).

---

## 5. Núcleo do framework (`app/core`)

Esta é a parte mais "framework-like" do projeto — três classes pequenas que resolvem, de forma explícita, o que frameworks maiores escondem em muita "mágica".

### 5.1. `Router.php`

```php
public function get(string $path, string $action): void
{
    $this->add('GET', $path, $action);
}
```
`get()` e `post()` são apenas atalhos que chamam `add()`, guardando cada rota como um array `['method' => ..., 'path' => ..., 'action' => ...]` na propriedade `$routes`.

```php
public function dispatch(string $uri, string $method): void
{
    $uri = trim(parse_url($uri, PHP_URL_PATH), '/');
    // remove o prefixo da subpasta, se o projeto estiver em /studyhub/public
    ...
    foreach ($this->routes as $route) {
        if ($route['method'] !== $method) continue;
        $pattern = $this->buildPattern($route['path']);
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);
            $this->callAction($route['action'], array_values($matches));
            return;
        }
    }
    http_response_code(404);
    ...
}
```
`dispatch()` é chamado uma única vez, pelo `public/index.php`, a cada requisição. Ele:
1. Extrai o caminho da URL (sem domínio nem query string).
2. Remove o prefixo da subpasta (para funcionar tanto em `localhost/studyhub/public` quanto em outra configuração).
3. Percorre todas as rotas cadastradas procurando uma que combine com o método HTTP e o caminho.
4. Se encontrar, chama o Controller/método correspondente, passando os parâmetros capturados (como o `{id}` de `materias/{id}/editar`).
5. Se nenhuma rota bater, responde 404.

```php
private function buildPattern(string $path): string
{
    $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $path);
    return '#^' . $pattern . '$#';
}
```
Essa função converte rotas como `materias/{id}/editar` em uma expressão regular (`#^materias/([^/]+)/editar$#`), permitindo capturar o valor do `{id}` como grupo de captura.

### 5.2. `Controller.php`

Classe base da qual **todos** os 13 controllers herdam. Fornece funcionalidades comuns para não repetir código:

```php
protected function render(string $view, array $data = [], string $layout = 'app'): void
{
    extract($data);
    $viewFile = __DIR__ . '/../views/' . $view . '.php';
    ...
    $content = function () use ($viewFile, $data) {
        extract($data);
        require $viewFile;
    };
    $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';
    if (file_exists($layoutFile)) {
        require $layoutFile;
    } else {
        $content();
    }
}
```
Esse é o método mais importante da base. `render('materias/index', $dados, 'app')` faz três coisas:
1. Transforma as chaves do array `$dados` em variáveis PHP (`extract`) — por isso, dentro da view, dá para usar `$subjects` diretamente em vez de `$dados['subjects']`.
2. Cria uma *closure* (`$content`) que, quando chamada, inclui a view desejada.
3. Inclui o **layout** (o "molde" com sidebar/header, por exemplo), que por sua vez chama `$content()` no ponto exato onde o conteúdo da página deve aparecer.

Esse padrão (layout com "buraco" preenchido por uma closure) é o que permite reaproveitar a mesma sidebar/header em todas as telas autenticadas sem copiar e colar HTML.

```php
protected function requireAuth(): array
{
    if (empty($_SESSION['user_id'])) {
        $this->redirect('login');
    }
    return ['id' => $_SESSION['user_id'], 'name' => $_SESSION['user_name'] ?? ''];
}
```
Chamado no início de **todo** método de controller que exige login. Se não houver `user_id` na sessão, redireciona para `/login` e interrompe a execução (o `redirect()` chama `exit`).

```php
protected function csrfToken(): string { ... }
protected function verifyCsrf(): void { ... }
```
Implementam a proteção CSRF: `csrfToken()` gera (ou reaproveita) um token aleatório guardado na sessão; `verifyCsrf()` compara o token enviado no formulário com o da sessão usando `hash_equals()` (comparação seguro contra *timing attacks*). Todo método que recebe POST chama `verifyCsrf()` antes de processar qualquer dado.

```php
protected function flash(string $key, ?string $message = null) { ... }
```
Implementa *flash messages*: mensagens de sucesso/erro que sobrevivem a **um único redirecionamento**. Se chamado com `$message`, grava na sessão; se chamado sem, lê e **apaga** da sessão (por isso a mensagem só aparece uma vez, mesmo se a página for recarregada depois).

### 5.3. `Model.php`

Classe base abstrata da qual todos os 8 models herdam. Fornece um mini-ORM com operações CRUD genéricas:

```php
public function create(array $data): int
{
    $columns = array_keys($data);
    $placeholders = array_map(fn($c) => ':' . $c, $columns);
    $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table, implode(', ', $columns), implode(', ', $placeholders));
    $stmt = $this->db->prepare($sql);
    $stmt->execute($data);
    return (int) $this->db->lastInsertId();
}
```
`create()` monta dinamicamente um `INSERT` a partir das chaves do array recebido — por exemplo, `create(['name' => 'JavaScript', 'user_id' => 1])` gera `INSERT INTO subjects (name, user_id) VALUES (:name, :user_id)`. Como o array é passado inteiro para `execute()`, o PDO faz o *binding* de cada parâmetro automaticamente. **Nunca há concatenação de valores no SQL** — é sempre placeholder + binding.

O mesmo padrão se repete em `update()` (monta `UPDATE ... SET campo = :campo, ...`) e `find()`/`where()` (montam `SELECT`). Os models filhos (como `Task`, `Subject`) estendem essa base com métodos específicos — por exemplo, `Task::filtered()` monta uma query mais elaborada com `JOIN` e filtros dinâmicos, mas sempre reaproveitando `$this->query()` (que também usa *prepared statements*).

> **Por que isso importa para a segurança:** como toda entrada de dado do usuário vira um parâmetro *bindado* (nunca uma string concatenada), fica estruturalmente impossível fazer SQL Injection através dos formulários do sistema.

---

## 6. Configuração (`config/database.php`)

```php
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
```
A função `db()` usa uma variável `static` para garantir que **apenas uma conexão PDO** seja criada por requisição, não importa quantas vezes `db()` seja chamada (padrão *singleton*). `PDO::ATTR_EMULATE_PREPARES => false` é importante: força o uso de *prepared statements* nativos do MySQL (não emulados pelo PHP), o que é mais seguro e também é o motivo pelo qual, em algumas queries com subconsultas, foi necessário usar nomes de parâmetro diferentes mesmo repetindo o mesmo valor (o MySQL nativo não aceita reutilizar o mesmo placeholder nomeado duas vezes na mesma query).

Esse arquivo também define constantes como `APP_URL` (usada em todas as views para montar links absolutos) e `APP_ENV` (controla se erros PHP aparecem na tela — só em desenvolvimento).

---

## 7. Front controller (`public/index.php`)

```php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Model.php';

$router = new Router();
require __DIR__ . '/../routes/web.php';
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
```
Este é o **único ponto de entrada** de toda a aplicação (por isso "front controller"). Toda requisição HTTP passa por aqui graças ao `.htaccess` (explicado a seguir). O arquivo: inicia a sessão PHP, carrega as classes essenciais, cria o Router, registra todas as rotas (executando `routes/web.php`) e finalmente despacha a requisição atual.

**`public/.htaccess`:**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```
Essas regras dizem ao Apache: "se a URL solicitada não corresponder a um arquivo ou pasta real dentro de `public/`, encaminhe tudo para `index.php`". É isso que permite ter URLs "bonitas" como `/materias/nova` em vez de `/index.php?page=materias&action=nova`.

---

## 8. Rotas (`routes/web.php`)

Arquivo puramente declarativo — apenas chamadas a `$router->get()` e `$router->post()`, uma por linha, agrupadas por módulo com comentários. Ele é `require`ido de dentro do `index.php`, então a variável `$router` já está disponível nesse escopo. Exemplo do padrão repetido em cada módulo (usando Matérias):

```php
$router->get('materias', 'SubjectController@index');          // listar
$router->get('materias/nova', 'SubjectController@createForm'); // formulário de criação
$router->post('materias', 'SubjectController@store');           // salvar nova
$router->get('materias/{id}/editar', 'SubjectController@editForm'); // formulário de edição
$router->post('materias/{id}', 'SubjectController@update');      // salvar edição
$router->post('materias/{id}/excluir', 'SubjectController@destroy'); // excluir
$router->post('materias/{id}/arquivar', 'SubjectController@archive'); // arquivar/restaurar
```
Esse padrão de 7 rotas (listar / form de criar / salvar criar / form de editar / salvar editar / excluir / ação extra) se repete, com pequenas variações, em Tarefas, Metas, Anotações e Avaliações — é o "esqueleto" de CRUD usado no projeto inteiro.

---

## 9. Módulo: Autenticação

**Arquivos:** `AuthController.php` · `models/User.php` · `views/auth/{login,register,forgot}.php` · `views/layouts/auth.php`

### 9.1. Cadastro (`register()`)

```php
$errors = [];
if ($name === '' || mb_strlen($name) < 3) { $errors[] = 'Informe seu nome completo.'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Informe um e-mail válido.'; }
if (mb_strlen($password) < 6) { $errors[] = 'A senha deve ter pelo menos 6 caracteres.'; }
if ($password !== $confirm) { $errors[] = 'As senhas não coincidem.'; }
if (empty($errors) && $this->userModel->emailExists($email)) { $errors[] = 'Este e-mail já está cadastrado.'; }
```
Cada regra de validação é checada e, se falhar, adiciona uma mensagem ao array `$errors`. A checagem de e-mail duplicado só roda **se as validações anteriores passarem** (`empty($errors) &&`), evitando uma consulta ao banco desnecessária quando o formulário já está claramente inválido.

```php
$userId = $this->userModel->register($name, $email, $password, $course, $institution);
session_regenerate_id(true);
$_SESSION['user_id']   = $userId;
$_SESSION['user_name'] = $name;
```
Após criar o usuário, `session_regenerate_id(true)` troca o ID da sessão (mantendo os dados) — prática de segurança que impede *session fixation* (um atacante não pode "forçar" um ID de sessão conhecido antes do login). Só depois disso a sessão recebe `user_id`, efetivando o login automático pós-cadastro.

No **Model** (`User.php`), o cadastro em si:
```php
public function register(string $name, string $email, string $password, ?string $course, ?string $institution): int
{
    return $this->create([
        'name' => $name, 'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'course' => $course, 'institution' => $institution,
    ]);
}
```
`password_hash()` com o algoritmo `PASSWORD_BCRYPT` transforma a senha em um hash irreversível antes de qualquer gravação no banco — em nenhum momento a senha em texto puro chega a ser armazenada.

### 9.2. Login (`login()`)

```php
$user = $this->userModel->findByEmail($email);
if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
    $this->flash('error', 'E-mail ou senha incorretos.');
    $this->redirect('login');
}
```
Note que a mensagem de erro é **idêntica** tanto para "e-mail não existe" quanto para "senha errada". Isso é proposital: se as mensagens fossem diferentes, um atacante poderia usar o formulário de login para descobrir quais e-mails estão cadastrados no sistema (*user enumeration*).

```php
if ($remember) {
    $token = bin2hex(random_bytes(32));
    $this->userModel->update($user['id'], ['remember_token' => $token]);
    setcookie('studyhub_remember', $token, time() + (86400 * 30), '/', '', false, true);
}
```
O "lembrar de mim" gera um token aleatório de 32 bytes (64 caracteres hexadecimais), salva no banco e cria um cookie válido por 30 dias. O último parâmetro `true` do `setcookie()` marca o cookie como `HttpOnly` — isso impede que ele seja lido por JavaScript (mitigando roubo de token via XSS).

### 9.3. Recuperação de senha (`forgot()`)

```php
if ($user) {
    $token = bin2hex(random_bytes(32));
    $this->userModel->update($user['id'], ['remember_token' => $token]);
}
$this->flash('message', 'Se o e-mail informado estiver cadastrado, você receberá instruções...');
```
Mesmo princípio da mensagem genérica do login: a resposta ao usuário é **idêntica** independentemente de o e-mail existir ou não no banco, para não revelar quais contas existem. O envio real do e-mail (com link contendo o token) fica marcado como próximo passo — a infraestrutura (geração e armazenamento do token) já está pronta.

### 9.4. Logout (`logout()`)

```php
setcookie('studyhub_remember', '', time() - 3600, '/');
$_SESSION = [];
session_destroy();
$this->redirect('login');
```
Apaga o cookie de "lembrar de mim" (definindo uma expiração no passado), esvazia todas as variáveis de sessão e destrói a sessão no servidor — um logout completo, não apenas um redirecionamento.

---

## 10. Módulo: Dashboard

**Arquivos:** `DashboardController.php` · `views/dashboard/index.php` · `assets/js/dashboard.js`

O `DashboardController::index()` não tem lógica de negócio própria — sua função é **agregar dados de vários models** em um único array para a view:

```php
$minutesToday   = $sessionModel->minutesToday($userId);
$subjectsCount  = count($subjectModel->forUser($userId));
$pendingTasks   = $taskModel->countByStatus($userId, 'pendente') + $taskModel->countByStatus($userId, 'em_andamento');
$subjectsProgress = $subjectModel->progressByUser($userId);
$weeklyMinutes     = $sessionModel->minutesByDay($userId, 7);
$upcomingTasks     = $taskModel->upcoming($userId, 4);
$upcomingAssessments = $assessmentModel->upcoming($userId, 4);
```
Cada uma dessas chamadas é uma consulta independente e otimizada (nenhuma retorna mais dados do que a tela precisa — por exemplo, `upcoming($userId, 4)` já limita a 4 registros direto no SQL, em vez de buscar tudo e cortar em PHP).

Um destaque é `Subject::progressByUser()`, que calcula o progresso de cada matéria em uma única query com `JOIN` e subquery:
```sql
SELECT s.id, s.name, s.color,
       COUNT(DISTINCT t.id) AS total_tasks,
       COUNT(DISTINCT CASE WHEN t.status = 'concluida' THEN t.id END) AS done_tasks,
       COALESCE(SUM(DISTINCT ss.minutes_total), 0) AS minutes_total
FROM subjects s
LEFT JOIN tasks t ON t.subject_id = s.id AND t.user_id = s.user_id
LEFT JOIN (SELECT subject_id, SUM(duration_minutes) AS minutes_total FROM study_sessions WHERE user_id = :user_id_sessions GROUP BY subject_id) ss ON ss.subject_id = s.id
WHERE s.user_id = :user_id_subjects AND s.is_archived = 0
GROUP BY s.id, s.name, s.color
```
A subquery (`ss`) soma os minutos estudados por matéria **antes** do JOIN principal — isso evita o problema clássico de "multiplicar" a soma de minutos quando uma matéria tem várias tarefas e várias sessões ao mesmo tempo (por isso também o `SUM(DISTINCT ...)` na consulta externa, como camada extra de proteção contra duplicação).

No lado do **JavaScript** (`dashboard.js`), os dados chegam prontos via uma variável global injetada pela própria view:
```php
<script>
const dashboardData = {
    subjects: <?= json_encode(array_map(fn($s) => [...], $subjectsProgress)) ?>,
    weekly: <?= json_encode($weeklyMinutes) ?>
};
</script>
```
O PHP serializa os dados em JSON diretamente no HTML gerado — não há uma segunda requisição AJAX só para buscar os dados dos gráficos. O `dashboard.js` então usa esses dados para montar o gráfico de rosca (progresso por matéria) e o de barras (horas por dia) com Chart.js:
```js
const found = dashboardData.weekly.find((w) => w.session_date === iso);
totals.push(found ? Math.round((parseInt(found.total_minutes, 10) / 60) * 100) / 100 : 0);
```
Esse trecho percorre os últimos 7 dias (gerados em JS) e, para cada um, procura se existe uma entrada correspondente nos dados vindos do PHP — dias sem sessão de estudo aparecem como `0` no gráfico, em vez de simplesmente não aparecerem.

---

## 11. Módulo: Matérias

**Arquivos:** `SubjectController.php` · `models/Subject.php` · `views/materias/{index,form}.php`

### 11.1. Padrão de autorização (`belongsToUser`)

```php
public function belongsToUser(int $id, int $userId): bool
{
    $row = $this->queryOne('SELECT id FROM subjects WHERE id = :id AND user_id = :user_id', [
        'id' => $id, 'user_id' => $userId,
    ]);
    return $row !== null;
}
```
Esse método (repetido, com pequenas variações, em praticamente todos os models) é a base da autorização do sistema: antes de editar, excluir ou arquivar qualquer registro, o Controller sempre chama `belongsToUser()` primeiro. Isso é o que impede um usuário autenticado de manipular a URL (trocando o `{id}`) para editar dados de outra pessoa — mesmo estando logado, ele só consegue agir sobre registros cujo `user_id` bate com o da própria sessão.

```php
public function editForm(string $id): void
{
    $user = $this->requireAuth();
    $userId = (int) $user['id'];
    $subjectId = (int) $id;
    if (!$this->subjectModel->belongsToUser($subjectId, $userId)) {
        $this->flash('error', 'Matéria não encontrada.');
        $this->redirect('materias');
    }
    ...
}
```
Note que a mensagem de erro é "não encontrada", não "você não tem permissão" — outra decisão deliberada para não revelar a um possível atacante que aquele ID *existe*, só pertence a outra pessoa.

### 11.2. Arquivar/restaurar (`archive()`)

```php
$current = $this->subjectModel->find($subjectId);
$newStatus = (int) $current['is_archived'] === 1 ? 0 : 1;
$this->subjectModel->update($subjectId, ['is_archived' => $newStatus]);
```
Um único método trata os dois sentidos da ação: lê o estado atual e inverte. A view usa a mesma rota (`POST materias/{id}/arquivar`) tanto para o botão "Arquivar" quanto para "Restaurar", mudando apenas o texto exibido.

### 11.3. Validação compartilhada

```php
private function validatedData(): ?array
{
    ...
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) { $color = '#2563eb'; }
    if ($startDate && $endDate && $endDate < $startDate) {
        $errors[] = 'A data prevista de conclusão não pode ser anterior à data de início.';
    }
    if (!empty($errors)) { $this->flash('error', implode(' ', $errors)); return null; }
    return [...];
}
```
Esse método é chamado tanto por `store()` (criar) quanto por `update()` (editar) — evita duplicar as mesmas regras de validação em dois lugares. Retorna `null` em caso de erro (e já grava a flash message), ou o array de dados prontos para salvar em caso de sucesso. Esse padrão de "método `validatedData()` privado, compartilhado entre criar/editar" se repete em Tarefas, Metas, Anotações e Avaliações.

---

## 12. Módulo: Tarefas

**Arquivos:** `TaskController.php` · `models/Task.php` · `views/tarefas/{index,form}.php` · `assets/js/confirm-delete.js`

### 12.1. Filtros dinâmicos (`Task::filtered()`)

```php
$conditions = ['t.user_id = :user_id'];
$params = ['user_id' => $userId];

if ($period === 'hoje') { $conditions[] = 't.due_date = CURDATE()'; }
elseif ($period === 'semana') { $conditions[] = 't.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)'; }

if (!empty($filters['status'])) { $conditions[] = 't.status = :status'; $params['status'] = $filters['status']; }
if (!empty($filters['priority'])) { $conditions[] = 't.priority = :priority'; $params['priority'] = $filters['priority']; }
...
$sql = "SELECT t.*, s.name AS subject_name, s.color AS subject_color FROM tasks t LEFT JOIN subjects s ON s.id = t.subject_id
        WHERE " . implode(' AND ', $conditions) . " ORDER BY FIELD(t.status, 'em_andamento', 'pendente', 'concluida', 'cancelada'), ...";
```
Esse método monta a cláusula `WHERE` **dinamicamente**: começa sempre com o filtro obrigatório de `user_id` e vai empilhando condições extras conforme os filtros ativos (período, status, prioridade, matéria, busca por texto). Isso evita ter uma query fixa gigante com múltiplos `OR`/`IS NULL` — só entra na query o que o usuário realmente filtrou. O `ORDER BY FIELD(...)` é um recurso do MySQL que permite ordenar por uma lista customizada de valores (tarefas "em andamento" aparecem primeiro, depois "pendentes", etc.), em vez da ordem alfabética padrão.

### 12.2. Concluir com um clique (`toggleComplete()`)

```php
$task = $this->taskModel->find($taskId);
$isCompleted = $task['status'] === 'concluida';
$this->taskModel->update($taskId, [
    'status'       => $isCompleted ? 'pendente' : 'concluida',
    'completed_at' => $isCompleted ? null : date('Y-m-d H:i:s'),
]);
```
O checkbox na listagem não abre um formulário — ele envia um POST direto para essa rota. O método alterna entre concluída/pendente e ajusta o campo `completed_at` de acordo (importante: esse campo é a base do gráfico "Tarefas concluídas por dia" nas Estatísticas, então ele precisa ser limpo — não só o status — ao reabrir uma tarefa).

### 12.3. Confirmação de exclusão reutilizável (`confirm-delete.js`)

```js
document.querySelectorAll('.form-confirm-delete').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.getAttribute('data-confirm') || 'Tem certeza que deseja excluir este item?';
        if (!window.confirm(message)) { event.preventDefault(); }
    });
});
```
Um único arquivo JS, carregado em todos os módulos com exclusão (Matérias, Tarefas, Metas, Anotações, Avaliações). Qualquer formulário com a classe `form-confirm-delete` ganha automaticamente uma confirmação nativa do navegador antes do envio — o texto vem do atributo `data-confirm`, definido individualmente em cada view (ex: `Excluir a tarefa "Estudar JS"?`).

---

## 13. Módulo: Calendário

**Arquivos:** `CalendarController.php` · `models/Event.php` · `views/calendario/index.php` · `assets/js/calendario.js`

### 13.1. Feed agregado (`data()`)

O calendário não é "dono" da maior parte do que exibe — ele **agrega** dados de quatro tabelas diferentes em um único formato (o formato de evento que o FullCalendar entende):

```php
foreach ((new Task())->withDueDate($userId) as $t) {
    $start = $t['due_date'] . ($t['due_time'] ? 'T' . $t['due_time'] : '');
    $items[] = [
        'id' => 'task-' . $t['id'], 'title' => '📝 ' . $t['title'], 'start' => $start,
        'allDay' => empty($t['due_time']), 'color' => $t['subject_color'] ?? '#2563eb',
        'extendedProps' => ['type' => 'tarefa', 'status' => $t['status']],
    ];
}
```
O mesmo padrão se repete para avaliações, sessões de estudo e eventos — cada bloco lê de um model diferente e converte para o mesmo formato `{id, title, start, allDay, color, extendedProps}`. O prefixo no `id` (`task-`, `assessment-`, `session-`, `event-`) é o que permite ao JavaScript, no clique de um evento, saber de qual "tipo" ele é e decidir se deve ser editável ou não. Ao final, `$this->json($items)` devolve tudo como um array JSON — é essa URL (`/calendario/eventos`) que o FullCalendar consome via `events:` na sua configuração.

### 13.2. Por que só "compromissos" são editáveis no calendário

```js
eventClick(info) {
    const props = info.event.extendedProps;
    if (props.type !== 'evento') {
        return; // Somente eventos/compromissos criados pelo usuário são editáveis aqui.
    }
    ...
}
```
Tarefas, avaliações e sessões de estudo já têm suas próprias telas de CRUD completo (com mais campos do que caberia em um modal simples). Editá-las pelo calendário duplicaria a lógica de validação em dois lugares — por isso, o clique só abre o modal de edição quando `type === 'evento'` (ou seja, quando o item foi criado como compromisso avulso).

### 13.3. CRUD via Fetch API (sem recarregar a página)

```js
eventForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('event_id').value;
    const url = id ? `${window.APP_URL}/calendario/eventos/${id}` : `${window.APP_URL}/calendario/eventos`;
    const formData = new FormData(eventForm);
    const response = await fetch(url, { method: 'POST', body: formData });
    const result = await response.json();
    if (result.success) { eventModal.hide(); calendar.refetchEvents(); }
    ...
});
```
Se o campo oculto `event_id` estiver preenchido, a requisição vai para a rota de **update**; se estiver vazio, vai para a de **criação** — o mesmo formulário serve para os dois casos. Ao receber `{success: true}`, o modal fecha e `calendar.refetchEvents()` manda o FullCalendar buscar o feed atualizado, sem precisar recarregar a página inteira.

---

## 14. Módulo: Modo Foco

**Arquivos:** `FocusController.php` · `views/foco/index.php` · `assets/js/foco.js`

### 14.1. Backend: registrar a sessão (`store()`)

```php
$subjectModel = new Subject();
if (!$subjectModel->belongsToUser((int) $subjectId, (int) $user['id'])) {
    $this->json(['success' => false, 'message' => 'Matéria inválida.'], 422);
}
$sessionModel->create([
    'user_id' => (int) $user['id'], 'subject_id' => (int) $subjectId,
    'duration_minutes' => $duration, 'session_type' => 'foco',
    'session_date' => date('Y-m-d'), 'started_at' => $_POST['started_at'] ?? date('Y-m-d H:i:s'),
    'finished_at' => date('Y-m-d H:i:s'),
]);
```
Esse endpoint é chamado via `fetch()` pelo JavaScript do cronômetro, não pelo envio de um formulário tradicional — por isso responde em JSON. Mesmo sendo uma chamada AJAX "interna" (disparada pelo próprio cronômetro, não por um clique direto do usuário em "salvar"), a matéria informada ainda passa por `belongsToUser()`: o `subject_id` chega via POST, então, tecnicamente, poderia ser adulterado antes de chegar ao servidor — a validação no backend nunca deve confiar apenas em que o frontend "só manda valores válidos".

### 14.2. Frontend: o cronômetro (`foco.js`)

O núcleo do cronômetro é um `setInterval` de 1 segundo que decrementa `remainingSeconds`:
```js
function tick() {
    remainingSeconds--;
    if (remainingSeconds <= 0) { handlePhaseComplete(); return; }
    render();
}
```
Quando chega a zero, `handlePhaseComplete()` decide o que fazer dependendo da fase atual:
```js
if (phase === 'foco') {
    registerSession(focusMinutes, 'Você estudou por ' + focusMinutes + ' minutos. Hora da pausa!');
    phase = 'pausa';
    totalSeconds = breakMinutes * 60;
} else {
    phase = 'foco';
    totalSeconds = focusMinutes * 60;
}
```
Só o fim de um ciclo de **foco** dispara `registerSession()` — o fim da **pausa** apenas volta para a fase de foco, sem gerar registro no banco. Isso implementa a regra "pausas não contam como tempo estudado".

O anel de progresso é um círculo SVG animado via CSS (`stroke-dashoffset`):
```js
const elapsedRatio = 1 - remainingSeconds / totalSeconds;
ringProgress.style.strokeDashoffset = `${CIRCUMFERENCE * (1 - elapsedRatio)}`;
```
`CIRCUMFERENCE` é a circunferência do círculo (`2 * π * raio`), pré-calculada uma vez. Conforme o tempo passa, `elapsedRatio` vai de 0 a 1, e o `stroke-dashoffset` (a "parte visível" do traço do círculo) diminui — visualmente, o anel colorido "cresce" ao redor do círculo cinza de fundo. A transição suave vem de uma regra CSS (`transition: stroke-dashoffset 1s linear`), não de JavaScript.

### 14.3. Botão "Finalizar" (encerrar antes do tempo acabar)

```js
finishBtn.addEventListener('click', () => {
    if (phase !== 'foco') return;
    stopInterval();
    const elapsedMinutes = Math.max(1, Math.round((totalSeconds - remainingSeconds) / 60));
    registerSession(elapsedMinutes, 'Sessão encerrada. Você estudou por ' + elapsedMinutes + ' minutos.');
    resetTimer();
});
```
Diferente do fim natural do ciclo (que sempre registra a duração completa, ex: 25 minutos), finalizar manualmente registra apenas o tempo **efetivamente decorrido** até aquele momento (`totalSeconds - remainingSeconds`), arredondado para o minuto mais próximo, com um mínimo de 1 minuto (para não gerar sessões de "0 minutos" se alguém clicar em finalizar logo após iniciar).

---

## 15. Módulo: Metas

**Arquivos:** `GoalController.php` · `models/Goal.php` · `views/metas/{index,form}.php`

### 15.1. Progresso rápido (`updateProgress()`)

```php
$increment = (float) ($_POST['increment'] ?? 1);
$newValue = max(0, (float) $goal['current_value'] + $increment);
$isCompleted = $newValue >= (float) $goal['target_value'] ? 1 : 0;
$this->goalModel->update($goalId, ['current_value' => $newValue, 'is_completed' => $isCompleted]);
```
O botão "+1 progresso" na listagem envia um POST simples (não é AJAX — é um formulário HTML comum que recarrega a página). `max(0, ...)` impede que o progresso fique negativo. A conclusão (`is_completed`) é recalculada a **cada** atualização de progresso, então nunca fica dessincronizada do valor real — não existe um cenário em que `current_value >= target_value` mas `is_completed` continue em `0`.

### 15.2. Cálculo de percentual na view

```php
$pct = $g['target_value'] > 0 ? min(100, round(($g['current_value'] / $g['target_value']) * 100)) : 0;
```
O `min(100, ...)` trava a barra de progresso visual em 100% mesmo se `current_value` ultrapassar `target_value` (ex: meta de "resolver 30 exercícios", usuário resolveu 35) — evita uma barra de progresso "vazando" para fora do card.

---

## 16. Módulo: Anotações

**Arquivos:** `NoteController.php` · `models/Note.php` · `views/anotacoes/{index,form}.php`

### 16.1. Busca (`Note::search()`)

```php
if ($term !== '') {
    $conditions[] = '(n.title LIKE :term OR n.content LIKE :term OR n.tags LIKE :term)';
    $params['term'] = '%' . $term . '%';
}
if ($subjectId) { $conditions[] = 'n.subject_id = :subject_id'; $params['subject_id'] = $subjectId; }
```
A pesquisa usa `LIKE '%termo%'` simultaneamente em três colunas (título, conteúdo, tags), combinadas com `OR` dentro de um único grupo de parênteses — assim, o filtro de matéria (se houver) se aplica com `AND` sobre o resultado da busca textual, não interfere com a lógica interna do `OR`.

### 16.2. Normalização de tags

```php
if ($tags !== null) {
    $tags = implode(',', array_filter(array_map('trim', explode(',', $tags))));
}
```
O usuário digita algo como `"javascript,  funcoes ,  "` (com espaços inconsistentes e uma vírgula sobrando no fim). Esse trecho: separa por vírgula (`explode`), remove espaços de cada item (`array_map('trim', ...)`), descarta itens vazios (`array_filter`) e junta tudo de volta com vírgula simples (`implode`) — resultando em `"javascript,funcoes"`, sempre no mesmo formato, o que facilita tanto a exibição quanto uma futura busca exata por tag.

---

## 17. Módulo: Avaliações

**Arquivos:** `AssessmentController.php` · `models/Assessment.php` · `views/avaliacoes/{index,form}.php`

### 17.1. Regra de negócio: avaliação exige matéria

```php
public function createForm(): void
{
    $subjects = $this->subjectModel->forUser((int) $user['id']);
    if (empty($subjects)) {
        $this->flash('error', 'Cadastre uma matéria antes de criar uma avaliação.');
        $this->redirect('avaliacoes');
    }
    ...
}
```
Diferente de tarefas, metas, anotações e eventos (onde a matéria é opcional), uma avaliação **sempre** pertence a uma matéria — reflexo direto da coluna `subject_id NOT NULL` na tabela `assessments`. Em vez de deixar o usuário abrir um formulário com um `<select>` vazio (e falhar só depois de tentar salvar), o Controller já intercepta antes de exibir o formulário e redireciona com uma mensagem explicativa.

### 17.2. Peso e nota opcionais

```php
$weight = trim($_POST['weight'] ?? '') !== '' ? (float) $_POST['weight'] : null;
$grade = trim($_POST['grade'] ?? '') !== '' ? (float) $_POST['grade'] : null;
```
Em vez de simplesmente `(float) $_POST['weight']` (que transformaria um campo vazio em `0`, um valor "falso" — pois `0` é diferente de "ainda não informado"), o código checa explicitamente se o campo foi preenchido antes de converter. Isso permite que a nota fique como `NULL` no banco até a correção acontecer, e a interface exibe um traço (`—`) em vez de `0,0` para avaliações ainda não corrigidas.

---

## 18. Módulo: Estatísticas

**Arquivos:** `StatsController.php` · `views/estatisticas/index.php` · `assets/js/estatisticas.js`

### 18.1. Agregações de tempo

```php
public function minutesThisWeek(int $userId): int
{
    $row = $this->queryOne(
        "SELECT COALESCE(SUM(duration_minutes), 0) AS total FROM study_sessions
         WHERE user_id = :user_id AND YEARWEEK(session_date, 1) = YEARWEEK(CURDATE(), 1)",
        ['user_id' => $userId]
    );
    return (int) ($row['total'] ?? 0);
}
```
`YEARWEEK(data, 1)` é uma função do MySQL que retorna o "ano+semana" de uma data, com o modo `1` configurando a semana para começar na **segunda-feira** (padrão brasileiro/ISO), em vez de domingo. Comparar `YEARWEEK(session_date, 1) = YEARWEEK(CURDATE(), 1)` é uma forma eficiente de filtrar "tudo que aconteceu na semana atual" sem precisar calcular manualmente as datas de início/fim da semana em PHP.

### 18.2. Evolução semanal (`minutesByWeek()`)

```php
$sql = "SELECT YEARWEEK(session_date, 1) AS year_week, MIN(session_date) AS week_start, SUM(duration_minutes) AS total_minutes
        FROM study_sessions
        WHERE user_id = :user_id AND session_date >= DATE_SUB(CURDATE(), INTERVAL :weeks WEEK)
        GROUP BY YEARWEEK(session_date, 1) ORDER BY year_week ASC";
```
Agrupa as sessões por semana (`GROUP BY YEARWEEK(...)`) e soma os minutos de cada uma. `MIN(session_date)` dentro do grupo serve só para ter uma data representativa daquela semana (usada depois para gerar o rótulo "Sem. 25/08" no gráfico). Esse método usa `bindValue()` explicitamente (em vez de passar um array simples para `execute()`) porque precisa declarar o tipo `PDO::PARAM_INT` — evita qualquer ambiguidade de tipo na cláusula `INTERVAL :weeks WEEK`.

### 18.3. Gráficos no frontend (`estatisticas.js`)

Os quatro gráficos seguem o mesmo princípio: gerar, em JavaScript, a lista de datas/semanas esperada (últimos 14 dias, últimas 8 semanas) e depois "casar" cada uma com os dados vindos do PHP:
```js
function lastNDates(n) {
    const dates = [];
    const today = new Date();
    for (let i = n - 1; i >= 0; i--) {
        const d = new Date(today);
        d.setDate(today.getDate() - i);
        dates.push(d);
    }
    return dates;
}
```
Gerar as datas no frontend (em vez de esperar que o backend "preencha os buracos") garante que dias sem nenhuma sessão registrada apareçam no gráfico como uma barra em `0`, não como uma barra ausente — o eixo X sempre tem exatamente 14 posições, na ordem certa.

---

## 19. Módulo: Perfil

**Arquivos:** `ProfileController.php` · `views/perfil/index.php`

### 19.1. Upload de foto com validação real de MIME type

```php
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (!isset($allowedMimes[$mime])) {
    $this->flash('error', 'Formato de imagem não suportado. Use JPG, PNG ou WEBP.');
    $this->redirect('perfil');
}
```
Esse é um ponto de segurança importante: em vez de confiar na extensão do arquivo enviado (`$_FILES['avatar']['name']`, que o usuário controla totalmente e pode renomear qualquer arquivo para `foto.jpg`), o código usa a extensão `fileinfo` do PHP para **inspecionar os bytes reais** do arquivo e determinar seu tipo MIME verdadeiro. Um arquivo `.php` renomeado para `.jpg` seria detectado como `text/x-php` (ou similar) e rejeitado — não como `image/jpeg`.

```php
$filename = 'avatar_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $allowedMimes[$mime];
```
O nome do arquivo salvo é **gerado pelo servidor** (nunca reaproveita o nome original enviado pelo usuário) — isso elimina qualquer risco de *path traversal* (nomes como `../../config/database.php`) e evita colisão de nomes entre usuários diferentes.

### 19.2. Troca de senha com verificação da senha atual

```php
if (!$this->userModel->verifyPassword($current, $user['password'])) {
    $this->flash('error', 'Senha atual incorreta.');
    $this->redirect('perfil');
}
```
Mesmo o usuário já estando autenticado (com uma sessão válida), a troca de senha exige a senha **atual** novamente — prática padrão que reduz o risco de alguém trocar a senha de uma conta através de uma sessão que ficou aberta em um computador compartilhado.

---

## 20. Módulo: Configurações

**Arquivos:** `SettingsController.php` · `views/configuracoes/index.php` · `assets/js/configuracoes.js`

### 20.1. Tema persistente vs. preferências locais

```php
public function updateTheme(): void
{
    $user = $this->requireAuth();
    $theme = $_POST['theme'] ?? 'light';
    $theme = in_array($theme, ['light', 'dark'], true) ? $theme : 'light';
    $this->userModel->updateTheme((int) $user['id'], $theme);
    $_SESSION['theme'] = $theme;
    $this->json(['success' => true, 'theme' => $theme]);
}
```
O tema é a única preferência salva **no banco de dados** (por isso, o usuário abre o sistema em qualquer dispositivo e o tema escolhido continua o mesmo). Já as preferências de notificações/comportamento (que não fazem parte do schema principal do banco, definido desde a primeira etapa do projeto) ficam salvas apenas no `localStorage` do navegador:
```js
const STORAGE_KEY = 'studyhub_prefs';
toggle.addEventListener('change', () => {
    const prefs = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    prefs[key] = toggle.checked;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
});
```
Essa é uma decisão consciente de arquitetura: como essas preferências são cosméticas (não afetam nenhuma regra de negócio nem precisam ser vistas em outro dispositivo), guardá-las no navegador evita alterar o schema do banco já validado nas etapas anteriores.

### 20.2. Exclusão de conta

```php
if (!$this->userModel->verifyPassword($password, $profile['password'])) {
    $this->flash('error', 'Senha incorreta. A conta não foi excluída.');
    $this->redirect('configuracoes');
}
$this->userModel->delete($userId);
$_SESSION = [];
session_destroy();
```
A exclusão exige senha (mesmo padrão de segurança da troca de senha no Perfil). O `delete()` remove apenas a linha da tabela `users` — todo o resto (matérias, tarefas, sessões, metas, anotações, avaliações, eventos) desaparece **automaticamente**, por conta das constraints `ON DELETE CASCADE` definidas no schema do banco (ver seção 4). Não é necessário nenhum código PHP adicional para "limpar" os dados relacionados.

---

## 21. Front-end: CSS e JavaScript

### 21.1. Organização do CSS

O projeto evita um único arquivo CSS gigante — cada módulo tem seu próprio arquivo (`materias.css`, `tarefas.css`, `foco.css`, etc.), todos carregados a partir de `style.css` (que define as variáveis de cor/tipografia globais via `:root`) e finalizados por `responsive.css` (que sobrescreve regras específicas para telas menores). Essa ordem de carregamento é o que permite que os ajustes de responsividade tenham prioridade sobre os estilos "de desktop" de cada módulo.

O tema escuro é implementado inteiramente com **variáveis CSS** (custom properties):
```css
:root { --sh-bg: #f4f6fb; --sh-surface: #ffffff; --sh-text: #1e2432; ... }
[data-bs-theme="dark"] { --sh-bg: #0f1420; --sh-surface: #171d2b; --sh-text: #e7eaf3; ... }
```
Como o restante do CSS do projeto sempre referencia `var(--sh-bg)`, `var(--sh-text)` etc. (nunca uma cor fixa), a troca de tema é só uma questão de alternar o atributo `data-bs-theme` no `<html>` — nenhum CSS precisa ser recarregado ou duplicado para o modo escuro.

### 21.2. Organização do JavaScript

Cada arquivo JS tem uma responsabilidade única, seguindo a orientação do prompt original ("não coloque todo JavaScript em um único arquivo"):

| Arquivo | Responsabilidade |
|---|---|
| `app.js` | Abrir/fechar a sidebar no mobile |
| `theme.js` | Alternar tema pelo ícone do header |
| `confirm-delete.js` | Confirmação antes de excluir (reutilizado em 5 módulos) |
| `dashboard.js` / `estatisticas.js` | Montagem dos gráficos Chart.js de cada tela |
| `calendario.js` | FullCalendar + modal de eventos via Fetch API |
| `foco.js` | Lógica do cronômetro Pomodoro |
| `configuracoes.js` | Switch de tema + preferências locais |
| `auth.js` | Mostrar/ocultar senha nos formulários de login/cadastro |
| `landing.js` | Comportamento da navbar da landing page |

---

## 22. Segurança — resumo consolidado

| Mecanismo | Onde está implementado |
|---|---|
| Senhas com hash (`password_hash`/`password_verify`) | `User.php`, usado em `AuthController` e `ProfileController` |
| 100% das queries via PDO *prepared statements* | Toda a camada `app/models` |
| Proteção contra XSS (`htmlspecialchars` em toda saída) | Todas as views |
| Tokens CSRF em todo formulário POST | `Controller::csrfToken()`/`verifyCsrf()`, usado em todos os controllers |
| Autorização por usuário (`belongsToUser()`) | Todos os models de dados do usuário |
| Sessão regenerada no login/cadastro | `AuthController::login()`/`register()` |
| Cookies sensíveis marcados `HttpOnly` | Cookie de "lembrar de mim" |
| Mensagens de erro genéricas (anti-enumeração) | Login e recuperação de senha |
| Validação real de tipo de arquivo (não só extensão) | Upload de avatar em `ProfileController` |
| Nomes de arquivo gerados pelo servidor | Upload de avatar |
| Confirmação de senha para ações destrutivas | Troca de senha e exclusão de conta |
| Exclusão em cascata consistente | Constraints `ON DELETE CASCADE`/`SET NULL` no banco |

---

## 23. Sugestões para a documentação acadêmica (TCC)

Para os capítulos formais do trabalho, esta implementação já fornece a base para:

- **Diagrama de casos de uso** — cada rota de `routes/web.php` corresponde, em geral, a um caso de uso (ex: "Cadastrar tarefa", "Iniciar sessão de foco", "Excluir conta").
- **DER (Diagrama Entidade-Relacionamento)** — a seção 4 desta documentação, junto com o `database/studyhub.sql`, já descreve entidades, atributos e relacionamentos prontos para serem desenhados.
- **Modelo lógico/físico** — o próprio `studyhub.sql` já é o modelo físico; o lógico pode ser derivado removendo os detalhes específicos do MySQL (tipos, engine, índices).
- **Dicionário de dados** — a tabela de cada seção do capítulo 4 pode ser expandida coluna a coluna (tipo, tamanho, nulidade, chave) diretamente a partir do `CREATE TABLE` de cada tabela.
- **Requisitos funcionais/não funcionais** — os 11 módulos descritos nas seções 9–20 mapeiam diretamente para requisitos funcionais; a seção 22 (segurança) e a responsividade (`responsive.css`) mapeiam para requisitos não funcionais.
- **Arquitetura MVC** — a seção 2 já descreve o fluxo de uma requisição de ponta a ponta, ilustração que pode virar diretamente um diagrama de arquitetura na apresentação.

