<?php
// 1. Garante a proteção de sessão e a conexão com a base de dados
require_once __DIR__ . '/../config/verifica_sessao.php';
require_once __DIR__ . '/../config/conexao.php';

// 2. Inclui o cabeçalho visual padrão do sistema
$estiloPagina = 'listaralunos.css';
include_once __DIR__ . '/../includes/header.php';

$alunos = [];
$erro = null;

try {
    // A exclusão é lógica: a lista deve mostrar somente os alunos ativos.
    $sql = "SELECT id, nome, matricula, email, cpf, data_nascimento, ativo 
            FROM alunos 
            WHERE ativo = 1
            ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = 'Erro ao consultar a base de dados: ' . $e->getMessage();
}
?>

        <main class="main-container">
            <div class="top-actions">
                <h2>Gestão de Alunos</h2>
                <a href="cadastrar.php" class="btn-primary">+ Cadastrar Aluno</a>
            </div>

            <!-- Mensagem de erro caso ocorra falha no SQL -->
            <?php if ($erro): ?>
                <div class="mensagem-erro" role="alert"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <!-- Tabela de Alunos -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Matrícula</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>CPF</th>
                            <th>Data Nasc.</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($alunos)): ?>
                            <?php foreach ($alunos as $aluno): ?>
                                <tr>
                                    <td><?= htmlspecialchars($aluno['id']) ?></td>
                                    <td><strong><?= htmlspecialchars($aluno['matricula']) ?></strong></td>
                                    <td><?= htmlspecialchars($aluno['nome']) ?></td>
                                    <td><?= htmlspecialchars($aluno['email']) ?></td>
                                    <td><?= htmlspecialchars($aluno['cpf'] ?? 'Não informado') ?></td>
                                    <td>
                                        <!-- Formata a data de YYYY-MM-DD para DD/MM/YYYY -->
                                        <?= $aluno['data_nascimento'] ? date('d/m/Y', strtotime($aluno['data_nascimento'])) : '-' ?>
                                    </td>
                                    <td>
                                        <span class="<?= $aluno['ativo'] ? 'status-ativo' : 'status-inativo' ?>"><?= $aluno['ativo'] ? 'Ativo' : 'Inativo' ?></span>
                                    </td>
                                    <td>
                                        <a
                                            class="action-link edit"
                                            href="editar.php?id=<?= $aluno['id'] ?>"
                                        >
                                            Editar
                                        </a>
                                        <a
                                            class="action-link delete"
                                            href="excluir.php?id=<?= $aluno['id'] ?>"
                                            onclick="
                                                return confirm(
                                                    'Tem certeza que deseja excluir este aluno?',
                                                );
                                            "
                                        >
                                            Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">Nenhum aluno cadastrado até o momento.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <?php include_once __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
