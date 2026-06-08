<?php

class PcdController {
    private $pcdModel;

    public function __construct() {
        $this->pcdModel = new Pcd();
    }

    /**
     * Exibe o formulário de cadastro público (wizard).
     */
    public function cadastro() {
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        $tiposDeficiencia = $this->pcdModel->getTiposDeficiencia();
        view('cadastro-pcd', ['tiposDeficiencia' => $tiposDeficiencia]);
    }

    /**
     * Salva o cadastro do PCD vindo do formulário público (via AJAX Fetch ou POST).
     */
    public function store() {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
        
        $isJson = (strpos($accept, 'application/json') !== false) || (strpos($contentType, 'application/json') !== false);
        
        if (strpos($contentType, "application/json") !== false) {
            $content = trim(file_get_contents("php://input"));
            $data = json_decode($content, true);
        } else {
            $data = $_POST;
        }
        
        if ($isJson) {
            header('Content-Type: application/json');
            
            if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['data_nascimento'])) {
                echo json_encode(['success' => false, 'message' => 'Nome, CPF e data de nascimento são obrigatórios.']);
                exit;
            }

            if ($this->pcdModel->existsCpf($data['cpf'])) {
                echo json_encode(['success' => false, 'message' => 'Este CPF já está cadastrado.']);
                exit;
            }

            // Validar arquivos obrigatórios no cadastro público
            $requiredFiles = ['doc_rg', 'doc_residencia', 'doc_laudo'];
            foreach ($requiredFiles as $fileKey) {
                if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['success' => false, 'message' => 'Os documentos anexados (RG/CNH, Comprovante de Residência e Laudo Médico) são obrigatórios.']);
                    exit;
                }
            }

            try {
                // Parser robusto para deficiências
                $defIds = [];
                if (isset($data['deficiencias_ids'])) {
                    $defIds = $data['deficiencias_ids'];
                } elseif (isset($data['deficiencia_id'])) {
                    $defIds = $data['deficiencia_id'];
                } elseif (isset($data['deficiencias'])) {
                    $defIds = $data['deficiencias'];
                }
                if (!is_array($defIds)) {
                    if (empty($defIds)) {
                        $defIds = [];
                    } elseif (is_string($defIds) && strpos($defIds, ',') !== false) {
                        $defIds = array_map('trim', explode(',', $defIds));
                    } else {
                        $defIds = [$defIds];
                    }
                }
                $data['deficiencias_ids'] = array_filter(array_map('intval', $defIds));
                $data['status'] = 'pendente';
                
                $pcdId = $this->pcdModel->create($data);
                $this->handleFileUploads($pcdId, null);
                
                echo json_encode(['success' => true]);
                exit;
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar cadastro: ' . $e->getMessage()]);
                exit;
            }
        } else {
            // Caso seja submetido como POST padrão
            if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['data_nascimento'])) {
                $_SESSION['error_msg'] = "Nome, CPF e data de nascimento são obrigatórios.";
                redirect('/cadastro-pcd');
            }
            if ($this->pcdModel->existsCpf($data['cpf'])) {
                $_SESSION['error_msg'] = "Este CPF já está cadastrado.";
                redirect('/cadastro-pcd');
            }

            // Validar arquivos obrigatórios no cadastro público padrão
            $requiredFiles = ['doc_rg', 'doc_residencia', 'doc_laudo'];
            foreach ($requiredFiles as $fileKey) {
                if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['error_msg'] = "Os documentos anexados (RG/CNH, Comprovante de Residência e Laudo Médico) são obrigatórios.";
                    redirect('/cadastro-pcd');
                }
            }
            try {
                // Parser robusto para deficiências
                $defIds = [];
                if (isset($data['deficiencias_ids'])) {
                    $defIds = $data['deficiencias_ids'];
                } elseif (isset($data['deficiencia_id'])) {
                    $defIds = $data['deficiencia_id'];
                } elseif (isset($data['deficiencias'])) {
                    $defIds = $data['deficiencias'];
                }
                if (!is_array($defIds)) {
                    if (empty($defIds)) {
                        $defIds = [];
                    } elseif (is_string($defIds) && strpos($defIds, ',') !== false) {
                        $defIds = array_map('trim', explode(',', $defIds));
                    } else {
                        $defIds = [$defIds];
                    }
                }
                $data['deficiencias_ids'] = array_filter(array_map('intval', $defIds));
                $data['status'] = 'pendente';

                $pcdId = $this->pcdModel->create($data);
                $this->handleFileUploads($pcdId, null);
                redirect('/cadastro-pcd?sucesso=1');
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Erro: " . $e->getMessage();
                redirect('/cadastro-pcd');
            }
        }
    }

    /**
     * Lista de PCDs no painel administrativo.
     */
    public function index() {
        checkPermission(['admin_total', 'admin_parcial']);
        $pcds = $this->pcdModel->all();
        
        $success = $_SESSION['success_msg'] ?? null;
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        view('admin/pcds/index', [
            'activeTab' => 'cadastros',
            'pcds' => $pcds,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Exibe formulário de criação de PCD no painel administrativo.
     */
    public function create() {
        checkPermission(['admin_total']);
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        $tiposDeficiencia = $this->pcdModel->getTiposDeficiencia();
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        view('admin/pcds/create', [
            'activeTab' => 'cadastros',
            'tiposDeficiencia' => $tiposDeficiencia,
            'error' => $error
        ]);
    }

    /**
     * Salva o PCD criado pelo administrador (POST).
     */
    public function storeAdmin() {
        checkPermission(['admin_total']);
        $data = $_POST;

        if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['rg']) || empty($data['data_nascimento'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            redirect('/admin/pcds/create');
        }

        if ($this->pcdModel->existsCpf($data['cpf'])) {
            $_SESSION['error_msg'] = "Este CPF já está cadastrado.";
            redirect('/admin/pcds/create');
        }

        // Validar arquivos obrigatórios no cadastro administrativo
        $requiredFiles = ['doc_rg', 'doc_residencia', 'doc_laudo'];
        foreach ($requiredFiles as $fileKey) {
            if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error_msg'] = "Os documentos anexados (RG/CNH, Comprovante de Residência e Laudo Médico) são obrigatórios.";
                redirect('/admin/pcds/create');
            }
        }

        // Parser robusto para deficiências
        $defIds = [];
        if (isset($data['deficiencias_ids'])) {
            $defIds = $data['deficiencias_ids'];
        } elseif (isset($data['deficiencia_id'])) {
            $defIds = $data['deficiencia_id'];
        } elseif (isset($data['deficiencias'])) {
            $defIds = $data['deficiencias'];
        }
        if (!is_array($defIds)) {
            if (empty($defIds)) {
                $defIds = [];
            } elseif (is_string($defIds) && strpos($defIds, ',') !== false) {
                $defIds = array_map('trim', explode(',', $defIds));
            } else {
                $defIds = [$defIds];
            }
        }
        $data['deficiencias_ids'] = array_filter(array_map('intval', $defIds));
        $data['status'] = 'deferido';

        try {
            $pcdId = $this->pcdModel->create($data);
            
            // Processa uploads vinculando ao administrador logado
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $this->handleFileUploads($pcdId, $userId);
            
            $_SESSION['success_msg'] = "PCD cadastrado com sucesso!";
            redirect('/admin/pcds');
        } catch (Exception $e) {
            $_SESSION['error_msg'] = "Erro ao cadastrar PCD: " . $e->getMessage();
            redirect('/admin/pcds/create');
        }
    }

    /**
     * Exibe formulário de edição de PCD no painel administrativo.
     */
    public function edit() {
        checkPermission(['admin_total', 'admin_parcial']);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('/admin/pcds');
        }

        $pcd = $this->pcdModel->find($id);
        if (!$pcd) {
            $_SESSION['error_msg'] = "PCD não encontrado.";
            redirect('/admin/pcds');
        }

        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        $tiposDeficiencia = $this->pcdModel->getTiposDeficiencia();
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        // Extrai apenas os IDs das deficiências do PCD para facilitar na view
        $pcdDefIds = array_column($pcd['deficiencias'], 'id');

        view('admin/pcds/edit', [
            'activeTab' => 'cadastros',
            'pcd' => $pcd,
            'pcdDefIds' => $pcdDefIds,
            'tiposDeficiencia' => $tiposDeficiencia,
            'error' => $error
        ]);
    }

    /**
     * Salva as edições do PCD (POST).
     */
    public function update() {
        checkPermission(['admin_total']);
        $id = $_POST['id'] ?? null;
        if (!$id) {
            redirect('/admin/pcds');
        }

        $data = $_POST;
        if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['rg']) || empty($data['data_nascimento'])) {
            $_SESSION['error_msg'] = "Preencha todos os campos obrigatórios.";
            redirect('/admin/pcds/edit?id=' . $id);
        }

        if ($this->pcdModel->existsCpf($data['cpf'], $id)) {
            $_SESSION['error_msg'] = "Este CPF já está cadastrado para outro PCD.";
            redirect('/admin/pcds/edit?id=' . $id);
        }

        // Parser robusto para deficiências
        $defIds = [];
        if (isset($data['deficiencias_ids'])) {
            $defIds = $data['deficiencias_ids'];
        } elseif (isset($data['deficiencia_id'])) {
            $defIds = $data['deficiencia_id'];
        } elseif (isset($data['deficiencias'])) {
            $defIds = $data['deficiencias'];
        }
        if (!is_array($defIds)) {
            if (empty($defIds)) {
                $defIds = [];
            } elseif (is_string($defIds) && strpos($defIds, ',') !== false) {
                $defIds = array_map('trim', explode(',', $defIds));
            } else {
                $defIds = [$defIds];
            }
        }
        $data['deficiencias_ids'] = array_filter(array_map('intval', $defIds));
        $existingPcd = $this->pcdModel->find($id);
        $data['status'] = $data['status'] ?? ($existingPcd['status'] ?? 'deferido');

        try {
            $this->pcdModel->update($id, $data);
            
            // Processa uploads vinculando ao administrador logado
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $this->handleFileUploads($id, $userId);
            
            $_SESSION['success_msg'] = "PCD atualizado com sucesso!";
            redirect('/admin/pcds');
        } catch (Exception $e) {
            $_SESSION['error_msg'] = "Erro ao atualizar PCD: " . $e->getMessage();
            redirect('/admin/pcds/edit?id=' . $id);
        }
    }

    /**
     * Remove um PCD (soft delete).
     */
    public function delete() {
        checkPermission(['admin_total']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->pcdModel->delete($id);
                $_SESSION['success_msg'] = "Cadastro de PCD excluído com sucesso!";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Erro ao excluir PCD: " . $e->getMessage();
            }
        }
        redirect('/admin/pcds');
    }

    /**
     * Defere (aprova) um PCD pendente.
     */
    public function deferir() {
        checkPermission(['admin_total']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->pcdModel->updateStatus($id, 'deferido');
                $_SESSION['success_msg'] = "Cadastro de PCD deferido com sucesso!";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Erro ao deferir PCD: " . $e->getMessage();
            }
        }
        redirect('/admin/pcds');
    }

    /**
     * Indefere (rejeita e exclui) um PCD pendente.
     */
    public function indeferir() {
        checkPermission(['admin_total']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->pcdModel->delete($id);
                $_SESSION['success_msg'] = "Cadastro de PCD indeferido e excluído com sucesso!";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Erro ao indeferir PCD: " . $e->getMessage();
            }
        }
        redirect('/admin/pcds');
    }

    /**
     * Processa os uploads de arquivos e os salva no banco de dados.
     */
    private function handleFileUploads($pcdId, $userId) {
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../../public/uploads/';

        // Cria a pasta de uploads se não existir
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filesToProcess = [
            'doc_rg' => 'rg',
            'doc_residencia' => 'comprovante_residencia',
            'doc_laudo' => 'laudo_medico'
        ];

        foreach ($filesToProcess as $fileInputName => $docType) {
            if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$fileInputName];
                
                // Valida tamanho
                if ($file['size'] > $maxSize) {
                    throw new Exception("O arquivo de " . strtoupper($fileInputName) . " excede o limite de 5MB.");
                }

                // Valida extensão
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExtensions)) {
                    throw new Exception("Tipo de arquivo inválido para " . strtoupper($fileInputName) . ". Apenas PDF, JPG, JPEG e PNG são permitidos.");
                }

                // Gera nome único para o arquivo
                $newFileName = 'pcd_doc_' . $docType . '_' . $pcdId . '_' . time() . '_' . uniqid() . '.' . $ext;
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // Se estiver atualizando (edição), marca o documento anterior do mesmo tipo como deletado
                    $this->pcdModel->softDeleteDocumentByType($pcdId, $docType);

                    // Insere o novo documento no banco
                    $dbPath = 'uploads/' . $newFileName;
                    $this->pcdModel->addDocument($pcdId, $userId, $docType, $dbPath);
                } else {
                    throw new Exception("Falha ao mover arquivo de " . strtoupper($fileInputName) . " para o diretório de destino.");
                }
            }
        }
    }
}
