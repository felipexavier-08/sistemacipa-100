<?php
/**
 * Helper class para gerenciar alertas do sistema
 */
class AlertHelper {
    
    /**
     * Define uma mensagem de sucesso na sessão
     */
    public static function sucesso($mensagem) {
        $_SESSION['sucesso'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de erro na sessão
     */
    public static function erro($mensagem) {
        $_SESSION['erro'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de informação na sessão
     */
    public static function info($mensagem) {
        $_SESSION['info'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de aviso na sessão
     */
    public static function aviso($mensagem) {
        $_SESSION['warning'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de sucesso específica para funcionários
     */
    public static function sucessoFuncionario($mensagem) {
        $_SESSION['sucesso_funcionario'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de erro específica para funcionários
     */
    public static function erroFuncionario($mensagem) {
        $_SESSION['erro_funcionario'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de sucesso específica para documentos
     */
    public static function sucessoDocumento($mensagem) {
        $_SESSION['sucesso_documento'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de erro específica para documentos
     */
    public static function erroDocumento($mensagem) {
        $_SESSION['erro_documento'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de sucesso específica para eleições
     */
    public static function sucessoEleicao($mensagem) {
        $_SESSION['sucesso_eleicao'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de erro específica para eleições
     */
    public static function erroEleicao($mensagem) {
        $_SESSION['erro_eleicao'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de sucesso específica para candidatos
     */
    public static function sucessoCandidato($mensagem) {
        $_SESSION['sucesso_candidato'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de erro específica para candidatos
     */
    public static function erroCandidato($mensagem) {
        $_SESSION['erro_candidato'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de sucesso específica para votos
     */
    public static function sucessoVoto($mensagem) {
        $_SESSION['sucesso_voto'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de erro específica para votos
     */
    public static function erroVoto($mensagem) {
        $_SESSION['erro_voto'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de sucesso específica para atas
     */
    public static function sucessoAta($mensagem) {
        $_SESSION['sucesso_ata'] = $mensagem;
    }
    
    /**
     * Define uma mensagem de erro específica para atas
     */
    public static function erroAta($mensagem) {
        $_SESSION['erro_ata'] = $mensagem;
    }
}
?>
