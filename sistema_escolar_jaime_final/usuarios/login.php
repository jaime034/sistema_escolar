<?php
// 1. Inicia a sessão no PHP
session_start();

// 2. Inclui o ficheiro de conexão com a base de dados
require_once '../config/conexao.php';

$erro = '';

// 3. Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (!empty($usuario) && !empty($senha)) {
        try {
            // CORREÇÃO: Procura apenas pela coluna 'nome' ou 'usuario' na tabela
            // Ajuste 'usuario' para 'nome' caso a tua coluna se chame 'nome'
            $sql = 'SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':usuario' => $usuario]);
            $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Se não encontrou pela coluna 'usuario', tenta pela coluna 'nome' (Fallback de segurança)
            if (!$dadosUsuario) {
                $sql = 'SELECT * FROM usuarios WHERE nome = :usuario LIMIT 1';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':usuario' => $usuario]);
                $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Valida a palavra-passe e inicia a sessão
            if ($dadosUsuario && password_verify($senha, $dadosUsuario['senha_hash'])) {
                $_SESSION['usuario_id'] = $dadosUsuario['id'];
                $_SESSION['usuario_nome'] = $dadosUsuario['nome'] ?? $dadosUsuario['usuario'];

                // Redireciona diretamente para o Dashboard
                header('Location: ../dashboard.php');
                exit();
            } else {
                $erro = 'Utilizador ou palavra-passe incorretos.';
            }
        } catch (PDOException $e) {
            $erro = 'Erro no sistema: ' . $e->getMessage();
        }
    } else {
        $erro = 'Preencha todos os campos para entrar.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Login - Sistema Escolar</title>
        <link rel="stylesheet" href="../assets/css/matriculas.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <main class="page auth-page">
            <h1>Sistema Escolar</h1>
            <?php if (!empty($erro)): ?>
                <p class="message" role="alert"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>
            <form class="card" method="POST" action="login.php">
                <div class="fields">
                    <div class="field">
                        <label for="usuario">Utilizador</label>
                        <input
                            type="text"
                            id="usuario"
                            name="usuario"
                            required
                            autofocus
                            placeholder="Digite seu usuário"
                        />
                    </div>
                    <div class="field">
                        <label for="senha">Palavra-passe</label>
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            required
                            placeholder="Digite sua senha"
                        />
                    </div>
                </div>
                <div class="form-actions">
                    <button class="primary" type="submit">Entrar</button>
                </div>
            </form>
        </main>
        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
