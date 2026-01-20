<?php
    require_once __DIR__ . "/../models/Eleicao.php";
    require_once __DIR__ . "/../models/Documento.php";
    require_once __DIR__ . "/../repositories/DocumentoDAO.php";
    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
    require_once __DIR__ . "/../utils/Util.php";

    class EleicaoController {
        private ?DocumentoDAO $documentoDAO;
        private ?EleicaoDAO $eleicaoDAO;

        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->documentoDAO = new DocumentoDAO();
            $this->eleicaoDAO = new EleicaoDAO();
        }

        public function inserirEleicao($requisicao) {
            if ($requisicao === "GET") {
                // Verificar se existe eleição aberta ou finalizada recentemente
                $eleicaoAberta = $this->eleicaoDAO->buscarEleicaoAberta();
                $eleicoesFinalizadas = $this->eleicaoDAO->buscarEleicoesFinalizadas();
                
                // Priorizar eleição aberta, senão verificar finalizadas
                if ($eleicaoAberta) {
                    $_SESSION['eleicao_anterior'] = $eleicaoAberta;
                } elseif (!empty($eleicoesFinalizadas)) {
                    // Pegar apenas a mais recente
                    $_SESSION['eleicao_anterior'] = $eleicoesFinalizadas[0];
                }
                
                // Buscar todos os documentos
                $documentos = $this->documentoDAO->buscarTodos();
                
                // Converter para objetos Documento
                if (!empty($documentos)) {
                    $documentos = Util::converterArrayDoc($documentos);
                } else {
                    $documentos = [];
                }
                
                $_SESSION["documentos"] = $documentos;
                include "./views/eleicao/cadastrar.php";
            }

            if ($requisicao === "POST") {
                // Validar dados recebidos
                if (empty($_POST["idDocumento"]) || empty($_POST["dataInicioEleicao"]) || 
                    empty($_POST["dataFimEleicao"]) || empty($_POST["statusEleicao"])) {
                    $_SESSION['erro_eleicao'] = "Todos os campos são obrigatórios.";
                    $documentos = $this->documentoDAO->buscarTodos();
                    $_SESSION["documentos"] = !empty($documentos) ? Util::converterArrayDoc($documentos) : [];
                    include "./views/eleicao/cadastrar.php";
                    return;
                }

                // Verificar se existe eleição anterior e se usuário confirmou apagar
                if (isset($_SESSION['eleicao_anterior'])) {
                    $eleicaoAnterior = $_SESSION['eleicao_anterior'];
                    $statusReal = $eleicaoAnterior['status_real'] ?? $eleicaoAnterior['status_eleicao'];
                    
                    // Se for eleição aberta, só permitir criar se confirmou apagar
                    if ($statusReal === 'ABERTA' && !isset($_POST['confirmar_apagar'])) {
                        $_SESSION['erro_eleicao'] = "É necessário confirmar a criação de nova eleição quando há uma eleição aberta existente.";
                        $documentos = $this->documentoDAO->buscarTodos();
                        $_SESSION["documentos"] = !empty($documentos) ? Util::converterArrayDoc($documentos) : [];
                        include "./views/eleicao/cadastrar.php";
                        return;
                    }
                    
                    // Se confirmou apagar eleição aberta, apagar a anterior
                    if ($statusReal === 'ABERTA' && isset($_POST['confirmar_apagar'])) {
                        $this->eleicaoDAO->deletar($eleicaoAnterior['id_eleicao']);
                    }
                }

                // Buscar documento relacionado
                $respostaDocumento = $this->documentoDAO->buscarPorId($_POST["idDocumento"]);
                
                if (!$respostaDocumento) {
                    $_SESSION['erro_eleicao'] = "Documento não encontrado.";
                    $documentos = $this->documentoDAO->buscarTodos();
                    $_SESSION["documentos"] = !empty($documentos) ? Util::converterArrayDoc($documentos) : [];
                    include "./views/eleicao/cadastrar.php";
                    return;
                }

                // Criar objeto Documento
                $documento = new Documento(
                    $respostaDocumento['data_inicio_documento'],
                    $respostaDocumento['data_fim_documento'],
                    $respostaDocumento['titulo_documento'],
                    $respostaDocumento['tipo_documento'],
                    $respostaDocumento['pdf_documento'] ?? '',
                    $respostaDocumento['data_hora_documento'] ?? '',
                    $respostaDocumento['id_documento']
                );
                
                // Criar objeto Eleicao
                $eleicao = new Eleicao(
                    $_POST["dataInicioEleicao"],
                    $_POST["dataFimEleicao"],
                    $_POST["statusEleicao"],
                    $documento
                );

                // Inserir eleição
                $resposta = $this->eleicaoDAO->inserir($eleicao);

                if ($resposta) {
                    unset($_SESSION['erro_eleicao']);
                    unset($_SESSION['eleicao_anterior']);
                    $_SESSION['sucesso_eleicao'] = "Eleição cadastrada com sucesso!";
                    header("Location: /code/cipa_t1/");
                    exit;
                } else {
                    $_SESSION['erro_eleicao'] = "Erro ao cadastrar eleição. Tente novamente.";
                    $documentos = $this->documentoDAO->buscarTodos();
                    $_SESSION["documentos"] = !empty($documentos) ? Util::converterArrayDoc($documentos) : [];
                    include "./views/eleicao/cadastrar.php";
                }
            }
        }
    }

?>