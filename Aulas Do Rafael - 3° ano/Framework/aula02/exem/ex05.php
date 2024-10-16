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
    <h5 class="card-title text-center">Jogo da ficha</h5>

    <p class="card-text" style="text-align: justify">Um jogo consiste em se retirar duas fichas de um saco
contendo fichas brancas e pretas. Dependendo da combinação de cores das fichas
retiradas, o jogador ser pago na seguinte proporção: com duas fichas brancas o jogador perde tudo, com uma branca e uma preta
recebe metade do que apostou, com um preta e uma branca recebe seu dinheiro de
volta e com duas pretas recebe o dobro.</p>
    
        
        <div class="row">
            <div class="col-md-4">
                <label for="n2" class="form">Valor a ser apostado:</label>
                <input name="cpValor" type="text" class="form-control" id="Input02">
            </div>
                <div class="col-md-2">
                <br>
                <input name="btnCalcular" type="submit" class="btn btn-primary w-100" value="apostar">
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
                <th>Primeira Ficha</th>
                <th>Segunda Ficha</th>
                <th>Rateio</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>

                <?php



                if (empty($_GET)){ ?>
                            
                            <tr>
                            <td>
                                <?php echo '---' ?>
                            </td>
        
                            <td>
                                <?php 
                                    echo '-----'; 
                                ?>
                            </td>
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
                        

                    $ficha1 = rand(0, 1);
                    $ficha2 = rand(0, 1);

                    if ($ficha1 == 1)
                        $ficha1 = 'preto';

                
                    if ($ficha1 == 0)
                        $ficha1 = 'branco';

                    if ($ficha2 == 1)
                        $ficha2 = 'preto';

                
                    if ($ficha2 == 0)
                        $ficha2 = 'branco';





                    echo $ficha1?>
                    
                </td>

                <td>
                    <?php echo $ficha2 ?>
                </td>
                
                <td>
                    <?php 
                    $multiplicador = 0;
                        if($ficha1 == 'branco' && $ficha2 == 'branco')
                            echo '0x';
                        if($ficha1 == 'branco' && $ficha2 == 'preto'){
                            echo '1/2x'; 
                            $multiplicador = 0.5;
                        }
                        if($ficha2 == 'branco' && $ficha1 == 'preto'){
                            echo '1x'; 
                            $multiplicador = 1;
                        }
                        if($ficha1 == 'preto' && $ficha2 == 'preto'){
                            echo '2x'; 
                            $multiplicador = 2;
                        }
                     ?>
                </td>

                <td>
                    <?php 
                        $valor=$_GET['cpValor'];
                        echo $valor*$multiplicador;
                    }
                    ?>
                </td>

            </tr>

        </tbody>
    </table>
