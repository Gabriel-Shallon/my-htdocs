<?php
    class LoggerTXT extends Logger{
        // public function __construct($filename){
        //     parent::__construct($filename);
        // }
        public function write($message){
            date_default_timezone_set('America/Cuiaba');
            $time = date('Y-m-d H:i:s');
            
            $text = "$time :: $message";
            
            $arquivo = fopen($this->filename,'a');
            fwrite($arquivo, $text . "\n");
            fclose($arquivo);
        }
    }