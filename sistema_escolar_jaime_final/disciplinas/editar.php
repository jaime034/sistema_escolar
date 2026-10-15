<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
// Inclui o ficheiro de ligação à base de dados
require_once '../config/conexao.php';

// Captura e sanitiza o ID vindo da URL
$id = intval($_GET['id'] ?? 0);
$mensagem = '';

// Redireciona ou interrompe se o ID for inválido
if ($id <= 0) {
    die('ID de disciplina inválido.');
}

/*
|--------------------------------------------------------------------------
| 1. BUSCAR DADOS ATUAIS DA DISCIPLINA
|--------------------------------------------------------------------------
*/
try {
    $sql = 'SELECT id, nome, carga_horaria FROM disciplinas WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $disciplina = $stmt->fetch();

    if (!$disciplina) {
        die('Disciplina não encontrada.');
    }
} catch (PDOException $e) {
    die('Erro ao carregar disciplina: ' . $e->getMessage());
}

/*
|--------------------------------------------------------------------------
| 2. PROCESSAR O FORMULÁRIO DE ATUALIZAÇÃO (MÉTODO POST)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização dos dados recebidos do formulário
    $nome = trim($_POST['nome'] ?? '');
    $carga_horaria = intval($_POST['carga_horaria'] ?? 0);

    // Validação dos campos obrigatórios
    if (!empty($nome) && $carga_horaria > 0) {
        try {
            // Executa a atualização no banco de dados
            $sql =
                'UPDATE disciplinas SET nome = :nome, carga_horaria = :carga_horaria WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':carga_horaria' => $carga_horaria,
                ':id' => $id,
            ]);

            // Redireciona para a lista após a edição
            header('Location: listar.php');
            exit();
        } catch (PDOException $e) {
            $mensagem = 'Erro ao atualizar disciplina: ' . $e->getMessage();
        }
    } else {
        $mensagem = 'Por favor, preencha todos os campos corretamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Editar Disciplina</title>
        <link rel="stylesheet" href="../assets/css/editardisc.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <h2>Editar Disciplina</h2>
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem-erro" role="alert"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="nome">Nome da Disciplina:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($disciplina['nome']) ?>" required />
            <label for="carga_horaria">Carga Horária (em horas):</label>
            <input
                type="number"
                id="carga_horaria"
                name="carga_horaria"
                min="1"
                value="<?= htmlspecialchars($disciplina['carga_horaria']) ?>"
                required
            />
            <div class="form-actions">
                <button type="submit">Atualizar Disciplina</button>
                <a href="listar.php">Cancelar</a>
            </div>
        </form>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
