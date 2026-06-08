<?php

class HomeController {
    public function index() {
        $noticiaModel = new Noticia();
        $pcdModel = new Pcd();
        
        $noticias = $noticiaModel->allPublished();
        $ultimasNoticias = array_slice($noticias, 0, 3);
        
        $stats = $pcdModel->getHomeStats();

        // Carrega publicações do Instagram cacheado
        $cacheFile = __DIR__ . '/../../config/instagram_cache.json';
        $instagramPosts = [];
        if (file_exists($cacheFile)) {
            $instagramPosts = json_decode(file_get_contents($cacheFile), true);
        }
        
        view('home', [
            'ultimasNoticias' => $ultimasNoticias,
            'stats' => $stats,
            'instagramPosts' => $instagramPosts
        ]);
    }

    public function quemSomos() {
        $membroModel = new MembroConselho();
        $membrosAtuais = $membroModel->allActive();
        $presidentesHistoricos = $membroModel->allPresidents();
        view('quem-somos', [
            'membrosAtuais' => $membrosAtuais,
            'presidentesHistoricos' => $presidentesHistoricos
        ]);
    }

    public function iniciativas() {
        view('iniciativas');
    }

    public function noticias() {
        $noticiaModel = new Noticia();
        $noticias = $noticiaModel->allPublished();
        view('noticias', [
            'noticias' => $noticias
        ]);
    }

    public function transparencia() {
        $docModel = new DocumentoPublico();
        $membroModel = new MembroConselho();
        
        $documentos = $docModel->allPublished();
        $membrosAtuais = $membroModel->allActive();
        
        view('transparencia', [
            'documentos' => $documentos,
            'membrosAtuais' => $membrosAtuais
        ]);
    }

    public function apoio() {
        view('apoio');
    }

    public function contato() {
        view('contato');
    }

    public function submitContato() {
        header('Content-Type: application/json');
        
        $data = [
            'nome' => trim($_POST['nome'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'assunto' => trim($_POST['assunto'] ?? ''),
            'mensagem' => trim($_POST['mensagem'] ?? '')
        ];

        if (empty($data['nome']) || empty($data['email']) || empty($data['assunto']) || empty($data['mensagem'])) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
            exit;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'O endereço de e-mail informado é inválido.']);
            exit;
        }

        try {
            $contatoModel = new Contato();
            $contatoModel->create($data);
            echo json_encode(['status' => 'success', 'message' => 'Sua mensagem foi enviada com sucesso!']);
            exit;
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar a mensagem: ' . $e->getMessage()]);
            exit;
        }
    }
}
