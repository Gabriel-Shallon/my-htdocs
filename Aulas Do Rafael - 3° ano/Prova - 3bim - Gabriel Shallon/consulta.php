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

<h5 class="text-center mt-3 text-light ">Sistema Alertas</h5>
<div class="card mx-auto mt-3 border border-black border-3" style="width: 30%;" >
  <div class="card-body">
  
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="table-primary">
                    <h7>Simular sensores</h7>
                </th>
                <th class="table-primary">
                    <h7>Consultar notificações</h7>
                </th>   
            </tr>
        </thead>
        <tbody>
    <?php
        if(empty($_SESSION)){
            
    ?>

            <tr>
                <td colspan="4"><h7>Nenhum Registro.</h7></td>
            </tr>

    <?php
        }//if(empty($_GET))
        else{
            foreach (recuperarAlertas() as $alerta){
    ?>

            <tr>
                <td><?php echo $alerta['setor'] ?></td>
                <td><?php echo $alerta['tempo'] ?></td> 
            </tr>

    <?php 
            }//foreach
        }//if(!empty($_GET)) 
    
    ?>

        </tbody>
    </table>
    <a href="index.php">Voltar</a>
   </div>
</div>
</body>