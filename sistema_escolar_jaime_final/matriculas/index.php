<?php
// 1. Garante a proteção de sessão e a conexão PDO com a base de dados
require_once __DIR__ . '/../config/verifica_sessao.php';
require_once __DIR__ . '/../config/conexao.php';

// 2. Inclui o cabeçalho padrão
$estiloPagina = 'matriculas.css';
include_once __DIR__ . '/../includes/header.php';

$matriculas = [];
$erro = null;

try {
    // Consulta adaptada à estrutura real da tabela (aluno_id, disciplina_id, nota, media)
    $sql = "SELECT 
                m.aluno_id, 
                m.disciplina_id, 
                m.nota, 
                m.media,
                a.nome AS aluno_nome, 
                d.nome AS disciplina_nome 
            FROM matriculas m
            INNER JOIN alunos a ON a.id = m.aluno_id
            INNER JOIN disciplinas d ON d.id = m.disciplina_id
            WHERE a.ativo = 1
            ORDER BY a.nome ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = 'Erro ao carregar matrículas: ' . $e->getMessage();
}
?>

        <main class="main-container">
            <div class="top-actions">
                <h2>Gestão de Matrículas</h2>
                <a href="cadastrar.php" class="btn primary">+ Nova Matrícula</a>
            </div>

            <!-- Mensagem de erro caso haja alguma falha na consulta -->
            <?php if ($erro): ?>
                <div class="mensagem-erro" role="alert"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <!-- Tabela de Exibição das Matrículas -->
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Disciplina</th>
                            <th>Nota</th>
                            <th>Média</th>
                            <th>Situação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($matriculas)): ?>
                            <?php foreach ($matriculas as $mat): ?>
                                <?php
                                    // Lógica visual para verificar situação acadêmica com base na média
                                    $media = $mat['media'];
                                    $situacao = 'Pendente';
                                    $cor = '#f39c12'; // Laranja

                                    if ($media !== null) {
                                        if ($media >= 7.0) {
                                            $situacao = 'Aprovado';
                                            $cor = 'green';
                                        } else {
                                            $situacao = 'Reprovado';
                                            $cor = 'red';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($mat['aluno_nome']) ?></strong></td>
                                    <td><?= htmlspecialchars($mat['disciplina_nome']) ?></td>
                                    <td><?= $mat['nota'] !== null ? number_format($mat['nota'], 2, ',', '.') : '-' ?></td>
                                    <td><?= $mat['media'] !== null ? number_format($mat['media'], 2, ',', '.') : '-' ?></td>
                                    <td>
                                        <span class="<?= $media === null ? 'status-pendente' : ($media >= 7.0 ? 'status-ativo' : 'status-inativo') ?>"><?= $situacao ?></span>
                                    </td>
                                    <td>
                                        <!-- Chave composta passada no link de ação -->
                                        <a
                                            class="action-link edit"
                                            href="editar.php?aluno_id=<?= $mat['aluno_id'] ?>&disciplina_id=<?= $mat['disciplina_id'] ?>"
                                        >
                                            Editar
                                        </a>
                                        <a
                                            class="action-link delete"
                                            href="excluir.php?aluno_id=<?= $mat['aluno_id'] ?>&disciplina_id=<?= $mat['disciplina_id'] ?>"
                                            onclick="
                                                return confirm('Deseja realmente remover esta matrícula?');
                                            "
                                        >
                                            Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Nenhuma matrícula encontrada no sistema.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- Inclui o rodapé automaticamente -->
        <?php include_once __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
