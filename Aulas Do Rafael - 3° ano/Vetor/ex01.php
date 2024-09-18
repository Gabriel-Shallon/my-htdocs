<?php

    $frutas = array(['Carambola', 'Banana', 'Melão'], ['Maça', 'Morango', 'lanc']);
    var_dump($frutas);
    echo '<br>';
    print_r($frutas);
    echo '<br>';
    echo end ($frutas[0]);  //lê o vetor ao contrário ([0] == ultima posição)

    $Explosion = array('boom', 'kabum', 'cleber');
    echo '<br>';

    sort($Explosion); //deixa os valores do vetor em ordem crescente (alfabética ou numérica)
    echo '<pre/>';
    print_r($Explosion);
    

    $Nuremos = array(2, 3, 1);
    sort($Nuremos); //deixa os valores do vetor em ordem crescente (alfabética ou numérica)
    echo '<pre/>';
    print_r($Nuremos);

    
    rsort($Nuremos); //deixa os valores do vetor em ordem decrescente (alfabética ou numérica)
    echo '<pre/>';
    print_r($Nuremos);
?>