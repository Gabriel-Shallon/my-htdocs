<?php
// insert.php
   include 'Conexao.class.php';
   include 'Ocorrencia.class.php';

      $tipo = $_POST['tipo'];
      $descricao = $_POST['descricao'];

         $ocorrencia = new Ocorrencia($tipo, $descricao);

         $conexao = new Conexao();

         $conexao->insert($ocorrencia);

   header('Location: form.html');
?>
