<?php
    require_once  __DIR__ . "/../models/Documento.php";
    require_once __DIR__ . "/../utils/Util.php";
    require_once  __DIR__ . "/../repositories/DocumentoDAO.php";

    class DocumentoControlller {
        private ?DocumentoDAO $dao;
        public function __construct() {
            $this->dao = new DocumentoDAO();            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }

        public function buscarTodosDocumento($requisicao) {
            if ($requisicao === "GET") {
                $resposta = $this->dao->buscarTodos();
                
                if (!empty($resposta)) {
                    $resposta = Util::converterArrayDoc($resposta);
                    $_SESSION["documentos"] = $resposta;
                } else {
                    $_SESSION["documentos"] = [];
                }
                include "./views/documento/lista.php";
            }
        }

        /**
         * Cria um novo documento no sistema
         * GET: Exibe o formulário de cadastro
         * POST: Processa o cadastro do documento
         */
        public function criarDocumento($requisicao) {
            if ($requisicao === "GET") {
                // Limpar mensagens de erro anteriores
                unset($_SESSION['erro_documento']);
                include "./views/documento/cadastrar.php";
                return;
            }

            if ($requisicao === "POST") {
                // Validar dados obrigatórios
                if (empty($_POST["tituloDocumento"]) || empty($_POST["tipoDocumento"]) || 
                    empty($_POST["dataInicioDocumento"]) || empty($_POST["dataFimDocumento"])) {
                    $_SESSION['erro_documento'] = "Todos os campos são obrigatórios.";
                    include "./views/documento/cadastrar.php";
                    return;
                }

                // Validar datas
                $dataInicio = new DateTime($_POST["dataInicioDocumento"]);
                $dataFim = new DateTime($_POST["dataFimDocumento"]);
                if ($dataInicio >= $dataFim) {
                    $_SESSION['erro_documento'] = "A data de início deve ser anterior à data de fim.";
                    include "./views/documento/cadastrar.php";
                    return;
                }

                // Salvar arquivo PDF
                $caminhoPdf = $this->salvarPdf();
                if ($caminhoPdf === null) {
                    $_SESSION['erro_documento'] = "Erro ao salvar o arquivo PDF. Verifique o formato e tente novamente.";
                    include "./views/documento/cadastrar.php";
                    return;
                }

                // Criar objeto Documento
                $documento = new Documento(
                    $_POST["dataInicioDocumento"],
                    $_POST["dataFimDocumento"],
                    $_POST["tituloDocumento"],
                    $_POST["tipoDocumento"],
                    $caminhoPdf
                );

                // Salvar no banco de dados
                $respostaBD = $this->dao->inserir($documento);
                
                if ($respostaBD) {
                    unset($_SESSION['erro_documento']);
                    $_SESSION['sucesso_documento'] = "Documento cadastrado com sucesso!";
                    header("Location: /code/cipa_t1/");
                    exit;
                } else {
                    $_SESSION['erro_documento'] = "Erro ao cadastrar documento no banco de dados. Tente novamente.";
                    include "./views/documento/cadastrar.php";
                }
            }
        }

        /**
         * Salva o arquivo PDF enviado no servidor
         * @return string|null Caminho relativo do arquivo salvo ou null em caso de erro
         */
        private function salvarPdf() {
            require_once __DIR__ . "/../utils/FileUpload.php";
            
            // Definir caminhos das pastas (absolutos) - compatível com Windows e Linux
            $pastaBase = __DIR__ . '/../uploads';
            $pastaDestino = $pastaBase . '/pdf';
            
            error_log("Pasta base: " . $pastaBase);
            error_log("Pasta destino: " . $pastaDestino);
            
            $arquivo = $_FILES["pdfDocumento"];
            
            // Verificar se há erro no upload
            if ($arquivo["error"] !== UPLOAD_ERR_OK) {
                error_log("Erro no upload do arquivo. Código: " . $arquivo["error"]);
                error_log("Detalhes do arquivo: " . print_r($arquivo, true));
                return null;
            }
            
            // Validar se é PDF
            error_log("Arquivo recebido: " . $arquivo["name"]);
            error_log("Tamanho: " . $arquivo["size"]);
            error_log("Tipo MIME: " . (mime_content_type($arquivo["tmp_name"]) ?? 'N/A'));
            
            if (!FileUpload::validarPdf($arquivo)) {
                error_log("Validação de PDF falhou para: " . $arquivo["name"]);
                return null;
            }
            
            // Garantir que as pastas existam e tenham permissões corretas
            error_log("Verificando pasta base: " . $pastaBase);
            error_log("Verificando pasta destino: " . $pastaDestino);
            
            if (!FileUpload::garantirPasta($pastaBase)) {
                error_log("Erro ao criar/verificar pasta base: " . $pastaBase);
                return null;
            }
            
            if (!FileUpload::garantirPasta($pastaDestino)) {
                error_log("Erro ao criar/verificar pasta de destino: " . $pastaDestino);
                return null;
            }
            
            // Normalizar caminho da pasta de destino
            $pastaDestino = rtrim(str_replace('\\', '/', $pastaDestino), '/') . '/';

            // Gerar nome único para o arquivo
            $extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));
            $novoNomeArquivo = uniqid("doc_") . "." . $extensao;
            $caminhoCompleto = str_replace('\\', '/', $pastaDestino) . $novoNomeArquivo;

            // Mover arquivo para pasta de destino
            if (move_uploaded_file($arquivo["tmp_name"], $caminhoCompleto)) {
                // Ajustar permissões do arquivo (Linux/Unix)
                if (PHP_OS_FAMILY !== 'Windows') {
                    @chmod($caminhoCompleto, 0644);
                }
                // Retornar caminho relativo para salvar no banco de dados
                return "uploads/pdf/" . $novoNomeArquivo;
            } else {
                // Log de erro detalhado
                $error = error_get_last();
                $mensagemErro = $error ? $error['message'] : 'Erro desconhecido';
                error_log("Erro ao salvar PDF: " . $mensagemErro);
                error_log("Caminho completo: " . $caminhoCompleto);
                error_log("Arquivo temporário: " . $arquivo["tmp_name"]);
                return null;
            }
        }

        public function deletarDocumento($requisicao) {
            if ($requisicao === "GET") {
                $idDocumento = $_GET['id'] ?? null;
                
                if (!$idDocumento) {
                    $_SESSION['erro_documento'] = "ID do documento não informado.";
                    header("Location: /code/cipa_t1/documento/listar");
                    exit;
                }

                // Verificar se documento pode ser deletado (não está vinculado a eleição ATIVA)
                if (!$this->dao->podeSerDeletado($idDocumento)) {
                    $_SESSION['erro_documento'] = "Não é possível excluir este documento pois está vinculado a uma eleição ATIVA.";
                    header("Location: /code/cipa_t1/documento/listar");
                    exit;
                }

                // Buscar documento para excluir o arquivo PDF
                $documento = $this->dao->buscarPorId($idDocumento);
                
                // Excluir documento do banco
                $resultado = $this->dao->deletar($idDocumento);
                
                if ($resultado) {
                    // Tentar excluir o arquivo PDF do servidor
                    if ($documento && !empty($documento['pdf_documento'])) {
                        $caminhoArquivo = __DIR__ . "/../" . $documento['pdf_documento'];
                        if (file_exists($caminhoArquivo)) {
                            unlink($caminhoArquivo);
                        }
                    }
                    
                    $_SESSION['sucesso_documento'] = "Documento excluído com sucesso!";
                } else {
                    $_SESSION['erro_documento'] = "Erro ao excluir documento. Tente novamente.";
                }
                
                header("Location: /code/cipa_t1/documento/listar");
                exit;
            }
        }
    }
