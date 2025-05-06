<?php
    include 'inc/funcoes.php';

    if (!empty($_GET)){
        if ($_GET['acao']=='Adicionar'){
            header('Location:addcart.php');
        }//if botão adicionar
        if ($_GET['acao']=='Cancelar'){
            session_destroy();
            header('Location:index.php');
        }
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
        <div class="card w-75 m-auto mt-2">
        <img src="img/6.png" class="card-img-top m-auto" style="width: 15%">
            <div class="card-body">
                <div class="row">
                        <div class="col-md-4">
                            <label>Cliente</label>
                            <input name="cpCliente" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>CPF</label>
                            <input type="number" name="cpCPF" class="form-control">
                        </div>
                        <div class="col-md-4">
                        <input type="submit" name="acao" class="btn btn-primary w-100 mt-4 mb-4" value="Adicionar">
                        </div>

                        <table class="table mt-2">
                        <thead>
                            <tr class="table-dark">
                                <th>ID</th>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Qtd</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5">Nenhum produto.</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="col-md-12 text-end text-danger mb-4">
                        <label class="fs-4">Total R$ 0,00</label>
                    </div>
                        <div class="col-md-8">
                            <a href="index.php">Sair</a>
                        </div>
                        <div class="col-md-2">
                            <input type="submit" name="acao" class="btn btn-success w-100" value="Concluir">
                        </div>
                        <div class="col-md-2">
                            <input type="submit" name="acao" class="btn btn-danger w-100" value="Cancelar">
                        </div>
                        <div class="col-md-12 text-end mb-2 mt-4">
                            <label name="cpVendedor" class="fw-bold">Vendedor: <?php echo $_SESSION['us'];?></label>
                        </div>


                </div>





            </div>
        </div>
    </form>

        <footer class="text-white bg-dark w-100 text-center" style="position: fixed; bottom: 0; left: 0">
            Tecno Vendas 2024 - Mantido e desenvolvido por Juan Anástacio da Silva
        </footer>    
</body>
</html>