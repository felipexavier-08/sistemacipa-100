<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial - Funcionário</title>
    <style>
        .candidatos-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .candidato-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            cursor: pointer;
        }
        
        .candidato-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            border-color: #4b5c49;
        }
        
        .candidato-numero {
            background: #4b5c49;
            color: white;
            font-weight: bold;
            font-size: 1.5em;
            padding: 8px 16px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: inline-block;
        }
        
        .candidato-foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #1e3a5f;
            margin-bottom: 15px;
        }
        
        .candidato-nome {
            font-size: 1.1em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .candidato-sem-foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 3px solid #ddd;
            color: #666;
            font-size: 3em;
        }
        
        .search-container {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .search-input {
            width: 100%;
            max-width: 400px;
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            border-color: #4b5c49;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.1em;
            grid-column: 1 / -1;
        }
        
        .documentos-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .documento-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }
        
        .documento-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .documento-titulo {
            font-size: 1.2em;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .documento-data {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .documento-data::before {
            content: "📅";
        }
        
        .documento-btn {
            display: inline-block;
            background: #4b5c49;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s;
            text-align: center;
        }
        
        .documento-btn:hover {
            background: #4b5c49;
            color: white;
        }
        
        .documento-indisponivel {
            color: #999;
            font-style: italic;
            font-size: 0.9em;
        }
        
        .documento-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2em;
            opacity: 0.3;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-icon">👤</div>
        <div class="header-title">
            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['funcionario_logado']['nome_funcionario']); ?>!</h1>
            <p>Sistema CIPA - Área do Funcionário</p>
        </div>
        <div class="header-actions">
            <?php if (!empty($_SESSION['funcionario_logado']['cod_voto_funcionario']) && $_SESSION['funcionario_logado']['adm_funcionario'] != 1): ?>
                <span style="background-color: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; margin-right: 10px;">
                    🗳 Código: <?php echo htmlspecialchars($_SESSION['funcionario_logado']['cod_voto_funcionario']); ?>
                </span>
            <?php endif; ?>
            <a href="/code/cipa_t1/funcionario/alterar-senha" style="background-color: #4b5c49; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 10px; font-weight: bold; font-size: 0.9em;">
                🔐 Alterar Senha
            </a>
            <a href="/code/cipa_t1/logout">Sair</a>
        </div>
    </div>

    <?php
        // Buscar eleição ativa PRIMEIRO (antes dos alertas)
        require_once __DIR__ . "/../../repositories/EleicaoDAO.php";
        require_once __DIR__ . "/../../repositories/CandidatoDAO.php";
        require_once __DIR__ . "/../../repositories/DocumentoDAO.php";
        require_once __DIR__ . "/../../repositories/VotoDAO.php";
        require_once __DIR__ . "/../../utils/Util.php";

        $eleicaoDAO = new EleicaoDAO();
        $candidatoDAO = new CandidatoDAO();
        $documentoDAO = new DocumentoDAO();
        
        // Buscar eleição ativa (já com status atualizado no DAO)
        $eleicao = $eleicaoDAO->buscarEstatisticasEleicaoAtiva();
        
        // Se há eleição, buscar candidatos e documentos
        $candidatos = [];
        $documentos = [];
        if ($eleicao) {
            $candidatos = $candidatoDAO->buscarPorEleicao($eleicao['id_eleicao']);
            
            // Buscar apenas documentos da eleição aberta
            $documentosData = $documentoDAO->buscarPorEleicaoAberta();
            if (!empty($documentosData)) {
                $documentos = Util::converterArrayDoc($documentosData);
            }
        }
    ?>

    <!-- Alertas Fixos de Comprovante -->
    <div style="background: #f8f9fa; padding: 0; border-bottom: 1px solid #dee2e6;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 15px 20px;">
            <?php 
            // Verificar se o funcionário já votou na eleição ATUAL
            $funcionarioLogado = $_SESSION['funcionario_logado'];
            $idFuncionarioLogado = $funcionarioLogado['id_funcionario'];
            $jaVotou = false;
            
            // Só verificar se votou se houver eleição ATIVA e ABERTA
            if ($eleicao && $eleicao['status_eleicao'] === 'ABERTA') {
                $votoDAO = new VotoDAO();
                $jaVotou = $votoDAO->funcionarioJaVotou($idFuncionarioLogado, $eleicao['id_eleicao']);
            }
            ?>

            <!-- Botão Imprimir Comprovante (Voto Recente) -->
            <?php if (isset($_SESSION['comprovante_voto']) && $eleicao && $eleicao['status_eleicao'] === 'ABERTA'): ?>
                <div class="alert alert-success" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724; padding: 15px 20px; margin: 0;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px;">
                            <h4 style="margin: 0 0 8px 0; color: #155724; font-size: 16px;">🗳️ Voto Registrado com Sucesso!</h4>
                            <p style="margin: 0; color: #155724; font-size: 14px;">Seu voto foi registrado. Imprima seu comprovante quando desejar.</p>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <a href="/code/cipa_t1/voto/sucesso" class="btn-link" style="background-color: #28a745; color: white; padding: 10px 18px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; height: fit-content; margin-top: 5px; font-size: 14px;">
                                🖨️ Imprimir Comprovante
                            </a>
                            <form method="POST" action="/code/cipa_t1/limpar-comprovante" style="display: inline;">
                                <button type="submit" class="btn-link" style="background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; display: inline-block; font-weight: bold; font-size: 0.85em; height: fit-content; margin-top: 5px;" 
                                        title="Limpar comprovante da sessão">
                                    🗑️ Limpar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Botão Permanente de Imprimir Comprovante (Já Votou) -->
            <?php if ($jaVotou && !isset($_SESSION['comprovante_voto']) && $eleicao && $eleicao['status_eleicao'] === 'ABERTA'): ?>
                <div class="alert alert-info" style="background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; padding: 15px 20px; margin: 0; margin-top: 10px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px;">
                            <h4 style="margin: 0 0 8px 0; color: #0c5460; font-size: 16px;">🗳️ Você já votou nesta eleição!</h4>
                            <p style="margin: 0; color: #0c5460; font-size: 14px;">Clique abaixo para imprimir seu comprovante de voto.</p>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <a href="/code/cipa_t1/voto/reimprimir-comprovante" class="btn-link" style="background-color: #17a2b8; color: white; padding: 10px 18px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; height: fit-content; margin-top: 5px; font-size: 14px;">
                                🖨️ Imprimir Comprovante
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['sucesso_candidatura'])): ?>
            <div class="alert alert-success">
                <strong>Sucesso:</strong> <?php echo htmlspecialchars($_SESSION['sucesso_candidatura']); ?>
            </div>
            <?php unset($_SESSION['sucesso_candidatura']); ?>
        <?php endif; ?>

        <?php if ($eleicao): ?>
            <!-- Botão Gerenciar fora do card -->
            <?php if ($_SESSION['funcionario_logado']['adm_funcionario'] == 1): ?>
                <!-- Card clicável para admins -->
                <a href="/code/cipa_t1/eleicao/gerenciar" class="info-box-link" style="text-decoration: none; display: block; margin-bottom: 20px;">
                    <div class="info-box" style="position: relative; cursor: pointer; transition: all 0.3s ease;">
                        <div style="position: absolute; top: 10px; right: 10px; background-color: #007bff; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8em; font-weight: bold;">
                            ⚙️ Gerenciar
                        </div>
                        <h3>Eleição Ativa</h3>
                        <p><strong><?php echo htmlspecialchars($eleicao['titulo_documento']); ?></strong></p>
                        <p>Período: <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?></p>
                        <p>Status: <strong><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></strong></p>
                        
                        <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                            <form method="post" action="/code/cipa_t1/eleicao/fechar" style="display: inline;" onclick="event.stopPropagation();">
                                <input type="hidden" name="idEleicao" value="<?php echo $eleicao['id_eleicao']; ?>">
                                <button type="submit" class="btn-link" style="background-color: #dc3545; color: white; padding:10px 20px; border: none; border-radius:4px; cursor: pointer; display: inline-block; font-weight: bold;" 
                                        onclick="return confirm('Tem certeza que deseja finalizar esta eleição? Esta ação não poderá ser desfeita!')">
                                    🔒 Finalizar Eleição
                                </button>
                            </form>
                        </div>
                    </div>
                </a>
            <?php else: ?>
                <!-- Card normal para funcionários -->
                <div class="info-box">
                    <h3>Eleição Ativa</h3>
                    <p><strong><?php echo htmlspecialchars($eleicao['titulo_documento']); ?></strong></p>
                    <p>Período: <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?></p>
                    <p>Status: <strong><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></strong></p>
                    
                    <?php 
                    // Verificar se o funcionário já é candidato
                    $funcionarioLogado = $_SESSION['funcionario_logado'];
                    $idFuncionarioLogado = $funcionarioLogado['id_funcionario'];
                    $jaCandidato = false;
                    
                    if (!empty($candidatos)) {
                        foreach ($candidatos as $candidato) {
                            if ($candidato['usuario_fk'] == $idFuncionarioLogado) {
                                $jaCandidato = true;
                                break;
                            }
                        }
                    }
                    ?>
                    
                    <?php if (!$jaCandidato): ?>
                    <div style="margin-top: 15px;">
                        <a href="/code/cipa_t1/funcionario/candidatar-se" class="btn-link" style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">
                            🎯 Candidatar-se
                        </a>
                    </div>
                    <?php else: ?>
                    <div style="margin-top: 15px;">
                        <span style="background-color: #17a2b8; color: white; padding: 10px 20px; border-radius: 4px; display: inline-block;">
                            ✅ Você já é candidato nesta eleição
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info" style="background-color: #e3f2fd; border-left-color: #2196f3; padding: 15px 20px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center;">
                    <span style="font-size: 20px; margin-right: 12px;">📅</span>
                    <div>
                        <strong style="color: #1976d2; font-size: 16px;">Nenhuma eleição ativa</strong>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Não há eleições em andamento no momento. Aguarde uma nova eleição ser criada.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <h2>Documentos</h2>
        <?php if (!empty($documentos)): ?>
            <div class="documentos-container">
                <?php foreach ($documentos as $doc): ?>
                    <div class="documento-card">
                        <div class="documento-icon">📄</div>
                        <div class="documento-titulo">
                            <?php echo htmlspecialchars($doc->getTituloDocumento()); ?>
                        </div>
                        <div class="documento-data">
                            <?php echo date('d/m/Y', strtotime($doc->getDataFimDocumento())); ?>
                        </div>
                        <div>
                            <?php if (!empty($doc->getPdfDocumento())): ?>
                                <a href="/code/cipa_t1/<?php echo htmlspecialchars($doc->getPdfDocumento()); ?>" 
                                   target="_blank" 
                                   class="documento-btn">
                                    📄 Ver PDF
                                </a>
                            <?php else: ?>
                                <div class="documento-indisponivel">
                                    📄 Não disponível
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Nenhum documento disponível.
            </div>
        <?php endif; ?>

        <?php if ($eleicao && $eleicao['status_eleicao'] === 'ABERTA'): ?>
            <h2 style="margin-top: 40px;">Candidatos</h2>
            
            <!-- Campo de Busca -->
            <div class="search-container">
                <input type="text" 
                       id="searchCandidatos" 
                       class="search-input" 
                       placeholder="🔍 Pesquisar candidato pelo nome..."
                       autocomplete="off">
            </div>
            
            <!-- Container de Cards -->
            <div id="candidatosContainer" class="candidatos-container">
                <?php if (!empty($candidatos)): ?>
                    <?php foreach ($candidatos as $candidato): ?>
                        <div class="candidato-card" 
                             data-nome="<?php echo strtolower(htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario'])); ?>"
                             onclick="window.location.href='/code/cipa_t1/voto/votar?candidato=<?php echo urlencode($candidato['numero_candidato']); ?>'">
                            <div class="candidato-numero">
                                <?php echo htmlspecialchars($candidato['numero_candidato']); ?>
                            </div>
                            
                            <?php if (!empty($candidato['foto_candidato'])): ?>
                                <img src="/code/cipa_t1/<?php echo htmlspecialchars($candidato['foto_candidato']); ?>" 
                                     alt="Foto de <?php echo htmlspecialchars($candidato['nome_funcionario']); ?>" 
                                     class="candidato-foto">
                            <?php else: ?>
                                <div class="candidato-sem-foto">👤</div>
                            <?php endif; ?>
                            
                            <div class="candidato-nome">
                                <?php echo htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario']); ?>
                            </div>
                            
                            <div style="margin-top: 10px; font-size: 0.9em; color: #666;">
                                💭 Clique para votar neste candidato
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        Nenhum candidato cadastrado para esta eleição.
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="/code/cipa_t1/voto/votar" class="btn-link" style="font-size: 1.2em; padding: 15px 40px;">Votar</a>
            </div>
            
            <!-- Script de Busca -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchCandidatos');
                    const container = document.getElementById('candidatosContainer');
                    const cards = container.querySelectorAll('.candidato-card');
                    
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();
                        let hasResults = false;
                        
                        cards.forEach(card => {
                            const nome = card.getAttribute('data-nome');
                            if (nome.includes(searchTerm)) {
                                card.style.display = 'block';
                                hasResults = true;
                            } else {
                                card.style.display = 'none';
                            }
                        });
                        
                        // Mostrar mensagem se não houver resultados
                        let noResultsMsg = container.querySelector('.no-results');
                        if (!hasResults && searchTerm !== '') {
                            if (!noResultsMsg) {
                                noResultsMsg = document.createElement('div');
                                noResultsMsg.className = 'no-results';
                                noResultsMsg.textContent = 'Nenhum candidato encontrado para "' + searchTerm + '"';
                                container.appendChild(noResultsMsg);
                            }
                        } else if (noResultsMsg) {
                            noResultsMsg.remove();
                        }
                    });
                });
            </script>
        <?php elseif ($eleicao && $eleicao['status_eleicao'] !== 'ABERTA'): ?>
            <h2 style="margin-top: 40px;">Candidatos</h2>
            <div class="alert alert-info" style="background-color: #fff3cd; border-left-color: #ffc107; padding: 20px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center;">
                    <span style="font-size: 24px; margin-right: 15px;">🔒</span>
                    <div>
                        <strong style="color: #856404; font-size: 18px;">
                            <?php 
                            if ($eleicao['status_eleicao'] === 'VOTAÇÃO NÃO AUTORIZADA') {
                                echo 'A votação não foi autorizada';
                            } else {
                                echo 'A eleição ainda não foi iniciada';
                            }
                            ?>
                        </strong>
                        <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">
                            Período de votação: <?php echo date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])); ?> a <?php echo date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])); ?>
                        </p>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                            Status atual: <strong><?php echo htmlspecialchars($eleicao['status_eleicao']); ?></strong>
                        </p>
                        <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">
                            <?php 
                            if ($eleicao['status_eleicao'] === 'VOTAÇÃO NÃO AUTORIZADA') {
                                echo 'Aguarde a autorização da administração para iniciar a votação.';
                            } else {
                                echo 'Aguarde o período de votação começar para visualizar os candidatos e votar.';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php elseif ($eleicao): ?>
            <h2 style="margin-top: 40px;">Candidatos</h2>
            <div class="alert alert-info">
                Nenhum candidato cadastrado para esta eleição.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
