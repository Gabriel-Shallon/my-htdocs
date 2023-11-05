<?php
    class Connection{
        private function __construct(){}
        
        public static function open($arquivo){
            if (file_exists("config/{$arquivo}.ini"))
                $db = parse_ini_file("config/{$arquivo}.ini");
            else
                throw new Exception('Arquivo não encontrado.');

                $host =   $db['localhost'] ?? null;
                $port =   $db['3306'] ?? null;
                $dbname = $db['app'] ?? null;
                $user =   $db['root'] ?? null;
                $pass =   $db[''] ?? null;
                $type =   $db['mysql'] ?? null;

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