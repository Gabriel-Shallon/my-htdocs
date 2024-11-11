<?php

    include 'inc/function.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrada</title>
</head>
<body>
    <form>
        <h3>Sistema de entrada/saída</h3>
        <label>Nome</label>
        <input name="CPnome"><br>
        <label>CPF</label>
        <input name="CPcpf"><br>
        <input type="submit" value="registrar">
        <input type="reset" value="limpar"><br>
        <a href="index.php">Voltar</a>
    </form>
</body>
</html>


<?php

    if(!empty($_GET)){
        cadastrarEntrada($_GET['CPnome'], $_GET['CPcpf']);
    }

?>