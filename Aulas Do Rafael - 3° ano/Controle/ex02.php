<?php
    $suaidade = 29;
    $crianca = 12;
    $adulto = 18;
    $idoso = 65;
    
    // Aninhamento de estrutura de controle

    if ($suaidade <= $crianca)
        echo "Criança";

    if ($suaidade < $adulto)
        echo "Adolecente";

    if ($suaidade >= $adulto)
        echo 'Adulto';

    if ($suaidade > $idoso)
        echo 'Idoso';

?>