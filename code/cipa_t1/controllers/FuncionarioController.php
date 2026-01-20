<?php

    require_once __DIR__ . "/../models/Funcionario.php";
    require_once __DIR__ . "/../repositories/FuncionarioDAO.php";
    require_once __DIR__ . "/../utils/Util.php";
    require_once __DIR__ . "/../utils/AlertHelper.php";

    class FuncionarioController {

        private $dao;

        public function __construct() {


            if(session_status() === PHP_SESSION_NONE){
                session_start();
            }
            $this->dao = new FuncionarioDAO();
        }

        public function criarFuncionario($requisicao) {
            if($requisicao == "POST"){
                // Validar dados obrigatórios (incluindo código de voto)
                if (empty($_POST['nomeFuncionario']) || empty($_POST['sobrenomeFuncionario']) || 
                    empty($_POST['cpfFuncionario']) || empty($_POST['dataNascimentoFuncionario']) || 
                    empty($_POST['dataContratacaoFuncionario']) || empty($_POST['emailFuncionario']) || 
                    empty($_POST['senhaFuncionario']) || empty($_POST['codigoVotoFuncionario'])) {
                    AlertHelper::erroFuncionario("Todos os campos obrigatórios devem ser preenchidos, incluindo o código de voto.");
                    include "./views/funcionario/cadastrar.php";
                    return;
                }

                // Validar CPF (11 dígitos)
                if (!preg_match('/^[0-9]{11}$/', $_POST['cpfFuncionario'])) {
                    AlertHelper::erroFuncionario("CPF inválido. Digite apenas 11 números.");
                    include "./views/funcionario/cadastrar.php";
                    return;
                }

                // Validar telefone se preenchido
                if (!empty($_POST['telefoneFuncionario']) && !preg_match('/^[0-9]{11}$/', $_POST['telefoneFuncionario'])) {
                    AlertHelper::erroFuncionario("Telefone inválido. Digite apenas 11 números.");
                    include "./views/funcionario/cadastrar.php";
                    return;
                }

                // Validar email
                if (!filter_var($_POST['emailFuncionario'], FILTER_VALIDATE_EMAIL)) {
                    AlertHelper::erroFuncionario("E-mail inválido.");
                    include "./views/funcionario/cadastrar.php";
                    return;
                }

                $func = new Funcionario(
                    $_POST['nomeFuncionario'],
                    $_POST['sobrenomeFuncionario'],
                    $_POST['cpfFuncionario'],
                    $_POST['dataNascimentoFuncionario'],
                    $_POST['dataContratacaoFuncionario'],
                    $_POST['telefoneFuncionario'],
                    $_POST['matriculaFuncionario'],
                    isset($_POST['ativoFuncionario']) ? 1 : 0,
                    isset($_POST['admFuncionario']) ? 1 : 0,
                    $_POST['emailFuncionario'],
                    $_POST['senhaFuncionario'],
                    $_POST['codigoVotoFuncionario'], // Código de voto do formulário
                    0
                );

                // Debug logs
                error_log("DEBUG: Tentando cadastrar funcionário");
                error_log("DEBUG: Código de voto: " . $_POST['codigoVotoFuncionario']);
                error_log("DEBUG: Dados - Nome: " . $_POST['nomeFuncionario'] . ", Email: " . $_POST['emailFuncionario'] . ", CPF: " . $_POST['cpfFuncionario']);

                $respostaBD = $this->dao->inserir($func);
                error_log("DEBUG: Resultado do inserir: " . ($respostaBD ? 'SUCESSO' : 'FALHA'));
                
                if($respostaBD) {
                    // Enviar código de voto por email usando Brevo API apenas se não for administrador
                    $ehAdministrador = isset($_POST['admFuncionario']) ? 1 : 0;
                    
                    if (!$ehAdministrador) {
                        error_log("DEBUG: Tentando enviar email para: " . $_POST['emailFuncionario'] . " - Código: " . $_POST['codigoVotoFuncionario']);
                        
                        require_once __DIR__ . "/../utils/EmailServiceBrevo.php";
                        $emailService = new EmailServiceBrevo();
                        $emailEnviado = $emailService->enviarCodigoVoto($_POST['emailFuncionario'], $_POST['nomeFuncionario'], $_POST['codigoVotoFuncionario']);
                        
                        error_log("DEBUG: Resultado do envio de email: " . ($emailEnviado ? 'SUCESSO' : 'FALHA'));
                        
                        if ($emailEnviado) {
                            AlertHelper::sucessoFuncionario("Funcionário cadastrado com sucesso! Código de voto: " . $_POST['codigoVotoFuncionario'] . " - Enviado para o email: " . $_POST['emailFuncionario']);
                        } else {
                            // Email falhou - mostrar código na tela
                            AlertHelper::erroFuncionario("Funcionário cadastrado com sucesso, mas houve erro ao enviar o email. Código de voto: " . $_POST['codigoVotoFuncionario']);
                        }
                    } else {
                        // Administrador não recebe email com código de voto
                        AlertHelper::sucessoFuncionario("Funcionário administrador cadastrado com sucesso! Administradores não recebem código de voto.");
                    }
                    header("Location: /code/cipa_t1/");
                    exit;
                } else {
                    // Capturar informações detalhadas de erro
                    $errorInfo = $this->dao->getLastError();
                    $mensagemErro = "Erro ao cadastrar funcionário. Verifique os dados e tente novamente.";
                    
                    if ($errorInfo) {
                        $mensagemErro .= "<br><br><strong>Detalhes do erro:</strong><br>";
                        $mensagemErro .= "SQLSTATE: " . htmlspecialchars($errorInfo[0]) . "<br>";
                        $mensagemErro .= "Código: " . htmlspecialchars($errorInfo[1]) . "<br>";
                        $mensagemErro .= "Mensagem: " . htmlspecialchars($errorInfo[2]) . "<br>";
                    }
                    
                    AlertHelper::erroFuncionario($mensagemErro);
                    error_log("DEBUG: Redirecionando para formulário com erro - Falha na inserção no banco");
                    include "./views/funcionario/cadastrar.php";
                }

            }

            if($requisicao == "GET"){            
                
                include "./views/funcionario/cadastrar.php";
            }
        }

        public function buscarFuncionarioPorCpf($requisicao) {
            if($requisicao == "GET") {
                $cpf = $_GET['cpf'] ?? '';
                
                if (empty($cpf)) {
                    AlertHelper::erroFuncionario("CPF não informado para pesquisa.");
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                }
                
                // Validar CPF (11 dígitos)
                if (!preg_match('/^[0-9]{11}$/', $cpf)) {
                    AlertHelper::erroFuncionario("CPF inválido. Digite apenas 11 números.");
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                }
                
                // Buscar funcionário por CPF
                $funcionarioData = $this->dao->buscarPorCpf($cpf);
                
                if($funcionarioData) {
                    $funcionarios = Util::converterArrayFuncionario([$funcionarioData]);
                    $_SESSION['funcionarios'] = $funcionarios;
                    $_SESSION['cpf_pesquisado'] = $cpf; // Guardar CPF pesquisado
                    AlertHelper::sucessoFuncionario("Funcionário encontrado com o CPF: " . preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $cpf));
                } else {
                    $_SESSION['funcionarios'] = [];
                    AlertHelper::erroFuncionario("Nenhum funcionário encontrado com o CPF informado.");
                    $_SESSION['cpf_pesquisado'] = null;
                }
                
                header("Location: /code/cipa_t1/funcionario/listar");
                exit;
            }
        }

        public function buscarTodosFuncionarios($requisicao) {
            if($requisicao == "GET") {
                // Limpar CPF pesquisado ao listar todos
                $_SESSION['cpf_pesquisado'] = null;
                
                $funcionarios = $this->dao->buscarTodos();

                if(!empty($funcionarios)) {
                    $funcionarios = Util::converterArrayFuncionario($funcionarios);
                    
                    // Verificar se há eleição ativa e status de voto
                    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
                    $eleicaoDAO = new EleicaoDAO();
                    $idEleicaoAtiva = $eleicaoDAO->buscarEleicaoAberta();
                    
                    error_log("DEBUG: Eleição ativa encontrada: " . ($idEleicaoAtiva ? 'SIM' : 'NÃO'));
                    if($idEleicaoAtiva) {
                        error_log("DEBUG: ID Eleição Ativa: " . $idEleicaoAtiva['id_eleicao']);
                    }
                    
                    if($idEleicaoAtiva) {
                        require_once __DIR__ . "/../repositories/VotoDAO.php";
                        $votoDAO = new VotoDAO();
                        $idEleicao = (int)$idEleicaoAtiva['id_eleicao'];
                        
                        error_log("DEBUG: Verificando status de voto para " . count($funcionarios) . " funcionários");
                        
                        // Marcar status de voto para cada funcionário
                        foreach($funcionarios as $funcionario) {
                            $idFuncionario = $funcionario->getIdFuncionario();
                            $jaVotou = $votoDAO->funcionarioJaVotou($idFuncionario, $idEleicao);
                            $funcionario->setJaVotou($jaVotou);
                            error_log("DEBUG: Funcionário $idFuncionario - Status: " . ($jaVotou ? 'JÁ VOTOU' : 'NÃO VOTOU'));
                        }
                    } else {
                        error_log("DEBUG: Não há eleição ativa, marcando todos como não votaram");
                        // Se não há eleição ativa, marcar todos como não votaram
                        foreach($funcionarios as $funcionario) {
                            $funcionario->setJaVotou(false);
                        }
                    }
                    
                    $_SESSION['funcionarios'] = $funcionarios;
                    include "./views/funcionario/lista.php";
                }
                else{
                    $_SESSION['funcionarios'] = [];
                    include "./views/funcionario/lista.php";
                }
            }
        }              

        public function deletarFuncionario($requisicao) {
            if ($requisicao == "GET") {
                $resultado = $this->dao->deletar($_GET['id']);
                
                if ($resultado['sucesso']) {
                    AlertHelper::sucessoFuncionario("Funcionário excluído com sucesso!");
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                } else {
                    AlertHelper::erroFuncionario($resultado['motivo']);
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                }
            }
        }

        public function deletarFuncionarioForcado($requisicao) {
            if ($requisicao == "GET") {
                // Verificar se é administrador
                if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                    AlertHelper::erro("Apenas administradores podem usar a exclusão forçada.");
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                }
                
                $resultado = $this->dao->deletarForcado($_GET['id']);
                
                if ($resultado['sucesso']) {
                    $mensagem = "Funcionário excluído com sucesso!";
                    if ($resultado['votos_removidos'] > 0) {
                        $mensagem .= " ({$resultado['votos_removidos']} voto(s) removido(s))";
                    }
                    if ($resultado['candidaturas_removidas'] > 0) {
                        $mensagem .= " ({$resultado['candidaturas_removidas']} candidatura(s) removida(s))";
                    }
                    AlertHelper::sucessoFuncionario($mensagem);
                } else {
                    AlertHelper::erroFuncionario($resultado['motivo']);
                }
                header("Location: /code/cipa_t1/funcionario/listar");
                exit;
            }
        }


        public function cadastrarPorMatricula($requisicao) {
            if ($requisicao == "GET") {
                include "./views/funcionario/cadastrar_por_matricula.php";
            }

            if ($requisicao == "POST") {
                $funcionarioData = null;
                $acao = $_POST['acao'] ?? '';
                
                // Buscar por CPF apenas
                if ($acao === 'buscar_cpf' && !empty($_POST['cpfFuncionario'])) {
                    $funcionarioData = $this->dao->buscarPorCpf($_POST['cpfFuncionario']);
                }
                // Buscar por Matrícula apenas
                elseif ($acao === 'buscar_matricula' && !empty($_POST['matriculaFuncionario'])) {
                    $funcionarioData = $this->dao->buscarPorMatricula($_POST['matriculaFuncionario']);
                }
                // Buscar por ambos (comportamento original)
                elseif ($acao === 'buscar_ambos' && !empty($_POST['cpfFuncionario']) && !empty($_POST['matriculaFuncionario'])) {
                    $funcionarioData = $this->dao->buscarPorMatriculaECpf(
                        $_POST['matriculaFuncionario'],
                        $_POST['cpfFuncionario']
                    );
                }
                // Se nenhum campo foi preenchido ou ação inválida
                else {
                    $_SESSION['erro_matricula'] = "Preencha pelo menos um dos campos e selecione uma ação de busca.";
                    include "./views/funcionario/cadastrar_por_matricula.php";
                    return;
                }

                if ($funcionarioData) {
                    $funcionario = Util::converterArrayFuncionario([$funcionarioData])[0];
                    $_SESSION['funcionario_editar'] = $funcionario;
                    header("Location: /code/cipa_t1/funcionario/editar?id=" . $funcionario->getIdFuncionario());
                    exit;
                } else {
                    $_SESSION['erro_matricula'] = "Funcionário não encontrado com os dados informados.";
                    include "./views/funcionario/cadastrar_por_matricula.php";
                }
            }
        }

        public function editarFuncionario($requisicao) {
            if ($requisicao == "POST") {
                $func = new Funcionario(
                    $_POST['nomeFuncionario'],
                    $_POST['sobrenomeFuncionario'],
                    $_POST['cpfFuncionario'],
                    $_POST['dataNascimentoFuncionario'],
                    $_POST['dataContratacaoFuncionario'],
                    $_POST['telefoneFuncionario'],
                    $_POST['matriculaFuncionario'],
                    isset($_POST['ativoFuncionario']) ? 1 : 0,
                    isset($_POST['admFuncionario']) ? 1 : 0,
                    $_POST['emailFuncionario'],
                    $_POST['senhaFuncionario'], // Sempre usa a senha do formulário
                    $_POST['codigoVotoFuncionario'] ?? '', // Código de voto do formulário
                    $_POST['idFuncionario']
                    
                );
        
                $respostaBD = $this->dao->atualizar($func);
        
                if ($respostaBD) {
                    AlertHelper::sucessoFuncionario("Funcionário atualizado com sucesso!");
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                } else {
                    AlertHelper::erroFuncionario("Erro ao atualizar funcionário. Tente novamente.");
                    header("Location: /code/cipa_t1/funcionario/listar");
                    exit;
                }
            }
        
            if ($requisicao == "GET") {
                // Buscar funcionário do banco de dados
                $funcionarioData = $this->dao->buscarPorId($_GET['id']);
                if ($funcionarioData) {
                    $funcionario = Util::converterArrayFuncionario([$funcionarioData])[0];
                    $_SESSION['funcionario_editar'] = $funcionario;
                }
                include "./views/funcionario/editar.php";
            }
        }

        public function alterarSenha($requisicao) {
            if ($requisicao == "GET") {
                include "./views/funcionario/alterar-senha.php";
            }

            if ($requisicao == "POST") {
                $senhaAtual = $_POST['senhaAtual'] ?? '';
                $novaSenha = $_POST['novaSenha'] ?? '';
                $confirmarSenha = $_POST['confirmarSenha'] ?? '';

                // Validar campos
                if (empty($senhaAtual) || empty($novaSenha) || empty($confirmarSenha)) {
                    $_SESSION['erro_senha'] = "Todos os campos são obrigatórios.";
                    include "./views/funcionario/alterar-senha.php";
                    return;
                }

                // Validar senhas coincidem
                if ($novaSenha !== $confirmarSenha) {
                    $_SESSION['erro_senha'] = "A nova senha e a confirmação não coincidem.";
                    include "./views/funcionario/alterar-senha.php";
                    return;
                }

                // Validar tamanho mínimo
                if (strlen($novaSenha) < 8) {
                    $_SESSION['erro_senha'] = "A nova senha deve ter pelo menos 8 caracteres.";
                    include "./views/funcionario/alterar-senha.php";
                    return;
                }

                // Buscar dados atuais do funcionário
                $funcionarioLogado = $_SESSION['funcionario_logado'];
                $funcionarioData = $this->dao->buscarPorId($funcionarioLogado['id_funcionario']);

                if (!$funcionarioData) {
                    $_SESSION['erro_senha'] = "Funcionário não encontrado.";
                    include "./views/funcionario/alterar-senha.php";
                    return;
                }

                // Verificar senha atual
                // Verifica se a senha no banco é um hash (começa com $2y$) ou texto puro
                if (strpos($funcionarioData['senha_funcionario'], '$2y$') === 0) {
                    // Senha hasheada - usar password_verify
                    if (!password_verify($senhaAtual, $funcionarioData['senha_funcionario'])) {
                        $_SESSION['erro_senha'] = "Senha atual incorreta.";
                        include "./views/funcionario/alterar-senha.php";
                        return;
                    }
                } else {
                    // Senha em texto puro - comparação direta (compatibilidade com dados antigos)
                    if ($funcionarioData['senha_funcionario'] !== $senhaAtual) {
                        $_SESSION['erro_senha'] = "Senha atual incorreta.";
                        include "./views/funcionario/alterar-senha.php";
                        return;
                    }
                }

                // Atualizar senha
                $funcionario = new Funcionario(
                    $funcionarioData['nome_funcionario'],
                    $funcionarioData['sobrenome_funcionario'],
                    $funcionarioData['cpf_funcionario'],
                    $funcionarioData['data_nascimento_funcionario'],
                    $funcionarioData['data_contratacao_funcionario'],
                    $funcionarioData['telefone_funcionario'],
                    $funcionarioData['matricula_funcionario'],
                    $funcionarioData['ativo_funcionario'],
                    $funcionarioData['adm_funcionario'],
                    $funcionarioData['email_funcionario'],
                    $novaSenha,
                    $funcionarioData['cod_voto_funcionario'],
                    $funcionarioData['id_funcionario']
                );

                $respostaBD = $this->dao->atualizar($funcionario);

                if ($respostaBD) {
                    // Atualizar sessão
                    $_SESSION['funcionario_logado']['senha_funcionario'] = $novaSenha;
                    
                    AlertHelper::sucessoFuncionario("Senha alterada com sucesso!");
                    header("Location: /code/cipa_t1/funcionario/home");
                    exit;
                } else {
                    $_SESSION['erro_senha'] = "Erro ao alterar senha. Tente novamente.";
                    include "./views/funcionario/alterar-senha.php";
                }
            }
        }
    }

?>