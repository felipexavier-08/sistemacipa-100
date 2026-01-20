<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/code/cipa_t1/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cronograma Anual - Sistema CIPA</title>
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
        input[type="text"], input[type="date"], select, textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="date"]:focus, select:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        textarea {
            min-height: 60px;
            resize: vertical;
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
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
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
        .evento-form {
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .evento-form h3 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        .form-row .form-group:last-child {
            flex: 0.5;
        }
        .btn-group {
            text-align: center;
            margin: 30px 0;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .btn-group {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-icon">✏️</div>
        <div class="header-title">
            <h1>Editar Cronograma Anual da CIPA</h1>
            <p>Sistema CIPA</p>
        </div>
        <div class="header-actions no-print">
            <a href="/code/cipa_t1/cronograma/visualizar">Voltar</a>
        </div>
    </div>

    <div class="container">
        <?php include __DIR__ . '/../../components/alerts.php'; ?>

        <?php if (isset($_SESSION['cronograma_gerado']) && isset($_SESSION['dados_eleicao_cronograma'])): ?>
            
            <div class="cronograma-header">
                <h1>EDITAR CRONOGRAMA ANUAL DA CIPA</h1>
                <div class="info">
                    <strong>Eleição de Referência:</strong> <?php echo htmlspecialchars($_SESSION['dados_eleicao_cronograma']['titulo_documento']); ?>
                </div>
                <div class="info">
                    <strong>Data Final da Eleição:</strong> <?php echo date('d/m/Y', strtotime($_SESSION['dados_eleicao_cronograma']['data_fim_eleicao'])); ?>
                </div>
                <div class="info">
                    <strong>Data de Geração:</strong> <?php echo date('d/m/Y H:i:s'); ?>
                </div>
            </div>

            <div class="alert-info">
                <strong>📝 Instruções:</strong> Você pode editar datas, descrições e responsáveis de cada evento do cronograma. 
                Clique em "Salvar Alterações" quando terminar.
            </div>

            <form method="post" action="/code/cipa_t1/cronograma/atualizar">
                <?php foreach ($_SESSION['cronograma_gerado'] as $index => $item): ?>
                    <div class="evento-form">
                        <h3><?php echo htmlspecialchars($item['evento']); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="data_<?php echo $index; ?>">📅 Data:</label>
                                <input type="text" id="data_<?php echo $index; ?>" name="data_<?php echo $index; ?>" 
                                       value="<?php echo htmlspecialchars($item['data']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="responsavel_<?php echo $index; ?>">👤 Responsável:</label>
                                <input type="text" id="responsavel_<?php echo $index; ?>" name="responsavel_<?php echo $index; ?>" 
                                       value="<?php echo htmlspecialchars($item['responsavel']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="obrigatorio_<?php echo $index; ?>">✅ Obrigatório:</label>
                                <select id="obrigatorio_<?php echo $index; ?>" name="obrigatorio_<?php echo $index; ?>" required>
                                    <option value="Sim" <?php echo ($item['obrigatorio'] === 'Sim') ? 'selected' : ''; ?>>Sim</option>
                                    <option value="Não" <?php echo ($item['obrigatorio'] === 'Não') ? 'selected' : ''; ?>>Não</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descricao_<?php echo $index; ?>">📝 Descrição:</label>
                            <textarea id="descricao_<?php echo $index; ?>" name="descricao_<?php echo $index; ?>" required><?php echo htmlspecialchars($item['descricao']); ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="btn-group no-print">
                    <button type="submit" class="btn-primary">
                        💾 Salvar Alterações
                    </button>
                    <a href="/code/cipa_t1/cronograma/visualizar" class="btn-secondary">
                        ❌ Cancelar
                    </a>
                </div>
            </form>

        <?php else: ?>
            <div class="alert alert-info">
                Nenhum cronograma para editar. <a href="/code/cipa_t1/cronograma/gerar">Clique aqui para gerar um novo cronograma.</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const camposData = document.querySelectorAll('[name^="data_"]');
            const camposDescricao = document.querySelectorAll('[name^="descricao_"]');
            const camposResponsavel = document.querySelectorAll('[name^="responsavel_"]');
            
            let valido = true;
            
            // Validar campos obrigatórios
            camposData.forEach(campo => {
                if (!campo.value.trim()) {
                    valido = false;
                    campo.style.borderColor = '#dc3545';
                } else {
                    campo.style.borderColor = '#ddd';
                }
            });
            
            camposDescricao.forEach(campo => {
                if (!campo.value.trim()) {
                    valido = false;
                    campo.style.borderColor = '#dc3545';
                } else {
                    campo.style.borderColor = '#ddd';
                }
            });
            
            camposResponsavel.forEach(campo => {
                if (!campo.value.trim()) {
                    valido = false;
                    campo.style.borderColor = '#dc3545';
                } else {
                    campo.style.borderColor = '#ddd';
                }
            });
            
            if (!valido) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
                return false;
            }
        });

        // Limpar validação ao digitar
        document.querySelectorAll('input, textarea, select').forEach(campo => {
            campo.addEventListener('input', function() {
                this.style.borderColor = '#ddd';
            });
        });
    </script>

</body>
</html>
