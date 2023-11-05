<?php
class Ehgay {
   private $nome;
   private $resultado;

   public function __construct($nome, $resultado) {
      $this->nome = $nome;
      $this->resultado = $resultado;
   }

   public function getNome() {
      return $this->nome;
   }

   public function getResultado() {
      return $this->resultado;
   }
}