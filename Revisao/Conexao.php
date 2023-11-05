<?php
    //Active Record, Transaction, Logger, Criteria

    class Conexao{
       private $host = "localhost";
       private $dbname = "db";
       private $user = "root";
       private $pass = "";
       private $port = "3306";
       private $conn;

       private function __contruct(){
        try{
        $this -> conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};port={$this->port};", $this->user, $this->pass);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            print $e;
        }
       }

       public function getConn(){
            return $this->conn;
       }
    }