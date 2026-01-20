<?php
require_once __DIR__ . "/../models/Documento.php";
require_once __DIR__ . "/../utils/Conexao.php";

class DocumentoDAO extends Conexao {
    private $db;
    
    public function __construct() {
        $this->db = self::pegarConexao();
    }

    public function inserir(Documento $model) {
        try {
            if (!$this->db) {
                error_log("Erro: Conexão com banco de dados não estabelecida");
                return false;
            }

            $sql = "INSERT INTO documento (            
                data_inicio_documento, 
                data_fim_documento, 
                pdf_documento, 
                titulo_documento, 
                tipo_documento
            ) 
            VALUES (            
                :dataInicio, 
                :dataFim, 
                :pdf, 
                :titulo, 
                :tipo
            )";
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Erro ao preparar SQL: " . implode(", ", $this->db->errorInfo()));
                return false;
            }
            
            $stmt->bindValue(':dataInicio', $model->getDataInicioDocumento(), PDO::PARAM_STR);
            $stmt->bindValue(':dataFim', $model->getDataFimDocumento(), PDO::PARAM_STR);
            $stmt->bindValue(':pdf', $model->getPdfDocumento(), PDO::PARAM_STR);
            $stmt->bindValue(':titulo', $model->getTituloDocumento(), PDO::PARAM_STR);
            $stmt->bindValue(':tipo', $model->getTipoDocumento(), PDO::PARAM_STR);
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                error_log("Erro ao executar SQL: " . implode(", ", $stmt->errorInfo()));
            }
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro DocumentoDAO inserir: " . $e->getMessage());
            return false;
        }
    }

    public function buscarTodos() {
        try {
            // Buscar documentos com status de vinculação a eleições
            $sql = "SELECT d.*, 
                           CASE 
                               WHEN e.id_eleicao IS NOT NULL THEN e.status_eleicao
                               ELSE 'SEM_VINCULO'
                           END as status_eleicao_vinculada
                    FROM documento d
                    LEFT JOIN eleicao e ON d.id_documento = e.documento_fk
                    ORDER BY d.data_inicio_documento DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resposta = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resposta;
        } catch (PDOException $e) {
            error_log("Erro DocumentoDAO buscarTodos: " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId(int $id) {
        try {
            $sql = "SELECT * FROM documento WHERE id_documento = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_STR);
            $stmt->execute();
            $resposta = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resposta;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function estaVinculadoAEleicao(int $idDocumento) {
        try {
            // Verificar se está vinculado a alguma eleição ATIVA
            $sql = "SELECT COUNT(*) FROM eleicao 
                    WHERE documento_fk = :id_documento 
                    AND status_eleicao = 'ABERTA'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id_documento", $idDocumento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar vinculo de documento: " . $e->getMessage());
            return false;
        }
    }

    public function podeSerDeletado(int $idDocumento) {
        try {
            // Verificar se pode ser deletado (não está vinculado a eleição ATIVA)
            $sql = "SELECT COUNT(*) FROM eleicao 
                    WHERE documento_fk = :id_documento 
                    AND status_eleicao = 'ABERTA'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id_documento", $idDocumento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() == 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar se documento pode ser deletado: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorEleicaoAberta() {
        try {
            // Buscar apenas o documento vinculado à eleição aberta
            $sql = "SELECT d.* 
                    FROM documento d
                    INNER JOIN eleicao e ON d.id_documento = e.documento_fk
                    WHERE e.status_eleicao = 'ABERTA'
                    ORDER BY d.data_inicio_documento DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $resposta = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resposta;
        } catch (PDOException $e) {
            error_log("Erro DocumentoDAO buscarPorEleicaoAberta: " . $e->getMessage());
            return [];
        }
    }

    public function deletar(int $id) {
        try {
            // Iniciar transação
            $this->db->beginTransaction();
            
            // Buscar eleições vinculadas ao documento
            $sqlEleicoes = "SELECT id_eleicao FROM eleicao WHERE documento_fk = :id";
            $stmtEleicoes = $this->db->prepare($sqlEleicoes);
            $stmtEleicoes->bindValue(":id", $id, PDO::PARAM_INT);
            $stmtEleicoes->execute();
            $eleicoes = $stmtEleicoes->fetchAll(PDO::FETCH_COLUMN);
            
            // Para cada eleição vinculada, deletar registros dependentes primeiro
            foreach ($eleicoes as $idEleicao) {
                // Deletar registros de branconulo
                $sqlBrancoNulo = "DELETE FROM branconulo WHERE eleicao_fk = :id_eleicao";
                $stmtBrancoNulo = $this->db->prepare($sqlBrancoNulo);
                $stmtBrancoNulo->bindValue(":id_eleicao", $idEleicao, PDO::PARAM_INT);
                $stmtBrancoNulo->execute();
                
                // Deletar candidatos (já tem CASCADE, mas vamos garantir)
                $sqlCandidatos = "DELETE FROM candidato WHERE eleicao_fk = :id_eleicao";
                $stmtCandidatos = $this->db->prepare($sqlCandidatos);
                $stmtCandidatos->bindValue(":id_eleicao", $idEleicao, PDO::PARAM_INT);
                $stmtCandidatos->execute();
            }
            
            // Deletar as eleições vinculadas
            $sqlDeleteEleicoes = "DELETE FROM eleicao WHERE documento_fk = :id";
            $stmtDeleteEleicoes = $this->db->prepare($sqlDeleteEleicoes);
            $stmtDeleteEleicoes->bindValue(":id", $id, PDO::PARAM_INT);
            $stmtDeleteEleicoes->execute();
            
            // Finalmente deletar o documento
            $sql = "DELETE FROM documento WHERE id_documento = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            // Confirmar transação
            $this->db->commit();
            
            return $resultado;
        } catch (PDOException $e) {
            // Desfazer transação em caso de erro
            $this->db->rollBack();
            error_log("Erro ao deletar documento: " . $e->getMessage());
            return false;
        }
    }
}