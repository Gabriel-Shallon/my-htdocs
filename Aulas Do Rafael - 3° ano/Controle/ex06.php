<?php
    for ($i = 0; $i<11; $i++)
    echo $i.'<br>';

    echo '<br> Usando WHILE<br>';
    $a = 0;
    while ($a<11){
        echo $a.'<br>';
        $a++;
    }
    echo '<br>Repetição de 0 a 1000 de 5 em 5';

    for ($i = 0; $i <= 1000; $i+=5){
        echo $i.', ';
    }

    for ($i = 1000; $i >= 0; $i-=3){
        echo $i.', ';
    }
?>