<?php 
require_once '../../Controladores/ator.php';
require_once 'AddAtor.php';
require_once 'EditAtor.php';
require_once 'ViewA.php';
require_once 'DellAtor.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Atores</title>
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
						<h2>Atores</h2>
					</div>
					<div class="col-sm-6">
						<a>
						<button href="#AddAtor"class="btn btn-success" data-toggle="modal">Adiciona novo Ator</button>
						</a>	
						<a href="../Menu.html"><button class="btn btn-success" type="button">Voltar para o Menu</button></a>					
					</div>
				</div>
			</div>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Id</th>
						<th>Nome dos Atores</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
						<?php
                            foreach ($atores as $ator) {
                            echo '<tr>';
							echo '<td>' . $ator['ator_id'] . '</td>';
                            echo '<td>' . $ator['primeiro_nome'] ." ". $ator['ultimo_nome'] . '</td>';
							echo '<td style="white-space: nowrap;">';
                            echo '<div style="display: inline-block;">';
                            echo '<a href="ViewA.php?ator_id='. $ator['ator_id'] .'" class="view"><i class="material-icons" data-toggle="tooltip" title="View"></i></a>';
							echo '<a href="EditAtor.php?ator_id='. $ator['ator_id'] .'" class="edit"><i class="material-icons" data-toggle="tooltip" title="Edit"></i></a>';
                            echo '<a href="#DellAtor" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete"></i></a>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                            }
                        ?>
				</tbody>
			</table>
		</div>
	</div>        
</div>
</body>
</html>