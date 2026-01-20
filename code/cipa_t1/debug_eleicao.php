<?php
// Debug de status da eleição
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug de Status da Eleição</h1>";

require_once __DIR__ . "/repositories/EleicaoDAO.php";
require_once __DIR__ . "/repositories/CandidatoDAO.php";

$eleicaoDAO = new EleicaoDAO();
$candidatoDAO = new CandidatoDAO();

echo "<h2>📊 Todas as Eleições no Banco:</h2>";

// Buscar todas as eleições sem filtro
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cipa_t1', 'root', '');
    $sql = "SELECT e.*, d.titulo_documento 
            FROM eleicao e 
            INNER JOIN documento d ON e.documento_fk = d.id_documento 
            ORDER BY e.id_eleicao DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $todasEleicoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($todasEleicoes)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Título</th><th>Status</th><th>Data Início</th><th>Data Fim</th><th>Ações</th>";
        echo "</tr>";
        
        foreach ($todasEleicoes as $eleicao) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($eleicao['id_eleicao']) . "</td>";
            echo "<td>" . htmlspecialchars($eleicao['titulo_documento']) . "</td>";
            echo "<td><strong style='color: " . ($eleicao['status_eleicao'] === 'ABERTA' ? 'green' : 'red') . ";'>" . htmlspecialchars($eleicao['status_eleicao']) . "</strong></td>";
            echo "<td>" . date('d/m/Y', strtotime($eleicao['data_inicio_eleicao'])) . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($eleicao['data_fim_eleicao'])) . "</td>";
            echo "<td>";
            
            // Verificar candidatos
            $sqlCand = "SELECT COUNT(*) as total FROM candidato WHERE eleicao_fk = ?";
            $stmtCand = $pdo->prepare($sqlCand);
            $stmtCand->execute([$eleicao['id_eleicao']]);
            $totalCand = $stmtCand->fetch(PDO::FETCH_ASSOC)['total'];
            
            echo "<small>Cand: $totalCand</small>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhuma eleição encontrada no banco.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>🎯 Eleição Retornada por buscarEstatisticasEleicaoAtiva():</h2>";

$eleicaoAtiva = $eleicaoDAO->buscarEstatisticasEleicaoAtiva();

if ($eleicaoAtiva) {
    echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Eleição Encontrada:</h3>";
    echo "<p><strong>ID:</strong> " . htmlspecialchars($eleicaoAtiva['id_eleicao']) . "</p>";
    echo "<p><strong>Título:</strong> " . htmlspecialchars($eleicaoAtiva['titulo_documento']) . "</p>";
    echo "<p><strong>Status:</strong> <strong style='color: " . ($eleicaoAtiva['status_eleicao'] === 'ABERTA' ? 'green' : 'red') . ";'>" . htmlspecialchars($eleicaoAtiva['status_eleicao']) . "</strong></p>";
    echo "<p><strong>Data Início:</strong> " . date('d/m/Y', strtotime($eleicaoAtiva['data_inicio_eleicao'])) . "</p>";
    echo "<p><strong>Data Fim:</strong> " . date('d/m/Y', strtotime($eleicaoAtiva['data_fim_eleicao'])) . "</p>";
    echo "<p><strong>Total Candidatos:</strong> " . ($eleicaoAtiva['total_candidatos'] ?? 0) . "</p>";
    echo "</div>";
    
    // Buscar candidatos
    $candidatos = $candidatoDAO->buscarPorEleicao($eleicaoAtiva['id_eleicao']);
    
    if (!empty($candidatos)) {
        echo "<h3>Candidatos (" . count($candidatos) . "):</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Número</th><th>Nome</th></tr>";
        foreach ($candidatos as $candidato) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($candidato['numero_candidato']) . "</td>";
            echo "<td>" . htmlspecialchars($candidato['nome_funcionario'] . ' ' . $candidato['sobrenome_funcionario']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhum candidato encontrado.</p>";
    }
    
} else {
    echo "<p style='color: orange;'>Nenhuma eleição ativa encontrada (status = ABERTA).</p>";
}

echo "<h2>🔍 Verificação Manual:</h2>";

// Verificação manual do status
try {
    $sql = "SELECT e.*, d.titulo_documento,
            COUNT(DISTINCT c.id_candidato) as total_candidatos
            FROM eleicao e
            INNER JOIN documento d ON e.documento_fk = d.id_documento
            LEFT JOIN candidato c ON e.id_eleicao = c.eleicao_fk
            WHERE e.status_eleicao = 'ABERTA'
            GROUP BY e.id_eleicao, d.titulo_documento
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 4px;'>";
        echo "<h4>✅ Query manual encontrou:</h4>";
        echo "<p><strong>Status:</strong> " . htmlspecialchars($resultado['status_eleicao']) . "</p>";
        echo "<p><strong>Título:</strong> " . htmlspecialchars($resultado['titulo_documento']) . "</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 4px;'>";
        echo "<h4>❌ Query manual não encontrou nada com status = 'ABERTA'</h4>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro na query manual: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>💡 Possíveis Problemas:</h2>";
echo "<ul>";
echo "<li>✅ Se a eleição tem status 'VOTAÇÃO NÃO AUTORIZADA', não deve aparecer na lista de candidatos</li>";
echo "<li>✅ Se a eleição tem status 'ABERTA', deve mostrar os candidatos</li>";
echo "<li>❌ Se está mostrando candidatos com status diferente, há um bug na lógica</li>";
echo "</ul>";

echo "<p><a href='/code/cipa_t1/funcionario/home'>← Voltar para página do funcionário</a></p>";
?>
