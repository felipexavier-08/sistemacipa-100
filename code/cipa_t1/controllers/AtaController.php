<?php
    require_once __DIR__ . "/../repositories/EleicaoDAO.php";
    require_once __DIR__ . "/../repositories/CandidatoDAO.php";
    require_once __DIR__ . "/../repositories/VotoDAO.php";

    class AtaController {
        private ?EleicaoDAO $eleicaoDAO;
        private ?CandidatoDAO $candidatoDAO;
        private ?VotoDAO $votoDAO;

        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->eleicaoDAO = new EleicaoDAO();
            $this->candidatoDAO = new CandidatoDAO();
            $this->votoDAO = new VotoDAO();
        }

        public function listarEleicoesFinalizadas($requisicao) {
            if ($requisicao === "GET") {
                // Verificar se há eleição ativa
                $eleicaoAtiva = $this->eleicaoDAO->buscarEstatisticasEleicaoAtiva();
                
                if ($eleicaoAtiva) {
                    // Adicionar mensagem sobre eleição ativa
                    $_SESSION['mensagem_eleicao_ativa'] = [
                        'titulo' => $eleicaoAtiva['titulo_documento'],
                        'data_fim' => date('d/m/Y', strtotime($eleicaoAtiva['data_fim_eleicao']))
                    ];
                }
                
                $eleicoes = $this->eleicaoDAO->buscarEleicoesFinalizadas();
                $_SESSION['eleicoes_finalizadas'] = $eleicoes;
                include "./views/ata/listar_eleicoes.php";
            }
        }

        public function gerarAta($requisicao) {
            if ($requisicao === "GET") {
                if (!isset($_GET['eleicao']) || empty($_GET['eleicao'])) {
                    $_SESSION['erro_ata'] = "Eleição não especificada.";
                    header("Location: /code/cipa_t1/ata/listar");
                    exit;
                }

                $idEleicao = $_GET['eleicao'];
                
                // Verificar se eleição terminou
                if (!$this->eleicaoDAO->eleicaoTerminou($idEleicao)) {
                    $_SESSION['erro_ata'] = "Esta eleição ainda não foi finalizada.";
                    header("Location: /code/cipa_t1/ata/listar");
                    exit;
                }

                // Buscar dados da eleição
                $eleicao = $this->eleicaoDAO->buscarPorId($idEleicao);
                if (!$eleicao) {
                    $_SESSION['erro_ata'] = "Eleição não encontrada.";
                    header("Location: /code/cipa_t1/ata/listar");
                    exit;
                }

                // Buscar resultados
                $candidatos = $this->candidatoDAO->buscarResultadosPorEleicao($idEleicao);
                $brancosNulos = $this->votoDAO->buscarBrancosENulos($idEleicao);
                $totalVotos = $this->votoDAO->contarTotalVotos($idEleicao);

                $_SESSION['ata_eleicao'] = $eleicao;
                $_SESSION['ata_candidatos'] = $candidatos;
                $_SESSION['ata_brancos_nulos'] = $brancosNulos;
                $_SESSION['ata_total_votos'] = $totalVotos;

                include "./views/ata/gerar_ata.php";
            }
        }
    }
