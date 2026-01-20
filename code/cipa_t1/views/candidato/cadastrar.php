<?php
    // Inicia a sessão para acessar as eleições carregadas pelo controller
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    // No seu controller, você deve popular $_SESSION["eleicoes"] com objetos Eleicao
    $eleicoes = $_SESSION["eleicoes"] ?? [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Candidato - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">🎯</div>
        <div class="header-title">
            <h1>Cadastrar Candidato</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <div class="form-container">
            <h1>Dados da Candidatura</h1>

            <?php if (isset($_SESSION['erro_candidato'])): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_candidato']); ?>
                </div>
                <?php unset($_SESSION['erro_candidato']); ?>
            <?php endif; ?>

            <form method="post" action="/code/cipa_t1/candidato/cadastrar" enctype="multipart/form-data">
                <fieldset>
                    <legend>Vincular Funcionário</legend>
                    <label for="matriculaFuncionario">Matrícula do Funcionário:</label>
                    <input type="text" id="matriculaFuncionario" name="matriculaFuncionario" required maxlength="20" placeholder="Digite a matrícula">

                    <label for="cpfFuncionario">CPF do Funcionário:</label>
                    <input type="text" id="cpfFuncionario" name="cpfFuncionario" required maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números (11 dígitos)">
                    <small>O sistema buscará o funcionário automaticamente por esses dados.</small>
                </fieldset>

                <fieldset>
                    <legend>Dados da Candidatura</legend>
                    <label for="numeroCandidato">Número do Candidato:</label>
                    <input type="text" id="numeroCandidato" name="numeroCandidato" required placeholder="Ex: 101" maxlength="4">

                    <label for="cargoCandidato">Cargo:</label>
                    <select id="cargoCandidato" name="cargoCandidato" required>
                        <option value="">Selecione um cargo...</option>
                        <option value="Titular">Titular</option>
                        <option value="Indicado">Indicado</option>
                    </select>

                    <label for="fotoCandidato">Foto do Candidato:</label>
                    <input type="file" id="fotoCandidato" name="fotoCandidato" accept="image/*">
                    <small>A foto é opcional. Você pode cadastrar o candidato sem foto.</small>
                </fieldset>

                <button type="submit">Finalizar Cadastro</button>
            </form>
        </div>
    </div>

</body>
</html>