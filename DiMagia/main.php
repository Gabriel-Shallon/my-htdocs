<?php
    require_once 'Connection.php';

    $conn = Connection::open('banco');
    var_dump($conn);