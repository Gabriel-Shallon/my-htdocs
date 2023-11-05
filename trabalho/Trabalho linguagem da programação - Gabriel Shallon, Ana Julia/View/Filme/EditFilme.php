<?php
require_once '../../Controladores/filme.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="../../css/View.css">
</head>
<body>
		<form method="POST">
        <?php if (isset($filmes2)) { ?>
        <?php foreach($filmes2 as $filme){ ?>			
            <div class="table-wrapper">
                <div class="table-title">
                    <h2>Detalhes</h2>
                </div>
                <div class="table-content">
                    <label>Título:</label>
                    <div class="input-box">
                        <input type="text" class="form-control" name="titulo" required> 
                    </div>
                </div>
                <div class="table-content">
                    <label>Descrição:</label>
                    <div class="input-box">
                        <textarea class="form-control" name="descricao" required></textarea> 
                    </div>
                </div>	
                <div class="table-content">
                    <label>Ano de lançamento:</label>
                    <div class="input-box">
                        <input type="text" class="form-control" name="ano_de_lancamento" required>
                    </div>
                </div>
                <div class="table-content">
                    <label>Classificação:</label>
                    <div class="input-box">
                        <select class="form-control" name="classificacao" required>
                            <option value="">Classificações</option>
                            <option value="G">G</option>
                            <option value="PG">PG</option>
                            <option value="PG-13">PG-13</option>
                            <option value="R">R</option>
                            <option value="NC-17">NC-17</option>
                         </select>
                    </div>
                </div>
                <a href="ListaF.php" class="btn-back">Voltar</a>	
                <input type="submit" class="btn btn-primary" value="Salvar" name="submit">
                <input type="hidden" name="filme_id" value="<?php echo $filme_id; ?>">
            </div>
        <?php } ?>
    <?php } ?>
		</form>
</body>
</html>