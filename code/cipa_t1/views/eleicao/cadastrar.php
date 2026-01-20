<?php
    $documentos = $_SESSION["documentos"] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Eleição - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">✓</div>
        <div class="header-title">
            <h1>Cadastrar Eleição</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['sucesso_eleicao'])): ?>
            <div class="alert alert-success">
                <strong>Sucesso:</strong> <?php echo htmlspecialchars($_SESSION['sucesso_eleicao']); ?>
            </div>
            <?php unset($_SESSION['sucesso_eleicao']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['eleicao_anterior'])): ?>
            <?php 
            $eleicao = $_SESSION['eleicao_anterior'];
            $statusReal = $eleicao['status_real'] ?? $eleicao['status_eleicao'];
            $titulo = ($statusReal === 'FINALIZADA') ? 'Eleição Finalizada Encontrada' : 'Eleição Aberta Encontrada';
            $icone = ($statusReal === 'FINALIZADA') ? '🔒' : '⚠️';
            $mensagem = ($statusReal === 'FINALIZADA') ? 
                'Já existe uma eleição finalizada recentemente:' : 
                'Já existe uma eleição aberta:';
            $pergunta = ($statusReal === 'FINALIZADA') ? 
                'Deseja criar uma nova eleição? A anterior será mantida para consulta.' :
                'Deseja mesmo criar uma nova eleição? A anterior será apagada.';
            ?>
            <div class="alert alert-warning">
                <h3><?php echo $icone; ?> <?php echo htmlspecialchars($titulo); ?></h3>
                <p><?php echo htmlspecialchars($mensagem); ?></p>
                <ul>
                    <li><strong>Título:</strong> <?php echo htmlspecialchars($eleicao['titulo_documento'] ?? ''); ?></li>
                    <li><strong>Início:</strong> <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'] ?? '')); ?></li>
                    <li><strong>Fim:</strong> <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'] ?? '')); ?></li>
                    <li><strong>Status:</strong> <?php echo htmlspecialchars($statusReal); ?></li>
                </ul>
                <p><strong><?php echo htmlspecialchars($pergunta); ?></strong></p>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <h1>Dados da Eleição</h1>

            <?php if (isset($_SESSION['erro_eleicao'])): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_eleicao']); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/code/cipa_t1/eleicao/cadastrar">
                <input type="hidden" name="idEleicao" value="">

                <label for="idDocumento">Edital Relacionado:</label>
                <select id="idDocumento" name="idDocumento" required>
                    <option value="">Selecione um edital...</option>
                    <?php if (!empty($documentos) && is_array($documentos)): ?>
                        <?php foreach ($documentos as $doc): ?>
                            <?php
                                $idDoc = is_object($doc) ? $doc->getIdDocumento() : ($doc['id_documento'] ?? $doc['idDocumento'] ?? '');
                                $tituloDoc = is_object($doc) ? $doc->getTituloDocumento() : ($doc['titulo_documento'] ?? $doc['tituloDocumento'] ?? '');
                            ?>
                            <option value="<?php echo htmlspecialchars($idDoc); ?>">
                                <?php echo htmlspecialchars($tituloDoc); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Nenhum documento encontrado. Cadastre um documento primeiro.</option>
                    <?php endif; ?>
                </select>
                <?php if (empty($documentos)): ?>
                    <small>Nenhum documento disponível. <a href="/code/cipa_t1/documento/cadastrar">Cadastre um documento</a> primeiro.</small>
                <?php endif; ?>

                <label for="dataInicioEleicao">Data de Início:</label>
                <input type="date" id="dataInicioEleicao" name="dataInicioEleicao" required>

                <label for="dataFimEleicao">Data de Término:</label>
                <input type="date" id="dataFimEleicao" name="dataFimEleicao" required>

                <input type="hidden" id="statusEleicao" name="statusEleicao" value="ABERTA">
                <small><strong>Status:</strong> Aberta (automático) - A eleição será finalizada automaticamente na data de término</small>

                <?php if (isset($_SESSION['eleicao_anterior'])): ?>
            <div class="form-actions">
                <button type="submit" name="confirmar_apagar" value="sim" class="btn-primary">
                    <div class="btn-icon">✅</div>
                    <div class="btn-text">Sim, Criar Nova Eleição e Apagar Anterior</div>
                </button>
                <a href="/code/cipa_t1/" class="btn-secondary">
                    <div class="btn-icon">❌</div>
                    <div class="btn-text">Cancelar</div>
                </a>
            </div>
        <?php else: ?>
            <button type="submit">Salvar Eleição</button>
        <?php endif; ?>
            </form>
        </div>
    </div>

</body>
</html>