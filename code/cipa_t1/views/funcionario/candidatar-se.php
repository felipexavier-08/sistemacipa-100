<?php
    // Inicia a sessão para acessar os dados do funcionário logado
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    $funcionarioLogado = $_SESSION['funcionario_logado'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatar-se - Sistema CIPA</title>
    <style>


        .form-actions{
            display: flex;
            justify-content: space-around;
            justify-items: center;
            align-content: center;
        }

        .btn-cancel{
            background-color: #6c757d;
            align-self: center;
            color:white;
            padding: 3px;
            border-radius: 3px;
            margin-top: 5px;
        }

        .btn-cancel:hover{
            color: red;
        }

    </style>
</head>
<body>

    <div class="header">
        <div class="header-icon">🎯</div>
        <div class="header-title">
            <h1>Candidatar-se</h1>
            <p>Sistema CIPA - Autocandidatura</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/funcionario/home">Voltar</a>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h1>Formulário de Candidatura</h1>
            
            <div class="info-box">
                <h3>Dados do Funcionário</h3>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($funcionarioLogado['nome_funcionario'] . ' ' . $funcionarioLogado['sobrenome_funcionario']); ?></p>
                <p><strong>Matrícula:</strong> <?php echo htmlspecialchars($funcionarioLogado['matricula_funcionario']); ?></p>
                <p><strong>CPF:</strong> <?php echo htmlspecialchars($funcionarioLogado['cpf_funcionario']); ?></p>
            </div>

            <?php if (isset($_SESSION['erro_candidatura'])): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_candidatura']); ?>
                </div>
                <?php unset($_SESSION['erro_candidatura']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['sucesso_candidatura'])): ?>
                <div class="alert alert-success">
                    <strong>Sucesso:</strong> <?php echo htmlspecialchars($_SESSION['sucesso_candidatura']); ?>
                </div>
                <?php unset($_SESSION['sucesso_candidatura']); ?>
            <?php endif; ?>

            <form method="post" action="/code/cipa_t1/funcionario/candidatar-se" enctype="multipart/form-data">
                <fieldset>
                    <legend>Dados da Candidatura</legend>
                    
                    <label for="numeroCandidato">Número do Candidato:</label>
                    <input type="text" id="numeroCandidato" name="numeroCandidato" required placeholder="Ex: 101" maxlength="4" pattern="[0-9]{1,4}">
                    <small>Escolha um número único para sua candidatura (1 a 9999).</small>

                    <label for="cargoCandidato">Cargo:</label>
                    <input type="text" id="cargoCandidato" name="cargoCandidato" value="Titular" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                    <small>Cargo padrão para candidatura: Titular CIPA.</small>

                    <label for="fotoCandidato">Foto do Candidato (opcional):</label>
                    <input type="file" id="fotoCandidato" name="fotoCandidato" accept="image/*">
                    <small>Formatos aceitos: JPG e PNG. Tamanho máximo: 5MB.</small>
                </fieldset>

                <div class="form-actions">
                    <button type="submit" style="margin-top: 5px;" class="btn-primary">Confirmar Candidatura</button>
                    <a href="/code/cipa_t1/funcionario/home" style="margin-top: 5px;" class="btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
