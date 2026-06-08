<?php
// Inicia a sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader para carregar classes automaticamente (Models, Controllers, Config)
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../config/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Define a BASE_URL e a URI de rota dinamicamente para suportar execução em subdiretórios,
// domínios raiz, acessos diretos a index.php e rewrites do Apache (.htaccess)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = $_SERVER['SCRIPT_NAME']; // ex: /CMDPCD_SITE/public/index.php
$scriptDir = rtrim(dirname($scriptName), '/\\'); // ex: /CMDPCD_SITE/public
$projectRootDir = rtrim(dirname($scriptDir), '/\\'); // ex: /CMDPCD_SITE

if (strpos($requestUri, $scriptName) === 0) {
    $baseUrl = $scriptName;
    $routeUri = substr($requestUri, strlen($scriptName));
} elseif (strpos($requestUri, $scriptDir) === 0) {
    $baseUrl = $scriptDir;
    $routeUri = substr($requestUri, strlen($scriptDir));
} elseif ($projectRootDir !== '' && strpos($requestUri, $projectRootDir) === 0) {
    $baseUrl = $projectRootDir;
    $routeUri = substr($requestUri, strlen($projectRootDir));
} else {
    $baseUrl = '';
    $routeUri = $requestUri;
}

define('BASE_URL', $baseUrl);

// Função auxiliar global para renderizar views
function view($viewName, $data = []) {
    extract($data);
    $viewFile = __DIR__ . '/../app/views/' . $viewName . '.php';
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        // Redireciona para 404
        require __DIR__ . '/../app/views/404.php';
    }
}

// Helper para redirecionamento fácil
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

// Helper para verificar se está autenticado
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        redirect('/admin');
    }
}

// Helpers de Perfil e Controle de Acesso
function getPerfilUsuario() {
    if (isset($_SESSION['user_perfil_nome'])) {
        return $_SESSION['user_perfil_nome'];
    }
    if (isset($_SESSION['user_perfil_id'])) {
        $map = [1 => 'admin_total', 2 => 'admin_parcial', 3 => 'editor'];
        return $map[$_SESSION['user_perfil_id']] ?? '';
    }
    return '';
}

function isAdminTotal() {
    return getPerfilUsuario() === 'admin_total';
}

function isAdminParcial() {
    return getPerfilUsuario() === 'admin_parcial';
}

function isEditor() {
    return getPerfilUsuario() === 'editor';
}

function checkPermission($allowedRoles) {
    checkAuth();
    $perfil = getPerfilUsuario();
    if (!in_array($perfil, $allowedRoles)) {
        $_SESSION['error_msg'] = "Você não tem permissão para acessar esta página.";
        redirect('/admin/dashboard');
    }
}

// Carrega as rotas
require_once __DIR__ . '/../routes/web.php';
