<?php
require_once '../../Database/Conexao.class.php';
require_once '../../Controladores/Ator.class.php';

$conexao = new Conexao();
$conn = $conexao->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit']) && $_POST['submit'] === 'Inserir') {
        $ator_id = $_POST['ator_id'];
        $primeiro_nome = $_POST['primeiro_nome'];
        $ultimo_nome = $_POST['ultimo_nome'];
        $ator = new ator($ator_id, $primeiro_nome, $ultimo_nome);
        $conexao->insertA($ator);
    }elseif(isset($_POST['submit']) && $_POST['submit'] === 'Salva'){
		  $ator_id = $_POST["ator_id"];
      $primeiro_nome = $_POST['primeiro_nome'];
      $ultimo_nome = $_POST['ultimo_nome'];
		  $ator= new ator($ator_id,$primeiro_nome,$ultimo_nome);
      $conexao->updateA($ator_id,$primeiro_nome,$ultimo_nome);
	}
  }
  
	if (isset($_POST['submit']) && $_POST['submit'] === 'Delete') {
		$ator_id = $_POST['ator_id'];
		$conexao->deleteA($ator_id);
	}
  if (isset($_GET['ator_id'])) {
    $ator_id = $_GET['ator_id'];
    $atores2 = $conexao->selectAllA2($ator_id);
  }
$atores = $conexao->selectAllA('ator');
?>