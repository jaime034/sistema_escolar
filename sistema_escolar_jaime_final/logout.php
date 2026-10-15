<?php
// 1. Inicia a sessão para ter acesso aos dados guardados
session_start();

// 2. Limpa todas as variáveis da sessão
$_SESSION = [];

// 3. Destrói a sessão no servidor
session_destroy();

// 4. Redireciona para o ficheiro login.php localizado DENTRO da pasta usuarios
header('Location: usuarios/login.php?msg=desconectado');
exit();
?>
