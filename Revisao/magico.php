<?php
    class Record{

        private $data; //array


        public function __set($prop, $valor){
                $this->data[$prop] = $valor;
           // else
           // print "<br />Erro, não existe a propriedade '{$prop}'.<br />";
        }

        public function __get($prop){
                return $this->data[$prop];
        }

        public function __isset($prop){
             return isset($this->data[$prop]);
        }
        

    }

    $produto = new Record();
    $produto->nome = 'Neto';
    $produto->idade = 36;
    $produto->email = "NETO@gmail.com";
    $produto->telefone = "99 99999 9999";
    print "Nome: {$produto->nome} <br/> Idade: {$produto->idade} <br/> Email: {$produto->email} <br/> Telefone: {$produto->telefone} <br/>";
