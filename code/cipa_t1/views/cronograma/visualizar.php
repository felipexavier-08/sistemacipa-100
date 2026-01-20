<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cronograma Anual da CIPA - Sistema CIPA</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                font-size: 12px;
                line-height: 1.2;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 100%;
                margin: 0;
                padding: 10px;
            }
            .header {
                display: none !important;
            }
            .cronograma-header {
                display: none !important;
            }
            .observacoes-box {
                display: none !important;
            }
            .btn-group {
                display: none !important;
            }
            .alert {
                display: none !important;
            }
            .print-title {
                display: block !important;
            }
            table {
                font-size: 10px;
                width: 100%;
                margin: 0;
            }
            .table-container {
                margin: 0;
                overflow: visible;
            }
            .evento-titulo {
                font-size: 10px;
            }
            .evento-data {
                font-size: 9px;
                padding: 2px 4px;
            }
            .evento-descricao {
                font-size: 9px;
            }
            .evento-responsavel {
                font-size: 9px;
            }
            
            /* Remover cabeçalho e rodapé do navegador */
            @page {
                margin: 0.5in;
                size: auto;
            }
            
            /* Remover título da página e URL */
            @page :header {
                display: none;
            }
            
            @page :footer {
                display: none;
            }
            
            /* Para Chrome/Safari */
            @page {
                margin-top: 0.5in;
                margin-bottom: 0.5in;
            }
            
            h1 {
                page-break-before: auto;
                page-break-after: avoid;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
        
        .cronograma-header {
            background-color: #f8f9fa;
            border: 2px solid #dee2e6;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .cronograma-header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .cronograma-header .info {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }
        
        .cronograma-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .cronograma-table th {
            background-color: #007bff;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }
        
        .cronograma-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: top;
        }
        
        .cronograma-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .cronograma-table tr:hover {
            background-color: #e3f2fd;
        }
        
        .evento-titulo {
            font-weight: bold;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .evento-data {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        .evento-descricao {
            color: #666;
            font-size: 13px;
            margin: 5px 0;
        }
        
        .evento-responsavel {
            color: #007bff;
            font-size: 12px;
            font-weight: bold;
        }
        
        .obrigatorio-sim {
            color: #28a745;
            font-weight: bold;
        }
        
        .obrigatorio-nao {
            color: #6c757d;
        }
        
        .observacoes-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin-top: 20px;
            border-radius: 4px;
        }
        
        .observacoes-box h3 {
            color: #856404;
            margin-top: 0;
        }
        
        .btn-group {
            text-align: center;
            margin: 20px 0;
        }
        
        .btn-print {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
        }
        
        .btn-print:hover {
            background-color: #0056b3;
        }
        
        .btn-success {
            background-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-icon">📅</div>
        <div class="header-title">
            <h1>Cronograma Anual da CIPA</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions no-print">
            <a href="/code/cipa_t1/ata/listar">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>

        <?php if (isset($_SESSION['sucesso_cronograma'])): ?>
            <div class="alert alert-success" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724; padding: 15px 20px; margin-bottom: 20px; border-radius: 4px;">
                <strong>Sucesso:</strong> <?php echo htmlspecialchars($_SESSION['sucesso_cronograma']); ?>
            </div>
            <?php unset($_SESSION['sucesso_cronograma']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro_cronograma'])): ?>
            <div class="alert alert-error" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 15px 20px; margin-bottom: 20px; border-radius: 4px;">
                <strong>Erro:</strong> <?php echo htmlspecialchars($_SESSION['erro_cronograma']); ?>
            </div>
            <?php unset($_SESSION['erro_cronograma']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['cronograma_gerado']) && isset($_SESSION['dados_eleicao_cronograma'])): ?>
            
            <!-- Título apenas para impressão -->
            <div class="print-title" style="display: none; text-align: center; margin-bottom: 20px;">
                <h1 style="font-size: 18px; margin: 0; color: #2c3e50;">CRONOGRAMA ANUAL DA CIPA</h1>
                <p style="font-size: 12px; margin: 5px 0 0 0; color: #666;">
                    <?php echo htmlspecialchars($_SESSION['dados_eleicao_cronograma']['titulo_documento']); ?> - 
                    <?php echo date('d/m/Y', strtotime($_SESSION['dados_eleicao_cronograma']['data_fim_eleicao'])); ?>
                </p>
            </div>
            
            <div class="cronograma-header">
                <h1>CRONOGRAMA ANUAL DA CIPA</h1>
                <div class="info">
                    <strong>Eleição de Referência:</strong> <?php echo htmlspecialchars($_SESSION['dados_eleicao_cronograma']['titulo_documento']); ?>
                </div>
                <div class="info">
                    <strong>Data Final da Eleição:</strong> <?php echo date('d/m/Y', strtotime($_SESSION['dados_eleicao_cronograma']['data_fim_eleicao'])); ?>
                </div>
                <div class="info">
                    <strong>Mandato:</strong> 1 (um) ano
                </div>
                <div class="info">
                    <strong>Base Legal:</strong> NR-05 - Comissão Interna de Prevenção de Acidentes
                </div>
                <div class="info">
                    <strong>Data de Geração:</strong> <?php echo date('d/m/Y H:i:s'); ?>
                </div>
            </div>

            <div class="table-container">
                <table class="cronograma-table">
                    <thead>
                        <tr>
                            <th width="25%">Evento</th>
                            <th width="15%">Data</th>
                            <th width="35%">Descrição</th>
                            <th width="15%">Responsável</th>
                            <th width="10%">Obrigatório</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cronograma_gerado'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="evento-titulo"><?php echo htmlspecialchars($item['evento']); ?></div>
                                </td>
                                <td>
                                    <div class="evento-data"><?php echo htmlspecialchars($item['data']); ?></div>
                                </td>
                                <td>
                                    <div class="evento-descricao"><?php echo htmlspecialchars($item['descricao']); ?></div>
                                </td>
                                <td>
                                    <div class="evento-responsavel"><?php echo htmlspecialchars($item['responsavel']); ?></div>
                                </td>
                                <td>
                                    <?php if ($item['obrigatorio'] === 'Sim'): ?>
                                        <span class="obrigatorio-sim">Sim</span>
                                    <?php else: ?>
                                        <span class="obrigatorio-nao">Não</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="observacoes-box">
                <h3>📝 Observações sobre os Cálculos dos Prazos</h3>
                <p><strong>Base de Cálculo:</strong> A partir da data final da eleição informada, foi calculado um cronograma completo para o mandato de 1 (um) ano, conforme exigências da NR-05.</p>
                
                <p><strong>Critérios Utilizados:</strong></p>
                <ul>
                    <li><strong>Posse:</strong> Primeiro dia útil imediatamente seguinte à data final da eleição, garantindo início regular do mandato.</li>
                    <li><strong>Treinamento Inicial:</strong> Realizado em até 30 (trinta) dias após a posse, com carga horária mínima de 20 (vinte) horas, conforme item 5.32 da NR-05.</li>
                    <li><strong>Reuniões Ordinárias:</strong> Mensais, fixadas no dia 10 de cada mês, ajustadas para primeiro dia útil caso coincida com fim de semana ou feriado.</li>
                    <li><strong>Inspeções de Segurança:</strong> Mensais, programadas para o dia 25 de cada mês, também ajustadas para dias úteis, visando identificação de riscos ambientais.</li>
                    <li><strong>SIPAT:</strong> Semana Interna de Prevenção de Acidentes programada para meados de julho, período tradicional para realização de campanhas de conscientização.</li>
                    <li><strong>Processo Eleitoral Seguinte:</strong> Iniciado com 60 (sessenta) dias de antecedência ao término do mandato, compreendendo publicação de edital (D-60), período de inscrição (D+1 a D+15), votação (D+30) e apuração (D+32).</li>
                </ul>
                
                <p><strong>Ajustes de Dias Úteis:</strong> Todas as datas previstas para cair em sábados, domingos ou feriados foram automaticamente ajustadas para o primeiro dia útil subsequente, garantindo a efetividade das atividades programadas.</p>
                
                <p><strong>Conformidade Legal:</strong> Este cronograma atende integralmente aos requisitos da NR-05, garantindo a regularidade das atividades da CIPA durante todo o mandato.</p>
            </div>

            <div class="btn-group no-print">
                <a href="/code/cipa_t1/cronograma/editar" class="btn-print" style="background-color: #ffc107; color: #212529;">
                    ✏️ Editar Cronograma
                </a>
                <button onclick="window.print()" class="btn-print">
                    🖨️ Imprimir Cronograma
                </button>
                <a href="/code/cipa_t1/cronograma/exportar-excel" class="btn-print btn-success">
                    📊 Exportar para Excel
                </a>
                <a href="/code/cipa_t1/cronograma/gerar" class="btn-print btn-secondary">
                    🔄 Gerar Novo Cronograma
                </a>
            </div>

        <?php else: ?>
            <div class="alert alert-info">
                Nenhum cronograma gerado. <a href="/code/cipa_t1/cronograma/gerar">Clique aqui para gerar um novo cronograma.</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
