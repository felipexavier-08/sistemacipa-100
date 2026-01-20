<?php
    require_once __DIR__ . "/../models/Documento.php";
    require_once __DIR__ . "/../models/Funcionario.php";
    abstract class Util {
        public static function converterArrayDoc($arrayAssociativo) {
            $documentos = [];
            foreach ($arrayAssociativo as $item) {
                $documento = new Documento(
                    $item['data_inicio_documento'],  
                    $item['data_fim_documento'],     
                    $item['titulo_documento'],       
                    $item['tipo_documento'],         
                    $item['pdf_documento'],          
                    $item['data_hora_documento'],    
                    $item['id_documento'],
                    $item['status_eleicao_vinculada'] ?? 'SEM_VINCULO' // Adicionar status de vinculação
                );
                
                $documentos[] = $documento;
            }

            return $documentos;
        }

        public static function converterArrayFuncionario($arrayAssociativo) {
            $funcionarios = [];
            foreach ($arrayAssociativo as $item) {
                $funcionario = new Funcionario(
                    $item['nome_funcionario'],
                    $item['sobrenome_funcionario'],
                    $item['cpf_funcionario'],
                    $item['data_nascimento_funcionario'],
                    $item['data_contratacao_funcionario'],
                    $item['telefone_funcionario'],
                    $item['matricula_funcionario'],
                    (int)$item['ativo_funcionario'], // Convertido para int
                    (int)$item['adm_funcionario'], // Convertido para int
                    $item['email_funcionario'],
                    $item['senha_funcionario'],
                    $item['cod_voto_funcionario'] ?? '', // Código de voto do banco
                    $item['id_funcionario']
                    );
                    $funcionarios[] = $funcionario;
            }
            return $funcionarios;
        }
    }