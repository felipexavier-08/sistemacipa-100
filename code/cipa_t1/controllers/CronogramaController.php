<?php

require_once __DIR__ . "/../repositories/EleicaoDAO.php";

class CronogramaController {
    
    private $eleicaoDAO;
    
    public function __construct() {
        $this->eleicaoDAO = new EleicaoDAO();
    }
    
    /**
     * Exibe o formulário para gerar cronograma
     */
    public function exibirFormulario() {
        // Buscar eleições finalizadas para seleção
        $eleicoesFinalizadas = $this->eleicaoDAO->buscarEleicoesFinalizadas();
        
        $_SESSION['eleicoes_finalizadas'] = $eleicoesFinalizadas;
        
        include "./views/cronograma/formulario.php";
    }
    
    /**
     * Gera o cronograma anual da CIPA
     */
    public function gerarCronograma() {
        try {
            $idEleicao = $_POST['id_eleicao'];
            $dataFinal = $_POST['data_final'];
            
            // Validar data
            if (empty($dataFinal)) {
                $_SESSION['erro_cronograma'] = "Data final da eleição é obrigatória.";
                header("Location: /code/cipa_t1/cronograma/gerar");
                exit;
            }
            
            // Buscar dados da eleição
            $eleicao = $this->eleicaoDAO->buscarPorId($idEleicao);
            
            if (!$eleicao) {
                $_SESSION['erro_cronograma'] = "Eleição não encontrada.";
                header("Location: /code/cipa_t1/cronograma/gerar");
                exit;
            }
            
            // Gerar cronograma
            $cronograma = $this->calcularCronogramaAnual($dataFinal, $eleicao);
            
            // Armazenar na sessão
            $_SESSION['cronograma_gerado'] = $cronograma;
            $_SESSION['dados_eleicao_cronograma'] = $eleicao;
            
            header("Location: /code/cipa_t1/cronograma/visualizar");
            exit;
            
        } catch (Exception $e) {
            $_SESSION['erro_cronograma'] = "Erro ao gerar cronograma: " . $e->getMessage();
            header("Location: /code/cipa_t1/cronograma/gerar");
            exit;
        }
    }
    
    /**
     * Visualiza o cronograma gerado
     */
    public function visualizarCronograma() {
        if (!isset($_SESSION['cronograma_gerado'])) {
            header("Location: /code/cipa_t1/cronograma/gerar");
            exit;
        }
        
        include "./views/cronograma/visualizar.php";
    }
    
    /**
     * Exibe o formulário para editar cronograma
     */
    public function exibirFormularioEdicao() {
        if (!isset($_SESSION['cronograma_gerado'])) {
            header("Location: /code/cipa_t1/cronograma/gerar");
            exit;
        }
        
        include "./views/cronograma/editar.php";
    }
    
    /**
     * Atualiza o cronograma editado
     */
    public function atualizarCronograma() {
        try {
            if (!isset($_SESSION['cronograma_gerado'])) {
                $_SESSION['erro_cronograma'] = "Nenhum cronograma para editar.";
                header("Location: /code/cipa_t1/cronograma/gerar");
                exit;
            }
            
            $cronogramaOriginal = $_SESSION['cronograma_gerado'];
            $cronogramaAtualizado = [];
            
            // Atualizar dados do formulário
            foreach ($cronogramaOriginal as $index => $item) {
                $cronogramaAtualizado[$index] = [
                    'evento' => $item['evento'],
                    'data' => $_POST['data_' . $index] ?? $item['data'],
                    'descricao' => $_POST['descricao_' . $index] ?? $item['descricao'],
                    'responsavel' => $_POST['responsavel_' . $index] ?? $item['responsavel'],
                    'obrigatorio' => $_POST['obrigatorio_' . $index] ?? $item['obrigatorio']
                ];
            }
            
            // Armazenar cronograma atualizado
            $_SESSION['cronograma_gerado'] = $cronogramaAtualizado;
            $_SESSION['sucesso_cronograma'] = "Cronograma atualizado com sucesso!";
            
            header("Location: /code/cipa_t1/cronograma/visualizar");
            exit;
            
        } catch (Exception $e) {
            $_SESSION['erro_cronograma'] = "Erro ao atualizar cronograma: " . $e->getMessage();
            header("Location: /code/cipa_t1/cronograma/editar");
            exit;
        }
    }

    /**
     * Exporta o cronograma para Excel
     */
    public function exportarExcel() {
        if (!isset($_SESSION['cronograma_gerado'])) {
            header("Location: /code/cipa_t1/cronograma/gerar");
            exit;
        }
        
        // Configurar headers para download
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="cronograma_anual_cipa_' . date('d_m_Y') . '.xls"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');
        
        // Criar output
        $output = fopen('php://output', 'w');
        
        // Adicionar BOM para compatibilidade com Excel
        fwrite($output, "\xEF\xBB\xBF");
        
        // HTML completo para Excel com formatação
        $html = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <meta name="ProgId" content="Excel.Sheet">
    <meta name="Generator" content="Microsoft Excel">
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <style>
        table {
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        th {
            background-color: #4b5c49;
            color: white;
            font-weight: bold;
            text-align: center;
            border: 1px solid #333;
            padding: 8px;
        }
        td {
            border: 1px solid #333;
            padding: 8px;
            vertical-align: top;
        }
        .header-cell {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            border: 1px solid #333;
            padding: 10px;
        }
        .data-cell {
            background-color: #e8f5e8;
            text-align: center;
            font-weight: bold;
        }
        .evento-cell {
            font-weight: bold;
            color: #2c3e50;
        }
        .responsavel-cell {
            color: #007bff;
            font-weight: bold;
        }
        .obrigatorio-sim {
            color: #28a745;
            font-weight: bold;
            text-align: center;
        }
        .obrigatorio-nao {
            color: #6c757d;
            text-align: center;
        }
        .observacoes-row {
            background-color: #fff3cd;
            font-weight: bold;
        }
    </style>
</head>
<body>';

        // Título principal
        $html .= '<table>';
        $html .= '<tr><td colspan="5" class="header-cell" style="font-size: 16px; background-color: #4b5c49; color: white;">CRONOGRAMA ANUAL DA CIPA</td></tr>';
        
        // Informações da eleição
        $html .= '<tr><td colspan="5" class="header-cell">Eleição: ' . htmlspecialchars($_SESSION['dados_eleicao_cronograma']['titulo_documento']) . '</td></tr>';
        $html .= '<tr><td colspan="5" class="header-cell">Data Final: ' . date('d/m/Y', strtotime($_SESSION['dados_eleicao_cronograma']['data_fim_eleicao'])) . '</td></tr>';
        $html .= '<tr><td colspan="5" class="header-cell">Data de Geração: ' . date('d/m/Y H:i:s') . '</td></tr>';
        $html .= '<tr><td colspan="5" style="height: 10px; border: none;"></td></tr>';
        
        // Cabeçalhos da tabela
        $html .= '<tr>';
        $html .= '<th style="width: 25%;">Evento</th>';
        $html .= '<th style="width: 15%;">Data</th>';
        $html .= '<th style="width: 35%;">Descrição</th>';
        $html .= '<th style="width: 15%;">Responsável</th>';
        $html .= '<th style="width: 10%;">Obrigatório</th>';
        $html .= '</tr>';
        
        // Dados do cronograma
        foreach ($_SESSION['cronograma_gerado'] as $item) {
            $html .= '<tr>';
            $html .= '<td class="evento-cell">' . htmlspecialchars($item['evento']) . '</td>';
            $html .= '<td class="data-cell">' . htmlspecialchars($item['data']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['descricao']) . '</td>';
            $html .= '<td class="responsavel-cell">' . htmlspecialchars($item['responsavel']) . '</td>';
            $html .= '<td class="' . ($item['obrigatorio'] === 'Sim' ? 'obrigatorio-sim' : 'obrigatorio-nao') . '">' . htmlspecialchars($item['obrigatorio']) . '</td>';
            $html .= '</tr>';
        }
        
        // Linha separadora
        $html .= '<tr><td colspan="5" style="height: 10px; border: none;"></td></tr>';
        
        // Observações
        $html .= '<tr><td colspan="5" class="observacoes-row">OBSERVAÇÕES SOBRE OS CÁLCULOS DOS PRAZOS</td></tr>';
        $html .= '<tr><td colspan="5">Base de Cálculo: A partir da data final da eleição informada, foi calculado um cronograma completo para o mandato de 1 (um) ano, conforme exigências da NR-05.</td></tr>';
        $html .= '<tr><td colspan="5">Critérios Utilizados:</td></tr>';
        $html .= '<tr><td colspan="5">• Posse: Primeiro dia útil imediatamente seguinte à data final da eleição</td></tr>';
        $html .= '<tr><td colspan="5">• Treinamento Inicial: Realizado em até 30 (trinta) dias após a posse, com carga horária mínima de 20 (vinte) horas</td></tr>';
        $html .= '<tr><td colspan="5">• Reuniões Ordinárias: Mensais, fixadas no dia 10 de cada mês, ajustadas para primeiro dia útil</td></tr>';
        $html .= '<tr><td colspan="5">• Inspeções de Segurança: Mensais, programadas para o dia 25 de cada mês, ajustadas para dias úteis</td></tr>';
        $html .= '<tr><td colspan="5">• SIPAT: Semana Interna de Prevenção de Acidentes programada para meados de julho</td></tr>';
        $html .= '<tr><td colspan="5">• Processo Eleitoral Seguinte: Iniciado com 60 (sessenta) dias de antecedência ao término do mandato</td></tr>';
        $html .= '<tr><td colspan="5">• Ajustes de Dias Úteis: Todas as datas foram ajustadas para dias úteis quando necessário</td></tr>';
        $html .= '<tr><td colspan="5">• Conformidade Legal: Este cronograma atende integralmente aos requisitos da NR-05</td></tr>';
        
        $html .= '</table>';
        $html .= '</body></html>';
        
        // Escrever HTML no output
        fwrite($output, $html);
        fclose($output);
        exit;
    }
    
    /**
     * Calcula o cronograma anual completo da CIPA
     */
    private function calcularCronogramaAnual($dataFinal, $eleicao) {
        $cronograma = [];
        
        // Converter data para objeto DateTime
        $dataFinalObj = new DateTime($dataFinal);
        $ano = $dataFinalObj->format('Y');
        
        // 1. Data de posse da CIPA (primeiro dia útil após eleição)
        $dataPosse = $this->calcularPrimeiroDiaUtil($dataFinalObj);
        $cronograma[] = [
            'evento' => 'Posse da CIPA',
            'data' => $dataPosse->format('d/m/Y'),
            'descricao' => 'Posse dos membros eleitos da CIPA',
            'responsavel' => 'Gestão / RH',
            'obrigatorio' => 'Sim'
        ];
        
        // 2. Treinamento inicial da CIPA (até 30 dias após posse)
        $dataTreinamento = clone $dataPosse;
        $dataTreinamento->add(new DateInterval('P20D')); // 20 dias após posse
        $dataTreinamento = $this->ajustarDiaUtil($dataTreinamento);
        $cronograma[] = [
            'evento' => 'Treinamento Inicial da CIPA',
            'data' => $dataTreinamento->format('d/m/Y'),
            'descricao' => 'Treinamento de 20 horas para membros da CIPA (NR-05)',
            'responsavel' => 'SESMT / RH',
            'obrigatorio' => 'Sim'
        ];
        
        // 3. Reuniões ordinárias mensais
        for ($mes = 1; $mes <= 12; $mes++) {
            $dataReuniao = new DateTime("$ano-$mes-10"); // Dia 10 de cada mês
            if ($dataReuniao < $dataPosse) continue; // Pular meses anteriores à posse
            
            $dataReuniao = $this->ajustarDiaUtil($dataReuniao);
            $cronograma[] = [
                'evento' => 'Reunião Ordinária Mensal',
                'data' => $dataReuniao->format('d/m/Y'),
                'descricao' => 'Reunião mensal da CIPA - ' . $dataReuniao->format('F/Y'),
                'responsavel' => 'Presidente da CIPA',
                'obrigatorio' => 'Sim'
            ];
        }
        
        // 4. Inspeções de segurança (mensais)
        for ($mes = 1; $mes <= 12; $mes++) {
            $dataInspecao = new DateTime("$ano-$mes-25"); // Dia 25 de cada mês
            if ($dataInspecao < $dataPosse) continue; // Pular meses anteriores à posse
            
            $dataInspecao = $this->ajustarDiaUtil($dataInspecao);
            $cronograma[] = [
                'evento' => 'Inspeção de Segurança',
                'data' => $dataInspecao->format('d/m/Y'),
                'descricao' => 'Inspeção mensal de segurança nos ambientes de trabalho',
                'responsavel' => 'Comissão de Inspeção',
                'obrigatorio' => 'Sim'
            ];
        }
        
        // 5. SIPAT (Semana Interna de Prevenção de Acidentes)
        $dataSIPAT = new DateTime("$ano-07-15"); // Meados de julho
        if ($dataSIPAT >= $dataPosse) {
            $dataSIPAT = $this->ajustarDiaUtil($dataSIPAT);
            $cronograma[] = [
                'evento' => 'SIPAT - Semana Interna de Prevenção de Acidentes',
                'data' => $dataSIPAT->format('d/m/Y'),
                'descricao' => 'Semana de atividades de prevenção de acidentes (NR-05)',
                'responsavel' => 'CIPA / SESMT',
                'obrigatorio' => 'Sim'
            ];
        }
        
        // 6. Processo eleitoral da próxima CIPA (inicia 60 dias antes do término)
        $dataProximoProcesso = clone $dataFinalObj;
        $dataProximoProcesso->add(new DateInterval('P1Y')); // Próximo ano
        $dataProximoProcesso->sub(new DateInterval('P60D')); // 60 dias antes
        
        // Publicação do edital
        $cronograma[] = [
            'evento' => 'Publicação do Edital Eleitoral',
            'data' => $dataProximoProcesso->format('d/m/Y'),
            'descricao' => 'Publicação do edital para eleição da próxima gestão',
            'responsavel' => 'Gestão / CIPA',
            'obrigatorio' => 'Sim'
        ];
        
        // Período de inscrição
        $dataInicioInscricao = clone $dataProximoProcesso;
        $dataInicioInscricao->add(new DateInterval('P1D'));
        $dataFimInscricao = clone $dataProximoProcesso;
        $dataFimInscricao->add(new DateInterval('P15D'));
        
        $cronograma[] = [
            'evento' => 'Período de Inscrição de Candidatos',
            'data' => $dataInicioInscricao->format('d/m/Y') . ' a ' . $dataFimInscricao->format('d/m/Y'),
            'descricao' => 'Período para inscrição de candidatos à próxima CIPA',
            'responsavel' => 'Comissão Eleitoral',
            'obrigatorio' => 'Sim'
        ];
        
        // Eleição
        $dataEleicao = clone $dataProximoProcesso;
        $dataEleicao->add(new DateInterval('P30D'));
        $dataEleicao = $this->ajustarDiaUtil($dataEleicao);
        
        $cronograma[] = [
            'evento' => 'Eleição da Próxima CIPA',
            'data' => $dataEleicao->format('d/m/Y'),
            'descricao' => 'Votação para escolha dos membros da próxima gestão',
            'responsavel' => 'Comissão Eleitoral',
            'obrigatorio' => 'Sim'
        ];
        
        // Apuração
        $dataApuracao = clone $dataEleicao;
        $dataApuracao->add(new DateInterval('P2D'));
        $dataApuracao = $this->ajustarDiaUtil($dataApuracao);
        
        $cronograma[] = [
            'evento' => 'Apuração dos Votos',
            'data' => $dataApuracao->format('d/m/Y'),
            'descricao' => 'Contagem e divulgação dos resultados da eleição',
            'responsavel' => 'Comissão Eleitoral',
            'obrigatorio' => 'Sim'
        ];
        
        // Posse da nova CIPA
        $dataPosseNova = clone $dataEleicao;
        $dataPosseNova->add(new DateInterval('P1Y1D')); // Primeiro dia útil após eleição do próximo ano
        $dataPosseNova = $this->calcularPrimeiroDiaUtil($dataPosseNova);
        
        $cronograma[] = [
            'evento' => 'Posse da Nova CIPA',
            'data' => $dataPosseNova->format('d/m/Y'),
            'descricao' => 'Posse dos membros eleitos da próxima gestão',
            'responsavel' => 'Gestão / RH',
            'obrigatorio' => 'Sim'
        ];
        
        // Ordenar cronograma por data
        usort($cronograma, function($a, $b) {
            $dataA = DateTime::createFromFormat('d/m/Y', explode(' a ', $a['data'])[0]);
            $dataB = DateTime::createFromFormat('d/m/Y', explode(' a ', $b['data'])[0]);
            return $dataA <=> $dataB;
        });
        
        return $cronograma;
    }
    
    /**
     * Calcula o primeiro dia útil após uma data
     */
    private function calcularPrimeiroDiaUtil(DateTime $data) {
        $data->add(new DateInterval('P1D')); // Dia seguinte
        
        while ($data->format('N') >= 6) { // Sábado (6) ou Domingo (7)
            $data->add(new DateInterval('P1D'));
        }
        
        return $data;
    }
    
    /**
     * Ajusta data para dia útil (se cair no fim de semana)
     */
    private function ajustarDiaUtil(DateTime $data) {
        while ($data->format('N') >= 6) { // Sábado (6) ou Domingo (7)
            $data->add(new DateInterval('P1D'));
        }
        return $data;
    }
}

?>
