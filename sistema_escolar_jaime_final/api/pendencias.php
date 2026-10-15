<?php
/**
 * API REST - Consulta de Pendências Acadêmicas e Desempenho
 * Sistema Escolar EETEPA / TechEdu Pro
 */

// Define o cabeçalho para resposta em formato JSON e codificação UTF-8
header('Content-Type: application/json; charset=utf-8');

// Garante a validação da sessão e a conexão PDO com o banco de dados
require_once __DIR__ . '/../config/verifica_sessao.php';
require_once __DIR__ . '/../config/conexao.php';

// Limite configurável para nota de aprovação
define('NOTA_CORTE_APROVACAO', 7.0);

$resposta = [
    'status' => 'sucesso',
    'timestamp' => date('Y-m-d H:i:s'),
    'resumo' => [
        'total_pendencias_notas' => 0,
        'total_alunos_risco' => 0,
    ],
    'pendencias_notas' => [],
    'alunos_em_risco' => [],
];

try {
    /* -------------------------------------------------------------------------
     * 1. CONSULTA DE NOTAS/MÉDIAS PENDENTES
     * Localiza matrículas ativas sem lançamento de nota ou média.
     * ------------------------------------------------------------------------- */
    $sqlPendencias = "
        SELECT 
            a.id AS aluno_id,
            a.nome AS aluno_nome,
            a.matricula AS aluno_matricula,
            d.id AS disciplina_id,
            d.nome AS disciplina_nome
        FROM matriculas m
        INNER JOIN alunos a ON a.id = m.aluno_id
        INNER JOIN disciplinas d ON d.id = m.disciplina_id
        WHERE a.ativo = 1 
          AND (m.nota IS NULL OR m.media IS NULL)
        ORDER BY a.nome ASC, d.nome ASC
    ";

    $stmtPendencias = $pdo->prepare($sqlPendencias);
    $stmtPendencias->execute();
    $pendencias = $stmtPendencias->fetchAll(PDO::FETCH_ASSOC);

    /* -------------------------------------------------------------------------
     * 2. CONSULTA DE ALUNOS EM RISCO ACADÊMICO
     * Identifica alunos ativos com média calculada abaixo do limite (7.0).
     * ------------------------------------------------------------------------- */
    $sqlRisco = "
        SELECT 
            a.id AS aluno_id,
            a.nome AS aluno_nome,
            a.matricula AS aluno_matricula,
            d.nome AS disciplina_nome,
            m.media AS media_atual
        FROM matriculas m
        INNER JOIN alunos a ON a.id = m.aluno_id
        INNER JOIN disciplinas d ON d.id = m.disciplina_id
        WHERE a.ativo = 1 
          AND m.media IS NOT NULL 
          AND m.media < :nota_corte
        ORDER BY m.media ASC
    ";

    $stmtRisco = $pdo->prepare($sqlRisco);
    $stmtRisco->bindValue(':nota_corte', NOTA_CORTE_APROVACAO);
    $stmtRisco->execute();
    $alunosRisco = $stmtRisco->fetchAll(PDO::FETCH_ASSOC);

    // Mapeia os dados formatados para o payload de saída
    $resposta['pendencias_notas'] = array_map(function ($item) {
        return [
            'aluno_id' => (int) $item['aluno_id'],
            'aluno_nome' => $item['aluno_nome'],
            'aluno_matricula' => $item['aluno_matricula'],
            'disciplina_id' => (int) $item['disciplina_id'],
            'disciplina_nome' => $item['disciplina_nome'],
            'mensagem' => 'Lançamento de nota/média pendente',
        ];
    }, $pendencias);

    $resposta['alunos_em_risco'] = array_map(function ($item) {
        $media = (float) $item['media_atual'];
        $pontosFaltantes = NOTA_CORTE_APROVACAO - $media;

        return [
            'aluno_id' => (int) $item['aluno_id'],
            'aluno_nome' => $item['aluno_nome'],
            'aluno_matricula' => $item['aluno_matricula'],
            'disciplina_nome' => $item['disciplina_nome'],
            'media_atual' => number_format($media, 2, '.', ''),
            'pontos_para_aprovação' => number_format($pontosFaltantes, 2, '.', ''),
            'situacao' => 'Em Recuperação / Risco Acadêmico',
        ];
    }, $alunosRisco);

    // Atualiza o resumo com as métricas obtidas
    $resposta['resumo']['total_pendencias_notas'] = count($resposta['pendencias_notas']);
    $resposta['resumo']['total_alunos_risco'] = count($resposta['alunos_em_risco']);

    // Retorna a resposta com status HTTP 200 (OK)
    http_response_code(200);
    echo json_encode($resposta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // Trata falhas de execução no banco de dados com HTTP 500
    http_response_code(500);
    echo json_encode(
        [
            'status' => 'erro',
            'mensagem' => 'Erro interno ao consultar pendências acadêmicas.',
            'detalhe' => $e->getMessage(),
        ],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
    );
}
