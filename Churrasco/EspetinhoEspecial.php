<?php 
    
    class EspetinhoEspecial extends Espetinho {
        private $ingredientesAdd;
        private $desconto;
    
        public function __construct($nome, $preco, $quantidade, $ingredientesAdd, $desconto) {
            parent::__construct($nome, $preco, $quantidade);
            $this->ingredientesAdd = $ingredientesAdd;
            $this->desconto = $desconto;
        }
    
        public function getIngredientesAdd() {
            return $this->ingredientesAdd;
        }
    
        public function getDesconto() {
            return $this->desconto;
        }
    
        public function calcularTotal() {
            $precoTotal = $this->preco * $this->quantidade;
            $precoTotal -= $this->desconto;
            return $precoTotal;
        }
        
        
        public function __toString() {
            return parent::__toString() . ", Ingredientes: {$this->ingredientesAdd}, Desconto: R$ {$this->desconto}";
        }
    }