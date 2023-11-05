<?php
    class Connection
    {
        private function __construct(){}

        public static function open($banco)
        {
            if(file_exists("{$banco}.ini"))
                $db = parse_ini_file("{$banco}.ini");
            else
                throw new Exception("O arquivo '{$banco}.ini' não existe");

            $type = $db['type'] ?? null;
            $host = $db['host'] ?? null;
            $port =  $db['port'] ?? null;
            $dbname = $db['dbname'] ?? null;
            $user = $db['user'] ?? null;
            $pass = $db['pass'] ?? null;

            switch($type)
            {
                case 'mysql':
                    $conn = new PDO("{$type};host={$host};dbname={$dbname}",$user,$pass);
                    break;

                case 'sqlite':
                    $conn = new PDO("{$type}:{$dbname}");
                    break;
            }

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
    }
?>