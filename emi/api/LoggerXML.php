<?php
    class LoggerXML extends Logger{

        public function write($message){
            date_default_timezone_set('America/Cuiaba');
            $time = date("Y-m-d H:i:s");

            $text = "<log>\n";
            $text .= "<time>{$time}</time>\n";
            $text .= "<message>{$message}</message>\n";
            $text .= "</log>\n";

            $arquivo = fopen($this->filename,'a');
            fwrite($arquivo, $text);
            fclose($arquivo);
        }
    }