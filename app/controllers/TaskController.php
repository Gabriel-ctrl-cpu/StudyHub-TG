<?php
/**
 * app/controllers/TaskController.php
 *
 * CRUD completo de tarefas: listar (com filtros), criar, editar,
 * excluir, concluir/reabrir rapidamente.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Subject.php';

class TaskController extends Controller
{
    private Task $modeloTarefa;
    private Subject $modeloMateria;

    public function __construct()
    {
        $this->modeloTarefa = new Task();
        $this->modeloMateria = new Subject();
    }

    // -------------------------------------------------------------
    // LISTAGEM (com filtros)
    // -------------------------------------------------------------

    public function index(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $filtros = [
            'period'     => $_GET['periodo'] ?? 'todas',
            'status'     => $_GET['status'] ?? '',
            'priority'   => $_GET['prioridade'] ?? '',
            'subject_id' => $_GET['materia'] ?? '',
            'search'     => trim($_GET['q'] ?? ''),
        ];

        $tarefas = $this->modeloTarefa->filtered($idUsuario, $filtros);
        $materias = $this->modeloMateria->forUser($idUsuario);

        $this->render('tarefas/index', [
            'tarefas'    => $tarefas,
            'materias' => $materias,
            'filtros'  => $filtros,
            'sucesso'  => $this->flash('success'),
        ], 'app');
    }

    // -------------------------------------------------------------
    // CRIAR
    // -------------------------------------------------------------

    public function createForm(): void
    {
        $usuario = $this->requireAuth();
        $materias = $this->modeloMateria->forUser((int) $usuario['id']);

        $this->render('tarefas/form', [
            'tarefa'      => null,
            'materias'  => $materias,
            'tokenCsrf' => $this->csrfToken(),
            'erro'     => $this->flash('error'),
        ], 'app');
    }

    public function store(): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();

        $dados = $this->validatedData((int) $usuario['id']);
        if ($dados === null) {
            $this->redirect('tarefas/nova');
        }

        $dados['user_id'] = (int) $usuario['id'];
        $this->modeloTarefa->create($dados);

        $this->flash('success', 'Tarefa criada com sucesso.');
        $this->redirect('tarefas');
    }

    // -------------------------------------------------------------
    // EDITAR
    // -------------------------------------------------------------

    public function editForm(string $id): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];
        $idTarefa = (int) $id;

        if (!$this->modeloTarefa->belongsToUser($idTarefa, $idUsuario)) {
            $this->flash('error', 'Tarefa não encontrada.');
            $this->redirect('tarefas');
        }

        $tarefa = $this->modeloTarefa->find($idTarefa);
        $materias = $this->modeloMateria->forUser($idUsuario);

        $this->render('tarefas/form', [
            'tarefa'      => $tarefa,
            'materias'  => $materias,
            'tokenCsrf' => $this->csrfToken(),
            'erro'     => $this->flash('error'),
        ], 'app');
    }

    public function update(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idTarefa = (int) $id;

        if (!$this->modeloTarefa->belongsToUser($idTarefa, $idUsuario)) {
            $this->flash('error', 'Tarefa não encontrada.');
            $this->redirect('tarefas');
        }

        $dados = $this->validatedData($idUsuario);
        if ($dados === null) {
            $this->redirect('tarefas/' . $idTarefa . '/editar');
        }

        if ($dados['status'] === 'concluida') {
            $dados['completed_at'] = date('Y-m-d H:i:s');
        }

        $this->modeloTarefa->update($idTarefa, $dados);

        $this->flash('success', 'Tarefa atualizada com sucesso.');
        $this->redirect('tarefas');
    }

    // -------------------------------------------------------------
    // EXCLUIR
    // -------------------------------------------------------------

    public function destroy(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idTarefa = (int) $id;

        if ($this->modeloTarefa->belongsToUser($idTarefa, $idUsuario)) {
            $this->modeloTarefa->delete($idTarefa);
            $this->flash('success', 'Tarefa excluída com sucesso.');
        } else {
            $this->flash('error', 'Tarefa não encontrada.');
        }

        $this->redirect('tarefas');
    }

    // -------------------------------------------------------------
    // CONCLUIR / REABRIR (alternância rápida via formulário simples)
    // -------------------------------------------------------------

    public function toggleComplete(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idTarefa = (int) $id;

        if ($this->modeloTarefa->belongsToUser($idTarefa, $idUsuario)) {
            $tarefa = $this->modeloTarefa->find($idTarefa);
            $metaConcluida = $tarefa['status'] === 'concluida';

            $this->modeloTarefa->update($idTarefa, [
                'status'       => $metaConcluida ? 'pendente' : 'concluida',
                'completed_at' => $metaConcluida ? null : date('Y-m-d H:i:s'),
            ]);
        }

        $this->redirect('tarefas');
    }

    // -------------------------------------------------------------
    // Validação compartilhada (criar/editar)
    // -------------------------------------------------------------

    private function validatedData(int $idUsuario): ?array
    {
        $titulo = trim($_POST['title'] ?? '');
        $descricao = trim($_POST['description'] ?? '') ?: null;
        $idMateria = trim($_POST['subject_id'] ?? '') ?: null;
        $prioridade = $_POST['priority'] ?? 'media';
        $situacao = $_POST['status'] ?? 'pendente';
        $dataPrazo = trim($_POST['due_date'] ?? '') ?: null;
        $horaPrazo = trim($_POST['due_time'] ?? '') ?: null;

        $erros = [];

        if ($titulo === '' || mb_strlen($titulo) < 3) {
            $erros[] = 'Informe um título para a tarefa.';
        }
        if (!in_array($prioridade, ['alta', 'media', 'baixa'], true)) {
            $prioridade = 'media';
        }
        if (!in_array($situacao, ['pendente', 'em_andamento', 'concluida', 'cancelada'], true)) {
            $situacao = 'pendente';
        }
        if ($idMateria !== null && !$this->modeloMateria->belongsToUser((int) $idMateria, $idUsuario)) {
            $erros[] = 'Matéria inválida.';
        }

        if (!empty($erros)) {
            $this->flash('error', implode(' ', $erros));
            return null;
        }

        return [
            'title'       => $titulo,
            'description' => $descricao,
            'subject_id'  => $idMateria,
            'priority'    => $prioridade,
            'status'      => $situacao,
            'due_date'    => $dataPrazo,
            'due_time'    => $horaPrazo,
        ];
    }
}
