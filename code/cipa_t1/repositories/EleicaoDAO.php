<?php
require_once __DIR__ . "/../models/Eleicao.php";
require_once __DIR__ . "/../utils/Conexao.php";

class EleicaoDAO extends Conexao {
    private ?PDO $db;
    public function __construct() {
        $this->db = $this::pegarConexao();
    }

        // INSERIR - Inserir nova eleição
        public function inserir(Eleicao $model) {
            try {
                $sql = "INSERT INTO eleicao (
                    data_inicio_eleicao,
                    data_fim_eleicao,
                    status_eleicao,
                    documento_fk
                ) VALUES (
                    :data_inicio,
                    :data_fim,
                    :status_e,
                    :doc
                )";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":data_inicio", $model->getDataInicioEleicao(), PDO::PARAM_STR);
                $stmt->bindValue(":data_fim", $model->getDataFimEleicao(), PDO::PARAM_STR);
                $stmt->bindValue(":status_e", $model->getStatusEleicao(), PDO::PARAM_STR);
                $stmt->bindValue(":doc", $model->getEditalFK()->getIdDocumento(), PDO::PARAM_INT);
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log($e->getMessage());
                return false;
            }
        }

        // BUSCAR ELEIÇÃO ABERTA - Buscar eleição com status ABERTA
        public function buscarEleicaoAberta() {
            try {
                $sql = "SELECT e.*, d.titulo_documento,
                        'ABERTA' as status_real
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        WHERE e.status_eleicao = 'ABERTA' 
                        LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleição aberta: " . $e->getMessage());
                return null;
            }
        }

        // BUSCAR TODAS - Buscar todas as eleições
        public function buscarTodas() {
            try {
                $sql = "SELECT e.*, d.titulo_documento 
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        ORDER BY e.data_inicio_eleicao DESC";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleições: " . $e->getMessage());
                return [];
            }
        }

        // BUSCAR POR ID - Buscar eleição por ID
        public function buscarPorId(int $id) {
            try {
                $sql = "SELECT e.*, d.titulo_documento 
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        WHERE e.id_eleicao = :id";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleição por ID: " . $e->getMessage());
                return null;
            }
        }

        // VERIFICAR SE VOTAÇÃO ESTÁ AUTORIZADA - Verificar se admin autorizou votação
        public function votacaoAutorizada(int $idEleicao) {
            try {
                $sql = "SELECT votacao_autorizada FROM eleicao WHERE id_eleicao = :id AND status_eleicao = 'ABERTA'";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado && $resultado['votacao_autorizada'] == 1;
            } catch (PDOException $e) {
                error_log("Erro ao verificar autorização de votação: " . $e->getMessage());
                return false;
            }
        }

        // AUTORIZAR VOTAÇÃO - Autorizar votação (bloqueando novas candidaturas)
        public function autorizarVotacao(int $idEleicao) {
            try {
                $sql = "UPDATE eleicao SET votacao_autorizada = 1 WHERE id_eleicao = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id", $idEleicao, PDO::PARAM_INT);
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao autorizar votação: " . $e->getMessage());
                return false;
            }
        }

        // BLOQUEAR VOTAÇÃO - Bloquear votação (permitindo novas candidaturas)
        public function bloquearVotacao(int $idEleicao) {
            try {
                $sql = "UPDATE eleicao SET votacao_autorizada = 0 WHERE id_eleicao = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id", $idEleicao, PDO::PARAM_INT);
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao bloquear votação: " . $e->getMessage());
                return false;
            }
        }

        // VERIFICAR SE ELEIÇÃO TERMINOU - Verificar se data fim já passou
        public function eleicaoTerminou(int $idEleicao) {
            try {
                $sql = "SELECT data_fim_eleicao, status_eleicao FROM eleicao WHERE id_eleicao = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$resultado) {
                    return false;
                }
                
                // Verificar se já está finalizada OU se a data já passou
                $hoje = date('Y-m-d');
                return $resultado['status_eleicao'] === 'FINALIZADA' || $resultado['data_fim_eleicao'] <= $hoje;
            } catch (PDOException $e) {
                error_log("Erro ao verificar se eleição terminou: " . $e->getMessage());
                return false;
            }
        }

        // BUSCAR ELEIÇÕES FINALIZADAS - Listar eleições encerradas
        public function buscarEleicoesFinalizadas() {
            try {
                $sql = "SELECT e.*, d.titulo_documento,
                        CASE 
                            WHEN e.status_eleicao = 'FINALIZADA' THEN 'FINALIZADA'
                            ELSE 'FINALIZADA'
                        END as status_real
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        WHERE e.data_fim_eleicao <= CURDATE() OR e.status_eleicao = 'FINALIZADA'
                        ORDER BY e.data_fim_eleicao DESC";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleições finalizadas: " . $e->getMessage());
                return [];
            }
        }

        // BUSCAR ESTATÍSTICAS ELEIÇÃO ATIVA - Dados da eleição atual
        public function buscarEstatisticasEleicaoAtiva() {
            try {
                // Primeiro, atualizar status de eleições que deveriam estar finalizadas
                $sqlUpdate = "UPDATE eleicao 
                             SET status_eleicao = 'FINALIZADA' 
                             WHERE status_eleicao = 'ABERTA' 
                             AND data_fim_eleicao <= CURDATE()";
                $stmtUpdate = $this->db->prepare($sqlUpdate);
                $stmtUpdate->execute();
                
                // Verificar se alguma eleição foi finalizada automaticamente
                // Se sim, resetar o status de voto de todos os funcionários
                $sqlCheck = "SELECT COUNT(*) as count 
                           FROM eleicao 
                           WHERE status_eleicao = 'FINALIZADA' 
                           AND data_fim_eleicao >= CURDATE() - INTERVAL 1 DAY";
                $stmtCheck = $this->db->prepare($sqlCheck);
                $stmtCheck->execute();
                $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                
                // Se houver eleições finalizadas recentemente (últimos 24h), resetar status de voto
                if ($result['count'] > 0) {
                    $sqlReset = "UPDATE funcionario SET votou_ultima_eleicao = 0 WHERE 1=1";
                    $stmtReset = $this->db->prepare($sqlReset);
                    $stmtReset->execute();
                }
                
                // Depois buscar os dados
                $sql = "SELECT e.*, d.titulo_documento,
                        COUNT(DISTINCT c.id_candidato) as total_candidatos
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        LEFT JOIN candidato c ON e.id_eleicao = c.eleicao_fk
                        WHERE e.status_eleicao = 'ABERTA'
                        GROUP BY e.id_eleicao, d.titulo_documento
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar estatísticas da eleição ativa: " . $e->getMessage());
                return null;
            }
        }

        // BUSCAR ELEIÇÃO ATIVA COM STATUS DE VOTAÇÃO - Para gerenciamento
        public function buscarEleicaoAtivaComStatusVotacao() {
            try {
                $sql = "SELECT e.*, d.titulo_documento,
                        COUNT(DISTINCT c.id_candidato) as total_candidatos
                        FROM eleicao e
                        INNER JOIN documento d ON e.documento_fk = d.id_documento
                        LEFT JOIN candidato c ON e.id_eleicao = c.eleicao_fk
                        WHERE e.status_eleicao = 'ABERTA'
                        GROUP BY e.id_eleicao, d.titulo_documento
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar eleição ativa com status: " . $e->getMessage());
                return null;
            }
        }

        // ATUALIZAR STATUS - Atualizar status da eleição
        public function atualizarStatus(int $idEleicao, string $novoStatus) {
            try {
                $sql = "UPDATE eleicao SET status_eleicao = :status WHERE id_eleicao = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":status", $novoStatus, PDO::PARAM_STR);
                $stmt->bindValue(":id", $idEleicao, PDO::PARAM_INT);
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao atualizar status da eleição: " . $e->getMessage());
                return false;
            }
        }

        // ATUALIZAR STATUS E DATA FIM - Atualizar status e data de término da eleição
        public function atualizarStatusEDataFim(int $idEleicao, string $novoStatus) {
            try {
                $this->db->beginTransaction();
                
                // Atualizar status da eleição
                $sql = "UPDATE eleicao SET status_eleicao = :status, data_fim_eleicao = CURDATE() WHERE id_eleicao = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":status", $novoStatus, PDO::PARAM_STR);
                $stmt->bindValue(":id", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                // Resetar status de voto dos funcionários quando eleição é finalizada
                // Isso garante que para a próxima eleição, todos começarão como "não votou"
                $sqlReset = "UPDATE funcionario SET votou_ultima_eleicao = 0 WHERE 1=1";
                $stmtReset = $this->db->prepare($sqlReset);
                $stmtReset->execute();
                
                $this->db->commit();
                return true;
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Erro ao atualizar status e data da eleição: " . $e->getMessage());
                return false;
            }
        }

        // ATUALIZAR DATA FIM - Atualizar data de término da eleição
        public function atualizarDataFim($idEleicao, $novaDataFim) {
            try {
                $sql = "UPDATE eleicao SET data_fim_eleicao = :data_fim WHERE id_eleicao = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":data_fim", $novaDataFim, PDO::PARAM_STR);
                $stmt->bindValue(":id", $idEleicao, PDO::PARAM_INT);
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao atualizar data de término da eleição: " . $e->getMessage());
                return false;
            }
        }

        // DELETAR - Excluir eleição
        public function deletar(int $id) {
            try {
                $sql = "DELETE FROM eleicao WHERE id_eleicao = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao deletar eleição: " . $e->getMessage());
                return false;
            }
        }
    }

?>