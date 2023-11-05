<?php
    if(file_exists('files.ini'))
    {
    $arquivo = parse_ini_file('files.ini');
    }
    
    print $arquivo['host'];