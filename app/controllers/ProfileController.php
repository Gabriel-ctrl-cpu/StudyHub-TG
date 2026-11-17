<?php
/**
 * app/controllers/ProfileController.php
 *
 * Exibe e atualiza os dados do perfil do usuário: informações
 * pessoais, foto de perfil (upload com validação) e senha.
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/StudySession.php';
require_once __DIR__ . '/../models/Task.php';

class ProfileController extends Controller
{
    private User $modeloUsuario;

    public function __construct()
    {
        $this->modeloUsuario = new User();
    }

    public function index(): void
    {
        $usuarioAutenticado = $this->requireAuth();
        $idUsuario = (int) $usuarioAutenticado['id'];
        $usuario = $this->modeloUsuario->find($idUsuario);

        $estatisticas = [
            'subjects'  => count((new Subject())->forUser($idUsuario)),
            'minutes'   => (new StudySession())->minutesTotal($idUsuario),
            'tasksDone' => (new Task())->countByStatus($idUsuario, 'concluida'),
        ];

        $this->render('perfil/index', [
            'usuario'      => $usuario,
            'estatisticas'     => $estatisticas,
            'tokenCsrf' => $this->csrfToken(),
            'sucesso'   => $this->flash('success'),
            'erro'     => $this->flash('error'),
        ], 'app');
    }

    // -------------------------------------------------------------
    // DADOS PESSOAIS
    // -------------------------------------------------------------

    public function update(): void
    {
        $usuarioAutenticado = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuarioAutenticado['id'];

        $nome = trim($_POST['name'] ?? '');
        $curso = trim($_POST['course'] ?? '') ?: null;
        $instituicao = trim($_POST['institution'] ?? '') ?: null;

        if ($nome === '' || mb_strlen($nome) < 3) {
            $this->flash('error', 'Informe um nome válido.');
            $this->redirect('perfil');
        }

        $this->modeloUsuario->update($idUsuario, [
            'name'        => $nome,
            'course'      => $curso,
            'institution' => $instituicao,
        ]);

        $_SESSION['user_name'] = $nome;
        $this->flash('success', 'Dados atualizados com sucesso.');
        $this->redirect('perfil');
    }

    // -------------------------------------------------------------
    // FOTO DE PERFIL
    // -------------------------------------------------------------

    public function updatePhoto(): void
    {
        $usuarioAutenticado = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuarioAutenticado['id'];

        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Selecione uma imagem válida.');
            $this->redirect('perfil');
        }

        $arquivo = $_FILES['avatar'];

        $mimesPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $infoArquivo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($infoArquivo, $arquivo['tmp_name']);
        finfo_close($infoArquivo);

        if (!isset($mimesPermitidos[$mime])) {
            $this->flash('error', 'Formato de imagem não suportado. Use JPG, PNG ou WEBP.');
            $this->redirect('perfil');
        }

        if ($arquivo['size'] > 2 * 1024 * 1024) { // 2 MB
            $this->flash('error', 'A imagem deve ter no máximo 2MB.');
            $this->redirect('perfil');
        }

        $pastaUpload = __DIR__ . '/../../public/assets/images/uploads/';
        if (!is_dir($pastaUpload)) {
            mkdir($pastaUpload, 0755, true);
        }

        $nomeArquivo = 'avatar_' . $idUsuario . '_' . bin2hex(random_bytes(8)) . '.' . $mimesPermitidos[$mime];
        $destino = $pastaUpload . $nomeArquivo;

        if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
            $this->flash('error', 'Não foi possível salvar a imagem. Tente novamente.');
            $this->redirect('perfil');
        }

        // Remove avatar antigo, se existir
        $atual = $this->modeloUsuario->find($idUsuario);
        if (!empty($atual['avatar'])) {
            $caminhoAntigo = $pastaUpload . basename($atual['avatar']);
            if (is_file($caminhoAntigo)) {
                @unlink($caminhoAntigo);
            }
        }

        $this->modeloUsuario->update($idUsuario, ['avatar' => $nomeArquivo]);
        $_SESSION['user_avatar'] = $nomeArquivo;

        $this->flash('success', 'Foto de perfil atualizada.');
        $this->redirect('perfil');
    }

    // -------------------------------------------------------------
    // SENHA
    // -------------------------------------------------------------

    public function updatePassword(): void
    {
        $usuarioAutenticado = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuarioAutenticado['id'];

        $atual = $_POST['current_password'] ?? '';
        $novo = $_POST['new_password'] ?? '';
        $confirmacao = $_POST['confirm_password'] ?? '';

        $usuario = $this->modeloUsuario->find($idUsuario);

        if (!$this->modeloUsuario->verifyPassword($atual, $usuario['password'])) {
            $this->flash('error', 'Senha atual incorreta.');
            $this->redirect('perfil');
        }
        if (mb_strlen($novo) < 6) {
            $this->flash('error', 'A nova senha deve ter pelo menos 6 caracteres.');
            $this->redirect('perfil');
        }
        if ($novo !== $confirmacao) {
            $this->flash('error', 'A confirmação de senha não corresponde.');
            $this->redirect('perfil');
        }

        $this->modeloUsuario->updatePassword($idUsuario, $novo);
        $this->flash('success', 'Senha alterada com sucesso.');
        $this->redirect('perfil');
    }
}
