<?php

    class FileUpload {
        

        public static function garantirPasta($caminho) {
            // Normalizar caminho (remover barras duplas)
            $caminho = str_replace(['\\', '//'], DIRECTORY_SEPARATOR, $caminho);
            
            // Se a pasta já existe e é gravável, retornar true
            if (file_exists($caminho) && is_writable($caminho)) {
                return true;
            }
            
            // Criar pasta se não existir
            if (!file_exists($caminho)) {
                $permissoes = (PHP_OS_FAMILY === 'Windows') ? null : 0755;
                
                if ($permissoes !== null) {
                    // Linux
                    if (!@mkdir($caminho, $permissoes, true)) {
                        return false;
                    }
                } else {
                    // Windows
                    if (!@mkdir($caminho, true)) {
                        return false;
                    }
                }
            }
            
            // Ajustar permissões se necessário (Linux)
            if (PHP_OS_FAMILY !== 'Windows') {
                @chmod($caminho, 0755);
            }
            
            // Verificar se agora é gravável
            if (!is_writable($caminho)) {
                return false;
            }
            
            return true;
        }
        
        /**
         * Valida se o arquivo é uma imagem válida
         */
        public static function validarImagem($arquivo) {
            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));
            
            if (!in_array($extensao, $extensoesPermitidas)) {
                return false;
            }
            
            // Verificar tipo MIME (se disponível)
            if (function_exists('mime_content_type')) {
                $tiposMimePermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $tipoMime = @mime_content_type($arquivo["tmp_name"]);
                
                if ($tipoMime && !in_array($tipoMime, $tiposMimePermitidos)) {
                    return false;
                }
            } elseif (function_exists('finfo_file')) {
                // Usar finfo se disponível
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $tipoMime = finfo_file($finfo, $arquivo["tmp_name"]);
                finfo_close($finfo);
                
                $tiposMimePermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if ($tipoMime && !in_array($tipoMime, $tiposMimePermitidos)) {
                    return false;
                }
            }
            
            return true;
        }
        
        /**
         * Valida se o arquivo é um PDF válido
         */
        public static function validarPdf($arquivo) {
            $extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));
            
            error_log("Validando PDF: " . $arquivo["name"]);
            error_log("Extensão: " . $extensao);
            error_log("Tamanho: " . $arquivo["size"]);
            
            if ($extensao !== 'pdf') {
                error_log("Extensão inválida: " . $extensao);
                return false;
            }
            
            // Verificação básica de tamanho (máximo 10MB)
            if ($arquivo["size"] > 10 * 1024 * 1024) {
                error_log("Arquivo muito grande: " . $arquivo["size"] . " bytes");
                return false;
            }
            
            // Verificação simples do tipo MIME
            $tipoMime = @mime_content_type($arquivo["tmp_name"]);
            error_log("Tipo MIME: " . $tipoMime);
            
            if ($tipoMime && strpos($tipoMime, 'pdf') === false) {
                error_log("Tipo MIME não contém PDF: " . $tipoMime);
                return false;
            }
            
            error_log("PDF validado com sucesso");
            return true;
        }
    }

?>
