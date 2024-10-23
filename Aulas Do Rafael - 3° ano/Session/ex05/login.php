<?php

    session_start();
    $cont = empty($_SESSION)? $_SESSION['contador']=3 : $_SESSION['contador'];
    //verifica se há uma varariavel chamada contador na session. Caso não, cria uma chamada de contador
    //se existe, ele reescreve o valor de cont com o da variavel contador

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Login</title>
</head>
<body>
    <form method="post">
        <label>Login</label>
        <input name="login">
        <br>
        <label>Senha</label>
        <input type="password" name="senha">
        <br>
        <h4>Número de tentativas: <?php echo $cont ?></h4>
        <input type="submit" value="Entrar"><br>

    </form>
</body>
</html>

<?php

    if (!empty($_POST)){

        $login=$_POST['login'];
        $senha=$_POST['senha'];

        if ($login=='adm' && $senha=='adm' && $cont>0)
            header('Location:logado.php');
            else{
                if ($cont>0){
                    $cont--;
                    $_SESSION['contador'] = $cont;
                    header('Location:error.php');
                }else
                    header('Location:block.php');
                
            }
    }

?>