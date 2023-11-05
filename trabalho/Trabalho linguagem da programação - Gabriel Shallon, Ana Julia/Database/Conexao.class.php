<?php
    class Conexao{
    private $host = 'localhost';
    private $dbname = 'locadora';
    private $user = 'root';
    private $pass = '';
    private $port = '3306';
    private $conn;

    public function __construct(){
        try{
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};port={$this->port}", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e){
            echo 'erro ao se conectar ao banco' . $e->getMessage();
        }
    }

    public function getConnection(){
        return $this->conn;
    }

    public function selectAllF(){
        $sql = "SELECT * FROM filme";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectAllF2($filme_id){
        $sql = "SELECT * FROM filme where filme_id=:filme_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':filme_id', $filme_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectAllA(){
        $sql = "SELECT * FROM ator";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function selectAllA2($ator_id){
        $sql = "SELECT * FROM ator where ator_id=:ator_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ator_id', $ator_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectAllC(){
        $sql = "SELECT * FROM categoria";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectAllC2($categoria_id){
        $sql = "SELECT * FROM categoria where categoria_id=:categoria_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindvalue(":categoria_id",$categoria_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function insertF($filme){
        $sql = "INSERT INTO filme(filme_id, titulo, descricao, ano_de_lancamento, classificacao) VALUES (:filme_id, :titulo, :descricao, :ano_de_lancamento, :classificacao)";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindValue(":filme_id", $filme->getFilme_id());
        $stmt ->bindValue(":titulo", $filme->getTitulo());
        $stmt ->bindValue(":descricao", $filme->getDescricao());
        $stmt ->bindValue(":ano_de_lancamento", $filme->getAno_de_lancamento());
        $stmt ->bindValue(":classificacao", $filme->getClassificacao());
        return $stmt->execute();
    }

    public function insertC($categoria){
        $sql = "INSERT INTO categoria(categoria_id, nome) VALUES (:categoria_id, :nome)";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindValue(":categoria_id", $categoria->getCategoria_id());
        $stmt ->bindValue(":nome", $categoria->getNome());
        return $stmt->execute();
    }

    public function insertA($ator){
        $sql = "INSERT INTO ator(ator_id, primeiro_nome, ultimo_nome) VALUES (:ator_id, :primeiro_nome, :ultimo_nome)";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindValue(":ator_id", $ator->getAtor_id());
        $stmt ->bindValue(":primeiro_nome", $ator->getPrimeiro_nome());
        $stmt ->bindValue(":ultimo_nome", $ator->getUltimo_nome());
        return $stmt->execute();
    }

    public function deleteF($filme_id){
        $sql = "DELETE FROM filme WHERE filme_id = :filme_id";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindParam(":filme_id", $filme_id);
        return $stmt->execute(); 
    }

    public function deleteC($categoria_id){
        $sql = "DELETE FROM categoria WHERE categoria_id = :categoria_id";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindParam(":categoria_id", $categoria_id);
        return $stmt->execute(); 
    }

    public function deleteA($ator_id){
        $sql = "DELETE FROM ator WHERE ator_id = :ator_id";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindParam(":ator_id", $ator_id);
        return $stmt->execute(); 
    }

    public function updateF($filme_id,$titulo,$descricao,$ano_de_lancamento,$classificacao){
        $sql = "UPDATE filme SET titulo = :titulo, descricao = :descricao, ano_de_lancamento = :ano_de_lancamento, classificacao = :classificacao WHERE filme_id = :filme_id";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindValue(":filme_id", $filme_id);
        $stmt->bindValue(":titulo", $titulo);
        $stmt->bindValue(":descricao", $descricao);
        $stmt->bindValue(":ano_de_lancamento", $ano_de_lancamento);
        $stmt->bindValue(":classificacao", $classificacao);
        return $stmt->execute();
    }

    public function updateA($ator_id,$primeiro_nome,$ultimo_nome){
        $sql = "UPDATE ator SET primeiro_nome = :primeiro_nome, ultimo_nome = :ultimo_nome WHERE ator_id = :ator_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":ator_id", $ator_id);
        $stmt->bindValue(":primeiro_nome", $primeiro_nome);
        $stmt->bindValue(":ultimo_nome", $ultimo_nome);
        return $stmt->execute();
    }
}
?>