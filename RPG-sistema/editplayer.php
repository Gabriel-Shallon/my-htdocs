<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php

include 'inc/func.php';


$dados = [];
foreach (array_column(getAllPlayers(), 'nome') as $_) {
    $dados[] = rand(1, 6); 
}

$resultado = iniciativa(array_column(getAllPlayers(), 'nome'), $dados);
foreach ($resultado as $item) {
    list($nome, $H, $total) = $item;
    echo "{$nome}, H={$H}, H+d={$total}<br>";
}

?>
</body>
</html>