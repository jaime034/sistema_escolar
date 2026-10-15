<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
require_once '../config/conexao.php';

// Captura o ID do aluno enviado pela URL ou formulário (Padrão: Aluno 1)
$aluno_id = intval($_GET['aluno_id'] ?? 1);

// 1. Busca os dados do Aluno
$sql_aluno = 'SELECT id, nome, matricula, email FROM alunos WHERE id = :id LIMIT 1';
$stmt_aluno = $pdo->prepare($sql_aluno);
$stmt_aluno->execute([':id' => $aluno_id]);
$aluno = $stmt_aluno->fetch(PDO::FETCH_ASSOC);

// Busca a lista de todos os alunos para o menu de seleção
$todos_alunos = $pdo
    ->query('SELECT id, nome FROM alunos WHERE ativo = 1 ORDER BY nome ASC')
    ->fetchAll(PDO::FETCH_ASSOC);

// 2. Busca as disciplinas e notas do aluno selecionado
$sql_notas = "SELECT d.nome AS disciplina, d.carga_horaria, m.nota, m.media 
              FROM matriculas m 
              INNER JOIN disciplinas d ON m.disciplina_id = d.id 
              WHERE m.aluno_id = :aluno_id 
              ORDER BY d.nome ASC";

$stmt_notas = $pdo->prepare($sql_notas);
$stmt_notas->execute([':aluno_id' => $aluno_id]);
$boletim = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Boletim Escolar - <?= htmlspecialchars($aluno['nome'] ?? 'Aluno') ?></title>
        <link rel="stylesheet" href="../assets/css/boletim.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <main class="report">
            <div class="report-tools no-print">
                <form class="student-picker" method="GET" action="boletim.php">
                    <label for="aluno_id">Selecionar Aluno:</label>
                    <select name="aluno_id" id="aluno_id" onchange="this.form.submit()">
                        <?php foreach ($todos_alunos as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= $a['id'] == $aluno_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <div class="tool-actions">
                    <button type="button" onclick="window.print()">Imprimir</button>
                    <a class="back" href="../dashboard.php">Voltar</a>
                </div>
            </div>
            <header class="report-header">
                <h1>Sistema Escolar EETEPA</h1>
                <p>Boletim de Desempenho Acadêmico</p>
            </header>
            <?php if ($aluno): ?>
                <div class="student-data">
                    <span>
                        <strong>Nome do Aluno:</strong>
                        <?= htmlspecialchars($aluno['nome']) ?>
                    </span>
                    <span>
                        <strong>Matrícula:</strong>
                        <?= htmlspecialchars($aluno['matricula']) ?>
                    </span>
                    <span>
                        <strong>E-mail:</strong>
                        <?= htmlspecialchars($aluno['email']) ?>
                    </span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Disciplina</th>
                                <th>Carga Horária</th>
                                <th>Nota / Média</th>
                                <th>Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($boletim) > 0): ?>
                                <?php foreach ($boletim as $item): ?>
                                    <?php
                                        $notaFinal = $item['media'] ?? ($item['nota'] ?? 0);
                                        $situacao = $notaFinal >= 7.0 ? 'Aprovado' : 'Recuperação';
                                        $classeSituacao = $notaFinal >= 7.0 ? 'aprovado' : 'recuperacao';
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['disciplina']) ?></td>
                                        <td><?= htmlspecialchars($item['carga_horaria']) ?>h</td>
                                        <td><?= number_format($notaFinal, 2, ',', '.') ?></td>
                                        <td class="<?= $classeSituacao ?>"><?= $situacao ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        Nenhuma disciplina ou nota registrada para este aluno.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="message" role="alert">Aluno não encontrado.</p>
            <?php endif; ?>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
