<?php
require_once __DIR__ . '/../config/verifica_sessao.php';

require_once '../config/conexao.php';

$aluno_id = intval($_GET['aluno_id'] ?? 0);
$disciplina_id = intval($_GET['disciplina_id'] ?? 0);

if ($aluno_id > 0 && $disciplina_id > 0) {
    try {
        $sql = "
            DELETE FROM matriculas
            WHERE aluno_id = :aluno_id
            AND disciplina_id = :disciplina_id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':aluno_id' => $aluno_id,
            ':disciplina_id' => $disciplina_id,
        ]);
    } catch (PDOException $e) {
        die('Erro ao excluir matrícula: ' . $e->getMessage());
    }
}

header('Location: listar.php');
exit();

?>
