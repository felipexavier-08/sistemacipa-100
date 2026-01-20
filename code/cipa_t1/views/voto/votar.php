<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votar - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">🗳️</div>
        <div class="header-title">
            <h1>Votação</h1>
            <p><?php echo htmlspecialchars($_SESSION['eleicao_votacao']['titulo_documento'] ?? 'Eleição'); ?></p>
        </div>
        <div class="header-actions">
            <?php if (!empty($_SESSION['funcionario_logado']['cod_voto_funcionario']) && $_SESSION['funcionario_logado']['adm_funcionario'] != 1): ?>
                <span style="background-color: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; margin-right: 10px;">
                    🗳 Código: <?php echo htmlspecialchars($_SESSION['funcionario_logado']['cod_voto_funcionario']); ?>
                </span>
            <?php endif; ?>
            <a href="/code/cipa_t1/funcionario/home">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <div class="form-container">
            <h1>Selecione seu voto</h1>

            <?php if (isset($_SESSION['erro_voto'])): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_voto']); ?>
                </div>
                <?php unset($_SESSION['erro_voto']); ?>
            <?php endif; ?>

            <form method="post" action="/code/cipa_t1/voto/votar">
                <input type="hidden" name="idEleicao" value="<?php echo $_SESSION['id_eleicao_votacao']; ?>">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="codigoVoto" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e3a5f;">
                        🗳 Digite seu Código de Voto:
                    </label>
                    <input type="text" 
                           id="codigoVoto" 
                           name="codigoVoto" 
                           placeholder="Ex: ABC123" 
                           style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 1em; text-transform: uppercase;"
                           required>
                    <small style="color: #666; font-size: 0.9em;">Digite o código que você recebeu para validar seu voto.(na parte superior da tela)</small>
                </div>

                <h2>Candidatos:</h2>
                
                <?php if (!empty($_SESSION['candidatos_votacao'])): ?>
                    <div style="display: grid; gap: 15px; margin: 20px 0;">
                        <?php foreach ($_SESSION['candidatos_votacao'] as $candidato): ?>
                            <div class="card" style="cursor: pointer; padding: 20px;">
                                <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                                    <input type="radio" 
                                           name="numeroCandidato" 
                                           value="<?php echo htmlspecialchars($candidato['numero_candidato']); ?>" 
                                           onchange="document.querySelectorAll('input[name=tipoVoto]').forEach(r => r.checked = false);"
                                           <?php if (isset($_SESSION['candidato_pre_selecionado']) && $_SESSION['candidato_pre_selecionado']['numero_candidato'] == $candidato['numero_candidato']): ?>
                                               checked
                                           <?php endif; ?>
                                           style="width: 20px; height: 20px; margin-right: 15px;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <?php if (!empty($candidato['foto_candidato'])): ?>
                                                <img src="/code/cipa_t1/<?php echo htmlspecialchars($candidato['foto_candidato']); ?>" 
                                                     alt="Foto" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 2px solid #ddd;">
                                            <?php endif; ?>
                                            <div>
                                                <strong style="font-size: 1.2em; color: #1e3a5f;">Número <?php echo htmlspecialchars($candidato['numero_candidato']); ?></strong><br>
                                                <strong style="font-size: 1.1em;"><?php echo htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario']); ?></strong><br>
                                                <span style="color: #666;"><?php echo htmlspecialchars($candidato['cargo_candidato']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (isset($_SESSION['candidato_pre_selecionado'])): ?>
                        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 20px 0; border-radius: 4px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 20px;">✅</span>
                                <div>
                                    <strong style="color: #1976d2;">Candidato pré-selecionado:</strong><br>
                                    <span style="color: #333;">
                                        Número <?php echo htmlspecialchars($_SESSION['candidato_pre_selecionado']['numero_candidato']); ?> - 
                                        <?php echo htmlspecialchars($_SESSION['candidato_pre_selecionado']['nome_funcionario'] . ' ' . $_SESSION['candidato_pre_selecionado']['sobrenome_funcionario']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        Nenhum candidato cadastrado para esta eleição.
                    </div>
                <?php endif; ?>

                <h3 style="margin-top: 30px;">Ou vote:</h3>
                <div class="card" style="cursor: pointer; padding: 20px; margin: 10px 0;">
                    <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                        <input type="radio" name="tipoVoto" value="BRANCO" onchange="document.querySelectorAll('input[name=numeroCandidato]').forEach(r => r.checked = false);" style="width: 20px; height: 20px; margin-right: 15px;">
                        <span style="font-size: 1.1em; font-weight: 600; color: #1e3a5f;">Voto Branco</span>
                    </label>
                </div>
                <div class="card" style="cursor: pointer; padding: 20px; margin: 10px 0;">
                    <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                        <input type="radio" name="tipoVoto" value="NULO" onchange="document.querySelectorAll('input[name=numeroCandidato]').forEach(r => r.checked = false);" style="width: 20px; height: 20px; margin-right: 15px;">
                        <span style="font-size: 1.1em; font-weight: 600; color: #1e3a5f;">Voto Nulo</span>
                    </label>
                </div>

                <button type="submit" style="width: 100%; margin-top: 30px; font-size: 1.1em; padding: 15px;">Confirmar Voto</button>
            </form>
        </div>
    </div>

</body>
</html>
