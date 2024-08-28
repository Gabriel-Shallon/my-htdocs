<?php

    $respostas = array(
        'nome'=> 'Rafael Garcia',
        'idade'=> 29,
        'gostou'=>2
    );
    
    echo '<br>';
    echo '<pre>';
    print_r($respostas);
    echo '<pre/>';

    $vetor = array(
        'nome'=> 'Ana',
        'idade'=> 18,
        'gostou'=>5
    );

    echo '<br>';
    echo '<pre>';
    print_r($vetor);
    echo '<pre/>';

    array_push($respostas,$vetor); //insere um novo registro no vetor

    echo '<br>';
    echo '<pre>';
    print_r($respostas);
    echo '<pre/>';

?>