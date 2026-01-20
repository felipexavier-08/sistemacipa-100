<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <title>ATA de Resultados - Eleição</title>
    <style>
        @media print {
            body { 
                background-color: white !important; 
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print { 
                display: none !important; 
            }
            .header {
                display: none !important;
            }
            .ata-container {
                background-color: white !important;
                padding: 40px !important;
                max-width: 800px !important;
                margin: 0 auto !important;
                box-shadow: none !important;
                border: none !important;
            }
            @page {
                margin: 20px;
                size: A4;
            }
            /* Ocultar qualquer URL ou elemento desnecessário */
            a[href]:after,
            a[href]:before {
                display: none !important;
            }
            /* Remover qualquer conteúdo de URL */
            body::before,
            body::after {
                display: none !important;
                content: none !important;
            }
            /* Ocultar elementos que possam conter URL */
            [class*="url"],
            [class*="link"],
            [class*="footer"],
            [class*="header"]:not(.ata-header),
            [class*="navigation"],
            [class*="menu"],
            [class*="breadcrumb"],
            [class*="path"],
            [class*="address"],
            [class*="location"] {
                display: none !important;
            }
            /* Ocultar qualquer elemento no topo */
            body > :first-child:not(.ata-container),
            body > div:first-child:not(.ata-container) {
                display: none !important;
            }
            /* Garantir que apenas o conteúdo da ATA seja exibido */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            /* Forçar ocultar qualquer texto de URL */
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        .ata-container {
            background-color: white;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .ata-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f1c21a;
            padding-bottom: 20px;
        }
        .ata-content {
            line-height: 1.8;
        }
        .resultados-table {
            width: 100%;
            margin: 20px 0;
        }
        .resultados-table th {
            background-color: #f1c21a;
            color: white;
            padding: 12px;
        }
        .resultados-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-icon">📊</div>
        <div class="header-title">
            <h1>ATA de Resultados</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <button onclick="window.print()" class="btn-link" style="border: none; cursor: pointer; background-color: #f1c21a; color: #4b5c49; margin-top: 0px;">Imprimir ATA</button>
            <a href="/code/cipa_t1/ata/listar">Voltar</a>
        </div>
    </div>

    <?php include __DIR__ . '/../../components/alerts.php'; ?>

    <div class="ata-container">
        <div class="ata-header">
            <h1>ATA DE APURAÇÃO DE RESULTADOS</h1>
            <h2><?php echo htmlspecialchars($_SESSION['ata_eleicao']['titulo_documento']); ?></h2>
        </div>

        <div class="ata-content">
            <p><strong>Data da Eleição:</strong> <?php echo date('d/m/Y', strtotime($_SESSION['ata_eleicao']['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($_SESSION['ata_eleicao']['data_fim_eleicao'])); ?></p>
            <p><strong>Data de Apuração:</strong> <?php echo date('d/m/Y'); ?></p>
            
            <h3 style="margin-top: 30px;">RESULTADOS DA ELEIÇÃO</h3>
            
            <table class="resultados-table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Nome do Candidato</th>
                        <th>Cargo</th>
                        <th>Total de Votos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['ata_candidatos'] as $candidato): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($candidato['numero_candidato']); ?></strong></td>
                            <td><?php echo htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario']); ?></td>
                            <td><?php echo htmlspecialchars($candidato['cargo_candidato']); ?></td>
                            <td><strong><?php echo htmlspecialchars($candidato['quantidade_voto_candidato']); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3 style="margin-top: 30px;">VOTOS BRANCOS E NULOS</h3>
            <table class="resultados-table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Votos Brancos</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['ata_brancos_nulos']['quantidade_branco']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Votos Nulos</strong></td>
                        <td><?php echo htmlspecialchars($_SESSION['ata_brancos_nulos']['quantidade_nulo']); ?></td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 30px; padding: 20px; background-color: #f9f9f9; border-radius: 5px;">
                <p><strong>Total de Votos Apurados:</strong> <?php echo htmlspecialchars($_SESSION['ata_total_votos']); ?></p>
            </div>

            <div style="margin-top: 50px; text-align: center;">
                <p>_________________________________</p>
                <p>Assinatura do Responsável</p>
            </div>
        </div>
    </div>

</body>
</html>
