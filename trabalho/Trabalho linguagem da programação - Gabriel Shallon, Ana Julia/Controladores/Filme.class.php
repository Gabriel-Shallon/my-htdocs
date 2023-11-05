<?php
    class filme{
        private $filme_id;
        private $titulo;
        private $descricao;
        private $ano_de_lancamento;
        private $classificacao;

        public function __construct($filme_id, $titulo, $descricao, $ano_de_lancamento, $classificacao){
            $this->filme_id = $filme_id;
            $this->titulo = $titulo;
            $this->descricao = $descricao;
            $this->ano_de_lancamento = $ano_de_lancamento;
            $this->classificacao = $classificacao;
        }

        public function getFilme_id(){
            return $this->filme_id;
        }

        public function getTitulo(){
            
            return $this->titulo;
        }

        public function getDescricao(){
            return $this->descricao;
        }

        public function getAno_de_lancamento(){
            return $this->ano_de_lancamento;
        }

        public function getClassificacao(){
            return $this->classificacao;
        }

    }
?>