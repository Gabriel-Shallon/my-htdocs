<?php 
require_once '../../Controladores/ator.php';
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
        <?php if (isset($atores2)) { ?>
        <?php foreach($atores2 as $ator){ ?>			
            <div class="table-wrapper">
                <div class="table-title">
                    <h2>Detalhes</h2>
                </div>
                <div class="table-content">
                    <label>Primeiro Nome</label>
                    <div class="input-box">
                        <input type="text" class="form-control" name="primeiro_nome" required> 
                    </div>
                </div>
                <div class="table-content">
                    <label>Ultimo Nome</label>
                    <div class="input-box">
                    <input type="text" class="form-control" name="ultimo_nome" required> 
                    </div>
                </div>	
                <a href="ListAtor.php" class="btn-back">Voltar</a>	
                <input type="submit" class="btn btn-primary" value="Salvar" name="submit">
                <input type="hidden" name="ator_id" value="<?php echo $ator_id; ?>">
            </div>
        <?php } ?>
    <?php } ?>
		</form>
</body>
</html>