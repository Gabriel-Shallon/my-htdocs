<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabuada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>
<body class="bg-info">
    <form action="ex02r.php">
        <div class="card mx-auto mt-5 w-75">
            <div class="card-body">
                <label>Informe um número</label>
                <br>
                <input name="cpNum" type="text">
                <br>
                <input type="submit" value="calcular">
                <input type="reset" value="limpar">
            </div>
            <div class="row">
            <div class="col-md12 m3">
                <a href="index.php" class="btn btn-link">Voltar</a>
            </div>
        </div>
        </div>
    </form>

    
</body>
</html>


<?php

    if (!empty($_GET)){ 

        $num=$_GET['cpNum'];
        header('Location:ex2r.php?num='.$num);

    }

?>