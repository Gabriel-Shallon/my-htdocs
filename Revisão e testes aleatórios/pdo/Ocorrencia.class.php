<?php
class Ocorrencia {
   private $tipo;
   private $descricao;

   public function __construct($tipo, $descricao) {
      $this->tipo = $tipo;
      $this->descricao = $descricao;
   }

   public function getTipo() {
      return $this->tipo;
   }

   public function getDescricao() {
      return $this->descricao;
   }
}