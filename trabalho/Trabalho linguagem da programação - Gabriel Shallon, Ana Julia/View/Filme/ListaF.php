<?php 
require_once '../../Controladores/filme.php';
require_once 'AddFilme.php';
require_once 'EditFilme.php';
require_once 'ViewF.php';
require_once 'DellFilme.php'; 

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Lista de Filmes</title>
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
						<h2>Filmes</h2>
					</div>
					<div class="col-sm-6">
					<nav class="navbar navbar-light bg-#435d7d">
    					<form class="form-inline ml-auto">
        					<input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
    					</form>
					</nav>
						<a>
						<button href="#AddFilme"class="btn btn-success" data-toggle="modal">Adiciona novo Filme</button>
						</a>			
					</div>
				</div>
			</div>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Id</th>
						<th>Nome dos Filmes</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                        foreach ($filmes as $filme) {
                            echo '<tr>';
                            echo '<td>' . $filme['filme_id'] . '</td>';
                            echo '<td>' . $filme['titulo'] . '</td>';
                            echo '<td style="white-space: nowrap;">';
                            echo '<div style="display: inline-block;">';
                            echo '<a href="ViewF.php?filme_id='. $filme['filme_id'] .'" class="view"><i class="material-icons" data-toggle="tooltip" title="View"></i></a>';
                            echo '<a href="EditFilme.php?filme_id='. $filme['filme_id'] .'" class="edit"><i class="material-icons" data-toggle="tooltip" title="Edit"></i></a>';
                            echo '<a href="#DellFilme" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete"></i></a>';
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
<script>
$(document).ready(function(){
  $(".form-control").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
</body>
</html>
