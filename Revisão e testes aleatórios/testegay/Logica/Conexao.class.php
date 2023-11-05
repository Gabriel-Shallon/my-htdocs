<?php



include 'config.php';

class Conexao {
   private $pdo;

   public function __construct() {
      global $dbConfig;
      try {
         $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};port={$dbConfig['port']}";
         $this->pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
      } catch (PDOException $e) {
         echo 'Erro na conexão com o Banco de Dados: ' . $e->getMessage();
      }
   }



   public function insertEhGay($ehgay) {
      $sql = "INSERT INTO ehgay (nome, resultado) VALUES (:nome, :resultado)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':nome', $ehgay->getNome());
      $stmt->bindValue(':resultado', $ehgay->getResultado());
      $stmt->execute();
  }
  
  public function insertResposta($resposta) {
      $sql = "INSERT INTO respostas (comeBananaDaCasca, posicaoComeBanana, roupaIntima, cuecaCor, cuecaTipo, cuecaMaterial, saborComida, tipoComida, chamarAnimalDeEst, tipoDeBixo, tipoPassaro, tamanhoPassaroTerrestre) VALUES (:comeBananaDaCasca, :posicaoComeBanana, :roupaIntima, :cuecaCor, :cuecaTipo, :cuecaMaterial, :saborComida, :tipoComida, :chamarAnimalDeEst, :tipoDeBixo, :tipoPassaro, :tamanhoPassaroTerrestre)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':comoComeBanana', $resposta->getComeBananaDaCasca());
      $stmt->bindValue(':posicaoComeBanana', $resposta->getPosicaoComeBanana());
      $stmt->bindValue(':roupaIntima', $resposta->getRoupaIntima());
      $stmt->bindValue(':cuecaCor', $resposta->getCuecaCor());
      $stmt->bindValue(':cuecaTipo', $resposta->getCuecaTipo());
      $stmt->bindValue(':cuecaMaterial', $resposta->getCuecaMaterial());
      $stmt->bindValue(':saborComida', $resposta->getSaborComida());
      $stmt->bindValue(':tipoComida', $resposta->getTipoComida());
      $stmt->bindValue(':chamarAnimalDeEst', $resposta->getChamarAnimalDeEst());
      $stmt->bindValue(':tipoDeBixo', $resposta->getTipoDeBixo());
      $stmt->bindValue(':tipoPassaro', $resposta->getTipoPassaro());
      $stmt->bindValue(':tamanhoPassaroTerrestre', $resposta->getTamanhoPassaroTerrestre());
      $stmt->execute();
  }
  

   public function selectAll() {
      $sql = "SELECT * FROM ehgay";
      $stmt = $this->pdo->query($sql);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }
}