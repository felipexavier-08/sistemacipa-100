<?php
    $comprovante = $_SESSION['comprovante_voto'] ?? null;
    $sucesso = $_SESSION['sucesso_voto'] ?? '';
    $sucessoEmail = $_SESSION['sucesso_email'] ?? '';
    
    // Buscar dados da eleição
    $eleicao = null;
    if ($comprovante) {
        require_once __DIR__ . "/../../repositories/EleicaoDAO.php";
        $eleicaoDAO = new EleicaoDAO();
        $eleicao = $eleicaoDAO->buscarPorId($comprovante['id_eleicao']);
    }
    
    // Limpar mensagem de email após exibir
    if ($sucessoEmail) {
        unset($_SESSION['sucesso_email']);
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voto Registrado - Sistema CIPA</title>
    <style>
        .comprovante-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border: 2px solid #4b5c49;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
        }
        
        .comprovante-header {
            background: #4b5c49;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
        }
        
        .comprovante-header h2 {
            color:  white;
            margin: 0;
            font-size: 1.5em;
        }
        
        .comprovante-info {
            text-align: left;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .comprovante-info p {
            margin: 10px 0;
            font-size: 1.1em;
        }
        
        .comprovante-info strong {
            color: #28a745;
            display: inline-block;
            width: 150px;
        }
        
        .comprovante-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }
        
        .btn-imprimir {
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            font-weight: bold;
            font-size: 1.1em;
            margin: 10px;
            box-shadow: 0 2px 4px rgba(40,167,69,0.3);
        }
        
        .btn-imprimir:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40,167,69,0.4);
        }
        
        .btn-voltar {
            background-color: #6c757d;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            font-weight: bold;
            font-size: 1.1em;
            margin: 10px;
        }
        
        .btn-voltar:hover {
            background-color: #5a6268;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            
            .comprovante-container, .comprovante-container * {
                visibility: visible;
            }
            
            .comprovante-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 20px;
            }
            
            .btn-imprimir, .btn-voltar {
                display: none;
            }
            
            /* Remover cabeçalho e rodapé do navegador */
            @page {
                margin: 0.5in;
                size: auto;
            }
            
            /* Remover título da página e URL */
            @page :header {
                display: none;
            }
            
            @page :footer {
                display: none;
            }
            
            /* Para Chrome/Safari */
            @page {
                margin-top: 0.5in;
                margin-bottom: 0.5in;
            }
            
            h2, h3 {
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            
            .comprovante-info {
                page-break-inside: avoid;
            }
        }
        
        .codigo-voto {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 1.2em;
            letter-spacing: 2px;
            display: inline-block;
            margin: 10px 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-icon">✅</div>
        <div class="header-title">
            <h1>Voto Registrado com Sucesso!</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/funcionario/home" class="btn-link">Página Inicial</a>
        </div>
    </div>

    <div class="container">
        <?php if ($sucessoEmail): ?>
            <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px; text-align: center;">
                <h4 style="margin: 5px 0 10px 0;">📧 Email Enviado!</h4>
                <p style="margin: 0;"><?php echo htmlspecialchars($sucessoEmail); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($comprovante && $eleicao): ?>
            <div class="comprovante-container">
                <div class="comprovante-header">
                    <h2>🗳️ Comprovante de Voto</h2>
                </div>
                
                <div class="comprovante-info">
                    <h3>Eleição Ativa</h3>
                    <p><strong>Edital:</strong> <?php echo htmlspecialchars($eleicao['titulo_documento'] ?? 'N/A'); ?></p>
                    <p><strong>Período:</strong> <?php echo ($eleicao['data_inicio_eleicao'] ?? null) ? date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])) : 'N/A'; ?> a <?php echo ($eleicao['data_fim_eleicao'] ?? null) ? date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])) : 'N/A'; ?></p>
                    
                    <h3 style="margin-top: 20px;">Dados do Eleitor</h3>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($comprovante['nome_funcionario'] ?? 'N/A'); ?></p>
                    <p><strong>CPF:</strong> <?php echo htmlspecialchars($comprovante['cpf_funcionario'] ?? 'N/A'); ?></p>
                    <p><strong>Código de Voto:</strong></p>
                    <div class="codigo-voto"><?php echo htmlspecialchars($comprovante['codigo_voto'] ?? 'N/A'); ?></div>
                    
                    <h3 style="margin-top: 20px;">Informações do Voto</h3>
                    <p><strong>Data/Hora:</strong> <?php echo htmlspecialchars($comprovante['data_voto'] ?? 'N/A'); ?></p>
                    <p style="color: #6c757d; font-size: 0.9em; margin-top: 15px;">
                        <em>Seu voto foi registrado de forma sigilosa e anônima.</em>
                    </p>
                </div>
                
                <div class="comprovante-footer">
                    <p style="color: #6c757d; font-size: 0.9em; margin-bottom: 20px;">
                        Este comprovante serve como registro do seu voto na eleição CIPA.<br>
                        Guarde-o para seus arquivos pessoais.
                    </p>
                    
                    <div style="text-align: center;">
                        <a href="javascript:window.print()" class="btn-imprimir">
                            🖨️ Imprimir Comprovante
                        </a>
                        <a href="/code/cipa_t1/funcionario/home" class="btn-voltar">
                            🏠 Página Inicial
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <h3>✅ Voto Registrado com Sucesso!</h3>
                <p>Seu voto foi registrado com sucesso na eleição.</p>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="/code/cipa_t1/funcionario/home" class="btn-link" style="font-size: 1.1em; padding: 12px 24px;">
                    🏠 Voltar para Página Inicial
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Não limpar mais a sessão automaticamente
        // O comprovante ficará disponível até que o usuário limpe manualmente
    </script>
</body>
</html>
