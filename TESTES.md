# StudyHub — Testes Manuais

Este documento descreve o roteiro de testes manuais do sistema, cobrindo os fluxos principais e os casos de borda de segurança. Recomendado para a seção de "Testes" do TCC.

> Pré-requisito: sistema instalado conforme o `README.md`, com o banco `studyhub.sql` importado (já contém os usuários de demonstração).

---

## 1. Autenticação

| # | Teste | Passos | Resultado esperado |
|---|-------|--------|---------------------|
| 1.1 | Cadastro com dados válidos | Acessar `/cadastro`, preencher nome, e-mail novo, senha (6+ caracteres) e confirmação | Redireciona para o Dashboard já autenticado |
| 1.2 | Cadastro com e-mail duplicado | Repetir o cadastro com `gabriel@studyhub.com` | Mensagem de erro "Este e-mail já está cadastrado", permanece na tela de cadastro |
| 1.3 | Cadastro com senhas diferentes | Preencher senha e confirmação diferentes | Mensagem "As senhas não coincidem" |
| 1.4 | Login com credenciais corretas | `gabriel@studyhub.com` / `senha123` | Redireciona para o Dashboard |
| 1.5 | Login com senha incorreta | E-mail correto + senha errada | Mensagem genérica "E-mail ou senha incorretos" (não revela qual campo está errado) |
| 1.6 | Login com e-mail inexistente | E-mail não cadastrado | Mesma mensagem genérica do teste 1.5 |
| 1.7 | Logout | Clicar em "Sair" no menu do usuário | Sessão destruída, redireciona para `/login` |
| 1.8 | Recuperação de senha | Acessar `/esqueci-senha`, informar e-mail cadastrado | Mensagem de confirmação genérica (não revela se o e-mail existe) |

## 2. Controle de acesso

| # | Teste | Passos | Resultado esperado |
|---|-------|--------|---------------------|
| 2.1 | Acesso sem login | Sem estar autenticado, acessar `/dashboard`, `/materias`, `/tarefas` etc. diretamente pela URL | Redireciona para `/login` |
| 2.2 | Acesso a dado de outro usuário | Logado como `gabriel@studyhub.com`, tentar acessar `/materias/{id}/editar` usando o ID de uma matéria de outro usuário (ex: `ana@studyhub.com`) | Mensagem "não encontrada" e redireciona — nunca exibe o dado alheio |
| 2.3 | Formulário sem CSRF token | Enviar um POST para qualquer rota removendo o campo `csrf_token` (via ferramenta como Postman) | HTTP 419, "Token de segurança inválido" |

## 3. Matérias

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 3.1 | Criar matéria com nome válido | Aparece na listagem, com progresso 0% |
| 3.2 | Criar matéria sem nome | Mensagem de erro, matéria não é criada |
| 3.3 | Editar matéria | Alterações refletem na listagem |
| 3.4 | Arquivar matéria | Sai da lista "Ativas", aparece em "Arquivadas" |
| 3.5 | Excluir matéria | Some da listagem; tarefas vinculadas ficam sem matéria (`subject_id = NULL`) |

## 4. Tarefas

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 4.1 | Criar tarefa vinculada a uma matéria | Aparece na listagem com a cor da matéria |
| 4.2 | Concluir tarefa pelo checkbox | Status muda para "Concluída", `completed_at` é preenchido |
| 4.3 | Filtro "Hoje" | Mostra apenas tarefas com `due_date` = data atual |
| 4.4 | Filtro "Alta prioridade" | Mostra apenas tarefas com prioridade alta |
| 4.5 | Pesquisar por título | Lista filtra corretamente pelo texto digitado |
| 4.6 | Excluir tarefa | Remove da listagem após confirmação |

## 5. Calendário

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 5.1 | Visualizar calendário | Tarefas com prazo, avaliações, sessões e eventos aparecem com cores diferentes |
| 5.2 | Criar compromisso clicando em um dia | Modal abre com a data preenchida; ao salvar, evento aparece no calendário |
| 5.3 | Editar compromisso | Clicar em um evento do tipo "compromisso" abre o modal preenchido |
| 5.4 | Excluir compromisso | Remove do calendário imediatamente (sem recarregar a página) |

## 6. Modo Foco

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 6.1 | Iniciar sem selecionar matéria | Campo de matéria é destacado como obrigatório; cronômetro não inicia |
| 6.2 | Ciclo completo de 25 minutos (ajustar o relógio do sistema para simular, se necessário) | Ao zerar, sessão é registrada automaticamente e mensagem de conclusão aparece |
| 6.3 | Finalizar antes do tempo | Sessão é registrada com a duração parcial decorrida |
| 6.4 | Pausa não é registrada | Após o ciclo de pausa terminar, nenhuma nova sessão é criada |

## 7. Metas

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 7.1 | Criar meta | Aparece na listagem com barra de progresso |
| 7.2 | Clicar em "+1 progresso" | Barra avança; ao atingir o valor alvo, meta é marcada como concluída automaticamente |
| 7.3 | Editar valores manualmente | Progresso recalculado corretamente |

## 8. Anotações

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 8.1 | Criar anotação com tags | Aparece na listagem com as tags exibidas |
| 8.2 | Pesquisar por palavra do conteúdo | Retorna apenas anotações que contêm o termo |
| 8.3 | Pesquisar por tag | Retorna anotações com aquela tag |

## 9. Avaliações

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 9.1 | Criar avaliação sem matéria cadastrada | Sistema bloqueia e pede para cadastrar uma matéria primeiro |
| 9.2 | Criar avaliação com nota em branco | Aceita — nota é preenchida depois da correção |
| 9.3 | Atualizar status para "Corrigido" com nota | Nota aparece na tabela |

## 10. Estatísticas

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 10.1 | Acessar `/estatisticas` | Todos os 4 gráficos carregam com dados reais do usuário logado |
| 10.2 | Comparar com outro usuário | Números diferem — cada usuário só vê seus próprios dados (sem ranking) |

## 11. Perfil e Configurações

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 11.1 | Alterar nome/curso/instituição | Dados refletem no header e no perfil |
| 11.2 | Upload de foto válida (JPG/PNG/WEBP, <2MB) | Avatar atualizado em toda a interface |
| 11.3 | Upload de arquivo inválido (ex: `.pdf` renomeado para `.jpg`) | Rejeitado — validação real de MIME type, não apenas da extensão |
| 11.4 | Alterar senha com senha atual errada | Mensagem "Senha atual incorreta"; senha não é alterada |
| 11.5 | Alterar senha corretamente | Logout e novo login funcionam apenas com a nova senha |
| 11.6 | Alternar tema claro/escuro | Persiste após logout/login novamente |
| 11.7 | Excluir conta com senha errada | Mensagem de erro, conta não é excluída |
| 11.8 | Excluir conta com senha correta | Conta e todos os dados vinculados são apagados; redireciona para login |

## 12. Responsividade

| # | Teste | Resultado esperado |
|---|-------|---------------------|
| 12.1 | Redimensionar para largura de celular (~375px) | Sidebar vira menu retrátil (ícone de hambúrguer no header) |
| 12.2 | Dashboard em tablet (~768px) | Cards de resumo reorganizam em 2 colunas |
| 12.3 | Tabelas (Avaliações, Sessões) em mobile | Possuem rolagem horizontal, sem quebrar o layout |
| 12.4 | Modo Foco em mobile | Cronômetro e botões continuam legíveis e utilizáveis |
| 12.5 | Calendário em mobile | Botões da barra de ferramentas do FullCalendar empilham verticalmente |

---

### Como reportar um teste com falha

Ao encontrar um comportamento diferente do esperado, anote: navegador/dispositivo usado, passos exatos para reproduzir, resultado obtido vs. esperado, e (se possível) uma captura de tela. Isso facilita tanto a correção quanto a documentação de bugs conhecidos no TCC.
