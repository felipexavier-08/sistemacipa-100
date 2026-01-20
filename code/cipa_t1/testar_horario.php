<?php
// Teste de horário
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir fuso horário do Brasil
date_default_timezone_set('America/Sao_Paulo');

echo "<h1>🕐 Teste de Horário</h1>";

echo "<h2>📊 Informações de Tempo:</h2>";
echo "<p><strong>Data/Hora Atual (Brasil):</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<p><strong>Timestamp:</strong> " . time() . "</p>";
echo "<p><strong>Timezone:</strong> " . date_default_timezone_get() . "</p>";
echo "<p><strong>GMT:</strong> " . date('c') . "</p>";

echo "<h2>🔍 Formatos de Data:</h2>";
echo "<ul>";
echo "<li><strong>d/m/Y H:i:</strong> " . date('d/m/Y H:i') . "</li>";
echo "<li><strong>d/m/Y H:i:s:</strong> " . date('d/m/Y H:i:s') . "</li>";
echo "<li><strong>H:i:</strong> " . date('H:i') . "</li>";
echo "<li><strong>Y-m-d H:i:s:</strong> " . date('Y-m-d H:i:s') . "</li>";
echo "</ul>";

echo "<h2>🌍 Comparação de Timezones:</h2>";
$timezones = ['America/Sao_Paulo', 'UTC', 'America/New_York', 'Europe/London'];
foreach ($timezones as $tz) {
    $original = date_default_timezone_get();
    date_default_timezone_set($tz);
    echo "<p><strong>$tz:</strong> " . date('d/m/Y H:i:s') . "</p>";
    date_default_timezone_set($original);
}

echo "<h2>📧 Teste de Email com Horário Corrigido:</h2>";

// Carregar serviço
require_once __DIR__ . '/utils/EmailServiceBrevo.php';

$config = require __DIR__ . '/config/email_brevo.php';

// Formulário de teste
$testEmail = $_POST['email'] ?? '';
$nomeTeste = $_POST['nome'] ?? 'Usuário Teste';

if ($testEmail && filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
    if (empty($config['api_key']) || $config['api_key'] === 'CHAVE_AQUI') {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 4px;'>";
        echo "<h3>❌ ERRO!</h3>";
        echo "<p>Configure sua API Key do Brevo primeiro!</p>";
        echo "</div>";
    } else {
        echo "<h2>🚀 Enviando Teste com Horário Corrigido...</h2>";
        echo "<p>Enviando para: <strong>" . htmlspecialchars($testEmail) . "</strong></p>";
        echo "<p>Horário do envio: <strong>" . date('d/m/Y H:i:s') . "</strong></p>";
        
        $emailService = new EmailServiceBrevo();
        
        $dadosTeste = [
            'eleicao' => 'Eleição Teste CIPA ' . date('Y'),
            'data_voto' => date('d/m/Y H:i:s'), // Agora com horário correto
            'codigo_voto' => 'TEST' . strtoupper(substr(md5(time()), 0, 6))
        ];
        
        $resultado = $emailService->enviarComprovanteVoto($testEmail, $nomeTeste, $dadosTeste);
        
        if ($resultado) {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px;'>";
            echo "<h3>✅ SUCESSO!</h3>";
            echo "<p>Email enviado com horário corrigido!</p>";
            echo "<p>Verifique se o horário no email está correto.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 4px;'>";
            echo "<h3>❌ FALHA!</h3>";
            echo "<p>Não foi possível enviar o email.</p>";
            echo "</div>";
        }
    }
}

echo "<form method='post' style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Seu Email:</label>";
echo "<input type='email' name='email' value='" . htmlspecialchars($testEmail) . "' required style='width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<button type='submit' style='background: #4b5c49; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>🕐 Testar Horário no Email</button>";
echo "</form>";

echo "<h2>🔧 Correções Aplicadas:</h2>";
echo "<ul>";
echo "<li>✅ <strong>VotoController.php</strong> - Adicionado timezone Brazil</li>";
echo "<li>✅ <strong>EmailServiceBrevo.php</strong> - Adicionado timezone Brazil</li>";
echo "<li>✅ <strong>date_default_timezone_set('America/Sao_Paulo')</strong> - Aplicado</li>";
echo "</ul>";

echo "<h2>⚠️ Importante:</h2>";
echo "<p>O horário agora deve aparecer corretamente no comprovante de voto.</p>";
echo "<p>Se ainda estiver errado, pode ser necessário configurar o timezone no php.ini do servidor.</p>";
?>
