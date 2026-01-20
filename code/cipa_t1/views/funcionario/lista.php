<?php
    $funcionarios = $_SESSION["funcionarios"] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionários - Sistema CIPA</title>
</head>
<body>

    <div class="header">
        <div class="header-icon">👥</div>
        <div class="header-title">
            <h1>Lista de Funcionários</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/funcionario/cadastrar" class="btn-link">Novo Funcionário</a>
            <a href="/code/cipa_t1/">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>
        
        <div class="info-box" style="margin-bottom: 20px;">
            <h3>🗑️ Regras de Exclusão de Funcionários</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="margin: 8px 0; padding: 8px; background: #d4edda; border-radius: 4px;">
                    <strong>✅ Podem ser excluídos normalmente:</strong> Funcionários que não votaram em eleições ABERTAS.
                </li>
                <li style="margin: 8px 0; padding: 8px; background: #fff3cd; border-radius: 4px;">
                    <strong>⚠️ Não podem ser excluídos:</strong> Funcionários que votaram ou são candidatos em eleições ABERTAS.
                </li>
                <li style="margin: 8px 0; padding: 8px; background: #f8d7da; border-radius: 4px;">
                    <strong>🔓 Eleições finalizadas:</strong> Votos em eleições FINALIZADAS não impedem a exclusão.
                </li>
            </ul>
        </div>
        
        <div class="table-container">
            <?php if (!empty($funcionarios) && is_array($funcionarios)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Sobrenome</th>
                            <th>CPF</th>
                            <th>Data de Nascimento</th>
                            <th>Data de Contratação</th>
                            <th>Status Voto</th>
                            <th>Ativo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $f): ?>
                            <?php
                                $id = $f->getIdFuncionario();
                                $nome = $f->getNomeFuncionario();
                                $sobrenome = $f->getSobrenomeFuncionario();
                                $cpf = $f->getCpfFuncionario();
                                $dataNasc = $f->getDataNascimentoFuncionario();
                                $dataContr = $f->getDataContratacaoFuncionario();
                                $ativo = $f->getAtivoFuncionario();
                                
                                $dataNascFormatada = $dataNasc ? date('d/m/Y', strtotime($dataNasc)) : '-';
                                $dataContrFormatada = $dataContr ? date('d/m/Y', strtotime($dataContr)) : '-';
                                
                                // Formatar CPF para exibição
                                $cpfFormatado = preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $cpf);
                                
                                // Verificar status de voto
                                $statusVoto = $f->getJaVotou();
                                $statusClasse = $statusVoto ? 'voto-realizado' : 'voto-pendente';
                                $statusIcone = $statusVoto ? '✅' : '⏳';
                                $statusTexto = $statusVoto ? 'Já votou' : 'Não votou';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($id); ?></td>
                                <td><?php echo htmlspecialchars($nome); ?></td>
                                <td><?php echo htmlspecialchars($sobrenome); ?></td>
                                <td><?php echo htmlspecialchars($cpfFormatado); ?></td>
                                <td><?php echo $dataNascFormatada; ?></td>
                                <td><?php echo $dataContrFormatada; ?></td>
                                <td class="<?php echo $statusClasse; ?>" style="text-align: center; font-weight: bold;">
                                    <span style="font-size: 1.2em;"><?php echo $statusIcone; ?></span>
                                    <br>
                                    <span style="font-size: 0.8em;"><?php echo $statusTexto; ?></span>
                                </td>
                                <td><?php echo ($ativo == 1) ? 'Sim' : 'Não'; ?></td>
                                <td>
                                    <a href="/code/cipa_t1/funcionario/editar?id=<?php echo $id; ?>" class="btn-link" style="padding: 5px 10px; font-size: 0.9em; margin-right: 5px;">Editar</a>
                                    <a href="/code/cipa_t1/funcionario/deletar?id=<?php echo $id; ?>" class="btn-link btn-secondary" style="padding: 5px 10px; font-size: 0.9em;" onclick="return confirm('Tem certeza que deseja deletar este funcionário?');">Deletar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    Nenhum funcionário encontrado.
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>