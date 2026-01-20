<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Cronograma Anual - Sistema CIPA</title>
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        input[type="date"], select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="date"]:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }
        .btn-primary {
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #1976d2;
        }
        .info-box ul {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-icon">📅</div>
        <div class="header-title">
            <h1>Gerar Cronograma Anual da CIPA</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/ata/listar">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>

        <div class="info-box">
            <h3>📋 Informações do Cronograma</h3>
            <p>O cronograma anual será gerado conforme as exigências da <strong>NR-05</strong> e conterá:</p>
            <ul>
                <li>Data de posse da CIPA</li>
                <li>Treinamento inicial (20 horas)</li>
                <li>Reuniões ordinárias mensais</li>
                <li>Inspeções de segurança mensais</li>
                <li>SIPAT (Semana Interna de Prevenção de Acidentes)</li>
                <li>Processo eleitoral da próxima gestão</li>
            </ul>
            <p><strong>Mandato:</strong> 1 ano de gestão</p>
        </div>

        <form method="post" action="/code/cipa_t1/cronograma/gerar">
            <div class="form-group">
                <label for="id_eleicao">📊 Eleição de Referência:</label>
                <select id="id_eleicao" name="id_eleicao" required>
                    <option value="">Selecione uma eleição finalizada...</option>
                    <?php if (!empty($_SESSION['eleicoes_finalizadas'])): ?>
                        <?php foreach ($_SESSION['eleicoes_finalizadas'] as $eleicao): ?>
                            <option value="<?php echo $eleicao['id_eleicao']; ?>">
                                <?php echo htmlspecialchars($eleicao['titulo_documento']); ?> 
                                (<?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="data_final">📅 Data Final da Eleição:</label>
                <input type="date" id="data_final" name="data_final" required>
                <small style="color: #666; font-size: 14px;">
                    Data em que a eleição foi finalizada. O cronograma será calculado a partir desta data.
                </small>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn-primary">
                    📅 Gerar Cronograma Anual
                </button>
            </div>
        </form>

        <div class="info-box" style="margin-top: 30px;">
            <h3>📝 Observações sobre os Cálculos</h3>
            <ul>
                <li><strong>Posse:</strong> Primeiro dia útil após a data final da eleição</li>
                <li><strong>Treinamento:</strong> Até 30 dias após a posse</li>
                <li><strong>Reuniões:</strong> Dia 10 de cada mês (ajustado para dia útil)</li>
                <li><strong>Inspeções:</strong> Dia 25 de cada mês (ajustado para dia útil)</li>
                <li><strong>SIPAT:</strong> Meados de julho</li>
                <li><strong>Próximo Processo Eleitoral:</strong> Inicia 60 dias antes do término do mandato</li>
            </ul>
        </div>
    </div>

</body>
</html>
