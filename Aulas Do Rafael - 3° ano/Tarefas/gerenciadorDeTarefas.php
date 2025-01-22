<?php
    include 'inc/functions.php';
    if (!empty($_GET)){
        if($_GET['acao']=='limpar'){
            limpar();
        }
        if ($_GET['acao']=='remover'){
            remover($_GET['id']);
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-info">

<h5 class="text-center mt-3">GERENCIADOR DE TAREFAS</h5>
<div class="card mx-auto mt-3" style="width: 30%;" >
  <div class="card-body">
  
    <table class="table"  style="height: 240px;">
        <thead style="height: 130px;">
            <tr>
                <th style="text-align: center;">
                    <a href="registrarTarefa.php" >Registrar Tarefa</a>
                </th>
                <th style="text-align: center;">
                    <a href="consultarTarefas.php" class="mx-auto">Consultar Tarefa</a>
                </th>   
            </tr>
        </thead>
        <tbody>