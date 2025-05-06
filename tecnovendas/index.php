<?php
    include 'inc/funcoes.php';
    if (!empty($_GET)){
        $login = $_GET['cpLogin'];
        $senha = $_GET['cpSenha'];
        $msg = logar($login,$senha);
        echo '<div class="alert alert-danger" role="alert">';
            echo $msg;
        echo '</div>';
    }else{
        echo "Conectado.";
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Tecno Vendas</title>
</head>
<body class="bg-secondary">
    <form>
        <div class="card w-25 m-auto mt-4">
            <img src="img/6.png" class="card-img-top w-50 m-auto">
            <div card-body class="m-2">
                <div class="row">
                    <div class="col-12">
                        <label>Login</label>
                        <input name="cpLogin" class="form-control">
                        <label class="mt-3">Senha</label>
                        <input type="password" name="cpSenha" class="form-control">
                        <input type="submit" name="botao"class="btn btn-primary w-100 mt-4 mb-4" value="Entrar">
                    </div>
                    <div class="col-6">
                        <a href="newuser.php">Sou novo aqui</a>
                    </div>
                    <div class="col-6">
                        <a href="#">Esqueci a senha</a>
                    </div>
                </div>
            </div>
        </div>    
        <footer class="text-white bg-dark w-100 text-center" style="position: fixed; bottom: 0; left: 0">
            Tecno Vendas 2024 - Mantido e desenvolvido por Juan Anástacio da Silva
        </footer>
    </form>
</body>
</html>