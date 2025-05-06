<?php
    include 'inc/funcoes.php';
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
                <div class="card w-75 m-auto mt-3">
                    <img src="img/6.png" class="card-img-top m-auto" style="width: 15%">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <label>Produto</label>
                                <input name="cpProduto" type="text" placeholder="Digite algo sobre o produto..." class="form-control">
                            </div>
                            <div class="col-md-2 mt-4">
                                <input type="submit" class="btn btn-success w-100" value="Buscar">
                            </div>
                            
                            <table class="table mt-2 mb-3">
                                <thead>
                                    <tr class="table-dark">
                                        <th>ID</th>
                                        <th>Produto</th>
                                        <th>Fabricante</th>
                                        <th>Preço</th>
                                        <th>Estoque</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $produtos = isset($_GET['cpProduto']) ? busca($_GET['cpProduto']) : '';
                                        if(empty($_GET) || empty($produtos)){
                                    ?>
                                    <tr>
                                        <td colspan="6">Nenhum produto.</td>
                                    </tr>
                                    
                                    <?php   
                                        }else{ 
                                            
                                        foreach($produtos as $produto){
                                    ?>
                                    
                                    <tr>
                                        <td><?php echo $produto['idproduto'] ?></td>
                                        <td><?php echo $produto['nomepro'] ?></td>
                                        <td><?php echo $produto['fab'] ?></td>
                                        <td><?php echo $produto['preco'] ?></td>
                                        <td><?php echo $produto['estoq'] ?></td>
                                        <td><a href="#">Selecionar</a></td>
                                    </tr>

                                    <?php }} ?>
                                    
                                </tbody>
                            </table>

                            <div class="col-md-8 ">
                                <a href="main.php">Voltar</a>
                            </div>
                            <div class="col-md-4 text-end">
                                <label class="fw-bold">Vendedor: <?php echo $_SESSION['us'];?></label>
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