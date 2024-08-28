<?php

    $atual = new Datetime();
    echo $atual->format('H:i:s - d/m/Y');

    $entrega = new DateInterval('P2Y15DT2H30M'); //+2 anos 15 dias 2 horas e 30 minutos
    $atual ->add($entrega);
    echo '<br>';
    echo $atual->format('H:i:s - d/m/Y');

?>