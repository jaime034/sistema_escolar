<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
// Inclui o ficheiro de ligação à base de dados
require_once '../config/conexao.php';

$mensagem = '';
$aluno_id = intval($_POST['aluno_id'] ?? 0);
$disciplina_id = intval($_POST['disciplina_id'] ?? 0);
$nota = trim($_POST['nota'] ?? '');
$media = trim($_POST['media'] ?? '');

// Vincula o aluno à disciplina, em vez de repetir o cadastro de aluno.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($aluno_id <= 0 || $disciplina_id <= 0) {
        $mensagem = 'Selecione o aluno e a disciplina.';
    } elseif (
        ($nota !== '' && (!is_numeric($nota) || $nota < 0 || $nota > 10)) ||
        ($media !== '' && (!is_numeric($media) || $media < 0 || $media > 10))
    ) {
        $mensagem = 'A nota e a média devem estar entre 0 e 10.';
    } else {
        try {
            $sql = 'INSERT INTO matriculas (aluno_id, disciplina_id, nota, media)
                    VALUES (:aluno_id, :disciplina_id, :nota, :media)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':aluno_id' => $aluno_id,
                ':disciplina_id' => $disciplina_id,
                ':nota' => $nota !== '' ? $nota : null,
                ':media' => $media !== '' ? $media : null,
            ]);
            header('Location: listar.php');
            exit();
        } catch (PDOException $e) {
            $mensagem =
                $e->getCode() === '23000'
                    ? 'Esta matrícula já existe ou o aluno/disciplina não foi encontrado.'
                    : 'Erro ao cadastrar matrícula: ' . $e->getMessage();
        }
    }
}

$alunos = $pdo
    ->query('SELECT id, nome FROM alunos WHERE ativo = 1 ORDER BY nome')
    ->fetchAll(PDO::FETCH_ASSOC);
$disciplinas = $pdo
    ->query('SELECT id, nome FROM disciplinas ORDER BY nome')
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cadastrar Nova Matrícula - Sistema Escolar</title>
        <link rel="stylesheet" href="../assets/css/matriculas.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <main class="page">
            <h1>Cadastrar Nova Matrícula</h1>
            <?php if (!empty($mensagem)): ?>
                <p class="message" role="alert"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>
            <form class="card" method="POST" action="">
                <div class="fields">
                    <div class="field">
                        <label for="aluno_id">Aluno:</label>
                        <select id="aluno_id" name="aluno_id" required>
                            <option value="">Selecione o aluno</option>
                            <?php foreach ($alunos as $aluno): ?>
                                <option value="<?= $aluno['id'] ?>" <?= $aluno_id == $aluno['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($aluno['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="disciplina_id">Disciplina:</label>
                        <select id="disciplina_id" name="disciplina_id" required>
                            <option value="">Selecione a disciplina</option>
                            <?php foreach ($disciplinas as $disciplina): ?>
                                <option value="<?= $disciplina['id'] ?>" <?= $disciplina_id == $disciplina['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($disciplina['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="nota">Nota:</label>
                        <input
                            type="number"
                            id="nota"
                            name="nota"
                            min="0"
                            max="10"
                            step="0.01"
                            value="<?= htmlspecialchars($nota) ?>"
                        />
                    </div>
                    <div class="field">
                        <label for="media">Média:</label>
                        <input
                            type="number"
                            id="media"
                            name="media"
                            min="0"
                            max="10"
                            step="0.01"
                            value="<?= htmlspecialchars($media) ?>"
                        />
                    </div>
                </div>
                <div class="form-actions">
                    <button class="primary" type="submit">Salvar Matrícula</button>
                    <a class="btn secondary" href="listar.php">Voltar para a Lista</a>
                </div>
            </form>
        </main>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
