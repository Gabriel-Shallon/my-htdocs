<?php

    
    $num=$_GET['cpNum'];


?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabuada Resultado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-info">
<div class="card w-50 mx-auto mt-3">
  <div class="card-body">
  <h5 class="card-title text-center">Tabuada</h5>
    <table class="table table-bordered table-info">
        <thead>
            <tr>
                <th>Cálculo</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>

           <?php 
            for($i = 0; $i<=10; $i++){   
           ?>
                <tr>
                    <td><?php echo $num." x ".$i ?></td>
                    <td><?php echo $num*$i ?></td>
                </tr>
           <?php 
            }
           ?>

           <!-- Ou -->

           <?php 

                // for($i = 0; $i<=10; $i++){  
                //     echo "<tr>
                //         <td>".$num." x ".$i."</td>
                //         <td>".$num*$i."</td>
                //     </tr>";
                // }

                // O resultado é idêntico, apenas muda a forma.

           ?>


        </tbody>
    </table>
    <div class="row">
        <div class="col-md12 m3">
            <a href="ex02.php" class="btn btn-link">Voltar</a>
        </div>
    </div>
  </div>
</div>
</body>
</html>