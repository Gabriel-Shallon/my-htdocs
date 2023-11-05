<?php
require_once 'ListAtor.php';
require_once '../../Database/Conexao.class.php';
require_once '../../Controladores/ator.php'; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deletar</title>
</head>
<body>
<div id="DellAtor" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method='POST'>
				<div class="modal-header">						
					<h4 class="modal-title">Deletar um Ator</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">					
					<p>Você tem certeza que deseja deletar essa Filme?</p>
					<p class="text-warning"><small>Essa ação não pode ser desfeits</small></p>
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-danger" value="Delete" name="submit">
				</div>
                <input type="hidden" name="ator_id" value="<?php echo $ator['ator_id']; ?>">
			</form>
		</div>
	</div>
</div>
</body>
</html>