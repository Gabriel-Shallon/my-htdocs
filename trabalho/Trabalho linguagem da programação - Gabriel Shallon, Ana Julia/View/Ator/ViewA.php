<?php require_once '../../Controladores/ator.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detalhes</title>
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
        <?php if (isset($atore2)) { 
            foreach($atore2 as $ator){ ?>			
            <div class="table-wrapper">
                <div class="table-title">
                    <h2>Detalhes</h2>
                </div>
                <div class="table-content">
                    <label>Primeiro Nome</label>
                    <div class="input-box">
                        <span><?php echo $ator['primeiro_nome']; ?></span> 
                    </div>
                </div>
                <div class="table-content">
                    <label>Ultimo Nome:</label>
                    <div class="input-box">
                        <span><?php echo $ator['ultimo_nome']; ?></span> 
                    </div>
                </div>	
                <a href="ListAtor.php" class="btn-back">Voltar</a>	
            </div>
        <?php } 
        } ?>
</body>
</html>