<?php
    
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Funcionário - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">🔍</div>
        <div class="header-title">
            <h1>Buscar Funcionário</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h1>Buscar Funcionário</h1>
            <p>Preencha pelo menos um dos campos abaixo para buscar o funcionário.</p>

            <?php if (isset($_SESSION['erro_matricula'])): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_matricula']); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/code/cipa_t1/funcionario/cadastrar-por-matricula">
                <label for="matriculaFuncionario">Matrícula:</label>
                <input type="text" id="matriculaFuncionario" name="matriculaFuncionario" placeholder="Digite a matrícula do funcionário">
                
                <label for="cpfFuncionario">CPF:</label>
                <input type="text" id="cpfFuncionario" name="cpfFuncionario" maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números (11 dígitos)">
                
                <div style="margin: 20px 0;">
                    <button type="submit" name="acao" value="buscar_matricula">Buscar por Matrícula</button>
                    <button type="submit" name="acao" value="buscar_cpf">Buscar por CPF</button>
                    <button type="submit" name="acao" value="buscar_ambos">Buscar por Matrícula e CPF</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
