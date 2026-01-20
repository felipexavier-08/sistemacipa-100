<?php
/**
 * Configuração específica para SendGrid
 * Guia passo a passo para integrar SendGrid
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar SendGrid - Sistema CIPA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 14px; }
        .step { margin: 20px 0; padding: 20px; border-left: 4px solid #007bff; background: #f8f9fa; border-radius: 0 8px 8px 0; }
        h1, h2 { color: #2c3e50; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .highlight { background: #fff3cd; padding: 15px; border-radius: 4px; border-left: 4px solid #ffc107; margin: 15px 0; }
        .code-block { background: #2d3748; color: #e2e8f0; padding: 20px; border-radius: 8px; margin: 15px 0; font-family: 'Courier New', monospace; }
        input[type="text"] { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px; margin: 5px 0; }
        input[type="text"]:focus { border-color: #007bff; outline: none; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Configurar SendGrid - Sistema CIPA</h1>
        
        <?php
        // Processar configuração
        if (isset($_POST['configurar'])) {
            $apiKey = $_POST['api_key'];
            $fromEmail = $_POST['from_email'];
            $fromName = $_POST['from_name'];
            
            // Validar API Key (formato básico)
            if (strlen($apiKey) < 20) {
                echo "<div class='error'>❌ API Key inválida. A API Key do SendGrid deve ter pelo menos 20 caracteres.</div>";
            } else {
                // Atualizar EmailService.php
                $emailServiceFile = __DIR__ . '/utils/EmailService.php';
                $content = file_get_contents($emailServiceFile);
                
                // Substituir configuração
                $content = preg_replace("/'password' => '.*?'/", "'password' => '$apiKey'", $content);
                $content = preg_replace("/'from_email' => '.*?'/", "'from_email' => '$fromEmail'", $content);
                $content = preg_replace("/'from_name' => '.*?'/", "'from_name' => '$fromName'", $content);
                
                if (file_put_contents($emailServiceFile, $content)) {
                    echo "<div class='success'>✅ Configuração SendGrid atualizada com sucesso!</div>";
                    echo "<div class='info'>📧 API Key configurada. Teste o envio abaixo.</div>";
                } else {
                    echo "<div class='error'>❌ Erro ao atualizar o arquivo EmailService.php. Verifique permissões.</div>";
                }
            }
        }
        
        // Testar configuração
        if (isset($_POST['testar'])) {
            require_once __DIR__ . '/utils/EmailService.php';
            
            $testEmail = $_POST['test_email'];
            $emailService = new EmailService();
            
            echo "<div class='step'>";
            echo "<h3>🧪 Testando Envio com SendGrid</h3>";
            echo "<p><strong>Para:</strong> $testEmail</p>";
            
            $resultado = $emailService->testarConfiguracao($testEmail);
            
            if ($resultado) {
                echo "<div class='success'>✅ Email enviado com sucesso via SendGrid!</div>";
                echo "<p>Verifique sua caixa de entrada (e também spam/lixeira)</p>";
            } else {
                echo "<div class='error'>❌ Falha ao enviar email via SendGrid</div>";
                echo "<p>Verifique:</p>";
                echo "<ul>";
                echo "<li>API Key está correta e ativa</li>";
                echo "<li>Conta SendGrid está verificada</li>";
                echo "<li>Remetente (from_email) está validado no SendGrid</li>";
                echo "</ul>";
            }
            echo "</div>";
        }
        ?>
        
        <div class="step">
            <h2>📋 Passo 1: Criar Conta SendGrid</h2>
            
            <div class="grid">
                <div>
                    <h3>1. Cadastre-se no SendGrid</h3>
                    <a href="https://signup.sendgrid.com" target="_blank" class="btn btn-success">📝 Cadastrar no SendGrid</a>
                    <p><small>Plano gratuito: 100 emails/dia</small></p>
                    
                    <h3>2. Verifique seu Email</h3>
                    <p>Confirme o email de confirmação do SendGrid</p>
                    
                    <h3>3. Complete o Cadastro</h3>
                    <p>Preencha as informações básicas do perfil</p>
                </div>
                
                <div>
                    <h3>📊 Benefícios do Plano Gratuito:</h3>
                    <ul>
                        <li>✅ 100 emails por dia</li>
                        <li>✅ SMTP + API</li>
                        <li>✅ Dashboard completo</li>
                        <li>✅ Estatísticas de envio</li>
                        <li>✅ Suporte a templates</li>
                        <li>✅ Não precisa de cartão</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="step">
            <h2>🔑 Passo 2: Gerar API Key</h2>
            
            <div class="highlight">
                <h3>Como Gerar API Key:</h3>
                <ol>
                    <li>Faça login no <a href="https://app.sendgrid.com" target="_blank">painel SendGrid</a></li>
                    <li>Vá para <strong>Settings → API Keys</strong></li>
                    <li>Clique em <strong>Create API Key</strong></li>
                    <li>Selecione <strong>Restricted Access</strong></li>
                    <li>Marque as permissões:
                        <ul>
                            <li>☑️ Mail Send → Send Mail</li>
                            <li>☑️ Mail Send → Send Mail (com anexos)</li>
                        </ul>
                    </li>
                    <li>Dê um nome (ex: "Sistema CIPA")</li>
                    <li>Clique em <strong>Create & View</strong></li>
                    <li><strong>Copie a API Key</strong> (ela só aparece uma vez!)</li>
                </ol>
            </div>
            
            <div class="code-block">
                🔑 Exemplo de API Key do SendGrid:
                SG.xxxxxxxxxx.yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy
            </div>
        </div>
        
        <div class="step">
            <h2>📧 Passo 3: Configurar Remetente</h2>
            
            <h3>Opção A: Usar Email Padrão (Recomendado)</h3>
            <p>Use um email genérico como <strong>noreply@seusistema.com</strong></p>
            
            <h3>Opção B: Validar seu Domínio</h3>
            <ol>
                <li>No painel SendGrid, vá para <strong>Settings → Sender Authentication</strong></li>
                <li>Escolha <strong>Authenticate Your Domain</strong></li>
                <li>Siga as instruções DNS</li>
                <li>Isso permite enviar de qualquer email do seu domínio</li>
            </ol>
            
            <div class="warning">
                ⚠️ <strong>Importante:</strong> Enquanto não validar o domínio, só poderá enviar de emails verificados individualmente.
            </div>
        </div>
        
        <div class="step">
            <h2>⚙️ Passo 4: Configurar Sistema</h2>
            
            <form method="post">
                <h3>Dados do SendGrid:</h3>
                
                <label for="api_key">🔑 API Key do SendGrid:</label>
                <input type="text" id="api_key" name="api_key" 
                       placeholder="SG.xxxxxxxxxx.yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy" 
                       required style="font-family: monospace;">
                
                <label for="from_email">📧 Email de Remetente:</label>
                <input type="email" id="from_email" name="from_email" 
                       placeholder="noreply@seusistema.com" 
                       value="noreply@seusistema.com" required>
                
                <label for="from_name">📝 Nome do Remetente:</label>
                <input type="text" id="from_name" name="from_name" 
                       placeholder="Sistema CIPA" 
                       value="Sistema CIPA" required>
                
                <button type="submit" name="configurar" class="btn btn-success">💾 Salvar Configuração</button>
            </form>
        </div>
        
        <div class="step">
            <h2>🧪 Passo 5: Testar Envio</h2>
            
            <form method="post">
                <h3>Teste de Envio:</h3>
                
                <label for="test_email">📧 Email para Teste:</label>
                <input type="email" id="test_email" name="test_email" 
                       placeholder="seuemail@teste.com" required>
                
                <button type="submit" name="testar" class="btn">📧 Enviar Email de Teste</button>
            </form>
        </div>
        
        <div class="step">
            <h2>📋 Resumo da Configuração</h2>
            
            <div class="grid">
                <div>
                    <h3>✅ Configuração Atual:</h3>
                    <?php
                    require_once __DIR__ . '/utils/EmailService.php';
                    $emailService = new EmailService();
                    $config = $emailService->testarConfiguracao('test@example.com');
                    
                    echo "<ul>";
                    echo "<li><strong>Host:</strong> smtp.sendgrid.net</li>";
                    echo "<li><strong>Port:</strong> 587 (TLS)</li>";
                    echo "<li><strong>Username:</strong> apikey</li>";
                    echo "<li><strong>Password:</strong> " . (strlen($emailService->config['password']) > 20 ? '✅ Configurada' : '❌ Não configurada') . "</li>";
                    echo "<li><strong>From:</strong> " . $emailService->config['from_email'] . "</li>";
                    echo "</ul>";
                    ?>
                </div>
                
                <div>
                    <h3>📊 Limites do Plano Gratuito:</h3>
                    <ul>
                        <li>📧 100 emails por dia</li>
                        <li>🔄 Reinicia à meia-noite (UTC)</li>
                        <li>📈 Dashboard completo</li>
                        <li>🔍 Relatórios detalhados</li>
                    </ul>
                    
                    <p><small>Para mais emails, planos pagos a partir de $15/mês</small></p>
                </div>
            </div>
        </div>
        
        <div class="step">
            <h2>🚀 Próximos Passos</h2>
            
            <ol>
                <li>✅ Criar conta SendGrid</li>
                <li>✅ Gerar API Key</li>
                <li>✅ Configurar remetente</li>
                <li>✅ Salvar configuração acima</li>
                <li>✅ Testar envio</li>
                <li>🔄 <strong>Cadastrar novo funcionário para testar</strong></li>
            </ol>
            
            <div class="success">
                🎉 <strong>Parabéns!</strong> Após configurar, todos os novos funcionários receberão código de voto por email automaticamente!
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/code/cipa_t1/" class="btn">🏠 Voltar ao Sistema</a>
            <a href="/code/cipa_t1/funcionario/cadastrar" class="btn btn-success">👤 Cadastrar Funcionário</a>
        </div>
    </div>
</body>
</html>
