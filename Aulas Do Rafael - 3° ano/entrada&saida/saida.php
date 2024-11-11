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

        <h3>Sistema de entrada/saída</h3>
        <table border="1">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Data/Hora</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                
                <?php
                    if(!empty($_GET)){
                ?>

                <tr>
                <td colspan="4">Nenhum Registro.</td>
                </tr>

                <?php
                    }//if(empty($_GET))

                    else{
                        foreach (buscaRegistro() as $dados){
                ?>

                <tr>
                    <td><?php echo $dados['nome'] ?></td>
                    <td><?php echo $dados['cpf'] ?></td>
                    <td><?php echo $dados['data'] ?></td>
                    <td><a href="index.php?acao=remove&cpf='.$dados['cpf']">saída</a></td>
                </tr>

                <?php 
                        }//foreach
                }//if(!empty($_GET)) 
                ?>

            </tbody>
        </table>
        <a href="index.php">Voltar</a>

</body>
</html>