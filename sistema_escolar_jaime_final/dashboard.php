<?php
// 1. Garante a verificação de sessão e a conexão PDO com a base de dados
require_once __DIR__ . '/config/verifica_sessao.php';
require_once __DIR__ . '/config/conexao.php';

// 2. Inclui o cabeçalho padrão com o logotipo e menu
include_once __DIR__ . '/includes/header.php';

// Inicialização das variáveis de contagem
$totalAlunos = 0;
$totalDisciplinas = 0;
$totalMatriculas = 0;
$ultimosAlunos = [];
$dadosGrafico = [];

try {
    // A exclusão é lógica: os indicadores consideram somente alunos ativos.
    $stmt = $pdo->query('SELECT COUNT(*) FROM alunos WHERE ativo = 1');
    $totalAlunos = $stmt->fetchColumn();

    // Busca o total de disciplinas
    $stmt = $pdo->query('SELECT COUNT(*) FROM disciplinas');
    $totalDisciplinas = $stmt->fetchColumn();

    // Busca o total de matrículas de alunos ativos
    $stmt = $pdo->query(
        'SELECT COUNT(*) FROM matriculas m INNER JOIN alunos a ON a.id = m.aluno_id WHERE a.ativo = 1',
    );
    $totalMatriculas = $stmt->fetchColumn();

    // Busca os últimos 5 alunos ativos cadastrados
    $stmt = $pdo->query(
        'SELECT id, nome, email, criado_em FROM alunos WHERE ativo = 1 ORDER BY id DESC LIMIT 5',
    );
    $ultimosAlunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Busca o total de alunos por disciplina para o gráfico
    $sqlGrafico = "SELECT d.nome AS disciplina, COUNT(a.id) AS total_alunos 
                   FROM disciplinas d 
                   LEFT JOIN matriculas m ON d.id = m.disciplina_id 
                   LEFT JOIN alunos a ON a.id = m.aluno_id AND a.ativo = 1
                   GROUP BY d.id";
    $stmtGrafico = $pdo->query($sqlGrafico);
    $dadosGrafico = $stmtGrafico->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = 'Erro ao carregar dados do Dashboard: ' . $e->getMessage();
}

// Prepara os dados para o gráfico em JavaScript
$labelsDisciplinas = array_column($dadosGrafico, 'disciplina');
$qtdMatriculas = array_column($dadosGrafico, 'total_alunos');
?>

        <!-- Importação da biblioteca de gráficos Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <main class="main-container dashboard-page">
            <h2>Dashboard Geral</h2>
            <p>Bem-vindo ao painel de controlo do seu sistema escolar.</p>

            <!-- 1. BLOCO DE CARDS INDICADORES -->
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total de Alunos</h3>
                    <div class="number"><?= $totalAlunos ?></div>
                </div>
                <div class="card green">
                    <h3>Disciplinas</h3>
                    <div class="number"><?= $totalDisciplinas ?></div>
                </div>
                <div class="card orange">
                    <h3>Matrículas Ativas</h3>
                    <div class="number"><?= $totalMatriculas ?></div>
                </div>
            </div>

            <!-- 2. PAINEL DUPLO: GRÁFICO E ÚLTIMOS CADASTROS -->
            <div class="dashboard-grid">
                <!-- Gráfico de Alunos por Disciplina -->
                <div class="panel">
                    <h3>Matrículas por Disciplina</h3>
                    <canvas id="graficoMatriculas" height="200"></canvas>
                </div>

                <!-- Tabela de Últimos Alunos Cadastrados -->
                <div class="panel">
                    <h3>Últimos Alunos Cadastrados</h3>
                    <div class="dashboard-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ultimosAlunos)): ?>
                                    <?php foreach ($ultimosAlunos as $aluno): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($aluno['nome']) ?></strong></td>
                                            <td><?= htmlspecialchars($aluno['email']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2">Nenhum aluno recente.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- 3. CONFIGURAÇÃO DO GRÁFICO JAVASCRIPT -->
        <script>
            const ctx = document.getElementById('graficoMatriculas').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labelsDisciplinas) ?>,
                    datasets: [
                        {
                            label: 'Número de Alunos',
                            data: <?= json_encode($qtdMatriculas) ?>,
                            backgroundColor: '#3498db',
                            borderRadius: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    },
                },
            });
        </script>

        <?php include_once __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
