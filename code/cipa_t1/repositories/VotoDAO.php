<?php
require_once __DIR__ . "/../models/Voto.php";
require_once __DIR__ . "/../utils/Conexao.php";

class VotoDAO extends Conexao {
        private ?PDO $db;

        public function __construct() {
            $this->db = self::pegarConexao();
        }

        // INSERIR - Inserir novo voto
        public function inserir(Voto $model, int $idFuncionario) {
            try {
                $sql = "INSERT INTO voto (
                    data_hora_voto,
                    usuario_fk
                ) VALUES (
                    NOW(),
                    :usuario_fk
                )";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erro ao inserir voto: " . $e->getMessage());
                return false;
            }
        }

        public function funcionarioJaVotou(int $idFuncionario, int $idEleicao) {
            try {
                // Verificar usando o campo votou_ultima_eleicao (mais eficiente)
                // Se votou_ultima_eleicao = 1, significa que votou na eleição atual
                $sql = "SELECT votou_ultima_eleicao 
                        FROM funcionario 
                        WHERE id_funcionario = :id_funcionario";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":id_funcionario", $idFuncionario, PDO::PARAM_INT);
                $stmt->execute();
                
                $votou = $stmt->fetchColumn() > 0;
                error_log("DEBUG: Verificando voto - Funcionário: $idFuncionario, Eleição: $idEleicao, Resultado: " . ($votou ? 'JÁ VOTOU' : 'NÃO VOTOU'));
                
                return $votou;
            } catch (PDOException $e) {
                error_log("Erro ao verificar se funcionário votou: " . $e->getMessage());
                // Em caso de erro, retornar false para permitir tentativa
                return false;
            }
        }

        // REGISTRAR VOTO - Registrar voto do funcionário em candidato
        public function registrarVoto(int $idFuncionario, int $idCandidato) {
            try {
                $this->db->beginTransaction();
                
                // Registrar o voto (sem referência ao candidato, apenas registro do voto)
                $sqlVoto = "INSERT INTO voto (data_hora_voto, usuario_fk) VALUES (NOW(), :usuario_fk)";
                error_log("DEBUG: SQL Voto - " . $sqlVoto);
                error_log("DEBUG: Parâmetros - usuario_fk: $idFuncionario");
                
                $stmtVoto = $this->db->prepare($sqlVoto);
                $stmtVoto->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                
                $executeResult = $stmtVoto->execute();
                error_log("DEBUG: Execute result - " . ($executeResult ? 'SUCCESS' : 'FAILED'));
                
                if (!$executeResult) {
                    $errorInfo = $stmtVoto->errorInfo();
                    error_log("DEBUG: PDO Error Info - " . print_r($errorInfo, true));
                    $this->db->rollBack();
                    return false;
                }
                
                // Atualizar quantidade de votos do candidato
                $sqlCandidato = "UPDATE candidato 
                                SET quantidade_voto_candidato = quantidade_voto_candidato + 1 
                                WHERE id_candidato = :id_candidato";
                error_log("DEBUG: SQL Candidato - " . $sqlCandidato);
                
                $stmtCandidato = $this->db->prepare($sqlCandidato);
                $stmtCandidato->bindValue(":id_candidato", $idCandidato, PDO::PARAM_INT);
                $candidatoResult = $stmtCandidato->execute();
                error_log("DEBUG: Candidato update result - " . ($candidatoResult ? 'SUCCESS' : 'FAILED'));
                
                // Marcar funcionário como votou na eleição atual
                $sqlFuncionario = "UPDATE funcionario 
                                 SET votou_ultima_eleicao = 1 
                                 WHERE id_funcionario = :id_funcionario";
                $stmtFuncionario = $this->db->prepare($sqlFuncionario);
                $stmtFuncionario->bindValue(":id_funcionario", $idFuncionario, PDO::PARAM_INT);
                $funcionarioResult = $stmtFuncionario->execute();
                error_log("DEBUG: Funcionario update result - " . ($funcionarioResult ? 'SUCCESS' : 'FAILED'));
                
                $this->db->commit();
                error_log("DEBUG: Transaction committed successfully");
                return true;
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Erro ao registrar voto: " . $e->getMessage());
                error_log("SQL executado: " . $sqlVoto);
                error_log("Parâmetros: idFuncionario=$idFuncionario, idCandidato=$idCandidato");
                return false;
            }
        }

        // REGISTRAR VOTO BRANCO OU NULO - Registrar voto em branco ou nulo
        public function registrarVotoBrancoOuNulo(int $idFuncionario, int $idEleicao, string $tipo) {
            try {
                $this->db->beginTransaction();

                // Registrar o voto
                $sqlVoto = "INSERT INTO voto (data_hora_voto, usuario_fk) VALUES (NOW(), :usuario_fk)";
                $stmtVoto = $this->db->prepare($sqlVoto);
                $stmtVoto->bindValue(":usuario_fk", $idFuncionario, PDO::PARAM_INT);
                $stmtVoto->execute();

                // Verificar se já existe registro de branco/nulo para esta eleição
                $sqlCheck = "SELECT id_branco_nulo FROM branconulo WHERE eleicao_fk = :eleicao_fk";
                $stmtCheck = $this->db->prepare($sqlCheck);
                $stmtCheck->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmtCheck->execute();
                $existe = $stmtCheck->fetchColumn();

                if ($existe) {
                    // Atualizar
                    $campo = $tipo === 'BRANCO' ? 'quantidade_branco' : 'quantidade_nulo';
                    $sqlUpdate = "UPDATE branconulo SET $campo = $campo + 1 WHERE eleicao_fk = :eleicao_fk";
                    $stmtUpdate = $this->db->prepare($sqlUpdate);
                    $stmtUpdate->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                    $stmtUpdate->execute();
                } else {
                    // Inserir
                    $quantidadeBranco = $tipo === 'BRANCO' ? 1 : 0;
                    $quantidadeNulo = $tipo === 'NULO' ? 1 : 0;
                    $sqlInsert = "INSERT INTO branconulo (quantidade_branco, quantidade_nulo, eleicao_fk) 
                                 VALUES (:branco, :nulo, :eleicao_fk)";
                    $stmtInsert = $this->db->prepare($sqlInsert);
                    $stmtInsert->bindValue(":branco", $quantidadeBranco, PDO::PARAM_INT);
                    $stmtInsert->bindValue(":nulo", $quantidadeNulo, PDO::PARAM_INT);
                    $stmtInsert->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                    $stmtInsert->execute();
                }
                
                // Marcar funcionário como votou na eleição atual
                $sqlFuncionario = "UPDATE funcionario 
                                 SET votou_ultima_eleicao = 1 
                                 WHERE id_funcionario = :id_funcionario";
                $stmtFuncionario = $this->db->prepare($sqlFuncionario);
                $stmtFuncionario->bindValue(":id_funcionario", $idFuncionario, PDO::PARAM_INT);
                $stmtFuncionario->execute();

                $this->db->commit();
                return true;
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Erro ao registrar voto branco/nulo: " . $e->getMessage());
                return false;
            }
        }

        // BUSCAR BRANCOS E NULOS - Contar votos brancos e nulos
        public function buscarBrancosENulos(int $idEleicao) {
            try {
                $sql = "SELECT quantidade_branco, quantidade_nulo 
                        FROM branconulo 
                        WHERE eleicao_fk = :eleicao_fk
                        LIMIT 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmt->execute();
                
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado ?: ['quantidade_branco' => 0, 'quantidade_nulo' => 0];
            } catch (PDOException $e) {
                error_log("Erro ao buscar brancos e nulos: " . $e->getMessage());
                return ['quantidade_branco' => 0, 'quantidade_nulo' => 0];
            }
        }

        // CONTAR TOTAL VOTOS - Contar todos os votos da eleição
        public function contarTotalVotos(int $idEleicao) {
            try {
                // Contar votos de candidatos
                $sqlCandidatos = "SELECT SUM(quantidade_voto_candidato) FROM candidato WHERE eleicao_fk = :eleicao_fk";
                $stmtCandidatos = $this->db->prepare($sqlCandidatos);
                $stmtCandidatos->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmtCandidatos->execute();
                $votosCandidatos = $stmtCandidatos->fetchColumn() ?: 0;

                // Contar brancos e nulos
                $sqlBrancosNulos = "SELECT SUM(quantidade_branco + quantidade_nulo) FROM branconulo WHERE eleicao_fk = :eleicao_fk";
                $stmtBrancosNulos = $this->db->prepare($sqlBrancosNulos);
                $stmtBrancosNulos->bindValue(":eleicao_fk", $idEleicao, PDO::PARAM_INT);
                $stmtBrancosNulos->execute();
                $votosBrancosNulos = $stmtBrancosNulos->fetchColumn() ?: 0;

                return intval($votosCandidatos) + intval($votosBrancosNulos);
            } catch (PDOException $e) {
                error_log("Erro ao contar total de votos: " . $e->getMessage());
                return 0;
            }
        }

        // CONTAR ELEITORES QUE VOTARAM - Contar funcionários que votaram
        public function contarEleitoresQueVotaram(int $idEleicao) {
            try {
                $sql = "SELECT COUNT(*) 
                        FROM funcionario 
                        WHERE votou_ultima_eleicao = 1";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                
                return $stmt->fetchColumn() ?: 0;
            } catch (PDOException $e) {
                error_log("Erro ao contar eleitores que votaram: " . $e->getMessage());
                return 0;
            }
        }
    }
