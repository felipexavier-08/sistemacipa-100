<?php    
    require_once __DIR__ . "/controllers/FuncionarioController.php";
    require_once __DIR__ . "/controllers/DocumentoControlller.php";
    require_once __DIR__ . "/controllers/EleicaoController.php";
    require_once __DIR__ . "/controllers/CandidatoController.php";
    require_once __DIR__ . "/controllers/VotoController.php";
    require_once __DIR__ . "/controllers/LoginController.php";
    require_once __DIR__ . "/controllers/AtaController.php";
    require_once __DIR__ . "/controllers/CronogramaController.php";

    // Iniciar sessão
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $funcionarioController = new FuncionarioController();
    $documentoController = new DocumentoControlller();
    $eleicaoController = new EleicaoController();
    $candidatoController = new CandidatoController();
    $votoController = new VotoController();
    $loginController = new LoginController();
    $ataController = new AtaController();
    $cronogramaController = new CronogramaController();
    
    //captura a url
    $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

    //caminho para a base do projeto no xampp
    
    $requisicao = $_SERVER["REQUEST_METHOD"];

   switch($url) {

        case "/code/cipa_t1/":
            // Verificar se está logado e se é admin
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            if ($_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/funcionario/home");
                exit;
            }
            
            // Carregar dados do dashboard
            require_once __DIR__ . "/repositories/EleicaoDAO.php";
            require_once __DIR__ . "/repositories/FuncionarioDAO.php";
            require_once __DIR__ . "/repositories/VotoDAO.php";
            
            $eleicaoDAO = new EleicaoDAO();
            $funcionarioDAO = new FuncionarioDAO();
            $votoDAO = new VotoDAO();
            
            // Buscar estatísticas
            $estatisticas = [
                'eleicao_ativa' => $eleicaoDAO->buscarEstatisticasEleicaoAtiva(),
                'total_funcionarios' => $funcionarioDAO->contarFuncionariosAtivos()
            ];
            
            // Se houver eleição ativa, buscar dados adicionais
            if ($estatisticas['eleicao_ativa']) {
                $idEleicao = $estatisticas['eleicao_ativa']['id_eleicao'];
                $estatisticas['total_votantes'] = $votoDAO->contarEleitoresQueVotaram($idEleicao);
                $estatisticas['porcentagem_votantes'] = $estatisticas['total_funcionarios'] > 0 
                    ? round(($estatisticas['total_votantes'] / $estatisticas['total_funcionarios']) * 100, 1)
                    : 0;
            }
            
            $_SESSION['dashboard_stats'] = $estatisticas;
            include "./views/home.php";
            break;

        case "/code/cipa_t1/login":
            $loginController->login($requisicao);
            break;

        case "/code/cipa_t1/logout":
            $loginController->logout();
            break;

        case "/code/cipa_t1/funcionario/home":
            // Verificar se está logado
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            
            // Usar apenas o home normal (sem versão mobile)
            include "./views/funcionario/home.php";
            break;
        
        case "/code/cipa_t1/funcionario/cadastrar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $funcionarioController->criarFuncionario($requisicao);
            break;

        case "/code/cipa_t1/funcionario/cadastrar-por-matricula":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $funcionarioController->cadastrarPorMatricula($requisicao);
            break;

        case "/code/cipa_t1/funcionario/listar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $funcionarioController->buscarTodosFuncionarios($requisicao);
            break;

        case "/code/cipa_t1/funcionario/buscar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $funcionarioController->buscarFuncionarioPorCpf($requisicao);
            break;

        case "/code/cipa_t1/eleicao/gerenciar":
            require_once __DIR__ . "/controllers/EleicaoGerenciarController.php";
            $eleicaoGerenciarController = new EleicaoGerenciarController();
            $eleicaoGerenciarController->gerenciar($requisicao);
            break;

        case "/code/cipa_t1/eleicao/estender":
            require_once __DIR__ . "/controllers/EleicaoGerenciarController.php";
            $eleicaoGerenciarController = new EleicaoGerenciarController();
            $eleicaoGerenciarController->estenderPeriodo($requisicao);
            break;

        case "/code/cipa_t1/eleicao/finalizar":
            require_once __DIR__ . "/controllers/EleicaoGerenciarController.php";
            $eleicaoGerenciarController = new EleicaoGerenciarController();
            $eleicaoGerenciarController->finalizar($requisicao);
            break;

        case "/code/cipa_t1/eleicao/autorizar-votacao":
            require_once __DIR__ . "/controllers/EleicaoGerenciarController.php";
            $eleicaoGerenciarController = new EleicaoGerenciarController();
            $eleicaoGerenciarController->autorizarVotacao($requisicao);
            break;

        case "/code/cipa_t1/eleicao/bloquear-votacao":
            require_once __DIR__ . "/controllers/EleicaoGerenciarController.php";
            $eleicaoGerenciarController = new EleicaoGerenciarController();
            $eleicaoGerenciarController->bloquearVotacao($requisicao);
            break;

        case "/code/cipa_t1/limpar-comprovante":
            // Limpar sessão do comprovante
            unset($_SESSION['comprovante_voto']);
            header("Location: /code/cipa_t1/funcionario/home");
            exit;
            break;

        case "/code/cipa_t1/funcionario/deletar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $funcionarioController->deletarFuncionario($requisicao);
            break;

        case "/code/cipa_t1/funcionario/deletar-forcado":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $funcionarioController->deletarFuncionarioForcado($requisicao);
            break;

        case "/code/cipa_t1/funcionario/editar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $funcionarioController->editarFuncionario($requisicao);
            break;

        case "/code/cipa_t1/documento/cadastrar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $documentoController->criarDocumento($requisicao);
            break;

        case "/code/cipa_t1/documento/listar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $documentoController->buscarTodosDocumento($requisicao);
            break;

        case "/code/cipa_t1/documento/deletar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $documentoController->deletarDocumento($requisicao);
            break;
            
        case "/code/cipa_t1/eleicao/cadastrar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $eleicaoController->inserirEleicao($requisicao);
            break;
            
        case "/code/cipa_t1/eleicao/fechar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $eleicaoController->fecharEleicao($requisicao);
            break;
            
        case "/code/cipa_t1/candidato/cadastrar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $candidatoController->criarCandidato($requisicao);
            break;

        case "/code/cipa_t1/funcionario/candidatar-se":
            // Verificar se está logado
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $candidatoController->autocandidatura($requisicao);
            break;

        case "/code/cipa_t1/funcionario/alterar-senha":
            // Verificar se está logado
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $funcionarioController->alterarSenha($requisicao);
            break;

        case "/code/cipa_t1/voto/votar":
            // Verificar se está logado
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $votoController->votar($requisicao);
            break;

        case "/code/cipa_t1/voto/listar-candidatos":
            // Verificar se está logado
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $votoController->listarCandidatos($requisicao);
            break;

        case "/code/cipa_t1/voto/sucesso":
            // Verificar se está logado
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $votoController->sucesso();
            break;

        case "/code/cipa_t1/voto/reimprimir-comprovante":
            // Verificar se está logado
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $votoController->reimprimirComprovante();
            break;

        case "/code/cipa_t1/ata/listar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $ataController->listarEleicoesFinalizadas($requisicao);
            break;

        case "/code/cipa_t1/ata/gerar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $ataController->gerarAta($requisicao);
            break;

        case "/code/cipa_t1/cronograma/gerar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            if ($requisicao === "GET") {
                $cronogramaController->exibirFormulario();
            } else {
                $cronogramaController->gerarCronograma();
            }
            break;

        case "/code/cipa_t1/cronograma/visualizar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $cronogramaController->visualizarCronograma();
            break;

        case "/code/cipa_t1/cronograma/editar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $cronogramaController->exibirFormularioEdicao();
            break;

        case "/code/cipa_t1/cronograma/atualizar":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $cronogramaController->atualizarCronograma();
            break;

        case "/code/cipa_t1/cronograma/exportar-excel":
            // Verificar se é admin
            if (!isset($_SESSION['funcionario_logado']) || $_SESSION['funcionario_logado']['adm_funcionario'] != 1) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            $cronogramaController->exportarExcel();
            break;
            
        default:
            // Se não estiver logado, redirecionar para login
            if (!isset($_SESSION['funcionario_logado'])) {
                header("Location: /code/cipa_t1/login");
                exit;
            }
            // Se for admin, ir para home admin, senão para home funcionário
            if ($_SESSION['funcionario_logado']['adm_funcionario'] == 1) {
                header("Location: /code/cipa_t1/");
            } else {
                header("Location: /code/cipa_t1/funcionario/home");
            }
            exit;
            break;

    }