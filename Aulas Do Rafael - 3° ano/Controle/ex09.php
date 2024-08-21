<?php
    $meses = array('Janeiro', 'Fevereiro', 'Março');

    foreach ($meses as $pos => $mes){
        echo '<br>posicao['.$pos.']: ';
        echo 'O mês é '.$mes;
    }

    echo '<br>';

    foreach ($meses as $mes)
        echo 'O mês é '.$mes.'<br>';