<?php

    $estados = ['MT', 'MS', 'SP'];
    array_push($estados, 'PR'); //inserir elemento no final do array

    echo '<br>';
    echo '<pre>';
    print_r($estados);
    echo '<pre/>';

    array_unshift($estados, 'TC'); //inserir elemento no inicio do array

    echo '<br>';
    echo '<pre>';
    print_r($estados);
    echo '<pre/>';

    array_shift($estados); //remove o elemento na primeira posição

    echo '<br>';
    echo '<pre>';
    print_r($estados);
    echo '<pre/>';

    array_pop($estados); //remove o elemento na ultima posição

    echo '<br>';
    echo '<pre>';
    print_r($estados);
    echo '<pre/>';

    $par = [2, 4, 6];
    $impar = [1, 3, 5];

    $numeros = array_merge($impar, $par); //junta os arrays num só

    echo '<br>';
    echo '<pre>';
    print_r($numeros);
    echo '<pre/>';

?>