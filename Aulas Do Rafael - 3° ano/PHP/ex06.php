<?php
    $frase = 'Luke, eu sou seu pai!';
    $palavra = 'eu';
    $posicao = strpos($frase,$palavra); //posição da string "palavra" ( = eu ) na string "frase" ( = Luke, eu sou seu pai! )
    echo 'posição na frase: '.$posicao;
    $vetor = explode(' ', $frase); //separar cada palavra em um vetor de acordo com critério
    // separa a string a cada espaço, e coloca as partes dentro do vetor
    var_dump($vetor);
    $senha = 'rafael123';
    $ultimosCaracteres = substr($senha, -3);
    echo '<br> Ultimos 3 caracteres: '.$ultimosCaracteres;
?>