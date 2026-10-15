<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
require_once '../config/conexao.php';

$id = $_GET['id'] ?? 0;
$mensagem = '';

// 1. Busca os dados atuais do aluno para preencher o formulário
$sql = 'SELECT * FROM alunos WHERE id = :id AND ativo = 1';
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$aluno = $stmt->fetch();

// Se o aluno não for encontrado, interrompe a execução
if (!$aluno) {
    die('Aluno não encontrado ou inativo.');
}

// 2. Processa a atualização quando o formulário é enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $matricula = trim($_POST['matricula']);
    $email = trim($_POST['email']);
    $data_nasc = $_POST['data_nascimento'];

    if (!empty($nome) && !empty($matricula) && !empty($email) && !empty($data_nasc)) {
        try {
            $sql = "UPDATE alunos SET 
                        nome = :nome, 
                        matricula = :matricula, 
                        email = :email, 
                        data_nascimento = :data_nascimento 
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':matricula' => $matricula,
                ':email' => $email,
                ':data_nascimento' => $data_nasc,
                ':id' => $id,
            ]);

            header('Location: listar.php');
            exit();
        } catch (PDOException $e) {
            $mensagem = 'Erro ao atualizar dados: ' . $e->getMessage();
        }
    } else {
        $mensagem = 'Por favor, preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Editar Aluno</title>
        <link rel="stylesheet" href="../assets/css/editaralunos.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <h2>Editar Aluno</h2>
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem-erro" role="alert"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input id="nome" type="text" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required />
            <label for="matricula">Matrícula:</label>
            <input
                id="matricula"
                type="text"
                name="matricula"
                value="<?= htmlspecialchars($aluno['matricula']) ?>"
                required
            />
            <label for="email">E-mail:</label>
            <input id="email" type="email" name="email" value="<?= htmlspecialchars($aluno['email']) ?>" required />
            <label for="data_nascimento">Data de Nascimento:</label>
            <input
                id="data_nascimento"
                type="date"
                name="data_nascimento"
                value="<?= htmlspecialchars((string) ($aluno['data_nascimento'] ?? '')) ?>"
                required
            />
            <div class="form-actions">
                <button type="submit">Atualizar</button>
                <a href="listar.php">Cancelar</a>
            </div>
        </form>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
