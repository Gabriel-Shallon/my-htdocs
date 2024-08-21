<?php
    // Operadores aritiméticos: - + / *
    // Operadores relacionais: > < == !=
    // Operadores lógicos: e ou não

    $a = 21.5;
    $b = 20.0;
    // O php não tem problema em comparar decimais e inteiros, diferente de outras linguagens como java

    var_dump($a>$b);
    // Devolve se o a é maior que b
    echo '<br>';
    
    var_dump($b>$a);
    // Devolve se o b é maior que a
    echo '<br>';

    var_dump($b==$a);
    // Devolve se o b é igual a
    echo '<br>';

    var_dump($b===$a);
    // Devolve se o tipo e o valor de b é igual ao tipo e o valor de a
    echo '<br>';

    var_dump($b!=$a);
    // Devolve se o b é diferente de a
    echo '<br>';
?>