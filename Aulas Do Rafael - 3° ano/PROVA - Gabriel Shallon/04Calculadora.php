<?php

    $int1 = 1500;
    $symbol = '*';
    $int2 = 5;

    

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