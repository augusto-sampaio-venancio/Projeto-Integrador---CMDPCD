<?php

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            $host = 'localhost';
            $dbname = 'cmpcd_jau';
            $user = 'root';
            // Tenta 'root' e depois '' como senhas padrão comuns
            $passwords = ['root', ''];
            $connected = false;
            $pdo = null;

            foreach ($passwords as $password) {
                try {
                    // Tenta conectar diretamente ao banco de dados
                    $pdo = new PDO(
                        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                        $user,
                        $password,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false,
                        ]
                    );
                    $connected = true;
                    break;
                } catch (PDOException $e) {
                    // Se o erro for de banco não existente, tenta criar o banco
                    if ($e->getCode() == 1049) {
                        try {
                            $tempPdo = new PDO(
                                "mysql:host=$host;charset=utf8mb4",
                                $user,
                                $password,
                                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                            );
                            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            
                            // Conecta novamente após criar
                            $pdo = new PDO(
                                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                                $user,
                                $password,
                                [
                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                    PDO::ATTR_EMULATE_PREPARES => false,
                                ]
                            );
                            $connected = true;
                            break;
                        } catch (PDOException $ex) {
                            // Continua para a próxima senha
                        }
                    }
                }
            }

            if (!$connected) {
                throw new Exception("Não foi possível conectar ao banco de dados MySQL com o usuário root.");
            }

            self::$instance = $pdo;
            self::checkAndSeedDatabase(self::$instance);
        }

        return self::$instance;
    }

    private static function checkAndSeedDatabase($pdo) {
        try {
            // Verifica se a tabela perfis_acesso existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'perfis_acesso'");
            $tableExists = $stmt->rowCount() > 0;

            if (!$tableExists) {
                // Importa o arquivo SQL
                $sqlPath = __DIR__ . '/../database/CMDPCD_PI_BD.sql';
                if (file_exists($sqlPath)) {
                    $sql = file_get_contents($sqlPath);
                    // O arquivo SQL cria a base e dá USE. Como já estamos conectados, podemos apenas executar.
                    // Para garantir compatibilidade com execuções multiplas, separamos as queries por ponto e vírgula
                    // mas limpamos comandos de DROP/CREATE DATABASE que possam dar erro no contexto atual
                    $sql = preg_replace('/DROP DATABASE IF EXISTS.*/i', '', $sql);
                    $sql = preg_replace('/CREATE DATABASE.*/i', '', $sql);
                    $sql = preg_replace('/USE cmpcd_jau.*/i', '', $sql);
                    
                    $pdo->exec($sql);
                }
            }

            // Garante que existam perfis de acesso
            $stmt = $pdo->query("SELECT COUNT(*) FROM perfis_acesso");
            if ($stmt->fetchColumn() == 0) {
                $pdo->exec("INSERT INTO perfis_acesso (id, nome, descricao) VALUES 
                    (1, 'admin_total', 'Administrador com acesso total ao sistema'),
                    (2, 'admin_parcial', 'Administrador com acesso parcial'),
                    (3, 'editor', 'Editor de conteúdos')
                ");
            }

            // Garante que exista pelo menos um usuário administrador padrão
            $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
            if ($stmt->fetchColumn() == 0) {
                $senhaHash = password_hash('admin', PASSWORD_DEFAULT);
                $pdo->exec("INSERT INTO usuarios (perfil_id, nome_completo, cpf, celular, email, senha, status) VALUES 
                    (1, 'Administrador Geral', '12345678901', '14999999999', 'admin@admin.com', '$senhaHash', 'ativo')
                ");
            }

            // Garante que existam alguns tipos de deficiência padrão
            $stmt = $pdo->query("SELECT COUNT(*) FROM tipos_deficiencia");
            if ($stmt->fetchColumn() == 0) {
                $pdo->exec("INSERT INTO tipos_deficiencia (nome, descricao) VALUES 
                    ('Física', 'Alteração completa ou parcial de um ou mais segmentos do corpo humano'),
                    ('Auditiva', 'Perda bilateral, parcial ou total de quarenta decibéis (dB) ou mais'),
                    ('Visual', 'Cegueira, baixa visão ou visão monocular'),
                    ('Intelectual', 'Funcionamento intelectual significativamente inferior à média'),
                    ('Múltipla', 'Associação de duas ou mais deficiências'),
                    ('TEA', 'Transtorno do Espectro Autista'),
                    ('Outros', 'Outras condições não listadas')
                ");
            }

        } catch (Exception $e) {
            // Silencia ou registra erros de seed para não travar a aplicação se o banco já estiver parcialmente pronto
            error_log("Seed Error: " . $e->getMessage());
        }
    }
}
