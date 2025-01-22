<?php
    include("inc/functions.php");
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
<form>
<h5 class="text-center mt-3">CADASTRAR TAREFA</h5>
<div class="card mx-auto mt-2" style="width: 30%;">
    <div class="card-body mx-auto">

 
        <label>Oque precisa Lembrar de fazer?</label><br>
        <input name="cpTarefa"><br>
        <label>Prioridade</label><br>
        <input name="cpPrioridade"><br>
        <input type="submit" value="Cadastrar">
        <input type="reset" value="Limpar"><br>
        <a href="gerenciadorDeTarefas.php">Voltar</a>

    
    </div>
</div>
</form>
</body>
</html>


<?php
    if(!empty($_GET)){
        echo cadastrar($_GET['cpTarefa'],$_GET['cpPrioridade']);
    }
?>