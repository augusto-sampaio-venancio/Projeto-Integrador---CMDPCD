<?php

class AuthController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Exibe o formulário de login.
     */
    public function loginForm() {
        // Se já estiver logado, redireciona para o painel
        if (isset($_SESSION['user_id'])) {
            redirect('/admin/dashboard');
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        view('admin/login', ['error' => $error]);
    }

    /**
     * Processa a tentativa de login (POST).
     */
    public function login() {
        $loginVal = trim($_POST['login'] ?? '');
        $passwordVal = trim($_POST['senha'] ?? '');

        if (empty($loginVal) || empty($passwordVal)) {
            $_SESSION['login_error'] = "Preencha todos os campos.";
            redirect('/admin');
        }

        // Tenta achar por email ou cpf
        $user = $this->usuarioModel->findByEmailOrCpf($loginVal);

        if ($user && password_verify($passwordVal, $user['senha'])) {
            if ($user['status'] !== 'ativo') {
                $_SESSION['login_error'] = "Usuário inativo ou bloqueado.";
                redirect('/admin');
            }

            // Define variáveis de sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome_completo'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_perfil_id'] = $user['perfil_id'];
            $_SESSION['user_perfil_nome'] = $user['perfil_nome'];

            // Atualiza último login no banco
            $this->usuarioModel->updateUltimoLogin($user['id']);

            redirect('/admin/dashboard');
        } else {
            $_SESSION['login_error'] = "E-mail/CPF ou senha incorretos.";
            redirect('/admin');
        }
    }

    /**
     * Encerra a sessão (Logout).
     */
    public function logout() {
        // Limpa todas as variáveis de sessão
        $_SESSION = [];

        // Destrói o cookie de sessão se houver
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destrói a sessão
        session_destroy();

        redirect('/admin');
    }
}
