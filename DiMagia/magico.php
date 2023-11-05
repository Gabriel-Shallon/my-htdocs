<?php
    class Record
    {
        private $data; // array


        public  function __set($prop, $valor)
        {
            $this->data[$prop] = $valor;
        }

        public function __get($prop)
        {
            return $this->data[$prop];
        }

        public function __isset($prop)
        {
            return isset($this->data[$prop]);
        }
    }

    $produto = new Record();
    $produto->nome = 'DiMagia';
    $produto->idade = 20000000;
    $produto->email = 'legalize@nuclear.bomb';
    $produto->telefone = '(69) 99999-6969';
    print "Nome: {$produto->nome} <br/> Idade: {$produto->idade} <br/> Email: {$produto->email} <br/> Telefone: {$produto->telefone}";