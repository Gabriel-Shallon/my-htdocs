<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprimento de fio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-info">
<form>
<div class="card mx-auto mt-5 w-50">
  <div class="card-body">
    <h5 class="card-title text-center">Divisão Tratada</h5>

    <p class="card-text" style="text-align: justify">Escreva os números:</p>
    
        
        <div class="row">
            <div class="col-md-4">
                <label for="n1" class="form">Dividendo:</label>
                <input name="cpNum1" type="text" class="form-control" id="Input01">
            </div>
            <div class="col-md-4">
                <label for="n2" class="form">Divisor:</label>
                <input name="cpNum2" type="text" class="form-control" id="Input02">
            </div>
                <div class="col-md-2">
                <br>
                <input name="btnCalcular" type="submit" class="btn btn-primary w-100" value="analisar">
            </div>
            <div class="col-md-2">
                <br>
                <input name="btnLimpar" type="reset" class="btn btn-danger w-100" value="limpar">
        </div>
        </div>
      
        <div class="row">
            <div class="col-md12 m3">
                <a href="index.php" class="btn btn-link">Voltar</a>
            </div>
        </div>
        
  </div>
</div>
</form>
</body>
</html>


<table class="table table-bordered table-info w-50 mx-auto mt-4">
        <thead>
            <tr>
                <th>Cálculo</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>

                <?php if (empty($_GET)){ ?>
                            
                            <tr>
                            <td>
                                <?php echo '---' ?>
                            </td>
        
                            <td>
                                <?php 
                                    echo '-----'; 
                                    }else{
                                ?>
                            </td>
                        </tr>          
                    <tr>
                        
                    <td><?php
                        
                    $num1=$_GET['cpNum1'];
                    $num2=$_GET['cpNum2'];

                    echo $num1.' / '.$num2 ?></td>

                    <td>
                        <?php 
                    
                    if($num2==0)
                        echo 'O divisor não pode ser 0!!!'; 

                    if($num2!=0)
                        echo $num1/$num2; 

                    }
                    
                    ?>
                </tr>

        </tbody>
    </table>
