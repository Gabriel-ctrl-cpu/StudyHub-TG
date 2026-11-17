<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class SettingsController extends Controller
{
    private User $modeloUsuario;

    public function __construct()
    {
        $this->modeloUsuario = new User();
    }

    public function index(): void
    {
        $usuario = $this->requireAuth();
        $perfil = $this->modeloUsuario->find((int) $usuario['id']);

        $this->render('configuracoes/index', [
            'perfil'   => $perfil,
            'tokenCsrf' => $this->csrfToken(),
            'sucesso'   => $this->flash('success'),
            'erro'     => $this->flash('error'),
        ], 'app');
    }

    public function updateTheme(): void
    {
        $usuario = $this->requireAuth();
        $tema = $_POST['theme'] ?? 'light';
        $tema = in_array($tema, ['light', 'dark'], true) ? $tema : 'light';

        $this->modeloUsuario->updateTheme((int) $usuario['id'], $tema);
        $_SESSION['theme'] = $tema;

        $this->json(['success' => true, 'theme' => $tema]);
    }

    public function deleteAccount(): void
    {
        $usuario = $this->requireAuth();
        $this->verifyCsrf();
        $idUsuario = (int) $usuario['id'];

        $senha = $_POST['password'] ?? '';
        $perfil = $this->modeloUsuario->find($idUsuario);

        if (!$this->modeloUsuario->verifyPassword($senha, $perfil['password'])) {
            $this->flash('error', 'Senha incorreta. A conta não foi excluída.');
            $this->redirect('configuracoes');
        }

        $this->modeloUsuario->delete($idUsuario);

        $_SESSION = [];
        session_destroy();
        setcookie('studyhub_remember', '', time() - 3600, '/');

        $this->redirect('login');
    }
}
