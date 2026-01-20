<?php

// Definir fuso horário do Brasil
date_default_timezone_set('America/Sao_Paulo');

class EmailServiceBrevo {
    private $config;
    private $apiKey;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../config/email_brevo.php';
        $this->apiKey = $this->config['api_key'];
    }
    
    public function enviarComprovanteVoto($email, $nomeFuncionario, $dadosComprovante) {
        try {
            // Preparar dados para API Brevo
            $payload = [
                'sender' => [
                    'name' => $this->config['from_name'],
                    'email' => $this->config['from_email']
                ],
                'to' => [
                    [
                        'name' => $nomeFuncionario,
                        'email' => $email
                    ]
                ],
                'subject' => 'Comprovante de Voto - Eleição CIPA',
                'htmlContent' => $this->getTemplateComprovante($nomeFuncionario, $dadosComprovante)
            ];
            
            // Enviar requisição para API
            $response = $this->makeRequest($payload);
            
            if ($response && isset($response['messageId'])) {
                error_log("EMAIL BREVO SUCCESS: Comprovante enviado para $email");
                return true;
            } else {
                error_log("EMAIL BREVO ERROR: Falha ao enviar para $email - " . json_encode($response));
                return false;
            }
            
        } catch (Exception $e) {
            error_log("EMAIL BREVO EXCEPTION: " . $e->getMessage());
            return false;
        }
    }
    
    public function enviarCodigoVoto($email, $nomeFuncionario, $codigoVoto) {
        try {
            // Preparar dados para API Brevo
            $payload = [
                'sender' => [
                    'name' => $this->config['from_name'],
                    'email' => $this->config['from_email']
                ],
                'to' => [
                    [
                        'name' => $nomeFuncionario,
                        'email' => $email
                    ]
                ],
                'subject' => 'Seu Código de Voto - Sistema CIPA',
                'htmlContent' => $this->getTemplateCodigoVoto($nomeFuncionario, $codigoVoto)
            ];
            
            // Enviar requisição para API
            $response = $this->makeRequest($payload);
            
            if ($response && isset($response['messageId'])) {
                error_log("EMAIL BREVO SUCCESS: Código de voto enviado para $email");
                return true;
            } else {
                error_log("EMAIL BREVO ERROR: Falha ao enviar código para $email - " . json_encode($response));
                return false;
            }
            
        } catch (Exception $e) {
            error_log("EMAIL BREVO EXCEPTION: " . $e->getMessage());
            return false;
        }
    }
    
    public function enviarAtaFinal($email, $nomeFuncionario, $dadosAta) {
        try {
            // Preparar dados para API Brevo
            $payload = [
                'sender' => [
                    'name' => $this->config['from_name'],
                    'email' => $this->config['from_email']
                ],
                'to' => [
                    [
                        'name' => $nomeFuncionario,
                        'email' => $email
                    ]
                ],
                'subject' => 'ATA Final - Eleição CIPA Finalizada',
                'htmlContent' => $this->getTemplateAta($nomeFuncionario, $dadosAta)
            ];
            
            // Enviar requisição para API
            $response = $this->makeRequest($payload);
            
            if ($response && isset($response['messageId'])) {
                error_log("EMAIL BREVO SUCCESS: ATA enviada para $email");
                return true;
            } else {
                error_log("EMAIL BREVO ERROR: Falha ao enviar ATA para $email - " . json_encode($response));
                return false;
            }
            
        } catch (Exception $e) {
            error_log("EMAIL BREVO EXCEPTION: " . $e->getMessage());
            return false;
        }
    }
    
    public function enviarAtaParaTodosFuncionarios($funcionarios, $dadosAta) {
        try {
            $enviadosComSucesso = 0;
            $falhas = 0;
            
            foreach ($funcionarios as $funcionario) {
                // Enviar apenas para funcionários com email e ativos
                if (!empty($funcionario['email_funcionario']) && $funcionario['ativo_funcionario'] == 1) {
                    $resultado = $this->enviarAtaFinal(
                        $funcionario['email_funcionario'],
                        $funcionario['nome_funcionario'],
                        $dadosAta
                    );
                    
                    if ($resultado) {
                        $enviadosComSucesso++;
                    } else {
                        $falhas++;
                    }
                    
                    // Pequeno delay para não sobrecarregar a API
                    usleep(100000); // 0.1 segundo
                }
            }
            
            error_log("EMAIL BREVO ATA: Enviados com sucesso: $enviadosComSucesso, Falhas: $falhas");
            return [
                'enviados' => $enviadosComSucesso,
                'falhas' => $falhas,
                'total' => $enviadosComSucesso + $falhas
            ];
            
        } catch (Exception $e) {
            error_log("EMAIL BREVO EXCEPTION: " . $e->getMessage());
            return false;
        }
    }
    
    private function makeRequest($payload) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->config['api_url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'api-key: ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 201) { // Brevo retorna 201 para sucesso
            return json_decode($response, true);
        } else {
            error_log("EMAIL BREVO HTTP ERROR: $httpCode - $response");
            return false;
        }
    }
    
    private function getTemplateComprovante($nome, $dados) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <div style='background: #4b5c49; color: white; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>🗳️ Comprovante de Voto</h2>
                    <p style='margin: 5px 0 0 0;'>Sistema CIPA</p>
                </div>
                
                <div style='padding: 20px;'>
                    <p>Prezado(a) <strong>" . htmlspecialchars($nome) . "</strong>,</p>
                    <p>Seu voto foi registrado com sucesso na eleição CIPA. Abaixo está seu comprovante:</p>
                    
                    <div style='border: 2px solid #4b5c49; padding: 20px; margin: 20px 0; background: #f8f9fa; border-radius: 4px;'>
                        <h3 style='color: #4b5c49; margin-bottom: 15px;'>📋 Dados da Eleição</h3>
                        
                        <div style='margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee;'>
                            <strong style='color: #4b5c49;'>Edital:</strong> " . htmlspecialchars($dados['eleicao']) . "
                        </div>
                        
                        <div style='margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee;'>
                            <strong style='color: #4b5c49;'>Data/Hora do Voto:</strong> " . htmlspecialchars($dados['data_voto']) . "
                        </div>
                        
                        <div style='margin: 10px 0; padding: 5px 0;'>
                            <strong style='color: #4b5c49;'>Código do Voto:</strong>
                        </div>
                        
                        <div style='background: #28a745; color: white; padding: 15px; text-align: center; font-weight: bold; font-size: 18px; border-radius: 4px; margin: 10px 0; letter-spacing: 2px;'>
                            " . htmlspecialchars($dados['codigo_voto']) . "
                        </div>
                        
                        <p style='margin-top: 15px; font-style: italic; color: #666; text-align: center;'>
                            <em>Seu voto foi registrado de forma sigilosa e anônima.</em>
                        </p>
                    </div>
                    
                    <p style='text-align: center; margin-top: 20px; font-weight: bold; color: #4b5c49;'>
                        📄 Guarde este comprovante para seus arquivos.
                    </p>
                </div>
                
                <div style='text-align: center; margin-top: 20px; padding: 20px; background: #f8f9fa; color: #666; border-top: 1px solid #eee;'>
                    <p style='margin: 0; font-size: 12px;'>Este é um email automático. Não responda.</p>
                    <p style='margin: 5px 0 0 0; font-size: 12px;'>Sistema CIPA - " . date('d/m/Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getTemplateCodigoVoto($nome, $codigoVoto) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <div style='background: #4b5c49; color: white; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>🔐 Seu Código de Voto</h2>
                    <p style='margin: 5px 0 0 0;'>Sistema CIPA</p>
                </div>
                
                <div style='padding: 20px;'>
                    <p>Prezado(a) <strong>" . htmlspecialchars($nome) . "</strong>,</p>
                    <p>Seu cadastro foi realizado com sucesso no Sistema CIPA. Abaixo está seu código de voto para as eleições:</p>
                    
                    <div style='border: 2px solid #4b5c49; padding: 20px; margin: 20px 0; background: #f8f9fa; border-radius: 4px; text-align: center;'>
                        <h3 style='color: #4b5c49; margin-bottom: 15px;'>🔑 Seu Código de Acesso</h3>
                        
                        <div style='background: #007bff; color: white; padding: 20px; text-align: center; font-weight: bold; font-size: 24px; border-radius: 4px; margin: 15px 0; letter-spacing: 3px; font-family: monospace;'>
                            " . htmlspecialchars($codigoVoto) . "
                        </div>
                        
                        <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin-top: 15px;'>
                            <h4 style='color: #856404; margin: 0 0 10px 0;'>⚠️ INSTRUÇÕES IMPORTANTES:</h4>
                            <ul style='text-align: left; margin: 0; padding-left: 20px; color: #856404;'>
                                <li>Guarde este código em local seguro</li>
                                <li>Não compartilhe com outras pessoas</li>
                                <li>Você precisará deste código para votar</li>
                                <li>Este código é pessoal e intransferível</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                        <h4 style='color: #0066cc; margin: 0 0 10px 0;'>📅 Próximos Passos:</h4>
                        <ol style='margin: 0; padding-left: 20px; color: #0066cc;'>
                            <li>Aguarde o início das eleições</li>
                            <li>Acesse o sistema com seu CPF</li>
                            <li>Use este código para votar</li>
                            <li>Receba o comprovante por email</li>
                        </ol>
                    </div>
                    
                    <p style='text-align: center; margin-top: 20px; font-weight: bold; color: #4b5c49;'>
                        🎯 Fique atento às datas da eleição!
                    </p>
                </div>
                
                <div style='text-align: center; margin-top: 20px; padding: 20px; background: #f8f9fa; color: #666; border-top: 1px solid #eee;'>
                    <p style='margin: 0; font-size: 12px;'>Este é um email automático. Não responda.</p>
                    <p style='margin: 5px 0 0 0; font-size: 12px;'>Sistema CIPA - " . date('d/m/Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getTemplateAta($nome, $dados) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5;'>
            <div style='max-width: 800px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <div style='background: #4b5c49; color: white; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>📋 ATA Final - Eleição CIPA</h2>
                    <p style='margin: 5px 0 0 0;'>Sistema CIPA</p>
                </div>
                
                <div style='padding: 20px;'>
                    <p>Prezado(a) <strong>" . htmlspecialchars($nome) . "</strong>,</p>
                    <p>Informamos que a eleição CIPA foi finalizada. Abaixo está a ATA final com os resultados:</p>
                    
                    <div style='border: 2px solid #4b5c49; padding: 20px; margin: 20px 0; background: #f8f9fa; border-radius:4px;'>
                        <h3 style='color: #4b5c49; margin-bottom: 15px;'>📋 Dados da Eleição</h3>
                        
                        <div style='margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee;'>
                            <strong style='color: #4b5c49;'>Edital:</strong> " . htmlspecialchars($dados['titulo_documento']) . "
                        </div>
                        
                        <div style='margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee;'>
                            <strong style='color: #4b5c49;'>Período:</strong> " . htmlspecialchars($dados['periodo']) . "
                        </div>
                        
                        <div style='margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee;'>
                            <strong style='color: #4b5c49;'>Data de Finalização:</strong> " . htmlspecialchars($dados['data_finalizacao']) . "
                        </div>
                        
                        <div style='margin: 10px 0; padding: 5px 0;'>
                            <strong style='color: #4b5c49;'>Status:</strong> <span style='background: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.9em;'>FINALIZADA</span>
                        </div>
                    </div>
                    
                    <div style='border: 2px solid #4b5c49; padding: 20px; margin: 20px 0; background: #f8f9fa; border-radius:4px;'>
                        <h3 style='color: #4b5c49; margin-bottom: 15px;'>🏆 Resultados da Eleição</h3>
                        
                        <div style='margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee;'>
                            <strong style='color: #4b5c49;'>Total de Votantes:</strong> " . htmlspecialchars($dados['total_votantes']) . "
                        </div>
                        
                        <div style='margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee;'>
                            <strong style='color: #4b5c49;'>Votos Brancos:</strong> " . htmlspecialchars($dados['votos_brancos']) . "
                        </div>
                        
                        <div style='margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #eee;'>
                            <strong style='color: #4b5c49;'>Votos Nulos:</strong> " . htmlspecialchars($dados['votos_nulos']) . "
                        </div>
                        
                        <div style='margin: 10px 0; padding: 5px 0;'>
                            <strong style='color: #4b5c49;'>Total de Votos Válidos:</strong> " . htmlspecialchars($dados['votos_validos']) . "
                        </div>
                    </div>
                    
                    <div style='border: 2px solid #4b5c49; padding: 20px; margin: 20px 0; background: #f8f9fa; border-radius:4px;'>
                        <h3 style='color: #4b5c49; margin-bottom: 15px;'>👥 Candidatos Eleitos</h3>
                        " . $dados['tabela_resultados'] . "
                    </div>
                    
                    <p style='text-align: center; margin-top: 20px; font-weight: bold; color: #4b5c49;'>
                        📄 A ATA completa está disponível no sistema.
                    </p>
                </div>
                
                <div style='text-align: center; margin-top: 20px; padding: 20px; background: #f8f9fa; color: #666; border-top: 1px solid #eee;'>
                    <p style='margin: 0; font-size: 12px;'>Este é um email automático. Não responda.</p>
                    <p style='margin: 5px 0 0 0; font-size: 12px;'>Sistema CIPA - " . date('d/m/Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
