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
            <div class="card w-75 m-auto mt-4">
                <img src="img/6.png" class="card-img-top m-auto" style="width: 15%">
                <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <label>Nome</label>
                        <input nome="cpProduto" class="form-control">
                    </div>
                    <div class="col-3">
                        <label>Tipo</label>
                        <select name="cpTipo" class="form-select">
                            <option selected>Escolha o tipo de usuário...</option>
                            <option value="Vendedor">Vendedor</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Estagiário">Estagiário</option>
                        </select>    
                    </div>
                    <div class="col-2">
                        <label>Login</label>
                        <input nome="cpLogin" class="form-control">
                    </div>
                    <div class="col-2">
                        <label>Senha</label>
                        <input nome="cpSenha" class="form-control">
                    </div>
                    <div class="col-2">
                        <input type="submit" nome="cpEntrar" class="btn btn-primary w-100 mt-4 mb-4" value="Entrar">
                    </div>
                </div>
                <div class="col-6">
                    <a href="index.php">Voltar</a>
                </div>
            </div>
        </div>
            <footer class="text-white bg-dark w-100 text-center" style="position: fixed; bottom: 0; left: 0">
                Tecno Vendas 2024 - Mantido e desenvolvido por Juan Anástacio da Silva
            </footer>
        </form>
        


</body>
</html>