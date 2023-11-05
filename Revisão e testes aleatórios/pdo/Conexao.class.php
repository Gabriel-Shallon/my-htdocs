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



   public function insert($ocorrencia) {
      $sql = "INSERT INTO ocorrencias (tipo, descricao) VALUES (:tipo, :descricao)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':tipo', $ocorrencia->getTipo());
      $stmt->bindValue(':descricao', $ocorrencia->getDescricao());
      $stmt->execute();
   }

   public function selectAll() {
      $sql = "SELECT * FROM ocorrencias";
      $stmt = $this->pdo->query($sql);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }
}