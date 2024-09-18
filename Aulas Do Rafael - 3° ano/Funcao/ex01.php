<?php

    $frase = 'Você vai cagar nas calças dia 13/09/2034!';
    $palavra = explode(' ', $frase); // string para vetor

    foreach ($palavra as $palavras){
        echo  '<br>'.$palavras;
    };

    $palavra[2] = 'peidar';
    foreach ($palavra as $palavras){
        echo  '<br>'.$palavras;
    };


    $montarFrase = implode(' ', $palavra); // vetor para string

?>