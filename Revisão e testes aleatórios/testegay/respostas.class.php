<?php
class Resposta {
   private $comeBananaDaCasca;
   private $posicaoComeBanana;
   private $roupaIntima;
   private $cuecaCor;
   private $cuecaTipo;
   private $cuecaMaterial;
   private $saborComida;
   private $tipoComida;
   private $chamarAnimalDeEst;
   private $tipoDeBixo;
   private $tipoPassaro;
   private $tamanhoPassaroTerrestre;

   public function __construct(
      $comeBananaDaCasca, $posicaoComeBanana, $roupaIntima, $cuecaCor,
      $cuecaTipo, $cuecaMaterial, $saborComida, $tipoComida, $chamarAnimalDeEst,
      $tipoDeBixo, $tipoPassaro, $tamanhoPassaroTerrestre
   ) {
      $this->comeBananaDaCasca = $comeBananaDaCasca;
      $this->posicaoComeBanana = $posicaoComeBanana;
      $this->roupaIntima = $roupaIntima;
      $this->cuecaCor = $cuecaCor;
      $this->cuecaTipo = $cuecaTipo;
      $this->cuecaMaterial = $cuecaMaterial;
      $this->saborComida = $saborComida;
      $this->tipoComida = $tipoComida;
      $this->chamarAnimalDeEst = $chamarAnimalDeEst;
      $this->tipoDeBixo = $tipoDeBixo;
      $this->tipoPassaro = $tipoPassaro;
      $this->tamanhoPassaroTerrestre = $tamanhoPassaroTerrestre;
   }

   public function getComeBananaDaCasca() {
      return $this->comeBananaDaCasca;
   }

   public function getPosicaoComeBanana() {
      return $this->posicaoComeBanana;
   }

   public function getRoupaIntima() {
      return $this->roupaIntima;
   }

   public function getCuecaCor() {
      return $this->cuecaCor;
   }

   public function getCuecaTipo() {
      return $this->cuecaTipo;
   }

   public function getCuecaMaterial() {
      return $this->cuecaMaterial;
   }

   public function getSaborComida() {
      return $this->saborComida;
   }

   public function getTipoComida() {
      return $this->tipoComida;
   }

   public function getChamarAnimalDeEst() {
      return $this->chamarAnimalDeEst;
   }

   public function getTipoDeBixo() {
      return $this->tipoDeBixo;
   }

   public function getTipoPassaro() {
      return $this->tipoPassaro;
   }

   public function getTamanhoPassaroTerrestre() {
      return $this->tamanhoPassaroTerrestre;
   }
}
