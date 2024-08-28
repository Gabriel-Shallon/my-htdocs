<?php

    echo time()/60/60/24/365; //anos desde 1970 (ou em segundos se retirar os '/')
    $form = strtotime('2024-08-02');
    echo '<br>';
    echo $form;

    echo '<br>'.date("F j, Y, g:i a");                 // March 10, 2001, 5:16 pm
    echo '<br>'.date("m.d.y");                         // 03.10.01
    echo '<br>'.date("j, n, Y");                       // 10, 3, 2001
    echo '<br>'.date("Ymd");                           // 20010310
    echo '<br>'.date('h-i-s, j-m-y, it is w Day');     // 05-16-18, 10-03-01, 1631 1618 6 Satpm01
    echo '<br>'.date('\i\t \i\s \t\h\e jS \d\a\y.');   // it is the 10th day.
    echo '<br>'.date("D M j G:i:s T Y");               // Sat Mar 10 17:16:18 MST 2001
    echo '<br>'.date('H:m:s \m \i\s\ \m\o\n\t\h');     // 17:03:18 m is month
    echo '<br>'.date("H:i:s");                         // 17:16:18
    echo '<br>'.date("Y-m-d H:i:s");                   // 2001-03-10 17:16:18 (the MySQL DATETIME format)

    echo '<br>'.date("H:i:s - l - d/m/y"); 

    $man = strtotime('+1 week +5 hour'); // Manipula o tempo e pode ser usado como parametro
    echo date('d/m/Y', $man);

    $dateRoo = strtotime('-6 hour'); 
    echo '<br>'.date("H:i:s - l - d/m/y", $dateRoo); 




    // set the default timezone to use.
    date_default_timezone_set('UTC');


    // Prints something like: Monday
    echo date("<br>l");

    // Prints something like: Monday 8th of August 2005 03:12:46 PM
    echo date('<br>l jS \of F Y h:i:s A');

    // Prints: July 1, 2000 is on a Saturday
    echo "<br>July 1, 2000 is on a " . date("l", mktime(0, 0, 0, 7, 1, 2000));

    /* use the constants in the format parameter */
    // prints something like: Wed, 25 Sep 2013 15:28:57 -0700
    echo date(DATE_RFC2822);

    // prints something like: 2000-07-01T00:00:00+00:00
    echo date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000));


?>