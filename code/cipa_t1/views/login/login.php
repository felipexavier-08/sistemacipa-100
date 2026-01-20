<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">✓</div>
        <div class="header-title">
            <h1>Sistema CIPA</h1>
            <p>Comissão Interna de Prevenção de Acidentes e Assédio</p>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <div class="form-container" style="max-width: 450px;">
            <h1 style="text-align: center;">Acesso ao Sistema</h1>

            <?php if (isset($_SESSION['erro_login'])): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_login']); ?>
                </div>
                <?php unset($_SESSION['erro_login']); ?>
            <?php endif; ?>

            <form method="post" action="/code/cipa_t1/login">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números (11 dígitos)">
                <small>Digite apenas números (11 dígitos)</small>

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required placeholder="Digite sua senha">
                <small><em>No primeiro acesso, use sua data de nascimento (DDMMAAAA)</em></small>

                <button type="submit" style="width: 100%;">Entrar</button>
            </form>
        </div>
    </div>

</body>
</html>
