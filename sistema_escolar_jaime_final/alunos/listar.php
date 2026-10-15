<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
// Inclui o ficheiro de ligação à base de dados
require_once '../config/conexao.php';

// A exclusão é lógica: a lista deve mostrar somente os alunos ativos.
try {
    $sql = "SELECT id, nome, matricula, email, data_nascimento, ativo
            FROM alunos
            WHERE ativo = 1
            ORDER BY nome ASC";
    $stmt = $pdo->query($sql);
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao consultar alunos: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gestão de Alunos - Sistema Escolar</title>
        <link rel="stylesheet" href="../assets/css/listaralunos.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <h2>Alunos Cadastrados</h2>
        <nav class="top-actions" aria-label="Ações principais">
            <a class="btn-primary" href="cadastrar.php">+ Cadastrar Novo Aluno</a>
            <a class="btn-secondary" href="../index.php">Página Inicial</a>
        </nav>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Matrícula</th>
                        <th>E-mail</th>
                        <th>Data de Nascimento</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($alunos) > 0): ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td><?= htmlspecialchars($aluno['nome']) ?></td>
                                <td><?= htmlspecialchars($aluno['matricula']) ?></td>
                                <td><?= htmlspecialchars($aluno['email']) ?></td>
                                <td><?= $aluno['data_nascimento'] ? date('d/m/Y', strtotime($aluno['data_nascimento'])) : '-' ?></td>
                                <td><?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                                <td class="acoes">
                                    <a
                                        class="action-link"
                                        href="../matriculas/boletim.php?aluno_id=<?= $aluno['id'] ?>"
                                    >
                                        Boletim
                                    </a>
                                    <a class="action-link edit" href="editar.php?id=<?= $aluno['id'] ?>">
                                        Editar
                                    </a>
                                    <a
                                        class="action-link delete"
                                        href="excluir.php?id=<?= $aluno['id'] ?>"
                                        onclick="
                                            return confirm('Tem certeza que deseja desativar este aluno?');
                                        "
                                    >
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Nenhum aluno cadastrado no sistema.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
