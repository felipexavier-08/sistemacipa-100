<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Candidatos - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">📋</div>
        <div class="header-title">
            <h1>Listar Candidatos</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <?php if (isset($_SESSION['candidatos_lista']) && !empty($_SESSION['candidatos_lista'])): ?>
            <h2>Candidatos da Eleição: <?php echo htmlspecialchars($_SESSION['eleicao_lista']['titulo_documento'] ?? ''); ?></h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>Foto</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['candidatos_lista'] as $candidato): ?>
                            <tr>
                                <td><strong style="font-size: 1.2em; color: #1e3a5f;"><?php echo htmlspecialchars($candidato['numero_candidato']); ?></strong></td>
                                <td><?php echo htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario']); ?></td>
                                <td><?php echo htmlspecialchars($candidato['cargo_candidato']); ?></td>
                                <td>
                                    <?php if (!empty($candidato['foto_candidato'])): ?>
                                        <img src="/code/cipa_t1/<?php echo htmlspecialchars($candidato['foto_candidato']); ?>" 
                                             alt="Foto" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 2px solid #ddd;">
                                    <?php else: ?>
                                        <em>Sem foto</em>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($candidato['status_candidato']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Nenhum candidato cadastrado para a eleição ativa.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
