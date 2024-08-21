<?php
$condicao = true;
    $cont = 0;
    while ($condicao){
    $num = rand(0,100000000);
        if ($num == 727){
            echo 'VOCÊ GANHOU! WYSI';
            $condicao = false;
        }
        $cont++;
        //echo $num.', '; 
    }
    echo ' quantidade de sorteio: '.$cont;
?>