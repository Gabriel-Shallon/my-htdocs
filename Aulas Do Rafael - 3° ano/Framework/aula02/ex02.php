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
<form action="./ex02r.php">
<div class="card mx-auto mt-5 w-75">
  <div class="card-body">
    <h5 class="card-title text-center">COMPRIMENTO DO FIO</h5>

    <p class="card-text" style="text-align: justify"> Um eletricista
        precisa comprar fio que ir· passar, pelo telhado,
        por toda a diagonal de uma casa de formato
        retangular. Como ele não tem condições de
        medir a diagonal com precisão (ou talvez não
        queira...), a solução alternativa que ele
        encontrou foi medir os lados da casa, sabendo
        que a diagonal pode ser calculada com base
        nos lados pelo Teorema de Pitágoras (a2 = b2 + c2
        ). Considerando que a casa mede
        11,5 x 6,3 metros, faça um programa que calcule a quantidade mínima necessária de
        fio a ser comprada, com precisão em centímetros
    </p>
    
        
        <div class="row">
            <div class="col-md-4">
                <label for="n1" class="form">Medida parede 01 (m):</label>
                <input type="number" class="form-control" id="InputParede01">
            </div>
            <div class="col-md-4">
                <label for="n2" class="form">Medida parede 02 (m):</label>
                <input type="number" class="form-control" id="InputParede02">
            </div>
                <div class="col-md-2">
                <br>
                <input type="submit" class="btn btn-primary w-100" value="calcular">
            </div>
            <div class="col-md-2">
                <br>
                <input type="reset" class="btn btn-danger w-100" value="limpar">
        </div>
        </div>
      
        <div class="row">
            <div class="col-md12 m3">
                <a href="Framework/aula01/aa.php" class="btn btn-link">Voltar</a>
            </div>
        </div>
        
  </div>
</div>
</form>
</body>
</html>