<?php
require_once __DIR__ . "/../models/Funcionario.php";
require_once __DIR__ . "/../utils/Conexao.php";

class FuncionarioDAO extends Conexao {

    private $db;
    private $lastError = null;

    public function __construct() {
        try {
            $this->db = $this->pegarConexao();
        } catch (PDOException $e) {
            // Log de erro ou redirecionamento
            die("Erro ao conectar: " . $e->getMessage());
        }
    }

    public function getLastError() {
        return $this->lastError;
    }

    // CRIAR - Inserir novo funcionário
    public function inserir(Funcionario $model) {
        try {
            // Hash da senha antes de salvar
            $senhaHash = password_hash($model->getSenhaFuncionario(), PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO funcionario (
                nome_funcionario, 
                sobrenome_funcionario,
                cpf_funcionario, 
                data_nascimento_funcionario,
                data_contratacao_funcionario,
                telefone_funcionario, 
                matricula_funcionario,
                cod_voto_funcionario,
                ativo_funcionario,
                adm_funcionario, 
                email_funcionario,
                senha_funcionario,
                votou_ultima_eleicao
            ) VALUES (
                :nome, 
                :sobrenome, 
                :cpf, 
                :nasc, 
                :contra, 
                :tel, 
                :matri, 
                :voto, 
                :ativo, 
                :adm, 
                :email, 
                :senha,
                :votou
            )";
            
            error_log("DEBUG DAO: SQL - " . $sql);
            error_log("DEBUG DAO: Preparando statement");
            
            $stmt = $this->db->prepare($sql);

            error_log("DEBUG DAO: Bindando parâmetros");
            error_log("DEBUG DAO: Valores do modelo:");
            error_log("  - Nome: '" . $model->getNomeFuncionario() . "'");
            error_log("  - Sobrenome: '" . $model->getSobrenomeFuncionario() . "'");
            error_log("  - CPF: '" . $model->getCpfFuncionario() . "'");
            error_log("  - Data Nasc: '" . $model->getDataNascimentoFuncionario() . "'");
            error_log("  - Data Contr: '" . $model->getDataContratacaoFuncionario() . "'");
            error_log("  - Telefone: '" . $model->getTelefoneFuncionario() . "'");
            error_log("  - Matrícula: '" . $model->getMatriculaFuncionario() . "'");
            error_log("  - Código Voto: '" . $model->getCodigoVotoFuncionario() . "'");
            error_log("  - Ativo: " . ($model->getAtivoFuncionario() ? '1' : '0'));
            error_log("  - Admin: " . ($model->getAdmFuncionario() ? '1' : '0'));
            error_log("  - Email: '" . $model->getEmailFuncionario() . "'");
            error_log("  - Senha: [HASH PROTEGIDO]");
            
            $stmt->bindValue(':nome',      $model->getNomeFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':sobrenome', $model->getSobrenomeFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':cpf',       $model->getCpfFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':nasc',      $model->getDataNascimentoFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':contra',    $model->getDataContratacaoFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':tel',       $model->getTelefoneFuncionario() ?: '', PDO::PARAM_STR);
            $stmt->bindValue(':matri',     $model->getMatriculaFuncionario() ?: '', PDO::PARAM_STR);
            $stmt->bindValue(':voto',      $model->getCodigoVotoFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':ativo',     $model->getAtivoFuncionario() ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':adm',       $model->getAdmFuncionario() ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':email',     $model->getEmailFuncionario(), PDO::PARAM_STR);
            $stmt->bindValue(':senha',     $senhaHash, PDO::PARAM_STR);
            $stmt->bindValue(':votou',    0, PDO::PARAM_INT); // Novo funcionário não votou ainda

            error_log("DEBUG DAO: Executando query");
            $executeResult = $stmt->execute();
            error_log("DEBUG DAO: Execute result - " . ($executeResult ? 'SUCCESS' : 'FAILED'));
            
            if (!$executeResult) {
                $errorInfo = $stmt->errorInfo();
                $this->lastError = $errorInfo;
                error_log("DEBUG DAO: PDO Error Info - " . print_r($errorInfo, true));
            } else {
                $this->lastError = null;
            }
            
            return $executeResult;
        } catch (PDOException $e) {
            $this->lastError = [
                $e->getCode(),
                $e->getCode(),
                $e->getMessage()
            ];
            error_log("Erro ao inserir funcionário: " . $e->getMessage());
            error_log("SQL executado: " . $sql);
            error_log("Dados: " . print_r([
                'nome' => $model->getNomeFuncionario(),
                'cpf' => $model->getCpfFuncionario(),
                'email' => $model->getEmailFuncionario(),
                'codigo_voto' => $model->getCodigoVotoFuncionario()
            ], true));
            return false;
        }
    }


    // LER - Buscar todos os funcionários
    public function buscarTodos() {
        try {
            $sql = "SELECT * FROM funcionario";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionários: " . $e->getMessage());
            return [];
        }
    }

    // LER - Buscar por ID
    public function buscarPorId(int $id) {
        try {
            $sql = "SELECT * FROM funcionario WHERE id_funcionario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por ID: " . $e->getMessage());
            return null;
        }
    }

    // LER - Buscar por Matrícula e CPF
    public function buscarPorMatriculaECpf(string $matricula, string $cpf) {
        try {
            $sql = "SELECT * FROM funcionario WHERE matricula_funcionario = :matricula AND cpf_funcionario = :cpf LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':matricula', $matricula, PDO::PARAM_STR);
            $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por matrícula e CPF: " . $e->getMessage());
            return null;
        }
    }

    // LER - Buscar por CPF apenas
    public function buscarPorCpf(string $cpf) {
        try {
            $sql = "SELECT * FROM funcionario WHERE cpf_funcionario = :cpf LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por CPF: " . $e->getMessage());
            return null;
        }
    }

    // LER - Buscar por Matrícula apenas
    public function buscarPorMatricula(string $matricula) {
        try {
            $sql = "SELECT * FROM funcionario WHERE matricula_funcionario = :matricula LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':matricula', $matricula, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por matrícula: " . $e->getMessage());
            return null;
        }
    }

    // ATUALIZAR - Atualizar dados do funcionário
    public function atualizar(Funcionario $model) {
        try {
            // Se a senha foi preenchida, atualiza também
            if (!empty($model->getSenhaFuncionario())) {
                // Hash da nova senha
                $senhaHash = password_hash($model->getSenhaFuncionario(), PASSWORD_DEFAULT);
                
                $sql = "UPDATE funcionario 
                SET 
                    nome_funcionario = :nome,
                    sobrenome_funcionario = :sobrenome, 
                    cpf_funcionario = :cpf,
                    data_nascimento_funcionario = :nasc,
                    data_contratacao_funcionario = :contra,
                    telefone_funcionario = :tel,
                    matricula_funcionario = :matri,
                    cod_voto_funcionario = :voto,
                    ativo_funcionario = :ativo, 
                    adm_funcionario = :adm,
                    email_funcionario = :email,
                    senha_funcionario = :senha
                    WHERE id_funcionario = :id";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':nome',      $model->getNomeFuncionario());
                $stmt->bindValue(':sobrenome', $model->getSobrenomeFuncionario());
                $stmt->bindValue(':cpf',       $model->getCpfFuncionario());
                $stmt->bindValue(':nasc',      $model->getDataNascimentoFuncionario());
                $stmt->bindValue(':contra',    $model->getDataContratacaoFuncionario());
                $stmt->bindValue(':tel',       $model->getTelefoneFuncionario());
                $stmt->bindValue(':matri',     $model->getMatriculaFuncionario());
                $stmt->bindValue(':voto',      $model->getCodigoVotoFuncionario());
                $stmt->bindValue(':ativo',     $model->getAtivoFuncionario(), PDO::PARAM_INT);
                $stmt->bindValue(':adm',       $model->getAdmFuncionario(),   PDO::PARAM_INT);
                $stmt->bindValue(':email',     $model->getEmailFuncionario());
                $stmt->bindValue(':senha',     $senhaHash);
                $stmt->bindValue(':id',        $model->getIdFuncionario(),    PDO::PARAM_INT);
            } else {
                $sql = "UPDATE funcionario 
                SET 
                    nome_funcionario = :nome,
                    sobrenome_funcionario = :sobrenome, 
                    cpf_funcionario = :cpf,
                    data_nascimento_funcionario = :nasc,
                    data_contratacao_funcionario = :contra,
                    telefone_funcionario = :tel,
                    matricula_funcionario = :matri,
                    cod_voto_funcionario = :voto,
                    ativo_funcionario = :ativo, 
                    adm_funcionario = :adm,
                    email_funcionario = :email
                    WHERE id_funcionario = :id";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':nome',      $model->getNomeFuncionario());
                $stmt->bindValue(':sobrenome', $model->getSobrenomeFuncionario());
                $stmt->bindValue(':cpf',       $model->getCpfFuncionario());
                $stmt->bindValue(':nasc',      $model->getDataNascimentoFuncionario());
                $stmt->bindValue(':contra',    $model->getDataContratacaoFuncionario());
                $stmt->bindValue(':tel',       $model->getTelefoneFuncionario());
                $stmt->bindValue(':matri',     $model->getMatriculaFuncionario());
                $stmt->bindValue(':voto',      $model->getCodigoVotoFuncionario());
                $stmt->bindValue(':ativo',     $model->getAtivoFuncionario(), PDO::PARAM_INT);
                $stmt->bindValue(':adm',       $model->getAdmFuncionario(),   PDO::PARAM_INT);
                $stmt->bindValue(':email',     $model->getEmailFuncionario());
                $stmt->bindValue(':id',        $model->getIdFuncionario(),    PDO::PARAM_INT);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar funcionário: " . $e->getMessage());
            return false;
        }
    }

    // EXCLUIR - Deletar funcionário
    public function deletar(int $id) {
        try {
            // Iniciar transação
            $this->db->beginTransaction();
            
            // Primeiro, verificar se funcionário tem votos em eleições ABERTAS
            $sqlCheckVotos = "SELECT COUNT(*) 
                              FROM voto v
                              INNER JOIN eleicao e ON v.usuario_fk = e.id_eleicao
                              WHERE v.usuario_fk = :id 
                              AND e.status_eleicao = 'ABERTA'";
            $stmtCheckVotos = $this->db->prepare($sqlCheckVotos);
            $stmtCheckVotos->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtCheckVotos->execute();
            $temVotosEmEleicaoAberta = $stmtCheckVotos->fetchColumn() > 0;
            
            // Verificar se funcionário é candidato em eleições ABERTAS
            $sqlCheckCandidato = "SELECT COUNT(*) 
                                 FROM candidato c
                                 INNER JOIN eleicao e ON c.eleicao_fk = e.id_eleicao
                                 WHERE c.usuario_fk = :id 
                                 AND e.status_eleicao = 'ABERTA'";
            $stmtCheckCandidato = $this->db->prepare($sqlCheckCandidato);
            $stmtCheckCandidato->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtCheckCandidato->execute();
            $temCandidaturaEmEleicaoAberta = $stmtCheckCandidato->fetchColumn() > 0;
            
            if ($temVotosEmEleicaoAberta || $temCandidaturaEmEleicaoAberta) {
                // Se tem votos ou candidatura em eleição ABERTA, não pode excluir
                $this->db->rollBack();
                return [
                    'sucesso' => false,
                    'motivo' => $temVotosEmEleicaoAberta ? 
                        'Este funcionário não pode ser excluído pois registrou votos em eleição ABERTA.' :
                        'Este funcionário não pode ser excluído pois é candidato em eleição ABERTA.'
                ];
            }
            
            // Se não tem restrições em eleições abertas, pode excluir normalmente
            $sql = "DELETE FROM funcionario WHERE id_funcionario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            // Confirmar transação
            $this->db->commit();
            
            return [
                'sucesso' => $resultado,
                'motivo' => $resultado ? null : 'Erro ao excluir funcionário no banco de dados.'
            ];
            
        } catch (PDOException $e) {
            // Desfazer transação em caso de erro
            $this->db->rollBack();
            error_log("Erro ao deletar funcionário: " . $e->getMessage());
            return [
                'sucesso' => false,
                'motivo' => 'Erro no banco de dados: ' . $e->getMessage()
            ];
        }
    }

    // EXCLUIR FORÇADO - Deletar funcionário mesmo com votos (para admin)
    public function deletarForcado(int $id) {
        try {
            // Iniciar transação
            $this->db->beginTransaction();
            
            // Primeiro, deletar votos do funcionário (se existirem)
            $sqlDeleteVotos = "DELETE FROM voto WHERE usuario_fk = :id";
            $stmtDeleteVotos = $this->db->prepare($sqlDeleteVotos);
            $stmtDeleteVotos->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtDeleteVotos->execute();
            
            // Deletar candidaturas (se existirem)
            $sqlDeleteCandidatos = "DELETE FROM candidato WHERE usuario_fk = :id";
            $stmtDeleteCandidatos = $this->db->prepare($sqlDeleteCandidatos);
            $stmtDeleteCandidatos->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtDeleteCandidatos->execute();
            
            // Finalmente deletar o funcionário
            $sql = "DELETE FROM funcionario WHERE id_funcionario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            // Confirmar transação
            $this->db->commit();
            
            return [
                'sucesso' => $resultado,
                'motivo' => $resultado ? null : 'Erro ao excluir funcionário no banco de dados.',
                'votos_removidos' => $stmtDeleteVotos->rowCount(),
                'candidaturas_removidas' => $stmtDeleteCandidatos->rowCount()
            ];
            
        } catch (PDOException $e) {
            // Desfazer transação em caso de erro
            $this->db->rollBack();
            error_log("Erro ao deletar funcionário forçado: " . $e->getMessage());
            return [
                'sucesso' => false,
                'motivo' => 'Erro no banco de dados: ' . $e->getMessage()
            ];
        }
    }

    // Buscar por código de voto
    public function buscarPorCodigoVoto(string $codigoVoto) {
        try {
            $sql = "SELECT * FROM funcionario WHERE cod_voto_funcionario = :codigo AND ativo_funcionario = 1 LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':codigo', $codigoVoto, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por código de voto: " . $e->getMessage());
            return null;
        }
    }

    // Contar funcionários ativos
    public function contarFuncionariosAtivos() {
        try {
            $sql = "SELECT COUNT(*) FROM funcionario WHERE ativo_funcionario = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar funcionários ativos: " . $e->getMessage());
            return 0;
        }
    }
}