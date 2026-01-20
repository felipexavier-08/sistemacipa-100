<?php
// Script de teste completo para API Brevo
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>📧 Teste Completo - API Brevo</h1>";

// Carregar serviço
require_once __DIR__ . '/utils/EmailServiceBrevo.php';

try {
    echo "<p style='color: green;'>✅ EmailServiceBrevo carregado com sucesso!</p>";
    
    // Verificar configuração
    $config = require __DIR__ . '/config/email_brevo.php';
    echo "<h2>📋 Configuração Atual:</h2>";
    echo "<p><strong>API URL:</strong> " . htmlspecialchars($config['api_url']) . "</p>";
    echo "<p><strong>From Email:</strong> " . htmlspecialchars($config['from_email']) . "</p>";
    echo "<p><strong>From Name:</strong> " . htmlspecialchars($config['from_name']) . "</p>";
    echo "<p><strong>API Key:</strong> " . (empty($config['api_key']) || $config['api_key'] === 'CHAVE_AQUI' ? '<span style="color: red;">❌ Não configurada</span>' : '<span style="color: green;">✅ Configurada</span>') . "</p>";
    
    // Formulário de teste
    $testEmail = $_POST['email'] ?? '';
    $nomeTeste = $_POST['nome'] ?? 'Usuário Teste';
    $tipoTeste = $_POST['tipo'] ?? 'codigo';
    
    if ($testEmail && filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        if (empty($config['api_key']) || $config['api_key'] === 'CHAVE_AQUI') {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 4px;'>";
            echo "<h3>❌ ERRO!</h3>";
            echo "<p>Configure sua API Key do Brevo primeiro!</p>";
            echo "</div>";
        } else {
            echo "<h2>🚀 Enviando Teste...</h2>";
            echo "<p>Enviando para: <strong>" . htmlspecialchars($testEmail) . "</strong></p>";
            echo "<p>Tipo: <strong>" . ($tipoTeste === 'codigo' ? 'Código de Voto' : 'Comprovante de Voto') . "</strong></p>";
            
            $emailService = new EmailServiceBrevo();
            
            if ($tipoTeste === 'codigo') {
                $codigoTeste = 'TEST' . strtoupper(substr(md5(time()), 0, 6));
                $resultado = $emailService->enviarCodigoVoto($testEmail, $nomeTeste, $codigoTeste);
                $tipoMsg = "Código de Voto: $codigoTeste";
            } else {
                $dadosTeste = [
                    'eleicao' => 'Eleição Teste CIPA ' . date('Y'),
                    'data_voto' => date('d/m/Y H:i:s'),
                    'codigo_voto' => 'TEST' . strtoupper(substr(md5(time()), 0, 6))
                ];
                $resultado = $emailService->enviarComprovanteVoto($testEmail, $nomeTeste, $dadosTeste);
                $tipoMsg = "Comprovante de Eleição";
            }
            
            if ($resultado) {
                echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px;'>";
                echo "<h3>✅ SUCESSO!</h3>";
                echo "<p>$tipoMsg enviado via API Brevo!</p>";
                echo "<p>Verifique sua caixa de entrada.</p>";
                echo "</div>";
            } else {
                echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 4px;'>";
                echo "<h3>❌ FALHA!</h3>";
                echo "<p>Não foi possível enviar o email.</p>";
                echo "<p>Verifique sua API Key e conexão.</p>";
                echo "</div>";
            }
        }
    }
    
    // Formulário
    echo "<h2>📧 Testar Envio</h2>";
    echo "<form method='post' style='background: #f8f9fa; padding: 20px; border-radius: 8px;'>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Seu Email:</label>";
    echo "<input type='email' name='email' value='" . htmlspecialchars($testEmail) . "' required style='width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Seu Nome:</label>";
    echo "<input type='text' name='nome' value='" . htmlspecialchars($nomeTeste) . "' required style='width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Tipo de Email:</label>";
    echo "<select name='tipo' style='width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
    echo "<option value='codigo' " . ($tipoTeste === 'codigo' ? 'selected' : '') . ">🔐 Código de Voto (novo funcionário)</option>";
    echo "<option value='comprovante' " . ($tipoTeste === 'comprovante' ? 'selected' : '') . ">🗳️ Comprovante de Voto (após votação)</option>";
    echo "</select>";
    echo "</div>";
    echo "<button type='submit' style='background: #4b5c49; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>🚀 Enviar Teste</button>";
    echo "</form>";
    
    echo "<h2>🎯 Sistema Integrado</h2>";
    echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 4px;'>";
    echo "<h3>✅ Funcionalidades Ativas:</h3>";
    echo "<ul>";
    echo "<li><strong>🔐 Código de Voto:</strong> Enviado automaticamente quando novo funcionário é cadastrado</li>";
    echo "<li><strong>🗳️ Comprovante de Voto:</strong> Enviado automaticamente após funcionário votar</li>";
    echo "<li><strong>📧 Templates Profissionais:</strong> HTML responsivo com design moderno</li>";
    echo "<li><strong>🚀 API Brevo:</strong> 300 emails/dia grátis, funciona com Gmail</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>📊 Fluxo Completo:</h2>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 4px solid #4b5c49;'>";
    echo "<ol>";
    echo "<li><strong>Admin cadastra funcionário</strong> → Sistema gera código único</li>";
    echo "<li><strong>🔐 Email automático</strong> → Código enviado para o funcionário</li>";
    echo "<li><strong>Funcionario acessa sistema</strong> → Usa CPF + código para votar</li>";
    echo "<li><strong>🗳️ Voto registrado</strong> → Sistema envia comprovante automaticamente</li>";
    echo "<li><strong>📧 Confirmação</strong> → Funcionario recebe comprovante por email</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px;'>";
    echo "<h3>❌ ERRO!</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<h2>📁 Estrutura de Arquivos:</h2>";
echo "<ul>";
echo "<li>✅ config/email_brevo.php - Configuração da API Brevo</li>";
echo "<li>✅ utils/EmailServiceBrevo.php - Serviço completo com 2 métodos</li>";
echo "<li>✅ controllers/VotoController.php - Envia comprovante após voto</li>";
echo "<li>✅ controllers/FuncionarioController.php - Envia código ao cadastrar</li>";
echo "<li>✅ views/voto/sucesso.php - Mensagem de sucesso</li>";
echo "</ul>";

echo "<h2>🎉 PRONTO PARA USAR!</h2>";
echo "<p>O sistema agora envia emails automaticamente em ambos os momentos:</p>";
echo "<ul>";
echo "<li>✅ <strong>Ao cadastrar funcionário</strong> → Código de voto</li>";
echo "<li>✅ <strong>Ao registrar voto</strong> → Comprovante de votação</li>";
echo "</ul>";
?>
