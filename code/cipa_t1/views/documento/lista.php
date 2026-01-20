<?php
    $documentos = $_SESSION["documentos"] ?? [];
    
   /* // Função para verificar se o documento pode ser deletado
    function podeDeletar($statusEleicaoVinculada) {
        return $statusEleicaoVinculada === 'SEM_VINCULO' || $statusEleicaoVinculada === 'FINALIZADA';
    }
    
    // Função para obter o texto de status
    function getStatusTexto($status) {
        switch($status) {
            case 'SEM_VINCULO': return 'Sem vínculo';
            case 'ABERTA': return 'Eleição ABERTA';
            case 'FINALIZADA': return 'Eleição FINALIZADA';
            default: return $status;
        }
    }
    
    // Função para obter a cor do status
    function getStatusCor($status) {
        switch($status) {
            case 'SEM_VINCULO': return '#6c757d';
            case 'ABERTA': return '#28a745';
            case 'FINALIZADA': return '#dc3545';
            default: return '#6c757d';
        }
    } */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Documentos - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">📚</div>
        <div class="header-title">
            <h1>Lista de Documentos</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/documento/cadastrar" class="btn-link">Novo Documento</a>
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <div class="info-box" style="margin-bottom: 20px;">
            <h3>📋 Regras de Exclusão de Documentos</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="margin: 8px 0; padding: 8px; background: #d4edda; border-radius: 4px;">
                    <strong>✅ Podem ser excluídos:</strong> Documentos sem vínculo com eleições ou vinculados apenas a eleições FINALIZADAS
                </li>
                <li style="margin: 8px 0; padding: 8px; background: #f8d7da; border-radius: 4px;">
                    <strong>🚫 Não podem ser excluídos:</strong> Documentos vinculados a eleições ABERTAS (em andamento)
                </li>
            </ul>
        </div>
        
        <div class="table-container">
            <?php if (!empty($documentos) && is_array($documentos)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Tipo</th>
                            <!--<th>Status</th>-->
                            <th>Documento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $doc): ?>
                            <?php
                                if (is_object($doc)) {
                                    $id = $doc->getIdDocumento();
                                    $titulo = $doc->getTituloDocumento();
                                    $dataInicio = $doc->getDataInicioDocumento();
                                    $dataFim = $doc->getDataFimDocumento();
                                    $tipo = $doc->getTipoDocumento();
                                    $pdf = $doc->getPdfDocumento();
                                    /*$statusEleicao = 'SEM_VINCULO'; */
                                } else {
                                    $id = $doc['id_documento'] ?? $doc['IdDocumento'] ?? '';
                                    $titulo = $doc['titulo_documento'] ?? $doc['TituloDocumento'] ?? '';
                                    $dataInicio = $doc['data_inicio_documento'] ?? $doc['DataInicioDocumento'] ?? '';
                                    $dataFim = $doc['data_fim_documento'] ?? $doc['DataFimDocumento'] ?? '';
                                    $tipo = $doc['tipo_documento'] ?? $doc['TipoDocumento'] ?? '';
                                    $pdf = $doc['pdf_documento'] ?? $doc['PdfDocumento'] ?? '';
                                    /*$statusEleicao = $doc['status_eleicao_vinculada'] ?? 'SEM_VINCULO';*/
                                }
                                
                                $dataInicioFormatada = $dataInicio ? date('d/m/Y', strtotime($dataInicio)) : '-';
                                $dataFimFormatada = $dataFim ? date('d/m/Y', strtotime($dataFim)) : '-';
                                /*$statusTexto = getStatusTexto($statusEleicao);
                                $statusCor = getStatusCor($statusEleicao);
                                $podeDeletar = podeDeletar($statusEleicao);*/
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($titulo); ?></strong></td>
                                <td><?php echo $dataInicioFormatada; ?></td>
                                <td><?php echo $dataFimFormatada; ?></td>
                                <td><?php echo htmlspecialchars($tipo); ?></td>
                                <td>
                                    <?php if (!empty($pdf)): ?>
                                        <a href="/code/cipa_t1/<?php echo htmlspecialchars($pdf); ?>" target="_blank" class="btn-link" style="padding: 5px 10px; font-size: 0.9em;">Visualizar PDF</a>
                                    <?php else: ?>
                                        <em>Não disponível</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    
                                    <a href="/code/cipa_t1/documento/deletar?id=<?php echo $id; ?>" class="btn-link" style="padding: 5px 10px; font-size: 0.9em; background-color: #dc3545;" onclick="return confirm('Tem certeza que deseja excluir este documento? A ata da eleição em que esse documento está anexado será excluída também.');">Excluir</a>
                                    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    Nenhum documento cadastrado.
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>