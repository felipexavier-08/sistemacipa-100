<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Período Encerrado - Sistema CIPA</title>
</head>
<body>
    <div class="header">
        <div class="header-icon">✓</div>
        <div class="header-title">
            <h1>Sistema CIPA</h1>
            <p>Comissão Interna de Prevenção de Acidentes</p>
        </div>
        <div class="header-actions">
            <span style="color: rgba(255,255,255,0.9);"><?php echo htmlspecialchars($_SESSION['funcionario_logado']['nome_funcionario']); ?></span>
            <a href="/code/cipa_t1/logout">Sair</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <div class="form-container" style="max-width: 600px; text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 20px;">📅</div>
            
            <h1 style="color: #dc3545; margin-bottom: 20px;">Período de Candidaturas Encerrado</h1>
            
            <div style="background: #f8f9fa; padding: 30px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545;">
                <p style="font-size: 1.1em; margin: 0; color: #495057;">
                    <strong>O período de candidaturas para esta eleição foi encerrado.</strong>
                </p>
                <p style="margin: 15px 0; color: #6c757d;">
                    O administrador autorizou o início da votação e não são mais permitidas novas inscrições de candidatos.
                </p>
                <p style="margin: 15px 0; color: #28a745; font-weight: bold;">
                    🗳️ A votação está em andamento! Verifique sua página inicial para acessar a urna.
                </p>
            </div>

            <div style="background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #007bff;">
                <h3 style="margin-top: 0; color: #0056b3;">O que fazer agora?</h3>
                <ul style="text-align: left; color: #495057;">
                    <li>Aguarde o resultado da eleição</li>
                    <li>Os candidatos serão divulgados após o período de votação</li>
                    <li>Entre em contato com o administrador em caso de dúvidas</li>
                </ul>
            </div>

            <div style="margin-top: 30px;">
                <?php if ($_SESSION['funcionario_logado']['adm_funcionario'] == 1): ?>
                    <a href="/code/cipa_t1/" class="btn-link" style="background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px;">
                        🏠 Página Inicial (Admin)
                    </a>
                    
                    <a href="/code/cipa_t1/eleicao/gerenciar" class="btn-link" style="background-color: #6c757d; color: white; padding: 12px 30px; text-decoration: none; border-radius:4px; display: inline-block; margin: 5px;">
                        ⚙️ Gerenciar Eleição
                    </a>
                <?php else: ?>
                    <a href="/code/cipa_t1/funcionario/home" class="btn-link" style="background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px;">
                        🏠 Página Inicial
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
