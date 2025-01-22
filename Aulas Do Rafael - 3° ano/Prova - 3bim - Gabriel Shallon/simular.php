<?php
    include 'inc/funcoes.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Alertas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-secondary">

<form action="">
<h5 class="text-center mt-3 text-light ">Sistema Alertas</h5>
<div class="card mx-auto mt-3 border border-black border-3" style="width: 30%;" >
  <div class="card-body">
  
    <table class="table table-borderless">

        <tbody class="mt-4" >
            <tr>
                <td><input name="botao" type="submit" class="btn btn-success mt-1" style="width: 100%;" value="Sala"></td>
                <td><input name="botao" type="submit" class="btn btn-success mt-1" style="width: 100%;" value="Garagem"></td>
            </tr>
            <tr>
                <td><input name="botao" type="submit" class="btn btn-success mt-1" style="width: 100%;" value="Quarto"></td>
                <td><input name="botao" type="submit" class="btn btn-success mt-1" style="width: 100%;" value="Cozinha"></td>
            </tr>
        </tbody>
    </table>
    <a href="index.php">Voltar</a>
   </div>
</div>
</form>
</body>


<?php

    if(!empty($_GET)){
        cadastro($_GET['botao']);
    }

?>