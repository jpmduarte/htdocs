<?php
// Inicia a sessão
session_start();

// Destrói todos os dados da sessão
session_unset();  // Libera todas as variáveis da sessão
session_destroy(); // Destrói a sessão

// Redireciona para a página inicial ou login
header('Location: home/'); // Substitua pelo caminho correto para a página inicial
exit();
?>
