<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Goal.php';
require_once __DIR__ . '/../models/Subject.php';

class GoalController extends Controller
{
    private Goal $modeloMeta;
    private Subject $modeloMateria;

    public function __construct()
    {
        $this->modeloMeta = new Goal();
        $this->modeloMateria = new Subject();
    }

    public function index(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $this->modeloMeta->syncAutoProgress($idUsuario);
        $metas = $this->modeloMeta->forUser($idUsuario);

        $this->render('metas/index', [
            'metas'     => $metas,
            'tokenCsrf' => $this->csrfToken(),
            'sucesso'   => $this->flash('success'),
        ], 'app');
    }

    public function createForm(): void
    {
        $usuario = $this->requireAuth();
        $materias = $this->modeloMateria->forUser((int) $usuario['id']);

        $this->render('metas/form', [
            'meta'      => null,
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
            $this->redirect('metas/nova');
        }

        $dados['user_id'] = (int) $usuario['id'];
        $dados['is_completed'] = $dados['current_value'] >= $dados['target_value'] ? 1 : 0;
        $this->modeloMeta->create($dados);

        $this->flash('success', 'Meta criada com sucesso.');
        $this->redirect('metas');
    }

    public function editForm(string $id): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];
        $idMeta = (int) $id;

        $meta = $this->modeloMeta->find($idMeta);
        if (!$meta || (int) $meta['user_id'] !== $idUsuario) {
            $this->flash('error', 'Meta não encontrada.');
            $this->redirect('metas');
        }

        $materias = $this->modeloMateria->forUser($idUsuario);

        $this->render('metas/form', [
            'meta'      => $meta,
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
        $idMeta = (int) $id;

        $meta = $this->modeloMeta->find($idMeta);
        if (!$meta || (int) $meta['user_id'] !== $idUsuario) {
            $this->flash('error', 'Meta não encontrada.');
            $this->redirect('metas');
        }

        $dados = $this->validatedData($idUsuario);
        if ($dados === null) {
            $this->redirect('metas/' . $idMeta . '/editar');
        }

        $dados['is_completed'] = $dados['current_value'] >= $dados['target_value'] ? 1 : 0;
        $this->modeloMeta->update($idMeta, $dados);

        $this->flash('success', 'Meta atualizada com sucesso.');
        $this->redirect('metas');
    }

    public function destroy(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idMeta = (int) $id;

        $meta = $this->modeloMeta->find($idMeta);
        if ($meta && (int) $meta['user_id'] === $idUsuario) {
            $this->modeloMeta->delete($idMeta);
            $this->flash('success', 'Meta excluída com sucesso.');
        } else {
            $this->flash('error', 'Meta não encontrada.');
        }

        $this->redirect('metas');
    }

    public function updateProgress(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idMeta = (int) $id;

        $meta = $this->modeloMeta->find($idMeta);
        if (!$meta || (int) $meta['user_id'] !== $idUsuario) {
            $this->redirect('metas');
        }

        $incremento = (float) ($_POST['increment'] ?? 1);
        $novoValor = max(0, (float) $meta['current_value'] + $incremento);
        $metaConcluida = $novoValor >= (float) $meta['target_value'] ? 1 : 0;

        $this->modeloMeta->update($idMeta, [
            'current_value' => $novoValor,
            'is_completed'  => $metaConcluida,
        ]);

        $this->redirect('metas');
    }

    private function validatedData(int $idUsuario): ?array
    {
        $titulo = trim($_POST['title'] ?? '');
        $descricao = trim($_POST['description'] ?? '') ?: null;
        $idMateria = trim($_POST['subject_id'] ?? '') ?: null;
        $tipoMeta = $_POST['goal_type'] ?? 'personalizada';
        $valorAlvo = (float) ($_POST['target_value'] ?? 0);
        $valorAtual = (float) ($_POST['current_value'] ?? 0);
        $prazo = trim($_POST['deadline'] ?? '') ?: null;

        $erros = [];

        if ($titulo === '' || mb_strlen($titulo) < 3) {
            $erros[] = 'Informe um título para a meta.';
        }
        if (!in_array($tipoMeta, ['horas', 'tarefas', 'exercicios', 'personalizada'], true)) {
            $tipoMeta = 'personalizada';
        }
        if ($valorAlvo <= 0) {
            $erros[] = 'O valor da meta deve ser maior que zero.';
        }
        if ($idMateria !== null && !$this->modeloMateria->belongsToUser((int) $idMateria, $idUsuario)) {
            $erros[] = 'Matéria inválida.';
        }

        if (!empty($erros)) {
            $this->flash('error', implode(' ', $erros));
            return null;
        }

        return [
            'title'         => $titulo,
            'description'   => $descricao,
            'subject_id'    => $idMateria,
            'goal_type'     => $tipoMeta,
            'target_value'  => $valorAlvo,
            'current_value' => $valorAtual,
            'deadline'      => $prazo,
        ];
    }
}
