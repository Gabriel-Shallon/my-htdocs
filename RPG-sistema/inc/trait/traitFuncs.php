<?php
include_once "dmgModifiers.php";
include_once "disadvantagesFuncs.php";
include_once "advantagesFuncs.php";

//Central trait file

function listPlayerTraits(string $player): array{
    $pdo = conecta();
    $res = [];
    // vantagens
    $stmt = $pdo->prepare("
       SELECT a.name FROM RPG.advantages a
       JOIN RPG.player_advantages pa ON pa.advantage_id = a.id
       WHERE pa.player_name = ? 
     ");
    $stmt->execute([$player]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $n) {
        $res[] = slugify($n);
    }
    // desvantagens
    $stmt = $pdo->prepare("
       SELECT d.name FROM RPG.disadvantages d
       JOIN RPG.player_disadvantages pd ON pd.disadvantage_id = d.id
       WHERE pd.player_name = ?
     ");
    $stmt->execute([$player]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $n) {
        $res[] = slugify($n);
    }
    return $res;
}

function addPlayerTrait(string $player, int $traitId, string $type): bool{
    $pdo = conecta();
    if ($type === 'advantage') {
        $sql = "INSERT INTO RPG.player_advantages (player_name, advantage_id) VALUES (:player, :id)";
    } elseif ($type === 'disadvantage') {
        $sql = "INSERT INTO RPG.player_disadvantages (player_name, disadvantage_id) VALUES (:player, :id)";
    } else {
        throw new InvalidArgumentException('Tipo de trait inválido: ' . $type);
    }
    $stmt = $pdo->prepare($sql);
    return $stmt->execute(['player' => $player, 'id' => $traitId]);
}

function removePlayerTrait(string $player, int $traitId, string $type): bool{
    $pdo = conecta();
    if ($type === 'advantage') {
        $sql = "DELETE FROM RPG.player_advantages WHERE player_name = :player AND advantage_id = :id LIMIT 1";
    } elseif ($type === 'disadvantage') {
        $sql = "DELETE FROM RPG.player_disadvantages WHERE player_name = :player AND disadvantage_id = :id LIMIT 1";
    } else {
        throw new InvalidArgumentException('Tipo de trait inválido: ' . $type);
    }
    $stmt = $pdo->prepare($sql);
    return $stmt->execute(['player' => $player, 'id' => $traitId]);
}


// GET and SET Partner/Ally functions

function addPlayerAlly(string $owner, string $allyName): bool{
    $pdo = conecta();
    $stmt = $pdo->prepare("INSERT IGNORE INTO RPG.allies (dono, aliado) VALUES (:owner, :ally)");
    return $stmt->execute([':owner' => $owner, ':ally' => $allyName]);
}

function removePlayerAlly(string $owner, string $allyName): bool{
    $pdo = conecta();
    $stmt = $pdo->prepare("DELETE FROM RPG.allies WHERE dono = :owner AND aliado = :ally");
    return $stmt->execute([':owner' => $owner, ':ally' => $allyName]);
}

function getPlayerAllies(string $player){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT aliado FROM RPG.allies WHERE dono = :dono");
    $stmt->execute(['dono' => $player]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getAlliePlayer(string $aliado){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT dono FROM RPG.allies WHERE aliado = :aliado");
    $stmt->execute(['aliado' => $aliado]);
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

function getAllAllies(){
    $pdo = conecta();
    $stmt = $pdo->query("SELECT DISTINCT aliado FROM RPG.allies");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getPlayerPartner(string $player){
    $pdo = conecta();
    $sql = "SELECT a.aliado FROM `RPG`.`allies` AS a JOIN `RPG`.`player_advantages` AS pa ON pa.player_name = a.aliado JOIN `RPG`.`advantages` AS adv ON adv.id = pa.advantage_id  AND adv.name = 'Parceiro'WHERE a.dono = :dono";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['dono' => $player]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getPartnerOwner(string $partner){
    $pdo = conecta();
    $sql = "SELECT a.dono FROM `RPG`.`allies` AS a JOIN `RPG`.`player_advantages` AS pa ON pa.player_name = a.dono JOIN `RPG`.`advantages` AS adv ON adv.id = pa.advantage_id AND adv.name = 'Parceiro' WHERE a.aliado = :aliado";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['aliado' => $partner]);
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

?>