<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro na Votação</title>
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
            border: 2px solid #e9ecef;
        }
        
        .error-icon {
            font-size: 4em;
            margin-bottom: 20px;
            color: #dc3545;
        }
        
        .error-title {
            color: #1e3a5f;
            font-size: 1.8em;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .error-message {
            color: #6c757d;
            font-size: 1.1em;
            line-height: 1.5;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #4b5c49;
        }
        
        .btn-voltar {
            background: #4b5c49;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            font-size: 1em;
        }
        
        .btn-voltar:hover {
            background: #4b5c49;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30,58,138,0.15);
        }
        
        .btn-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🚫</div>
        <h1 class="error-title">Votação Indisponível</h1>
        
        <?php if (isset($_SESSION['erro_voto'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($_SESSION['erro_voto']); ?>
            </div>
        <?php else: ?>
            <div class="error-message">
                Ocorreu um erro ao acessar a página de votação. Tente novamente mais tarde.
            </div>
        <?php endif; ?>
        
        <div class="btn-container">
            <a href="/code/cipa_t1/" class="btn-voltar">
                🏠 Voltar para Página Inicial
            </a>
        </div>
    </div>
</body>
</html>
