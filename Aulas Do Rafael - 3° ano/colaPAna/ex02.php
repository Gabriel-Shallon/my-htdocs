<?php


function Guerra($inimigos, $aliados){
    echo $inimigos-$aliados.", ";
    if ($inimigos > $aliados){
        echo "Deu ruim! Fuga!";
    }
    if ($inimigos == $aliados){
        echo "Vai na sorte!";
    }
    if ($inimigos < $aliados){
        echo "Deu bom! Se joga!";
    }
}


$inimigos = 2000;
$aliados = 3;

Guerra($inimigos, $aliados);