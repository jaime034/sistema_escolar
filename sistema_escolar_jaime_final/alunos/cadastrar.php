<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
// Inclui o ficheiro de ligação à base de dados
require_once '../config/conexao.php';

$mensagem = '';

// Processa os dados quando o formulário é enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura e limpa os campos enviados
    $nome = trim($_POST['nome'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? '');

    // Validação básica de campos obrigatórios
    if (empty($nome) || empty($matricula) || empty($email)) {
        $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        try {
            // Insere o novo aluno no banco de dados com estado ativo
            $sql = "INSERT INTO alunos (nome, matricula, email, data_nascimento, ativo) 
                    VALUES (:nome, :matricula, :email, :data_nascimento, 1)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':matricula' => $matricula,
                ':email' => $email,
                ':data_nascimento' => !empty($data_nascimento) ? $data_nascimento : null,
            ]);

            // Redireciona para a lista de alunos após o sucesso
            header('Location: listar.php');
            exit();
        } catch (PDOException $e) {
            $mensagem = 'Erro ao cadastrar aluno: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cadastrar Novo Aluno - Sistema Escolar</title>
        <link rel="stylesheet" href="../assets/css/cadastraralunos.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <h2>Cadastrar Novo Aluno</h2>
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem-erro" role="alert"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="nome">Nome Completo:</label>
            <input
                type="text"
                id="nome"
                name="nome"
                placeholder="Digite o nome completo"
                required
            />
            <label for="matricula">Matrícula:</label>
            <input
                type="text"
                id="matricula"
                name="matricula"
                placeholder="Digite o número da matrícula"
                required
            />
            <label for="email">E-mail:</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="exemplo@dominio.com"
                required
            />
            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" />
            <div class="form-actions">
                <button type="submit">Salvar Aluno</button>
                <a href="listar.php">Voltar para a Lista</a>
            </div>
        </form>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
