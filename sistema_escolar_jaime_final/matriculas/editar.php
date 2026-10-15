<?php
require_once __DIR__ . '/../config/verifica_sessao.php';

require_once '../config/conexao.php';

$aluno_id = intval($_GET['aluno_id'] ?? 0);
$disciplina_id = intval($_GET['disciplina_id'] ?? 0);

$mensagem = '';

$sql = "
    SELECT
        m.*,
        a.nome AS aluno,
        d.nome AS disciplina
    FROM matriculas m
    INNER JOIN alunos a
        ON a.id = m.aluno_id
    INNER JOIN disciplinas d
        ON d.id = m.disciplina_id
    WHERE
        m.aluno_id = :aluno_id
    AND
        m.disciplina_id = :disciplina_id
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':aluno_id' => $aluno_id,
    ':disciplina_id' => $disciplina_id,
]);

$matricula = $stmt->fetch();

if (!$matricula) {
    die('Matrícula não encontrada.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nota = floatval($_POST['nota']);
    $media = trim($_POST['media'] ?? '') !== '' ? floatval($_POST['media']) : null;

    if ($nota < 0 || $nota > 10 || ($media !== null && ($media < 0 || $media > 10))) {
        $mensagem = 'A nota e a média devem estar entre 0 e 10.';
    } else {
        $sql = "
            UPDATE matriculas
            SET
                nota = :nota,
                media = :media
            WHERE
                aluno_id = :aluno_id
            AND
                disciplina_id = :disciplina_id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nota' => $nota,
            ':media' => $media,
            ':aluno_id' => $aluno_id,
            ':disciplina_id' => $disciplina_id,
        ]);

        header('Location: listar.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Editar Matrícula</title>
        <link rel="stylesheet" href="../assets/css/matriculas.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <main class="page">
            <h1>Editar Matrícula</h1>
            <p class="context"><?= htmlspecialchars($matricula['aluno']) ?> · <?= htmlspecialchars($matricula['disciplina']) ?></p>
            <?php if (!empty($mensagem)): ?>
                <p class="message" role="alert"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>
            <form class="card" method="POST">
                <div class="fields">
                    <div class="field">
                        <label for="aluno">Aluno</label>
                        <input id="aluno" type="text" value="<?= htmlspecialchars($matricula['aluno']) ?>" disabled />
                    </div>
                    <div class="field">
                        <label for="disciplina">Disciplina</label>
                        <input
                            id="disciplina"
                            type="text"
                            value="<?= htmlspecialchars($matricula['disciplina']) ?>"
                            disabled
                        />
                    </div>
                    <div class="field">
                        <label for="nota">Nota</label>
                        <input
                            id="nota"
                            type="number"
                            name="nota"
                            min="0"
                            max="10"
                            step="0.01"
                            value="<?= htmlspecialchars((string) ($matricula['nota'] ?? '')) ?>"
                            required
                        />
                    </div>
                    <div class="field">
                        <label for="media">Média</label>
                        <input
                            id="media"
                            type="number"
                            name="media"
                            min="0"
                            max="10"
                            step="0.01"
                            value="<?= htmlspecialchars((string) ($matricula['media'] ?? '')) ?>"
                        />
                    </div>
                </div>
                <div class="form-actions">
                    <button class="primary" type="submit">Atualizar</button>
                    <a class="btn secondary" href="listar.php">Cancelar</a>
                </div>
            </form>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
