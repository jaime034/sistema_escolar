<?php
session_start();

// O ../ serve para subir um nível e encontrar a pasta config
require_once '../config/conexao.php';

// Proteção: Apenas utilizadores logados podem cadastrar novos utilizadores
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$mensagem = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senhaPadrao = $_POST['senha_padrao'] ?? '';

    if (empty($usuario) || empty($senhaPadrao)) {
        $mensagem = 'Preencha o nome do utilizador e a senha padrão.';
    } else {
        try {
            // Verificação de duplicados
            $stmtCheck = $pdo->prepare('SELECT id FROM usuarios WHERE usuario = :usuario');
            $stmtCheck->execute([':usuario' => $usuario]);

            if ($stmtCheck->rowCount() > 0) {
                $mensagem = 'Este nome de utilizador já existe.';
            } else {
                $senhaHash = password_hash($senhaPadrao, PASSWORD_DEFAULT);

                $sql =
                    'INSERT INTO usuarios (nome, usuario, senha_hash, alterar_senha) VALUES (:nome, :usuario, :senha, 1)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nome' => $usuario,
                    ':usuario' => $usuario,
                    ':senha' => $senhaHash,
                ]);

                $sucesso = true;
                $mensagem = "Utilizador '{$usuario}' criado com sucesso!";
            }
        } catch (PDOException $e) {
            $mensagem = 'Erro ao cadastrar: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cadastrar Novo Utilizador - Sistema Escolar</title>
        <link rel="stylesheet" href="../assets/css/matriculas.css" />
        <link rel="stylesheet" href="../assets/css/rodape.css" />
    </head>
    <body>
        <main class="page auth-page">
            <h1>Cadastrar Novo Utilizador</h1>
            <?php if (!empty($mensagem)): ?>
                <p class="message<?= $sucesso ? ' success' : '' ?>" role="alert"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>
            <form class="card" method="POST" action="">
                <div class="fields">
                    <div class="field">
                        <label for="usuario">Nome de Utilizador:</label>
                        <input
                            type="text"
                            id="usuario"
                            name="usuario"
                            placeholder="Ex: joao_silva"
                            required
                        />
                    </div>
                    <div class="field">
                        <label for="senha_padrao">Senha Padrão (Temporária):</label>
                        <input
                            type="text"
                            id="senha_padrao"
                            name="senha_padrao"
                            value="123456"
                            required
                        />
                    </div>
                </div>
                <div class="form-actions">
                    <button class="primary" type="submit">Criar Utilizador</button>
                    <a class="btn secondary" href="../index.php">Voltar ao Painel Principal</a>
                </div>
            </form>
        </main>
        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
