<?php
require_once __DIR__ . "/../models/Candidato.php";
require_once __DIR__ . "/../utils/Conexao.php";
class CandidatoDAO extends Conexao {
        private ?PDO $db;
        public function __construct() {
            $this->db = $this::pegarConexao();
        }

        // INSERIR - Inserir novo candidato
        public function inserir(Candidato $model, int $idFuncionario, int $idEleicao) {
            try {
                $sql = "INSERT INTO candidato (
                        foto_candidato,
                        numero_candidato,
                        cargo_candidato,
                        data_registro_candidato,
                        status_candidato,
                        quantidade_voto_candidato,
                        usuario_fk,
                        eleicao_fk
                    ) VALUES (
                        :foto,
                        :numero,
                        :cargo,
                        CURDATE(),
                        :status,
                        0,
                        :usuario_fk,
                        :eleicao_fk
                    )";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":foto", $model->getFotoCandidato(), PDO::PARAM_STR);
                $stmt->bindValue(":numero", $model->getNumeroCandidato(), PDO::PARAM_STR);
                $stmt->bindValue(":cargo", $model->getCargoCandidato(), PDO::PARAM_STR);
                $stmt->bindValue(":status", $model->getStatusCandidatoAta(), PDO::PARAM_STR);
                $stmt->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao inserir candidato: " . $e->getMessage());
                return false;
            }
        }

        // BUSCAR POR ELEIÇÃO - Listar candidatos de uma eleição
        public function buscarPorEleicao(int $idEleicao) {
            try {
                $sql = "SELECT c.*, f.nome_funcionario, f.sobrenome_funcionario 
                        FROM candidato c
                        INNER JOIN funcionario f ON c.usuario_fk = f.id_funcionario
                        WHERE c.eleicao_fk = :eleicao_fk
                        ORDER BY c.numero_candidato";
                
                error_log("DEBUG CandidatoDAO: Buscando candidatos da eleição $idEleicao");
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("DEBUG CandidatoDAO: Encontrados " . count($candidatos) . " candidatos");
                
                return $candidatos;
            } catch (PDOException $e) {
                error_log("Erro ao buscar candidatos por eleição: " . $e->getMessage());
                return [];
            }
        }

        // BUSCAR POR NÚMERO - Buscar candidato por número na eleição
        public function buscarPorNumero(int $idEleicao, string $numeroCandidato) {
            try {
                $sql = "SELECT id_candidato FROM candidato 
                        WHERE eleicao_fk = :eleicao_fk 
                        AND numero_candidato = :numero
                        LIMIT 1";
                
                error_log("DEBUG CandidatoDAO: SQL - " . $sql);
                error_log("DEBUG CandidatoDAO: Parâmetros - eleicao_fk: $idEleicao, numero: $numeroCandidato");
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->bindValue(":numero", $numeroCandidato, PDO::PARAM_STR);
                $stmt->execute();
                
                $resultado = $stmt->fetchColumn();
                error_log("DEBUG CandidatoDAO: Resultado - " . ($resultado ? "ID: $resultado" : "NÃO ENCONTRADO"));
                
                return $resultado ?: null;
            } catch (PDOException $e) {
                error_log("Erro ao buscar candidato por número: " . $e->getMessage());
                return null;
            }
        }

        // BUSCAR POR NÚMERO E ELEIÇÃO - Verificar se número já existe na eleição
        public function buscarPorNumeroEEleicao(string $numeroCandidato, int $idEleicao) {
            try {
                $sql = "SELECT id_candidato FROM candidato 
                        WHERE eleicao_fk = :eleicao_fk 
                        AND numero_candidato = :numero
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->bindValue(":numero", $numeroCandidato, PDO::PARAM_STR);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar candidato por número e eleição: " . $e->getMessage());
                return false;
            }
        }

        // BUSCAR POR FUNCIONÁRIO E ELEIÇÃO - Verificar se Funcionário já é candidato
        public function buscarPorFuncionarioEEleicao(int $idFuncionario, int $idEleicao) {
            try {
                $sql = "SELECT id_candidato FROM candidato 
                        WHERE eleicao_fk = :eleicao_fk 
                        AND usuario_fk = :usuario_fk
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar candidato por funcionário e eleição: " . $e->getMessage());
                return false;
            }
        }

        // BUSCAR RESULTADOS POR ELEIÇÃO - Listar resultados ordenados por votos
        public function buscarResultadosPorEleicao(int $idEleicao) {
            try {
                $sql = "SELECT c.*, f.nome_funcionario, f.sobrenome_funcionario, f.data_contratacao_funcionario 
                        FROM candidato c
                        INNER JOIN funcionario f ON c.usuario_fk = f.id_funcionario
                        WHERE c.eleicao_fk = :eleicao_fk
                        ORDER BY c.quantidade_voto_candidato DESC, f.data_contratacao_funcionario ASC";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar resultados da eleição: " . $e->getMessage());
                return [];
            }
        }
    }