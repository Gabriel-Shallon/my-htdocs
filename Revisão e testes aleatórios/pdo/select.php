<?php
   include 'Conexao.class.php';
   include 'Ocorrencia.class.php';


   $conexao = new Conexao();
   $ocorrencias = $conexao->selectAll();
   
   if(empty($conexao->selectAll()))
      echo "Vazio";
   else{
      foreach ($ocorrencias as $row) {
         echo '<strong>' . ucfirst($row['tipo']) .
               ':</strong> ' . $row['descricao'] . '<br />';
      }
   }
?>