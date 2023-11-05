<?php 
require_once '../../Controladores/filme.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adicionar Filme</title>
</head>
<body>
<div id="AddFilme" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method='POST'>
				<div class="modal-header">						
					<h4 class="modal-title">Adicionar novo Filme</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">					
					<div class="form-group">
						<label>ID</label>
						<input type="text" class="form-control" name="filme_id" required>
					</div>
					<div class="form-group">
						<label>Titulo</label>
						<input type="text" class="form-control" name="titulo" required>
					</div>
					<div class="form-group">
						<label>Descrição</label>
						<textarea class="form-control" name="descricao" required></textarea>
					</div>	
					<div class="form-group">
						<label>Ano do lancamento</label>
						<input type="text" class="form-control" name="ano_de_lancamento" required>
					</div> 
					<div class="form-group">
						<label>Classificação</label>
						<input type="text" class="form-control" name="classificacao"required>
					</div>			
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-success" value="Inserir" name="submit">
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>