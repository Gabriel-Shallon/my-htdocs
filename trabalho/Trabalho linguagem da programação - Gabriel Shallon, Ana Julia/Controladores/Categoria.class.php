<?php
    class categoria{
        private $categoria_id;
        private $nome;

        public function __construct($categoria_id, $nome){
            $this->categoria_id = $categoria_id;
            $this->nome = $nome;

        }

        public function getCategoria_id(){
            return $this->categoria_id;
        }

        public function getNome(){
            return $this->nome;
        }
    }
?>