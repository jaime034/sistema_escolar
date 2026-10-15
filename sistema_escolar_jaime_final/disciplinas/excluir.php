<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
require_once '../config/conexao.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        // Exclui a disciplina correspondente ao ID informado
        $sql = 'DELETE FROM disciplinas WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        die('Erro ao excluir disciplina: ' . $e->getMessage());
    }
}

// Redireciona de volta para a lista de disciplinas
header('Location: listar.php');
exit();
