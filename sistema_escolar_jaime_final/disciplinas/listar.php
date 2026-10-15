<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
// Inclui o ficheiro de ligação à base de dados
require_once '../config/conexao.php';

try {
    // Consulta SQL para buscar todas as disciplinas ordenadas por nome
    $sql = 'SELECT id, nome, carga_horaria FROM disciplinas ORDER BY nome ASC';
    $stmt = $pdo->query($sql);
    $disciplinas = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Erro ao consultar disciplinas: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Lista de Disciplinas - Sistema Escolar</title>
        <link rel="stylesheet" href="../assets/css/listardisc.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <h2>Disciplinas Cadastradas</h2>
        <nav class="top-actions" aria-label="Ações principais">
            <a class="btn-primary" href="cadastrar.php">+ Cadastrar Nova Disciplina</a>
            <a class="btn-secondary" href="../index.php">Página Inicial</a>
        </nav>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome da Disciplina</th>
                        <th>Carga Horária</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($disciplinas) > 0): ?>
                        <?php foreach ($disciplinas as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['id']) ?></td>
                                <td><?= htmlspecialchars($d['nome']) ?></td>
                                <td><?= htmlspecialchars($d['carga_horaria']) ?>h</td>
                                <td class="acoes">
                                    <a class="action-link edit" href="editar.php?id=<?= htmlspecialchars($d['id']) ?>">
                                        Editar
                                    </a>
                                    <a
                                        class="action-link delete"
                                        href="excluir.php?id=<?= htmlspecialchars($d['id']) ?>"
                                        onclick="
                                            return confirm('Deseja realmente excluir esta disciplina?');
                                        "
                                    >
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Nenhuma disciplina cadastrada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
