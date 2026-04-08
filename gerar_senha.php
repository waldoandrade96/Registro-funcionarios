<?php
// Escolha a senha que você quer usar
$senha_desejada = "123456"; 

// Gera o hash seguro
$hash = password_hash($senha_desejada, PASSWORD_DEFAULT);

echo "Sua nova senha é: " . $senha_desejada . "<br>";
echo "O hash para colocar no banco é: <b>" . $hash . "</b>";
?>