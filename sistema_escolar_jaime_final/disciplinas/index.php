<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
// 1. Início da sessão e inclusão da conexão

// Garante que o usuário está logado (se não estiver, vai para o login)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../usuarios/login.php');
    exit();
}

// Inclui o arquivo de conexão PDO com o banco de dados
require_once '../config/conexao.php';

$mensagem = '';
$erro = '';

// 2. Lógica para cadastrar uma nova disciplina ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $nome = trim($_POST['nome']);
    $carga_horaria = intval($_POST['carga_horaria']);

    if (!empty($nome) && $carga_horaria > 0) {
        try {
            $sql = 'INSERT INTO disciplinas (nome, carga_horaria) VALUES (:nome, :carga_horaria)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':carga_horaria' => $carga_horaria,
            ]);
            $mensagem = 'Disciplina cadastrada com sucesso!';
        } catch (PDOException $e) {
            $erro = 'Erro ao cadastrar: ' . $e->getMessage();
        }
    } else {
        $erro = 'Preencha todos os campos corretamente.';
    }
}

// 3. Consulta para listar todas as disciplinas cadastradas
try {
    $sql_listar = 'SELECT * FROM disciplinas ORDER BY nome ASC';
    $stmt_listar = $pdo->query($sql_listar);
    $disciplinas = $stmt_listar->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $disciplinas = [];
    $erro = 'Erro ao carregar disciplinas: ' . $e->getMessage();
}
$estiloPagina = 'listardisc.css';
include_once __DIR__ . '/../includes/header.php';
?>

        <main class="main-container">
            <h2>Gestão de Disciplinas</h2>
            <nav class="top-actions">
                <a class="btn-primary" href="cadastrar.php">+ Cadastrar Nova Disciplina</a>
                <a class="btn-secondary" href="../index.php">Página Inicial</a>
            </nav>
            <?php if ( !empty($mensagem) ): ?>
                <p class="mensagem sucesso" role="status"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>
            <?php if (!empty($erro)): ?>
                <p class="mensagem erro" role="alert"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>
            <section class="cadastro-disciplina">
                <h3>Nova Disciplina</h3>
                <form method="POST" action="index.php" class="formulario-disciplina">
                    <label for="nome">Nome da Disciplina:</label>
                    <input
                        id="nome"
                        type="text"
                        name="nome"
                        placeholder="Nome da Disciplina (ex: Matemática)"
                        required
                    />
                    <label for="carga_horaria">Carga Horária:</label>
                    <input
                        id="carga_horaria"
                        type="number"
                        name="carga_horaria"
                        placeholder="Carga Horária (horas)"
                        required
                        min="1"
                    />
                    <button type="submit" name="cadastrar">Salvar Disciplina</button>
                </form>
            </section>
            <h3>Disciplinas Cadastradas</h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome da Disciplina</th>
                            <th>Carga Horária</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($disciplinas) > 0): ?>
                            <?php foreach ($disciplinas as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars($d['id']) ?></td>
                                    <td><?= htmlspecialchars($d['nome']) ?></td>
                                    <td><?= htmlspecialchars($d['carga_horaria']) ?>h</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">Nenhuma disciplina cadastrada ainda.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <?php include_once __DIR__ . '/../includes/footer.php'; ?>
    </body>
</html>
