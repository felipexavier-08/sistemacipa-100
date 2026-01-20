<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">🔐</div>
        <div class="header-title">
            <h1>Alterar Senha</h1>
            <p>Sistema CIPA - Área do Funcionário</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/funcionario/home">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <?php if (isset($_SESSION['erro_senha'])): ?>
            <div class="alert alert-error">
                <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_senha']); ?>
            </div>
            <?php unset($_SESSION['erro_senha']); ?>
        <?php endif; ?>
        
        <div class="form-container">
            <h1>Alterar Senha de Acesso</h1>
            <p style="margin-bottom: 20px; color: #666;">
                Preencha os campos abaixo para alterar sua senha. Sua senha atual será necessária para confirmar a alteração.
            </p>
            
            <form method="post" action="/code/cipa_t1/funcionario/alterar-senha">
                <label for="senhaAtual">Senha Atual:</label>
                <input type="password" id="senhaAtual" name="senhaAtual" required>
                <small>Digite sua senha atual para confirmar sua identidade</small>

                <label for="novaSenha">Nova Senha:</label>
                <input type="password" id="novaSenha" name="novaSenha" required minlength="8">
                <small>Mínimo 8 caracteres</small>

                <label for="confirmarSenha">Confirmar Nova Senha:</label>
                <input type="password" id="confirmarSenha" name="confirmarSenha" required minlength="8">
                <small>Digite novamente a nova senha para confirmar</small>

                <div style="margin-top: 30px; display: flex; gap: 10px;">
                    <button type="submit" style="background-color: #4b5c49; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 16px;">
                        🔐 Alterar Senha
                    </button>
                    <a href="/code/cipa_t1/funcionario/home" style="background-color: #6c757d; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; font-size: 16px;">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validação no cliente
        document.querySelector('form').addEventListener('submit', function(e) {
            const novaSenha = document.getElementById('novaSenha').value;
            const confirmarSenha = document.getElementById('confirmarSenha').value;
            
            if (novaSenha !== confirmarSenha) {
                e.preventDefault();
                alert('A nova senha e a confirmação não coincidem.');
                return false;
            }
            
            if (novaSenha.length < 8) {
                e.preventDefault();
                alert('A nova senha deve ter pelo menos 8 caracteres.');
                return false;
            }
        });

        // Mostrar/ocultar senha
        function toggleSenha(inputId) {
            const input = document.getElementById(inputId);
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
        }

        // Adicionar botões de mostrar/ocultar senha
        document.addEventListener('DOMContentLoaded', function() {
            const campos = ['senhaAtual', 'novaSenha', 'confirmarSenha'];
            
            campos.forEach(campoId => {
                const input = document.getElementById(campoId);
                const container = input.parentElement;
                
                // Criar container para o input e botão
                const wrapper = document.createElement('div');
                wrapper.style.display = 'flex';
                wrapper.style.gap = '10px';
                wrapper.style.alignItems = 'center';
                
                // Mover input para o wrapper
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);
                
                // Criar botão de mostrar/ocultar
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.innerHTML = '👁️';
                btn.style.padding = '8px';
                btn.style.border = '1px solid #ddd';
                btn.style.borderRadius = '4px';
                btn.style.background = '#f8f9fa';
                btn.style.cursor = 'pointer';
                btn.onclick = () => toggleSenha(campoId);
                
                wrapper.appendChild(btn);
            });
        });
    </script>

</body>
</html>
