<?php

    $pessoas = array();
    $pessoas = array(
        'Fulano' => 20,
        'Beltrano' => 14,
        'Capistrano' => 40
    ); // dessa forma, nos nomeamos as coordenadas do array. Invés de ser posição [0],[1] e [2], demos os nomes 'Capistrano', 'Beltrano' e 'Fulano'
    echo $pessoas['Beltrano'];

    echo '<br>';
    asort($pessoas); //pra vetor com rotulos como o do exemplo, precisamos usar asort para ordenar os valores de forma crescente
    echo '<pre>';
    print_r($pessoas);
    echo '<pre/>';

    echo '<br>';
    arsort($pessoas); //pra vetor com rotulos como o do exemplo, precisamos usar arsort para ordenar os valores de forma decrescente
    echo '<pre>';
    print_r($pessoas);
    echo '<pre/>';

    echo '<br>';
    ksort($pessoas); //pra vetor com rotulos como o do exemplo, precisamos usar ksort para ordenar os rotulos de forma crescente
    echo '<pre>';
    print_r($pessoas);
    echo '<pre/>';

    echo '<br>';
    krsort($pessoas); //pra vetor com rotulos como o do exemplo, precisamos usar krsort para ordenar os rotulos de forma decrescente
    echo '<pre>';
    print_r($pessoas);
    echo '<pre/>';

?>