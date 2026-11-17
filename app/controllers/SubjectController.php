<?php
/**
 * app/controllers/SubjectController.php
 *
 * CRUD completo de matérias (subjects): listar, criar, editar,
 * excluir e arquivar. Cada matéria mostra progresso (tarefas
 * concluídas) e tempo total estudado.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Subject.php';

class SubjectController extends Controller
{
    private Subject $modeloMateria;

    public function __construct()
    {
        $this->modeloMateria = new Subject();
    }

    // -------------------------------------------------------------
    // LISTAGEM
    // -------------------------------------------------------------

    public function index(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $mostrarArquivadas = isset($_GET['arquivadas']);
        $materias = $this->modeloMateria->progressByUser($idUsuario);

        if ($mostrarArquivadas) {
            $materias = $this->modeloMateria->forUser($idUsuario, true);
            $materias = array_filter($materias, fn($s) => (int) $s['is_archived'] === 1);
        }

        $this->render('materias/index', [
            'materias'     => $materias,
            'mostrarArquivadas' => $mostrarArquivadas,
            'sucesso'      => $this->flash('success'),
        ], 'app');
    }

    // -------------------------------------------------------------
    // CRIAR
    // -------------------------------------------------------------

    public function createForm(): void
    {
        $this->requireAuth();
        $this->render('materias/form', [
            'materia'   => null,
            'tokenCsrf' => $this->csrfToken(),
            'erro'     => $this->flash('error'),
        ], 'app');
    }

    public function store(): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();

        $dados = $this->validatedData();

        if ($dados === null) {
            $this->redirect('materias/nova');
        }

        $dados['user_id'] = (int) $usuario['id'];
        $this->modeloMateria->create($dados);

        $this->flash('success', 'Matéria criada com sucesso.');
        $this->redirect('materias');
    }

    // -------------------------------------------------------------
    // EDITAR
    // -------------------------------------------------------------

    public function editForm(string $id): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];
        $idMateria = (int) $id;

        if (!$this->modeloMateria->belongsToUser($idMateria, $idUsuario)) {
            $this->flash('error', 'Matéria não encontrada.');
            $this->redirect('materias');
        }

        $materia = $this->modeloMateria->find($idMateria);

        $this->render('materias/form', [
            'materia'   => $materia,
            'tokenCsrf' => $this->csrfToken(),
            'erro'     => $this->flash('error'),
        ], 'app');
    }

    public function update(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idMateria = (int) $id;

        if (!$this->modeloMateria->belongsToUser($idMateria, $idUsuario)) {
            $this->flash('error', 'Matéria não encontrada.');
            $this->redirect('materias');
        }

        $dados = $this->validatedData();

        if ($dados === null) {
            $this->redirect('materias/' . $idMateria . '/editar');
        }

        $this->modeloMateria->update($idMateria, $dados);

        $this->flash('success', 'Matéria atualizada com sucesso.');
        $this->redirect('materias');
    }

    // -------------------------------------------------------------
    // EXCLUIR
    // -------------------------------------------------------------

    public function destroy(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idMateria = (int) $id;

        if ($this->modeloMateria->belongsToUser($idMateria, $idUsuario)) {
            $this->modeloMateria->delete($idMateria);
            $this->flash('success', 'Matéria excluída com sucesso.');
        } else {
            $this->flash('error', 'Matéria não encontrada.');
        }

        $this->redirect('materias');
    }

    // -------------------------------------------------------------
    // ARQUIVAR / DESARQUIVAR
    // -------------------------------------------------------------

    public function archive(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idMateria = (int) $id;

        if ($this->modeloMateria->belongsToUser($idMateria, $idUsuario)) {
            $atual = $this->modeloMateria->find($idMateria);
            $novaSituacao = (int) $atual['is_archived'] === 1 ? 0 : 1;
            $this->modeloMateria->update($idMateria, ['is_archived' => $novaSituacao]);
            $this->flash('success', $novaSituacao ? 'Matéria arquivada.' : 'Matéria restaurada.');
        }

        $this->redirect('materias');
    }

    // -------------------------------------------------------------
    // Validação compartilhada (criar/editar)
    // -------------------------------------------------------------

    private function validatedData(): ?array
    {
        $nome = trim($_POST['name'] ?? '');
        $descricao = trim($_POST['description'] ?? '') ?: null;
        $cor = trim($_POST['color'] ?? '#2563eb');
        $descricaoObjetivo = trim($_POST['goal_description'] ?? '') ?: null;
        $dataInicio = trim($_POST['start_date'] ?? '') ?: null;
        $dataFim = trim($_POST['end_date'] ?? '') ?: null;

        $erros = [];

        if ($nome === '' || mb_strlen($nome) < 2) {
            $erros[] = 'Informe o nome da matéria.';
        }
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $cor)) {
            $cor = '#2563eb';
        }
        if ($dataInicio && $dataFim && $dataFim < $dataInicio) {
            $erros[] = 'A data prevista de conclusão não pode ser anterior à data de início.';
        }

        if (!empty($erros)) {
            $this->flash('error', implode(' ', $erros));
            return null;
        }

        return [
            'name'             => $nome,
            'description'      => $descricao,
            'color'            => $cor,
            'goal_description' => $descricaoObjetivo,
            'start_date'       => $dataInicio,
            'end_date'         => $dataFim,
        ];
    }
}
