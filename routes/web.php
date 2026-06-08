<?php

// Obtém a URI da rota pré-calculada pelo Front Controller
if (isset($routeUri)) {
    $requestUri = $routeUri;
} else {
    // Fallback caso o roteador seja incluído isoladamente
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (defined('BASE_URL') && BASE_URL !== '' && strpos($requestUri, BASE_URL) === 0) {
        $requestUri = substr($requestUri, strlen(BASE_URL));
    }
}

// Normaliza o caminho: garante que comece com '/' e não termine com '/' (exceto se for '/')
$requestUri = '/' . trim($requestUri, '/');
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Mapa de rotas do sistema
$routes = [
    'GET' => [
        '/' => 'HomeController@index',
        '/index.html' => 'HomeController@index',
        '/quem-somos' => 'HomeController@quemSomos',
        '/quem-somos.html' => 'HomeController@quemSomos',
        '/iniciativas' => 'HomeController@iniciativas',
        '/iniciativas.html' => 'HomeController@iniciativas',
        '/noticias' => 'HomeController@noticias',
        '/noticias.html' => 'HomeController@noticias',
        '/transparencia' => 'HomeController@transparencia',
        '/transparencia.html' => 'HomeController@transparencia',
        '/apoio' => 'HomeController@apoio',
        '/apoio.html' => 'HomeController@apoio',
        '/contato' => 'HomeController@contato',
        '/contato.html' => 'HomeController@contato',
        '/cadastro-pcd' => 'PcdController@cadastro',
        '/cadastro-pcd.html' => 'PcdController@cadastro',
        
        '/admin' => 'AuthController@loginForm',
        '/admin.html' => 'AuthController@loginForm',
        '/admin/logout' => 'AuthController@logout',
        '/admin/dashboard' => 'UsuarioController@dashboard',
        
        '/admin/usuarios' => 'UsuarioController@index',
        '/admin/usuarios/create' => 'UsuarioController@create',
        '/admin/usuarios/edit' => 'UsuarioController@edit',
        '/admin/usuarios/delete' => 'UsuarioController@delete',
        '/admin/conteudos' => 'ConteudoController@index',
        '/admin/conteudos/noticias' => 'ConteudoController@indexNoticias',
        '/admin/conteudos/noticias/create' => 'ConteudoController@createNoticia',
        '/admin/conteudos/noticias/edit' => 'ConteudoController@editNoticia',
        '/admin/conteudos/noticias/delete' => 'ConteudoController@deleteNoticia',
        '/admin/conteudos/membros' => 'ConteudoController@indexMembros',
        '/admin/conteudos/membros/create' => 'ConteudoController@createMembro',
        '/admin/conteudos/membros/edit' => 'ConteudoController@editMembro',
        '/admin/conteudos/membros/delete' => 'ConteudoController@deleteMembro',
        '/admin/conteudos/documentos' => 'ConteudoController@indexDocumentos',
        '/admin/conteudos/documentos/create' => 'ConteudoController@createDocumento',
        '/admin/conteudos/documentos/edit' => 'ConteudoController@editDocumento',
        '/admin/conteudos/documentos/delete' => 'ConteudoController@deleteDocumento',
        
        '/admin/mensagens' => 'MensagemController@index',
        '/admin/mensagens/lido' => 'MensagemController@markAsRead',
        '/admin/mensagens/delete' => 'MensagemController@delete',
        '/admin/estatisticas' => 'UsuarioController@estatisticas',
        
        '/admin/pcds' => 'PcdController@index',
        '/admin/pcds/create' => 'PcdController@create',
        '/admin/pcds/edit' => 'PcdController@edit',
        '/admin/pcds/delete' => 'PcdController@delete',
        '/admin/pcds/deferir' => 'PcdController@deferir',
        '/admin/pcds/indeferir' => 'PcdController@indeferir',
      ],
      'POST' => [
        '/cadastro-pcd' => 'PcdController@store',
        '/contato/enviar' => 'HomeController@submitContato',
        '/admin/login' => 'AuthController@login',
        '/admin/usuarios/store' => 'UsuarioController@store',
        '/admin/usuarios/update' => 'UsuarioController@update',
        '/admin/pcds/store' => 'PcdController@storeAdmin',
        '/admin/pcds/update' => 'PcdController@update',
        '/admin/conteudos/noticias/store' => 'ConteudoController@storeNoticia',
        '/admin/conteudos/noticias/update' => 'ConteudoController@updateNoticia',
        '/admin/conteudos/membros/store' => 'ConteudoController@storeMembro',
        '/admin/conteudos/membros/update' => 'ConteudoController@updateMembro',
        '/admin/conteudos/documentos/store' => 'ConteudoController@storeDocumento',
        '/admin/conteudos/documentos/update' => 'ConteudoController@updateDocumento',
        '/admin/mensagens/lido' => 'MensagemController@markAsRead',
        '/admin/mensagens/delete' => 'MensagemController@delete',
    ]
];

// Roteamento
if (isset($routes[$requestMethod][$requestUri])) {
    $action = $routes[$requestMethod][$requestUri];
    list($controllerName, $method) = explode('@', $action);
    
    // Instancia o Controller e executa o método
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $method)) {
            $controller->$method();
        } else {
            throw new Exception("Método $method não encontrado no controller $controllerName.");
        }
    } else {
        throw new Exception("Controller $controllerName não encontrado.");
    }
} else {
    // Rota não encontrada - renderiza 404
    http_response_code(404);
    view('404');
}
