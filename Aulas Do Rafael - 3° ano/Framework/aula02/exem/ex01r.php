<?php

    $medida1=$_GET['cpMedida1'];
    $medida2=$_GET['cpMedida2'];
    $resultado = (sqrt(($medida1**2)+($medida2**2)))*100;
    //recebe os valores enviado pela outra páginas (*100 ali pq é em centimetros)

?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprimento de fio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-info">
<form>
<div class="card w-75 mx-auto mt-3">
  <div class="card-body">
  <h5 class="card-title text-center">COMPRIMENTO DO FIO</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Medida da parede 01 (m)</th>
                <th>Medida da parede 02 (m)</th>
                <th>Comprimento de fio a ser comprado(cm)</th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo $medida1 ?></td>
            <td><?php echo $medida2 ?></td>
            <td><?php echo round($resultado, 2) ?></td>
            <!-- Os valores recebidos foram usados aqui -->
        </tbody>
    </table>

    <div class="row">
        <div class="col-md12 m3">
            <a href="ex01.php" class="btn btn-link">Voltar</a>
        </div>
    </div>
  </div>
</div>
    
</body>
</html>