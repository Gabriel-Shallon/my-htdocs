<?php
   include 'Conexao.class.php';
   include 'ehgay.class.php';


   $conexao = new Conexao();
   $ocorrencias = $conexao->selectAll();
   
   if(empty($conexao->selectAll()))
      echo "Vazio";
   else{
      foreach ($ehgay as $row) {
         echo '<strong>' . ucfirst($row['resultado']) .
               ':</strong> ' . $row['nome'] . '<br />';
      }
   }
?>