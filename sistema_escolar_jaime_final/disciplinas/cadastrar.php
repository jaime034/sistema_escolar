<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
require_once '../config/conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura e limpa os valores digitados no formulário
    $nome = trim($_POST['nome'] ?? '');
    $carga_horaria = intval($_POST['carga_horaria'] ?? 0);

    // Valida se o nome foi preenchido e se a carga horária é maior que zero
    if (!empty($nome) && $carga_horaria > 0) {
        try {
            // Insere a nova disciplina no banco de dados
            $sql = 'INSERT INTO disciplinas (nome, carga_horaria) VALUES (:nome, :carga_horaria)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':carga_horaria' => $carga_horaria,
            ]);

            // Redireciona para a listagem após salvar
            header('Location: listar.php');
            exit();
        } catch (PDOException $e) {
            $mensagem = 'Erro ao cadastrar disciplina: ' . $e->getMessage();
        }
    } else {
        $mensagem = 'Por favor, preencha o nome e uma carga horária válida.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cadastrar Nova Disciplina</title>
        <link rel="stylesheet" href="../assets/css/cadasdisc.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <h2>Cadastrar Nova Disciplina</h2>
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem-erro" role="alert"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="nome">Nome da Disciplina:</label>
            <input type="text" id="nome" name="nome" placeholder="Ex: Matemática" required />
            <label for="carga_horaria">Carga Horária (em horas):</label>
            <input
                type="number"
                id="carga_horaria"
                name="carga_horaria"
                min="1"
                placeholder="Ex: 80"
                required
            />
            <div class="form-actions">
                <button type="submit">Salvar Disciplina</button>
                <a href="listar.php">Voltar para a Lista</a>
            </div>
        </form>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
