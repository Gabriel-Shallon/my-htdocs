<?php
    if (file_exists('file.ini')){
        $arquivo = parse_ini_file('file.ini');
    }
    print $arquivo['host'].$arquivo['dbname'];
