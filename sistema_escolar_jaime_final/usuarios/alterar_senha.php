<?php
// Inicia a sessão PHP para identificar o utilizador logado
session_start();

// Inclui o ficheiro de conexão subindo um nível de pasta
require_once '../config/conexao.php';

// Proteção: Redireciona para o login caso o utilizador não esteja autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$mensagem = '';

// Processa o formulário quando enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    // Validação de preenchimento e igualdade das senhas
    if (empty($novaSenha) || empty($confirmarSenha)) {
        $mensagem = 'Por favor, preencha todos os campos.';
    } elseif ($novaSenha !== $confirmarSenha) {
        $mensagem = 'As senhas não coincidem. Tente novamente.';
    } elseif (strlen($novaSenha) < 6) {
        $mensagem = 'A nova senha deve ter pelo menos 6 caracteres.';
    } else {
        try {
            // Criptografa a nova senha escolhida pelo utilizador
            $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

            // Atualiza a senha e define alterar_senha = 0 no banco de dados
            $sql = 'UPDATE usuarios SET senha_hash = :senha, alterar_senha = 0 WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':senha' => $novaSenhaHash,
                ':id' => $_SESSION['usuario_id'],
            ]);

            // Redireciona para o painel principal após a alteração com sucesso
            header('Location: ../index.php');
            exit();
        } catch (PDOException $e) {
            $mensagem = 'Erro ao atualizar a senha: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Criar Nova Senha - Sistema Escolar</title>
        <link rel="stylesheet" href="../assets/css/matriculas.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <main class="page auth-page">
            <h1>Criar Nova Senha</h1>
            <p class="context">
                Por favor, defina a sua nova senha pessoal para continuar a utilizar o sistema.
            </p>
            <?php if (!empty($mensagem)): ?>
                <p class="message" role="alert"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>
            <form class="card" method="POST" action="">
                <div class="fields">
                    <div class="field">
                        <label for="nova_senha">Nova Senha:</label>
                        <input
                            type="password"
                            id="nova_senha"
                            name="nova_senha"
                            placeholder="Digite a nova senha (mín. 6 caracteres)"
                            required
                        />
                    </div>
                    <div class="field">
                        <label for="confirmar_senha">Confirme a Nova Senha:</label>
                        <input
                            type="password"
                            id="confirmar_senha"
                            name="confirmar_senha"
                            placeholder="Repita a nova senha"
                            required
                        />
                    </div>
                </div>
                <div class="form-actions">
                    <button class="primary" type="submit">Salvar Nova Senha</button>
                    <a class="btn secondary" href="../index.php">Voltar ao Painel Principal</a>
                </div>
            </form>
        </main>
        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
