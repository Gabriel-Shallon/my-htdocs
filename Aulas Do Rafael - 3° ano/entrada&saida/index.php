<?php

    include 'inc/function.php';
    if(!empty($_GET)){
       if($_GET['acao']=='limpa'){
        echo limparDados();
       }
       if($_GET['acao']=='remove'){
        echo registrarSaida($_GET['cpf']);
       }
    }

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
</head>
<body>
    <h3>Sistema de entrada/saída</h3>
    <a href="entrada.php">Regsitrar entrada</a><br>
    <a href="saida.php">Registrar saída</a><br>
    <a href="index.php?acao=limpa">Limpar dados</a><br>
</body>
</html>