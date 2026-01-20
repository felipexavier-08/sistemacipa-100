<?php
    require_once __DIR__ . "/../models/Eleicao.php";
    require_once __DIR__ . "/../models/Funcionario.php";
    require_once __DIR__ . "/../models/Candidato.php";
    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
    require_once __DIR__ . "/../repositories/FuncionarioDAO.php";
    require_once __DIR__ . "/../repositories/CandidatoDAO.php";
    require_once __DIR__ . "/../utils/Util.php";

    class CandidatoController {
        private ?FuncionarioDAO $funcionarioDAO;
        private ?EleicaoDAO $eleicaoDAO;
        private ?CandidatoDAO $candidatoDAO;
        
        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->funcionarioDAO = new FuncionarioDAO();
            $this->eleicaoDAO = new EleicaoDAO();
            $this->candidatoDAO = new CandidatoDAO();
        }

        public function criarCandidato($requisicao) {
            if ($requisicao === "GET") {
                // Verificar se há eleição aberta e se votação ainda não foi autorizada
                $eleicaoAberta = $this->eleicaoDAO->buscarEleicaoAberta();
                
                if (!$eleicaoAberta) {
                    $_SESSION['erro_candidato'] = "Não há eleição aberta no momento.";
                    include "./views/candidato/periodo_encerrado.php";
                    return;
                }

                $idEleicao = $eleicaoAberta['id_eleicao'];

                // Verificar se votação já foi autorizada (bloqueia novas candidaturas)
                if ($this->eleicaoDAO->votacaoAutorizada($idEleicao)) {
                    $_SESSION['erro_candidato'] = "O período de candidaturas encerrou. A votação foi autorizada pelo administrador.";
                    include "./views/candidato/periodo_encerrado.php";
                    return;
                }

                include "./views/candidato/cadastrar.php";
            }

            if ($requisicao === "POST") {
                // Buscar funcionário por matrícula e CPF
                $funcionarioData = $this->funcionarioDAO->buscarPorMatriculaECpf(
                    $_POST['matriculaFuncionario'],
                    $_POST['cpfFuncionario']
                );

                if (!$funcionarioData) {
                    $_SESSION['erro_candidato'] = "Funcionário não encontrado com a matrícula e CPF informados.";
                    include "./views/candidato/cadastrar.php";
                    return;
                }

                // Buscar eleição aberta
                $eleicaoAberta = $this->eleicaoDAO->buscarEleicaoAberta();
                if (!$eleicaoAberta) {
                    $_SESSION['erro_candidato'] = "Não há eleição aberta no momento.";
                    include "./views/candidato/cadastrar.php";
                    return;
                }

                $idEleicao = $eleicaoAberta['id_eleicao'];

                // Verificar se votação já foi autorizada (bloqueia novas candidaturas)
                if ($this->eleicaoDAO->votacaoAutorizada($idEleicao)) {
                    $_SESSION['erro_candidato'] = "O período de candidaturas encerrou. A votação foi autorizada pelo administrador.";
                    include "./views/candidato/periodo_encerrado.php";
                    return;
                }

                // Salvar foto (opcional)
                $caminhoFoto = "";
                if (isset($_FILES["fotoCandidato"]) && $_FILES["fotoCandidato"]["error"] === UPLOAD_ERR_OK) {
                    $caminhoFoto = $this->salvarFoto();
                    if ($caminhoFoto === null) {
                        $_SESSION['erro_candidato'] = "Erro ao salvar foto do candidato. Verifique as permissões da pasta uploads/fotos ou cadastre sem foto.";
                        include "./views/candidato/cadastrar.php";
                        return;
                    }
                } elseif (isset($_FILES["fotoCandidato"]) && $_FILES["fotoCandidato"]["error"] !== UPLOAD_ERR_NO_FILE) {
                    // Se houve erro no upload (mas não foi "nenhum arquivo")
                    $erroUpload = $_FILES["fotoCandidato"]["error"];
                    $mensagensErro = [
                        UPLOAD_ERR_INI_SIZE => "Arquivo muito grande (excede upload_max_filesize)",
                        UPLOAD_ERR_FORM_SIZE => "Arquivo muito grande (excede MAX_FILE_SIZE)",
                        UPLOAD_ERR_PARTIAL => "Upload parcial do arquivo",
                        UPLOAD_ERR_NO_TMP_DIR => "Pasta temporária não encontrada",
                        UPLOAD_ERR_CANT_WRITE => "Falha ao escrever arquivo no disco",
                        UPLOAD_ERR_EXTENSION => "Upload bloqueado por extensão"
                    ];
                    $_SESSION['erro_candidato'] = "Erro no upload: " . ($mensagensErro[$erroUpload] ?? "Erro desconhecido");
                    include "./views/candidato/cadastrar.php";
                    return;
                }

                // Criar candidato
                $candidato = new Candidato(
                    $caminhoFoto,
                    $_POST['numeroCandidato'],
                    $_POST['cargoCandidato'],
                    "ATIVO"
                );

                $idFuncionario = $funcionarioData['id_funcionario'];
                $resposta = $this->candidatoDAO->inserir($candidato, $idFuncionario, $idEleicao);

                if ($resposta) {
                    unset($_SESSION['erro_candidato']);
                    // Redirecionar para página admin
                    header("Location: /code/cipa_t1/");
                    exit;
                } else {
                    $_SESSION['erro_candidato'] = "Erro ao cadastrar candidato.";
                    include "./views/candidato/cadastrar.php";
                }
            }
        }

        public function autocandidatura($requisicao) {
            if ($requisicao === "GET") {
                // Buscar eleição aberta
                $idEleicao = $this->eleicaoDAO->buscarEleicaoAberta();
                
                // Verificar se há eleição aberta
                if (!$idEleicao) {
                    $_SESSION['erro_candidatura'] = "Não há eleição aberta no momento.";
                    include "./views/funcionario/candidatar-se.php";
                    return;
                }
                
                // Verificar se a votação já foi autorizada (bloqueia novas candidaturas)
                if ($idEleicao['votacao_autorizada'] == 1) {
                    $_SESSION['erro_candidatura'] = "A votação já foi iniciada. Não é possível mais se candidatar.";
                    include "./views/funcionario/candidatar-se.php";
                    return;
                }
                
                include "./views/funcionario/candidatar-se.php";
            }

            if ($requisicao === "POST") {
                // Usar o funcionário logado
                $idFuncionarioLogado = $_SESSION['funcionario_logado']['id_funcionario'];

                // Buscar eleição aberta
                $idEleicao = $this->eleicaoDAO->buscarEleicaoAberta();
                if (!$idEleicao) {
                    $_SESSION['erro_candidatura'] = "Não há eleição aberta no momento.";
                    include "./views/funcionario/candidatar-se.php";
                    return;
                }

                // Verificar se a votação já foi autorizada (bloqueia novas candidaturas)
                if ($idEleicao['votacao_autorizada'] == 1) {
                    $_SESSION['erro_candidatura'] = "A votação já foi iniciada. Não é possível mais se candidatar.";
                    include "./views/funcionario/candidatar-se.php";
                    return;
                }

                // Verificar se já é candidato nesta eleição
                $candidatoExistente = $this->candidatoDAO->buscarPorFuncionarioEEleicao($idFuncionarioLogado, $idEleicao['id_eleicao']);
                if ($candidatoExistente) {
                    $_SESSION['erro_candidatura'] = "Você já é candidato nesta eleição.";
                    include "./views/funcionario/candidatar-se.php";
                    return;
                }

                // Verificar se o número de candidato já está em uso
                $numeroExistente = $this->candidatoDAO->buscarPorNumeroEEleicao($_POST['numeroCandidato'], $idEleicao['id_eleicao']);
                if ($numeroExistente) {
                    $_SESSION['erro_candidatura'] = "Este número de candidato já está em uso nesta eleição.";
                    include "./views/funcionario/candidatar-se.php";
                    return;
                }

                // Salvar foto (opcional)
                $caminhoFoto = "";
                if (isset($_FILES["fotoCandidato"]) && $_FILES["fotoCandidato"]["error"] === UPLOAD_ERR_OK) {
                    $caminhoFoto = $this->salvarFoto();
                    if ($caminhoFoto === null) {
                        $_SESSION['erro_candidatura'] = "Erro ao salvar foto. Tente novamente ou cadastre sem foto.";
                        include "./views/funcionario/candidatar-se.php";
                        return;
                    }
                } elseif (isset($_FILES["fotoCandidato"]) && $_FILES["fotoCandidato"]["error"] !== UPLOAD_ERR_NO_FILE) {
                    // Se houve erro no upload (mas não foi "nenhum arquivo")
                    $erroUpload = $_FILES["fotoCandidato"]["error"];
                    $mensagensErro = [
                        UPLOAD_ERR_INI_SIZE => "Arquivo muito grande (excede upload_max_filesize)",
                        UPLOAD_ERR_FORM_SIZE => "Arquivo muito grande (excede MAX_FILE_SIZE)",
                        UPLOAD_ERR_PARTIAL => "Upload parcial do arquivo",
                        UPLOAD_ERR_NO_TMP_DIR => "Pasta temporária não encontrada",
                        UPLOAD_ERR_CANT_WRITE => "Falha ao escrever arquivo no disco",
                        UPLOAD_ERR_EXTENSION => "Upload bloqueado por extensão"
                    ];
                    $_SESSION['erro_candidatura'] = "Erro no upload: " . ($mensagensErro[$erroUpload] ?? "Erro desconhecido");
                    include "./views/funcionario/candidatar-se.php";
                    return;
                }

                // Criar candidato
                $candidato = new Candidato(
                    $caminhoFoto,
                    $_POST['numeroCandidato'],
                    $_POST['cargoCandidato'],
                    "ATIVO"
                );

                $resposta = $this->candidatoDAO->inserir($candidato, $idFuncionarioLogado, $idEleicao['id_eleicao']);

                if ($resposta) {
                    unset($_SESSION['erro_candidatura']);
                    $_SESSION['sucesso_candidatura'] = "Candidatura registrada com sucesso!";
                    header("Location: /code/cipa_t1/funcionario/home");
                    exit;
                } else {
                    $_SESSION['erro_candidatura'] = "Erro ao registrar candidatura.";
                    include "./views/funcionario/candidatar-se.php";
                }
            }
        }

        private function salvarFoto() {
            require_once __DIR__ . "/../utils/FileUpload.php";
            
            // Usar caminho absoluto baseado no diretório do projeto
            $pastaBase = __DIR__ . "/../uploads";
            $pastaDestino = $pastaBase . "/fotos";
            $arquivo = $_FILES["fotoCandidato"];
            
            // Verificar se há erro no upload
            if ($arquivo["error"] !== UPLOAD_ERR_OK) {
                error_log("Erro no upload do arquivo. Código: " . $arquivo["error"]);
                return null;
            }
            
            // Validar se é imagem
            if (!FileUpload::validarImagem($arquivo)) {
                error_log("Tentativa de upload de arquivo não-imagem: " . $arquivo["name"]);
                return null;
            }
            
            // Garantir que a pasta existe e tem permissões corretas
            if (!FileUpload::garantirPasta($pastaBase)) {
                error_log("Erro ao criar/verificar pasta base: " . $pastaBase);
                return null;
            }
            
            if (!FileUpload::garantirPasta($pastaDestino)) {
                error_log("Erro ao criar/verificar pasta de destino: " . $pastaDestino);
                return null;
            }
            
            // Adicionar barra final
            $pastaDestino = rtrim($pastaDestino, '/\\') . DIRECTORY_SEPARATOR;
            
            $extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));
            $novoNomeArquivo = uniqid("foto_") . "." . $extensao;
            $caminhoCompleto = $pastaDestino . $novoNomeArquivo;

            // Mover arquivo para pasta de destino
            if (move_uploaded_file($arquivo["tmp_name"], $caminhoCompleto)) {
                // Ajustar permissões do arquivo (Linux)
                if (PHP_OS_FAMILY !== 'Windows') {
                    @chmod($caminhoCompleto, 0644);
                }
                // Retornar caminho relativo para salvar no banco
                return "uploads/fotos/" . $novoNomeArquivo;
            } else {
                $error = error_get_last();
                $mensagemErro = $error ? $error['message'] : 'Erro desconhecido';
                error_log("Erro ao salvar foto: " . $mensagemErro);
                error_log("Caminho completo: " . $caminhoCompleto);
                error_log("Arquivo temporário: " . $arquivo["tmp_name"]);
                error_log("Pasta existe: " . (file_exists($pastaDestino) ? 'sim' : 'não'));
                error_log("Pasta é gravável: " . (is_writable($pastaDestino) ? 'sim' : 'não'));
                return null;
            }
        }
    }