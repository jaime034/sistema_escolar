<?php
require_once __DIR__ . '/../config/verifica_sessao.php';
require_once '../config/conexao.php';

// Obtém o ID do aluno passado pela URL
$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        // Exclusão Lógica: apenas desativa o registo
        $sql = 'UPDATE alunos SET ativo = 0 WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        die('Erro ao desativar aluno: ' . $e->getMessage());
    }
}

// Redireciona de volta para a lista atualizada de alunos
header('Location: listar.php');
exit();
