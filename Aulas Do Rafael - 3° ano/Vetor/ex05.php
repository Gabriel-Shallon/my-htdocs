<?php
    $a = array(12, 567, 34, 213, 2, 77);

    $flag = 0;
    while($flag <count($a)){
        echo $a[$flag];
        echo '<br>';
        $flag++;
    }
    echo '<br>';

    foreach($a as $dado){
        echo $dado.', <br>';
    }
    echo '<br>';

    foreach($a as $pos => $dado){
        echo 'posicao['.$pos.'] = '.$dado.'<br>';
    }

?>