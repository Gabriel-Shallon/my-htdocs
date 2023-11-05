<?php
    class Connection{
        private function __construct(){}
        
        public static function open($arquivo){
            if (file_exists("config/{$arquivo}.ini"))
                $db = parse_ini_file("config/{$arquivo}.ini");
            else
                throw new Exception('Arquivo não encontrado.');

                $host =   $db['host'] ?? null;
                $port =   $db['port'] ?? null;
                $dbname = $db['dbname'] ?? null;
                $user =   $db['user'] ?? null;
                $pass =   $db['pass'] ?? null;
                $type =   $db['type'] ?? null;

                switch($type){
                    case 'mysql':
                        $conn = new PDO("{$type}:;host={$host};dbname={$dbname};port={$port}",$user,$pass);
                        break;
                    case 'sqlite':
                        $conn = new PDO("{$type}:dbname={$dbname}");
                        break;
                }
                $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                return $conn;         
        }
    }