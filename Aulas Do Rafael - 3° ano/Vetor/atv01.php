<?php

    $a = array(12, 567, 34, 213, 2, 77);
    $b = array();
    $c = array();
    $flagB = 0;
    $flagC = 0;

    for($i = 0; $i < count($a); $i++){
        
        if($a[$i]%2 == 0){
            $b[$flagB] = $a[$i];
            $flagB++;
        }

        if($a[$i]%2 != 0){
            $c[$flagC] = $a[$i];
            $flagC++;
        }
    }

    echo '<br>';
    echo '<pre>';
    print_r($a);
    echo '<pre/>';

    echo '<br>';
    echo '<pre>';
    print_r($b);
    echo '<pre/>';

    echo '<br>';
    echo '<pre>';
    print_r($c);
    echo '<pre/>';



?>