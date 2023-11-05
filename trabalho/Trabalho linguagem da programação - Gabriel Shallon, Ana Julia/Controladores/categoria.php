<?php 
require_once '../../Database/Conexao.class.php';
require_once 'Categoria.class.php';
require_once '../../View/Categoria/AddC.php';

$conexao = new Conexao();
$conn = $conexao->getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit']) && $_POST['submit'] === 'Inserir') {
      $categoria_id = $_POST['id'];
      $nome = $_POST['nome'];
      $categoria = new categoria($categoria_id, $nome);
      $conexao->insertC($categoria);
    }
  }

  
  if (isset($_POST['submit']) && $_POST['submit'] === 'Delete') {
    $categoria_id = $_POST['categoria_id'];
    $conexao->deleteC($categoria_id);
  }
  if (isset($_GET['categoria_id'])) {
    $categoria_id = $_GET['categoria_id'];
    $categorias2 = $conexao->selectAllC2($categoria_id);
  }

$categorias = $conexao->selectAllC('categoria');
?>