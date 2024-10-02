<?php
function criarvetor($n1,$n2,$n3,$n4,$n5){
    $vetor = array($n1,$n2,$n3,$n4,$n5);
    return $vetor;
}


function mostrarVetor($vetor){
    foreach($vetor as $num){
        echo $num. ", ";
    }
    echo "<br>";
}

function SomarSubtrair($vetor){
    foreach($vetor as $pos => $valor){
        
        if ($valor%2 == 0){
            $vetor[$pos]+=10;
        }
        if ($valor%2 == 1){
            $vetor[$pos]-=5;
        }
    }
    return $vetor;
}

$vet1 = criarvetor(12,23,3,5,34);
mostrarVetor($vet1);
$vet2 = SomarSubtrair($vet1);
mostrarVetor($vet2);