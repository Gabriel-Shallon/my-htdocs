<?php 


    class Espetinho{

       protected $nome;
       protected $preco;
       protected $quantidade;


       public function __construct($nome, $preco, $quantidade) {
        $this->nome = $nome;
        $this->preco = $preco;
        $this->quantidade = $quantidade;
    }


            public function getNome(){
                return $this->nome;
            }
            public function getPreco(){
                return $this->preco;
            }
            public function getQuantidade(){
                return $this->quantidade;
            }

            public function setQuantidade($quantidade) {
                $this->quantidade = $quantidade;
            }


            public static function verificarQuantidadeTotal($espetinhos) {
                $quantidadeTotal = 0;
                foreach ($espetinhos as $espetinho) {
                    $quantidadeTotal += $espetinho->getQuantidade();
                }
                return $quantidadeTotal;
            }

            public function __toString() {
                return "Nome: {$this->nome}, Preço: R$ {$this->preco}";
            }
            



    }