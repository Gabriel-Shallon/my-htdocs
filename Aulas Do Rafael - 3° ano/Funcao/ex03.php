<?php

    function aliens(){
        return 567341234;
    }
    echo 'A população de aliens em andromeda é de: '.(aliens()*456);

    function somaMuitosAliens(...$ets){
        $flag = 0;
        for($i = 0; $i < count($ets); $i++){
            $flag += $ets[$i];
        }
        return $flag;
    }

    $AliensUniversoObservavel = somaMuitosAliens(aliens(), 457845678763);
    echo '<br>'.$AliensUniversoObservavel;


    function somaMuitosAliens2(...$ets){
        return array_sum($ets);
    }

    $aliensDaLua = somaMuitosAliens2(aliens(), 2);
    echo '<br>'.$aliensDaLua;

?>