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
<div class="card mx-auto mt-5 w-50">
  <div class="card-body">


  <table class="table table-bordered table-info w-50 mx-auto mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tarefa</th>
                <th>Data</th>
                <th>Prioridade</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
   
                    <?php
                    if(empty($_SESSION['tarefas'])){
                       echo '<tr><td colspan="5">Não há tarefas</td></tr>';
                    }else{
                        foreach (listar() as $tarefa) {
                    ?>

                            <tr>
                            <td>
                               <?php echo $tarefa['id'];?>
                            </td>
        
                            <td>
                                <?php echo $tarefa['fazer'];?>
                            </td>

                            <td>
                                <?php echo $tarefa['data'];?>
                            </td>
        
                            <td>
                                <?php echo $tarefa['prioridade'];?>
                            </td>

                            <td>
                                <?php echo '<a href="gerenciadorDeTarefas.php?acao=remover&id='.$tarefa['id'].'">Resolvido</a>'?>
                            </td>
                        </tr>          
                    <?php
                        }
                    }
                    ?>

        </tbody>
    </table>

    <a href="gerenciadorDeTarefas.php?acao=limpar">Limpar</a><br>
    <a href="gerenciadorDeTarefas.php">Voltar</a>
 </div>
 
</div>


</form>
</body>
</html>


