<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
// Inclui o ficheiro de ligação à base de dados
require_once '../config/conexao.php';

/*
|--------------------------------------------------------------------------
| CONSULTAR MATRÍCULAS
|--------------------------------------------------------------------------
| Procura as matrículas combinando os dados das tabelas de alunos 
| e disciplinas através da junção (JOIN).
*/
try {
    $sql = "
        SELECT 
            m.aluno_id, 
            m.disciplina_id, 
            m.nota, 
            m.media,
            a.nome AS nome_aluno, 
            d.nome AS nome_disciplina
        FROM matriculas m
        INNER JOIN alunos a ON m.aluno_id = a.id
        INNER JOIN disciplinas d ON m.disciplina_id = d.id
        WHERE a.ativo = 1
        ORDER BY a.nome ASC, d.nome ASC
    ";

    $stmt = $pdo->query($sql);
    $matriculas = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Erro ao consultar matrículas: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gestão de Matrículas - Sistema Escolar</title>
        <link rel="stylesheet" href="../assets/css/matriculas.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <main class="page">
            <h1>Matrículas Registradas</h1>
            <nav class="toolbar" aria-label="Ações principais">
                <a class="btn primary" href="cadastrar.php">+ Nova Matrícula</a>
                <a class="btn secondary" href="boletim.php">Ver Boletim</a>
                <a class="btn secondary" href="../index.php">Página Inicial</a>
            </nav>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Disciplina</th>
                            <th>Nota Inicial</th>
                            <th>Média Final</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($matriculas) > 0): ?>
                            <?php foreach ($matriculas as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars($m['nome_aluno']) ?></td>
                                    <td><?= htmlspecialchars($m['nome_disciplina']) ?></td>
                                    <td><?= $m['nota'] !== null ? number_format($m['nota'], 2, ',', '.') : '-' ?></td>
                                    <td><?= isset($m['media']) && $m['media'] !== null ? number_format($m['media'], 2, ',', '.') : '-' ?></td>
                                    <td>
                                        <div class="row-actions">
                                            <a href="boletim.php?aluno_id=<?= $m['aluno_id'] ?>">
                                                Boletim
                                            </a>
                                            <a
                                                class="edit"
                                                href="editar.php?aluno_id=<?= $m['aluno_id'] ?>&amp;disciplina_id=<?= $m['disciplina_id'] ?>"
                                            >
                                                Editar
                                            </a>
                                            <a
                                                class="delete"
                                                href="excluir.php?aluno_id=<?= $m['aluno_id'] ?>&amp;disciplina_id=<?= $m['disciplina_id'] ?>"
                                                onclick="
                                                    return confirm(
                                                        'Tem certeza que deseja remover esta matrícula?',
                                                    );
                                                "
                                            >
                                                Excluir
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">Nenhuma matrícula cadastrada no sistema.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
