<?php

// Definir fuso horário do Brasil
date_default_timezone_set('America/Sao_Paulo');

    require_once __DIR__ . "/../models/Voto.php";
    require_once __DIR__ . "/../repositories/VotoDAO.php";
    require_once __DIR__ . "/../repositories/FuncionarioDAO.php";
    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
    require_once __DIR__ . "/../utils/Util.php";
    require_once __DIR__ . "/../utils/AlertHelper.php";

    class VotoController {
        private ?VotoDAO $votoDAO;
        private ?CandidatoDAO $candidatoDAO;
        private ?EleicaoDAO $eleicaoDAO;
        private ?FuncionarioDAO $funcionarioDAO;

        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->votoDAO = new VotoDAO();
            $this->candidatoDAO = new CandidatoDAO();
            $this->eleicaoDAO = new EleicaoDAO();
            $this->funcionarioDAO = new FuncionarioDAO();
        }

        public function votar($requisicao) {
            if ($requisicao === "GET") {
                // Verificar se funcionário está logado
                if (!isset($_SESSION['funcionario_logado'])) {
                    $_SESSION['erro_voto'] = "Você precisa estar logado para acessar a votação.";
                    header("Location: /code/cipa_t1/login");
                    exit;
                }

                // Verificar se é administrador (administradores não podem votar)
                if ($_SESSION['funcionario_logado']['adm_funcionario'] == 1) {
                    $_SESSION['erro_voto'] = "Administradores não podem acessar a página de votação.";
                    header("Location: /code/cipa_t1/funcionario/home");
                    exit;
                }

                // Buscar eleição aberta
                $eleicaoAberta = $this->eleicaoDAO->buscarEleicaoAberta();
                
                if (!$eleicaoAberta) {
                    $_SESSION['erro_voto'] = "Não há eleição aberta no momento.";
                    include "./views/voto/erro.php";
                    return;
                }

                $idEleicao = $eleicaoAberta['id_eleicao'];

                // Verificar se votação está autorizada
                if (!$this->eleicaoDAO->votacaoAutorizada($idEleicao)) {
                    $_SESSION['erro_voto'] = "A votação ainda não foi autorizada pelo administrador. Aguarde o início do período de votação.";
                    include "./views/voto/erro.php";
                    return;
                }

                // Buscar candidatos da eleição
                $candidatos = $this->candidatoDAO->buscarPorEleicao($idEleicao);
                $eleicao = $this->eleicaoDAO->buscarPorId($idEleicao);
                
                $_SESSION['candidatos_votacao'] = $candidatos;
                $_SESSION['eleicao_votacao'] = $eleicao;
                $_SESSION['id_eleicao_votacao'] = $idEleicao;
                
                // Verificar se há candidato pré-selecionado na URL
                $candidatoPreSelecionado = null;
                if (isset($_GET['candidato'])) {
                    $numeroCandidato = $_GET['candidato'];
                    // Buscar os dados do candidato pré-selecionado
                    foreach ($candidatos as $candidato) {
                        if ($candidato['numero_candidato'] == $numeroCandidato) {
                            $candidatoPreSelecionado = $candidato;
                            break;
                        }
                    }
                }
                
                // Guardar candidato pré-selecionado na sessão
                $_SESSION['candidato_pre_selecionado'] = $candidatoPreSelecionado;
                
                include "./views/voto/votar.php";
            }

            if ($requisicao === "POST") {
                // Verificar se funcionário está logado
                if (!isset($_SESSION['funcionario_logado'])) {
                    $_SESSION['erro_voto'] = "Você precisa estar logado para votar.";
                    header("Location: /code/cipa_t1/login");
                    exit;
                }

                // Verificar se é administrador (administradores não podem votar)
                if ($_SESSION['funcionario_logado']['adm_funcionario'] == 1) {
                    $_SESSION['erro_voto'] = "Administradores não podem votar nas eleições.";
                    header("Location: /code/cipa_t1/funcionario/home");
                    exit;
                }

                $idFuncionario = $_SESSION['funcionario_logado']['id_funcionario'];
                $idEleicao = $_POST['idEleicao'];
                
                // Validar código de voto
                if (!isset($_POST['codigoVoto']) || empty(trim($_POST['codigoVoto']))) {
                    $_SESSION['erro_voto'] = "Digite seu código de voto para continuar.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }
                
                $codigoVotoDigitado = strtoupper(trim($_POST['codigoVoto']));
                $codigoVotoFuncionario = $_SESSION['funcionario_logado']['cod_voto_funcionario'] ?? '';
                
                if ($codigoVotoDigitado !== $codigoVotoFuncionario) {
                    $_SESSION['erro_voto'] = "Código de voto inválido. Verifique o código que você recebeu.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }
                
                // Verificar se eleição está realmente aberta
                $eleicaoStatus = $this->eleicaoDAO->buscarPorId($idEleicao);
                error_log("DEBUG: Verificando status da eleição - idEleicao: $idEleicao, status: " . ($eleicaoStatus['status_eleicao'] ?? 'NÃO ENCONTRADO') . ", data_fim: " . ($eleicaoStatus['data_fim_eleicao'] ?? 'NÃO ENCONTRADO'));

                if (!$eleicaoStatus || $eleicaoStatus['status_eleicao'] !== 'ABERTA') {
                    $_SESSION['erro_voto'] = "Esta eleição não está mais aberta para votação.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }

                // Verificar se votação está autorizada
                if (!$this->eleicaoDAO->votacaoAutorizada($idEleicao)) {
                    $_SESSION['erro_voto'] = "A votação não foi autorizada pelo administrador.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }

                // Verificar se já votou
                if ($this->votoDAO->funcionarioJaVotou($idFuncionario, $idEleicao)) {
                    $_SESSION['erro_voto'] = "Você já votou nesta eleição.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }

                // Processar voto
                if (isset($_POST['numeroCandidato']) && !empty($_POST['numeroCandidato'])) {
                    // Voto em candidato
                    $idCandidato = $this->candidatoDAO->buscarPorNumero($idEleicao, $_POST['numeroCandidato']);
                    
                    error_log("DEBUG: Buscando candidato - idEleicao: $idEleicao, numeroCandidato: " . $_POST['numeroCandidato'] . ", resultado: " . ($idCandidato ? 'ENCONTRADO' : 'NÃO ENCONTRADO'));
                    
                    if (!$idCandidato) {
                        $_SESSION['erro_voto'] = "Número de candidato inválido.";
                        header("Location: /code/cipa_t1/voto/votar");
                        exit;
                    }

                    // Registrar o voto
                    error_log("Tentando registrar voto - idFuncionario: $idFuncionario, idCandidato: $idCandidato");
                    $resultado = $this->votoDAO->registrarVoto($idFuncionario, $idCandidato);
                    error_log("Resultado do registro de voto: " . ($resultado ? 'SUCESSO' : 'FALHA'));
                } elseif (isset($_POST['tipoVoto']) && in_array($_POST['tipoVoto'], ['BRANCO', 'NULO'])) {
                    // Voto branco ou nulo
                    $resultado = $this->votoDAO->registrarVotoBrancoOuNulo($idFuncionario, $idEleicao, $_POST['tipoVoto']);
                } else {
                    $_SESSION['erro_voto'] = "Selecione um candidato ou voto branco/nulo.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }

                if ($resultado) {
                    unset($_SESSION['erro_voto']);
                    $_SESSION['sucesso_voto'] = "Voto registrado com sucesso!";
                    
                    // Adicionar informações do comprovante
                    $_SESSION['comprovante_voto'] = [
                        'id_eleicao' => $idEleicao,
                        'id_funcionario' => $idFuncionario,
                        'nome_funcionario' => $_SESSION['funcionario_logado']['nome_funcionario'] ?? 'N/A',
                        'cpf_funcionario' => $_SESSION['funcionario_logado']['cpf_funcionario'] ?? 'N/A',
                        'codigo_voto' => $_SESSION['funcionario_logado']['cod_voto_funcionario'] ?? 'N/A',
                        'data_voto' => date('d/m/Y H:i:s'),
                        'tipo_voto' => isset($_POST['numeroCandidato']) ? 'Voto Registrado' : (isset($_POST['tipoVoto']) ? 'Voto ' . $_POST['tipoVoto'] : 'Voto Registrado')
                    ];
                    
                    // Enviar comprovante por email via API Brevo
                    $this->enviarComprovantePorEmailBrevo($idEleicao, $idFuncionario);
                    
                    header("Location: /code/cipa_t1/voto/sucesso");
                    exit;
                } else {
                    $_SESSION['erro_voto'] = "Erro ao registrar voto. Tente novamente.";
                    header("Location: /code/cipa_t1/voto/votar");
                    exit;
                }
            }
        }

        public function listarCandidatos($requisicao) {
            if ($requisicao === "GET") {
                // Buscar eleição ativa
                $eleicaoAberta = $this->eleicaoDAO->buscarEleicaoAberta();
                
                if (!$eleicaoAberta) {
                    $_SESSION['erro_voto'] = "Não há eleição ativa no momento.";
                    include "./views/voto/erro.php";
                    return;
                }

                $idEleicao = $eleicaoAberta['id_eleicao'];

                // Verificar se é administrador - admins podem ver candidatos sempre
                $isAdmin = isset($_SESSION['funcionario_logado']) && $_SESSION['funcionario_logado']['adm_funcionario'] == 1;
                
                // Para não-admins, verificar se votação está autorizada
                if (!$isAdmin && !$this->eleicaoDAO->votacaoAutorizada($idEleicao)) {
                    $_SESSION['erro_voto'] = "Os candidatos só poderão ser visualizados após a autorização da votação pelo administrador.";
                    include "./views/voto/erro.php";
                    return;
                }

                $candidatos = $this->candidatoDAO->buscarPorEleicao($idEleicao);
                $eleicao = $this->eleicaoDAO->buscarPorId($idEleicao);
                
                $_SESSION['candidatos_lista'] = $candidatos;
                $_SESSION['eleicao_lista'] = $eleicao;

                include "./views/voto/listar_candidatos.php";
            }
        }

        public function sucesso() {
            include "./views/voto/sucesso.php";
        }

        /**
         * Reimprimir comprovante de voto
         */
        public function reimprimirComprovante() {
            try {
                $funcionarioLogado = $_SESSION['funcionario_logado'];
                $idFuncionario = $funcionarioLogado['id_funcionario'];
                
                // Buscar eleição ativa
                $eleicaoAtiva = $this->eleicaoDAO->buscarEleicaoAberta();
                
                if (!$eleicaoAtiva) {
                    $_SESSION['erro_voto'] = "Não há eleição ativa no momento.";
                    header("Location: /code/cipa_t1/funcionario/home");
                    exit;
                }
                
                // Verificar se o funcionário votou nesta eleição
                $jaVotou = $this->votoDAO->funcionarioJaVotou($idFuncionario, $eleicaoAtiva['id_eleicao']);
                
                if (!$jaVotou) {
                    $_SESSION['erro_voto'] = "Você ainda não votou nesta eleição.";
                    header("Location: /code/cipa_t1/funcionario/home");
                    exit;
                }
                
                // Buscar dados da eleição
                $eleicao = $this->eleicaoDAO->buscarPorId($eleicaoAtiva['id_eleicao']);
                
                // Criar comprovante
                $_SESSION['comprovante_voto'] = [
                    'id_eleicao' => $eleicaoAtiva['id_eleicao'],
                    'id_funcionario' => $idFuncionario,
                    'nome_funcionario' => $funcionarioLogado['nome_funcionario'] ?? 'N/A',
                    'cpf_funcionario' => $funcionarioLogado['cpf_funcionario'] ?? 'N/A',
                    'codigo_voto' => $funcionarioLogado['cod_voto_funcionario'] ?? 'N/A',
                    'data_voto' => date('d/m/Y H:i:s'),
                    'tipo_voto' => 'Voto Registrado',
                    'titulo_documento' => $eleicao['titulo_documento'] ?? 'N/A',
                    'data_inicio_eleicao' => $eleicao['data_inicio_eleicao'] ?? 'N/A',
                    'data_fim_eleicao' => $eleicao['data_fim_eleicao'] ?? 'N/A'
                ];
                
                // Redirecionar para página de sucesso
                header("Location: /code/cipa_t1/voto/sucesso");
                exit;
                
            } catch (Exception $e) {
                $_SESSION['erro_voto'] = "Erro ao gerar comprovante: " . $e->getMessage();
                header("Location: /code/cipa_t1/funcionario/home");
                exit;
            }
        }
        
        /**
         * Envia comprovante de voto por email via API Brevo
         */
        private function enviarComprovantePorEmailBrevo($idEleicao, $idFuncionario) {
            try {
                // Carregar serviço de email Brevo
                require_once __DIR__ . "/../utils/EmailServiceBrevo.php";
                $emailService = new EmailServiceBrevo();
                
                // Obter dados do funcionário
                $funcionario = $this->funcionarioDAO->buscarPorId($idFuncionario);
                
                // Obter dados da eleição
                $eleicao = $this->eleicaoDAO->buscarPorId($idEleicao);
                
                // Verificar se funcionário tem email
                if (empty($funcionario['email_funcionario'])) {
                    error_log("EMAIL BREVO: Funcionário {$idFuncionario} não tem email cadastrado");
                    return false;
                }
                
                // Preparar dados do comprovante
                $dadosComprovante = [
                    'eleicao' => $eleicao['titulo_documento'] ?? 'Eleição CIPA',
                    'data_voto' => date('d/m/Y H:i:s'),
                    'codigo_voto' => $funcionario['cod_voto_funcionario'] ?? 'N/A'
                ];
                
                // Enviar email via API Brevo
                $enviado = $emailService->enviarComprovanteVoto(
                    $funcionario['email_funcionario'],
                    $funcionario['nome_funcionario'],
                    $dadosComprovante
                );
                
                if ($enviado) {
                    $_SESSION['sucesso_email'] = "Comprovante enviado para seu email!";
                    error_log("EMAIL BREVO: Sucesso ao enviar para " . $funcionario['email_funcionario']);
                } else {
                    error_log("EMAIL BREVO: Falha ao enviar para " . $funcionario['email_funcionario']);
                }
                
                return $enviado;
                
            } catch (Exception $e) {
                error_log("EMAIL BREVO ERROR: " . $e->getMessage());
                return false;
            }
        }

    }

?>
