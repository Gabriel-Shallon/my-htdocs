<?php 
require_once '../../Controladores/categoria.php';
require_once 'AddC.php';
require_once 'ViewC.php';
require_once 'DellCatego.php' ;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Categoria</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="../../css/Tabela.css">
</head>
<body>
<div class="container-xl">
	<div class="table-responsive">
		<div class="table-wrapper">
			<div class="table-title">
				<div class="row">
					<div class="col-sm-6">
						<h2>Categorias</h2>
					</div>
					<div class="col-sm-6">
						<button href="#AddC" class="btn btn-success" data-toggle="modal">Adiciona nova Categoria</button>	
					</div>
				</div>
			</div>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Id</th>
						<th>Nome das Categorias</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
						<?php
                            foreach ($categorias as $categoria) {
                            echo '<tr>';
							echo '<td>' . $categoria['categoria_id'] . '</td>';
                            echo '<td>' . $categoria['nome'] . '</td>';
                            echo '<td style="white-space: nowrap;">';
                            echo '<div style="display: inline-block;">';
							echo '<a href="ViewC.php?categoria_id='. $categoria['categoria_id'] .'" class="view"><i class="material-icons" data-toggle="tooltip" title="View"></i></a>';
                            echo '<a href="#DellCatego" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete"></i></a>';
							echo '</td>';
                            echo '</div>';
                            }
                        ?>
				</tbody>
			</table>
			<?php  ?>
		</div>
	</div>        
</div>
</body>
</html>