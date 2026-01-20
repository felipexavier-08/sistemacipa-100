<?php
    require_once __DIR__ . "/../repositories/FuncionarioDAO.php";

    class LoginController {
        private ?FuncionarioDAO $funcionarioDAO;

        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->funcionarioDAO = new FuncionarioDAO();
        }

        public function login($requisicao) {
            if ($requisicao === "GET") {
                // Se já estiver logado, redirecionar
                if (isset($_SESSION['funcionario_logado'])) {
                    $this->redirecionarPorTipo();
                    exit;
                }
                include "./views/login/login.php";
            }

            if ($requisicao === "POST") {
                $cpf = $_POST['cpf'] ?? '';
                $senha = $_POST['senha'] ?? '';

                if (empty($cpf) || empty($senha)) {
                    $_SESSION['erro_login'] = "CPF e senha são obrigatórios.";
                    include "./views/login/login.php";
                    return;
                }

                // Buscar funcionário por CPF
                $funcionario = $this->funcionarioDAO->buscarPorCpf($cpf);

                if (!$funcionario) {
                    $_SESSION['erro_login'] = "CPF ou senha inválidos.";
                    include "./views/login/login.php";
                    return;
                }

                // Verificar se está ativo
                if ($funcionario['ativo_funcionario'] != 1) {
                    $_SESSION['erro_login'] = "Funcionário inativo. Entre em contato com o administrador.";
                    include "./views/login/login.php";
                    return;
                }

                // Verificar senha
                // Verifica se a senha no banco é um hash (começa com $2y$) ou texto puro
                if (strpos($funcionario['senha_funcionario'], '$2y$') === 0) {
                    // Senha hasheada - usar password_verify
                    if (!password_verify($senha, $funcionario['senha_funcionario'])) {
                        $_SESSION['erro_login'] = "CPF ou senha inválidos.";
                        include "./views/login/login.php";
                        return;
                    }
                } else {
                    // Senha em texto puro - comparação direta (compatibilidade com dados antigos)
                    if ($funcionario['senha_funcionario'] !== $senha) {
                        $_SESSION['erro_login'] = "CPF ou senha inválidos.";
                        include "./views/login/login.php";
                        return;
                    }
                }

                // Login bem-sucedido
                $_SESSION['funcionario_logado'] = $funcionario;
                unset($_SESSION['erro_login']);

                $this->redirecionarPorTipo();
            }
        }

        private function redirecionarPorTipo() {
            $funcionario = $_SESSION['funcionario_logado'];
            
            if ($funcionario['adm_funcionario'] == 1) {
                // Admin vai para página inicial administrativa
                header("Location: /code/cipa_t1/");
                exit;
            } else {
                // Funcionário vai para página inicial do funcionário
                header("Location: /code/cipa_t1/funcionario/home");
                exit;
            }
        }

        public function logout() {
            session_destroy();
            header("Location: /code/cipa_t1/login");
            exit;
        }
    }
