<?php
// Inicia a sessão caso ainda não esteja ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['usuario_id'])) {
    // Redireciona para a página de login
    header('Location: /sistema-escolar/usuarios/login.php');
    exit();
}
?>
