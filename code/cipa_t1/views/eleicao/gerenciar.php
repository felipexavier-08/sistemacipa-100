<?php
    $eleicaoAtiva = $_SESSION["eleicao_ativa"] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eleição Ativa - Sistema CIPA</title>
</head>
<body>
    <div class="header">
        <div class="header-icon">🗳️</div>
        <div class="header-title">
            <h1>Gerenciar Eleição Ativa</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions">
            <a href="/code/cipa_t1/home" class="btn-link">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>

        <?php if ($eleicaoAtiva && is_array($eleicaoAtiva)): ?>
            <div class="form-container">
                <h2>📊 Dados da Eleição Ativa</h2>
                
                <div class="info-box">
                    <h3>Informações da Eleição</h3>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($eleicaoAtiva['id_eleicao'] ?? 'N/A'); ?></p>
                    <p><strong>Data de Início:</strong> <?php echo ($eleicaoAtiva['data_inicio_eleicao'] ?? null) ? date('d/m/Y', strtotime($eleicaoAtiva['data_inicio_eleicao'])) : 'N/A'; ?></p>
                    <p><strong>Data de Término:</strong> <?php echo ($eleicaoAtiva['data_fim_eleicao'] ?? null) ? date('d/m/Y', strtotime($eleicaoAtiva['data_fim_eleicao'])) : 'N/A'; ?></p>
                    <p><strong>Status:</strong> <span class="status-ativo"><?php echo htmlspecialchars($eleicaoAtiva['status_eleicao'] ?? 'N/A'); ?></span></p>
                    <p><strong>Total de Candidatos:</strong> <?php echo htmlspecialchars($eleicaoAtiva['total_candidatos'] ?? '0'); ?></p>
                    <p><strong>Status da Votação:</strong> 
                        <?php if (($eleicaoAtiva['votacao_autorizada'] ?? 0) == 1): ?>
                            <span class="status-ativo" style="background: #28a745;">✅ AUTORIZADA</span>
                        <?php else: ?>
                            <span class="status-inativo" style="background: #ffc107; color: #856404;">⏳ NÃO AUTORIZADA</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="action-buttons" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: center; margin-top: 20px;flex-direction:column;">
                    <!-- Formulário Estender Período -->
                    <div style="display:flex;flex-direction:column; justify-content: center;align-items: center;flex : 1; min-width: 300px;">
                        <form method="POST" action="/code/cipa_t1/eleicao/estender">
                            <label for="novaDataFim" style="display: block; margin-bottom: 5px; font-weight: bold;">Nova Data de Término:</label>
                            <div style="display: flex;flex-direction:column; gap: 10px; align-items: center; justify-content: space-between;">
                                <input type="date" id="novaDataFim" name="novaDataFim" 
                                       min="<?php echo date('Y-m-d'); ?>" 
                                       value="<?php echo ($eleicaoAtiva['data_fim_eleicao'] ?? null) ? date('Y-m-d', strtotime($eleicaoAtiva['data_fim_eleicao'])) : date('Y-m-d'); ?>" 
                                       required
                                       style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                <button type="submit" class="btn-link" style="background-color: #17a2b8; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; white-space: nowrap;">
                                    📅 Estender
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Botões de Ação -->
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                        <?php if (($eleicaoAtiva['votacao_autorizada'] ?? 0) == 0): ?>
                            <form method="POST" action="/code/cipa_t1/eleicao/autorizar-votacao">
                                <button type="submit" class="btn-link" style="background-color: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; white-space: nowrap;"
                                        onclick="return confirm('Tem certeza que deseja autorizar a votação? Isso encerrará o período de candidaturas e liberará a votação para todos os funcionários!')">
                                    🗳️ Autorizar Votação
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="/code/cipa_t1/eleicao/bloquear-votacao">
                                <button type="submit" class="btn-link" style="background-color: #ffc107; color: #856404; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; white-space: nowrap;"
                                        onclick="return confirm('Tem certeza que deseja bloquear a votação? Isso reabrirá o período de candidaturas e bloqueará a votação!')">
                                    🚫 Bloquear Votação
                                </button>
                            </form>
                        <?php endif; ?>

                        <form method="POST" action="/code/cipa_t1/eleicao/finalizar">
                            <button type="submit" class="btn-link" style="background-color: #dc3545; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; white-space: nowrap;" 
                                    onclick="return confirm('Tem certeza que deseja finalizar esta eleição? A data de término será atualizada para hoje e você será redirecionado para gerar a ata!')">
                                🔒 Finalizar e Gerar Ata
                            </button>
                        </form>
                    </div>
                </div>

                <div class="info-box" style="margin-top: 20px;">
                    <h3>📝 Controle de Votação</h3>
                    <div style="background: <?php echo (($eleicaoAtiva['votacao_autorizada'] ?? 0) == 1) ? '#d4edda' : '#fff3cd'; ?>; padding: 15px; border-radius: 4px; margin: 10px 0;">
                        <?php if (($eleicaoAtiva['votacao_autorizada'] ?? 0) == 0): ?>
                            <h4 style="color: #856404; margin-top: 0;">⏳ Votação NÃO Autorizada</h4>
                            <ul style="color: #856404;">
                                <li>Funcionários podem se candidatar</li>
                                <li>Funcionários NÃO podem votar</li>
                                <li>Candidatos NÃO são visíveis</li>
                            </ul>
                        <?php else: ?>
                            <h4 style="color: #155724; margin-top: 0;">✅ Votação AUTORIZADA</h4>
                            <ul style="color: #155724;">
                                <li>Funcionários NÃO podem se candidatar</li>
                                <li>Funcionários podem votar</li>
                                <li>Candidatos são visíveis para votação</li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-box" style="margin-top: 20px;">
                    <h3>📝 Ações Disponíveis</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <strong>📅 Estender Período:</strong> Alterar a data de término da eleição para dar mais tempo para votação.
                        </li>
                        <li style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <strong>🗳️ Autorizar Votação:</strong> Iniciar o período de votação e encerrar as candidaturas.
                        </li>
                        <li style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <strong>🚫 Bloquear Votação:</strong> Reabrir período de candidaturas e bloquear votação.
                        </li>
                        <li style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <strong>🔒 Finalizar Eleição e Gerar Ata:</strong> Encerrar a eleição atual, atualizando a data de término para hoje e redirecionando automaticamente para gerar a ata.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-container" style="margin-top: 30px;">
                <h2>👥 Cadastro Rápido de Funcionário</h2>
                <p style="margin-bottom: 15px; color: #666;">
                    Cadastre novos funcionários que poderão participar da eleição atual.
                </p>
                <a href="/code/cipa_t1/funcionario/cadastrar" class="btn-link" style="background-color: #007bff; color: white; font-size: 1.1em; padding: 12px 24px; border: none; border-radius: 4px; text-decoration: none; display: inline-block; font-weight: bold;">
                    ➕ Cadastrar Novo Funcionário
                </a>
            </div>

        <?php else: ?>
            <div class="alert alert-info">
                <strong>Informação:</strong> Não há nenhuma eleição ativa no momento.
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="/code/cipa_t1/eleicao/cadastrar" class="btn-link" style="background-color: #007bff; color: white; font-size: 1.1em; padding: 12px 24px; border: none; border-radius: 4px; text-decoration: none; display: inline-block; font-weight: bold;">
                    ➕ Criar Nova Eleição
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
