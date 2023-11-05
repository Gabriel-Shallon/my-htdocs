<?php 
require_once '../../Database/Conexao.class.php';
require_once 'Filme.class.php';

$conexao = new Conexao();
$conn = $conexao->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit']) && $_POST['submit'] === 'Inserir') {
      $filme_id = $_POST['filme_id'];
      $titulo = $_POST['titulo'];
      $descricao = $_POST['descricao'];
      $ano_de_lancamento = $_POST['ano_de_lancamento'];
      $classificacao = $_POST['classificacao'];
      $filme = new filme($filme_id, $titulo, $descricao, $ano_de_lancamento, $classificacao);
      $conexao->insertF($filme);
    }elseif(isset($_POST['submit']) && $_POST['submit'] === 'Salvar'){
		  $filme_id = $_POST["filme_id"];
      $titulo = $_POST['titulo'];
      $descricao = $_POST['descricao'];
      $ano_de_lancamento = $_POST['ano_de_lancamento'];
      $classificacao = $_POST['classificacao'];
		  $filme= new Filme($filme_id,$titulo,$descricao,$ano_de_lancamento,$classificacao);
    	$conexao->updateF($filme_id,$titulo,$descricao,$ano_de_lancamento,$classificacao);
	  }
  }
	if (isset($_POST['submit']) && $_POST['submit'] === 'Delete') {
		$filme_id = $_POST['filme_id'];
		$conexao->deleteF($filme_id);
	}
if (isset($_GET['filme_id'])) {
  $filme_id = $_GET['filme_id'];
  $filmes2 = $conexao->selectAllF2($filme_id);
}
	$filmes = $conexao->selectAllF('filme');
?>