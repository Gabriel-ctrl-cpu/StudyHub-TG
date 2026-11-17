<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    private User $modeloUsuario;

    public function __construct()
    {
        $this->modeloUsuario = new User();
    }

    public function loginForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        $this->render('auth/login', [
            'tokenCsrf' => $this->csrfToken(),
            'erro'     => $this->flash('error'),
        ], 'auth');
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email    = trim($_POST['email'] ?? '');
        $senha = $_POST['password'] ?? '';
        $lembrar = isset($_POST['remember']);

        if ($email === '' || $senha === '') {
            $this->flash('error', 'Preencha e-mail e senha.');
            $this->redirect('login');
        }

        $usuario = $this->modeloUsuario->findByEmail($email);

        if (!$usuario || !$this->modeloUsuario->verifyPassword($senha, $usuario['password'])) {
            $this->flash('error', 'E-mail ou senha incorretos.');
            $this->redirect('login');
        }

        session_regenerate_id(true);
        $_SESSION['user_id']     = $usuario['id'];
        $_SESSION['user_name']   = $usuario['name'];
        $_SESSION['user_avatar'] = $usuario['avatar'];
        $_SESSION['theme']       = $usuario['theme'];

        if ($lembrar) {
            $token = bin2hex(random_bytes(32));
            $this->modeloUsuario->update($usuario['id'], ['remember_token' => $token]);
            setcookie('studyhub_remember', $token, time() + (86400 * 30), '/', '', false, true);
        }

        $this->redirect('dashboard');
    }

    public function registerForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        $this->render('auth/register', [
            'tokenCsrf' => $this->csrfToken(),
            'erro'     => $this->flash('error'),
        ], 'auth');
        $this->clearOld();
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $nome        = trim($_POST['name'] ?? '');
        $email       = trim(strtolower($_POST['email'] ?? ''));
        $senha    = $_POST['password'] ?? '';
        $confirmacao     = $_POST['confirm_password'] ?? '';
        $curso      = trim($_POST['course'] ?? '') ?: null;
        $instituicao = trim($_POST['institution'] ?? '') ?: null;

        $this->setOld([
            'name' => $nome, 'email' => $email, 'course' => $curso, 'institution' => $instituicao,
        ]);

        $erros = [];

        if ($nome === '' || mb_strlen($nome) < 3) {
            $erros[] = 'Informe seu nome completo.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        }
        if (mb_strlen($senha) < 6) {
            $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
        }
        if ($senha !== $confirmacao) {
            $erros[] = 'As senhas não coincidem.';
        }
        if (empty($erros) && $this->modeloUsuario->emailExists($email)) {
            $erros[] = 'Este e-mail já está cadastrado.';
        }

        if (!empty($erros)) {
            $this->flash('error', implode(' ', $erros));
            $this->redirect('cadastro');
        }

        $idUsuario = $this->modeloUsuario->register($nome, $email, $senha, $curso, $instituicao);

        session_regenerate_id(true);
        $_SESSION['user_id']     = $idUsuario;
        $_SESSION['user_name']   = $nome;
        $_SESSION['user_avatar'] = null;
        $_SESSION['theme']       = 'light';
        $this->clearOld();

        $this->redirect('dashboard');
    }

    public function forgotForm(): void
    {
        $this->render('auth/forgot', [
            'tokenCsrf' => $this->csrfToken(),
            'mensagem'   => $this->flash('message'),
        ], 'auth');
    }

    public function forgot(): void
    {
        $this->verifyCsrf();

        $email = trim($_POST['email'] ?? '');
        $usuario  = $this->modeloUsuario->findByEmail($email);

        // Por segurança, a mensagem não revela se o e-mail existe ou não.
        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $this->modeloUsuario->update($usuario['id'], ['remember_token' => $token]);
            // Em produção: enviar e-mail real com link contendo o token.
        }

        $this->flash('message', 'Se o e-mail informado estiver cadastrado, você receberá instruções para redefinir sua senha.');
        $this->redirect('esqueci-senha');
    }

    public function logout(): void
    {
        setcookie('studyhub_remember', '', time() - 3600, '/');
        $_SESSION = [];
        session_destroy();
        $this->redirect('login');
    }
}
