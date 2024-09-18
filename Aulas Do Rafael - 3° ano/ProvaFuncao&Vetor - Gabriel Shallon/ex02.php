<?php

    function Apostar($valorDepositado){
        $dinheiroGasto = 0;
        $dinheiroGanho = 0;
        $rodada = 0;
        while ($valorDepositado>=5){ 
            $rodada++;
            $valorDepositado-=5;
            $dinheiroGasto+=5;

            $r1 = roleta();
            $r2 = roleta();
            $r3 = roleta();

            echo "RODADA = ".$rodada."<br><br>Roleta 1: ".$r1."<br>Roleta 3: ".$r2."<br>Roleta 3: ".$r3;
            if ($r1 == $r2 && $r2 == $r3){
                $dinheiroGanho+=50;
                echo "<br>VOCÊ GANHOU >50R$< !!!!";
            }
            echo "<br>---------------------------------<br>";
            
        }
        echo "<br>---------------------------------<br>";
        echo "Dinheiro Gasto: ".$dinheiroGasto."<br>Dinheiro Ganho: ".$dinheiroGanho."<br><br>SALDO FINAL: ".$dinheiroGanho-$dinheiroGasto;

    }

    function roleta(){
        return rand(1,8);
    }

    $valorDepositado = 50;
    Apostar($valorDepositado);

?>