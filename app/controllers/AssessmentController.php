<?php
/**
 * app/controllers/AssessmentController.php
 *
 * CRUD completo de avaliações (provas, trabalhos, seminários,
 * projetos): criar, editar, excluir e registrar nota/status.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Assessment.php';
require_once __DIR__ . '/../models/Subject.php';

class AssessmentController extends Controller
{
    private Assessment $modeloAvaliacao;
    private Subject $modeloMateria;

    public function __construct()
    {
        $this->modeloAvaliacao = new Assessment();
        $this->modeloMateria = new Subject();
    }

    public function index(): void
    {
        $usuario = $this->requireAuth();
        $avaliacoes = $this->modeloAvaliacao->forUser((int) $usuario['id']);

        $this->render('avaliacoes/index', [
            'avaliacoes' => $avaliacoes,
            'tokenCsrf'   => $this->csrfToken(),
            'sucesso'     => $this->flash('success'),
        ], 'app');
    }

    public function createForm(): void
    {
        $usuario = $this->requireAuth();
        $materias = $this->modeloMateria->forUser((int) $usuario['id']);

        if (empty($materias)) {
            $this->flash('error', 'Cadastre uma matéria antes de criar uma avaliação.');
            $this->redirect('avaliacoes');
        }

        $this->render('avaliacoes/form', [
            'avaliacao' => null,
            'materias'   => $materias,
            'tokenCsrf'  => $this->csrfToken(),
            'erro'      => $this->flash('error'),
        ], 'app');
    }

    public function store(): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();

        $dados = $this->validatedData((int) $usuario['id']);
        if ($dados === null) {
            $this->redirect('avaliacoes/nova');
        }

        $dados['user_id'] = (int) $usuario['id'];
        $this->modeloAvaliacao->create($dados);

        $this->flash('success', 'Avaliação criada com sucesso.');
        $this->redirect('avaliacoes');
    }

    public function editForm(string $id): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];
        $idAvaliacao = (int) $id;

        if (!$this->modeloAvaliacao->belongsToUser($idAvaliacao, $idUsuario)) {
            $this->flash('error', 'Avaliação não encontrada.');
            $this->redirect('avaliacoes');
        }

        $avaliacao = $this->modeloAvaliacao->find($idAvaliacao);
        $materias = $this->modeloMateria->forUser($idUsuario);

        $this->render('avaliacoes/form', [
            'avaliacao' => $avaliacao,
            'materias'   => $materias,
            'tokenCsrf'  => $this->csrfToken(),
            'erro'      => $this->flash('error'),
        ], 'app');
    }

    public function update(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idAvaliacao = (int) $id;

        if (!$this->modeloAvaliacao->belongsToUser($idAvaliacao, $idUsuario)) {
            $this->flash('error', 'Avaliação não encontrada.');
            $this->redirect('avaliacoes');
        }

        $dados = $this->validatedData($idUsuario);
        if ($dados === null) {
            $this->redirect('avaliacoes/' . $idAvaliacao . '/editar');
        }

        $this->modeloAvaliacao->update($idAvaliacao, $dados);

        $this->flash('success', 'Avaliação atualizada com sucesso.');
        $this->redirect('avaliacoes');
    }

    public function destroy(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idAvaliacao = (int) $id;

        if ($this->modeloAvaliacao->belongsToUser($idAvaliacao, $idUsuario)) {
            $this->modeloAvaliacao->delete($idAvaliacao);
            $this->flash('success', 'Avaliação excluída com sucesso.');
        } else {
            $this->flash('error', 'Avaliação não encontrada.');
        }

        $this->redirect('avaliacoes');
    }

    private function validatedData(int $idUsuario): ?array
    {
        $titulo = trim($_POST['title'] ?? '');
        $idMateria = trim($_POST['subject_id'] ?? '');
        $tipo = $_POST['assessment_type'] ?? 'prova';
        $dataAvaliacao = trim($_POST['assessment_date'] ?? '');
        $horario = trim($_POST['assessment_time'] ?? '') ?: null;
        $descricao = trim($_POST['description'] ?? '') ?: null;
        $peso = trim($_POST['weight'] ?? '') !== '' ? (float) $_POST['weight'] : null;
        $nota = trim($_POST['grade'] ?? '') !== '' ? (float) $_POST['grade'] : null;
        $situacao = $_POST['status'] ?? 'agendado';

        $erros = [];

        if ($titulo === '') {
            $erros[] = 'Informe o título da avaliação.';
        }
        if ($idMateria === '' || !$this->modeloMateria->belongsToUser((int) $idMateria, $idUsuario)) {
            $erros[] = 'Selecione uma matéria válida.';
        }
        if ($dataAvaliacao === '') {
            $erros[] = 'Informe a data da avaliação.';
        }
        if (!in_array($tipo, ['prova', 'trabalho', 'seminario', 'projeto', 'outro'], true)) {
            $tipo = 'outro';
        }
        if (!in_array($situacao, ['agendado', 'realizado', 'corrigido'], true)) {
            $situacao = 'agendado';
        }

        if (!empty($erros)) {
            $this->flash('error', implode(' ', $erros));
            return null;
        }

        return [
            'subject_id'      => (int) $idMateria,
            'title'           => $titulo,
            'assessment_type' => $tipo,
            'assessment_date' => $dataAvaliacao,
            'assessment_time' => $horario,
            'description'     => $descricao,
            'weight'          => $peso,
            'grade'           => $nota,
            'status'          => $situacao,
        ];
    }
}
