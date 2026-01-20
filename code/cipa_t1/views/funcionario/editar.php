<?php
    $funcionario = $_SESSION['funcionario_editar'] ?? null;
    if (!$funcionario) {
        header("Location: /code/cipa_t1/funcionario/listar");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Funcionário - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">✏️</div>
        <div class="header-title">
            <h1>Editar Funcionário</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/funcionario/listar">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <div class="form-container">
            <h1>Dados do Funcionário</h1>
            <form method="post" action="/code/cipa_t1/funcionario/editar">
                <input type="hidden" name="idFuncionario" value="<?php echo $funcionario->getIdFuncionario(); ?>">

                <label for="nomeFuncionario">Nome:</label>
                <input type="text" id="nomeFuncionario" name="nomeFuncionario" value="<?php echo htmlspecialchars($funcionario->getNomeFuncionario()); ?>" required>

                <label for="sobrenomeFuncionario">Sobrenome:</label>
                <input type="text" id="sobrenomeFuncionario" name="sobrenomeFuncionario" value="<?php echo htmlspecialchars($funcionario->getSobrenomeFuncionario()); ?>" required>

                <label for="cpfFuncionario">CPF:</label>
                <input type="text" id="cpfFuncionario" name="cpfFuncionario" value="<?php echo htmlspecialchars($funcionario->getCpfFuncionario()); ?>" required maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números">
                <small>Digite apenas números (11 dígitos)</small>

                <label for="dataNascimentoFuncionario">Data de Nascimento:</label>
                <input type="date" id="dataNascimentoFuncionario" name="dataNascimentoFuncionario" value="<?php echo $funcionario->getDataNascimentoFuncionario(); ?>" required>

                <label for="dataContratacaoFuncionario">Data de Contratação:</label>
                <input type="date" id="dataContratacaoFuncionario" name="dataContratacaoFuncionario" value="<?php echo $funcionario->getDataContratacaoFuncionario(); ?>" required>

                <label for="telefoneFuncionario">Telefone:</label>
                <input type="tel" id="telefoneFuncionario" name="telefoneFuncionario" value="<?php echo htmlspecialchars($funcionario->getTelefoneFuncionario()); ?>" maxlength="11" pattern="[0-9]{11}" placeholder="Apenas números">
                <small>Digite apenas números (11 dígitos)</small>

                <label for="matriculaFuncionario">Matrícula:</label>
                <input type="text" id="matriculaFuncionario" name="matriculaFuncionario" value="<?php echo htmlspecialchars($funcionario->getMatriculaFuncionario()); ?>">

                <label for="codigoVotoFuncionario">Código de Voto:</label>
                <input type="text" id="codigoVotoFuncionario" name="codigoVotoFuncionario" value="<?php echo htmlspecialchars($funcionario->getCodigoVotoFuncionario()); ?>" maxlength="7">
                <small><em>Código único para votação. Máximo 7 caracteres.</em></small>

                <label for="emailFuncionario">Email:</label>
                <input type="email" id="emailFuncionario" name="emailFuncionario" value="<?php echo htmlspecialchars($funcionario->getEmailFuncionario()); ?>" required>

                <label for="senhaFuncionario">Senha:</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" id="senhaFuncionario" name="senhaFuncionario" value="•••••••••" readonly style="background-color: #f5f5f5; cursor: not-allowed; font-family: monospace;">
                    <button type="button" onclick="resetarSenha()" id="btnResetarSenha" style="padding: 6px 12px; background: #4b5c49; color: white; border: none; border-radius: 3px; cursor: pointer; font-weight: bold; font-size: 14px;">
                        Resetar Senha
                    </button>
                </div>
                <small><em>A senha não pode ser editada diretamente. Use "Resetar Senha" para definir como data de nascimento (DDMMAAAA)</em></small>

                <fieldset>
                    <legend>Configurações</legend>
                    <label for="ativoFuncionario" style="margin-top: 0;">
                        <input type="checkbox" id="ativoFuncionario" name="ativoFuncionario" value="1" <?php echo ($funcionario->getAtivoFuncionario() == 1) ? 'checked' : ''; ?>>
                        Funcionário Ativo
                    </label>

                    <label for="admFuncionario" style="margin-top: 10px;">
                        <input type="checkbox" id="admFuncionario" name="admFuncionario" value="1" <?php echo ($funcionario->getAdmFuncionario() == 1) ? 'checked' : ''; ?>>
                        Administrador
                    </label>
                </fieldset>

                <button type="submit">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script>
        function resetarSenha() {
            const dataNascimento = document.getElementById('dataNascimentoFuncionario').value;
            const senhaField = document.getElementById('senhaFuncionario');
            const btnResetar = document.getElementById('btnResetarSenha');
            
            if (!dataNascimento) {
                alert('Para resetar a senha, primeiro preencha a data de nascimento.');
                return;
            }
            
            // Converte YYYY-MM-DD para DDMMAAAA
            const partes = dataNascimento.split('-');
            const ano = partes[0];
            const mes = partes[1];
            const dia = partes[2];
            const novaSenha = dia + mes + ano;
            
            senhaField.value = novaSenha;
            senhaField.style.fontFamily = 'monospace';
            
            // Feedback visual
            btnResetar.style.background = '#3a4538';
            btnResetar.innerHTML = 'Senha Resetada!';
            
            setTimeout(() => {
                btnResetar.style.background = '#4b5c49';
                btnResetar.innerHTML = 'Resetar Senha';
            }, 2000);
        }

        // Inicializar campo como readonly
        document.addEventListener('DOMContentLoaded', function() {
            const senhaField = document.getElementById('senhaFuncionario');
            senhaField.setAttribute('readonly', true);
            senhaField.style.backgroundColor = '#f5f5f5';
            senhaField.style.cursor = 'not-allowed';
            senhaField.style.fontFamily = 'monospace';
        });
    </script>

</body>
</html>