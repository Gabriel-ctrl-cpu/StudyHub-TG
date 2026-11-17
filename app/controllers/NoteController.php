<?php
/**
 * app/controllers/NoteController.php
 *
 * CRUD completo de anotações, com pesquisa por texto/tag e filtro
 * por matéria — interface de bloco de notas.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/Subject.php';

class NoteController extends Controller
{
    private Note $modeloAnotacao;
    private Subject $modeloMateria;

    public function __construct()
    {
        $this->modeloAnotacao = new Note();
        $this->modeloMateria = new Subject();
    }

    public function index(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $termo = trim($_GET['q'] ?? '');
        $idMateria = !empty($_GET['materia']) ? (int) $_GET['materia'] : null;

        $anotacoes = $this->modeloAnotacao->search($idUsuario, $termo, $idMateria);
        $materias = $this->modeloMateria->forUser($idUsuario);

        $this->render('anotacoes/index', [
            'anotacoes'     => $anotacoes,
            'materias'  => $materias,
            'termo'      => $termo,
            'idMateria' => $idMateria,
            'tokenCsrf' => $this->csrfToken(),
            'sucesso'   => $this->flash('success'),
        ], 'app');
    }

    public function createForm(): void
    {
        $usuario = $this->requireAuth();
        $materias = $this->modeloMateria->forUser((int) $usuario['id']);

        $this->render('anotacoes/form', [
            'anotacao'      => null,
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
            $this->redirect('anotacoes/nova');
        }

        $dados['user_id'] = (int) $usuario['id'];
        $this->modeloAnotacao->create($dados);

        $this->flash('success', 'Anotação criada com sucesso.');
        $this->redirect('anotacoes');
    }

    public function editForm(string $id): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];
        $idAnotacao = (int) $id;

        if (!$this->modeloAnotacao->belongsToUser($idAnotacao, $idUsuario)) {
            $this->flash('error', 'Anotação não encontrada.');
            $this->redirect('anotacoes');
        }

        $anotacao = $this->modeloAnotacao->find($idAnotacao);
        $materias = $this->modeloMateria->forUser($idUsuario);

        $this->render('anotacoes/form', [
            'anotacao'      => $anotacao,
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
        $idAnotacao = (int) $id;

        if (!$this->modeloAnotacao->belongsToUser($idAnotacao, $idUsuario)) {
            $this->flash('error', 'Anotação não encontrada.');
            $this->redirect('anotacoes');
        }

        $dados = $this->validatedData($idUsuario);
        if ($dados === null) {
            $this->redirect('anotacoes/' . $idAnotacao . '/editar');
        }

        $this->modeloAnotacao->update($idAnotacao, $dados);

        $this->flash('success', 'Anotação atualizada com sucesso.');
        $this->redirect('anotacoes');
    }

    public function destroy(string $id): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];
        $idAnotacao = (int) $id;

        if ($this->modeloAnotacao->belongsToUser($idAnotacao, $idUsuario)) {
            $this->modeloAnotacao->delete($idAnotacao);
            $this->flash('success', 'Anotação excluída com sucesso.');
        } else {
            $this->flash('error', 'Anotação não encontrada.');
        }

        $this->redirect('anotacoes');
    }

    private function validatedData(int $idUsuario): ?array
    {
        $titulo = trim($_POST['title'] ?? '');
        $conteudo = trim($_POST['content'] ?? '');
        $idMateria = trim($_POST['subject_id'] ?? '') ?: null;
        $etiquetas = trim($_POST['tags'] ?? '') ?: null;

        $erros = [];

        if ($titulo === '') {
            $erros[] = 'Informe um título para a anotação.';
        }
        if ($conteudo === '') {
            $erros[] = 'O conteúdo da anotação não pode ficar vazio.';
        }
        if ($idMateria !== null && !$this->modeloMateria->belongsToUser((int) $idMateria, $idUsuario)) {
            $erros[] = 'Matéria inválida.';
        }

        if (!empty($erros)) {
            $this->flash('error', implode(' ', $erros));
            return null;
        }

        // Normaliza tags: "javascript, funcoes" -> "javascript,funcoes"
        if ($etiquetas !== null) {
            $etiquetas = implode(',', array_filter(array_map('trim', explode(',', $etiquetas))));
        }

        return [
            'title'      => $titulo,
            'content'    => $conteudo,
            'subject_id' => $idMateria,
            'tags'       => $etiquetas,
        ];
    }
}
