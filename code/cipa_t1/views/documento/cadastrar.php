<?php
    
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Documento - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">📄</div>
        <div class="header-title">
            <h1>Cadastrar Documento</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <div class="form-container">
            <h1>Dados do Documento</h1>

            <form method="post" action="/code/cipa_t1/documento/cadastrar" enctype="multipart/form-data">
                <input type="hidden" name="idDocumento" value="">

                <label for="tituloDocumento">Título do Documento:</label>
                <input type="text" id="tituloDocumento" name="tituloDocumento" required placeholder="Ex: ATA de Reunião - Janeiro 2024">

                <label for="tipoDocumento">Tipo de Documento:</label>
                <select id="tipoDocumento" name="tipoDocumento" required>
                    <option value="">Selecione o tipo...</option>
                    <option value="Ata">ATA</option>
                    <option value="Edital">Edital</option>                
                </select>

                <label for="dataInicioDocumento">Data de Início:</label>
                <input type="date" id="dataInicioDocumento" name="dataInicioDocumento" required>

                <label for="dataFimDocumento">Data de Término:</label>
                <input type="date" id="dataFimDocumento" name="dataFimDocumento" required>
                
                <label for="pdfDocumento">Documento PDF:</label>
                <input type="file" id="pdfDocumento" name="pdfDocumento" accept=".pdf" required>
                <small>Apenas arquivos PDF são aceitos</small>

                <button type="submit">Salvar Documento</button>
            </form>
        </div>
    </div>

</body>
</html>