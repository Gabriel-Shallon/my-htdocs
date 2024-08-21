<?php

    $int1 = 150;
    $symbol = '*';
    $int2 = 50;

    

    switch ($symbol){
        case '-':
            echo 'Subtração = '.$int1-$int2;
            break;

        case '+':
            echo 'Adição = '.$int1+$int2;
            break;

        case '/':
            echo 'Divisão = '.$int1/$int2;
            break;

        case '*':
            echo 'Multiplicação = '.$int1*$int2;
            break;

        default:
            echo 'Simbolo de operação inválido.';

        }
?>