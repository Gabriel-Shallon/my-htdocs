<?php 
require_once '../../Controladores/categoria.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adicionar Categoria</title>
</head>
<body>
<div id="AddC" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method='POST'>
				<div class="modal-header">						
				<h4 class="modal-title">Adicionar uma nova Categoria</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">					
					<div class="form-group">
						<label>ID</label>
						<input type="text" class="form-control" name="id" required>
					</div>
					<div class="form-group">
						<label>Nome</label>
						<input type="text" class="form-control" name="nome" required>
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