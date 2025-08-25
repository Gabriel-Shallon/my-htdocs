<?php
include_once './inc/generalFuncs.php';
include_once './inc/battleFuncs.php';

//GETS and SETS
function addPlayerVulnerability(string $playerName, string $vulnerabilityName){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT id FROM RPG.vulnerabilities WHERE name = :name");
    $stmt->execute([':name' => $vulnerabilityName]);
    $vulnerability = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO RPG.player_vulnerabilities (player_name, vulnerability_id) VALUES (:playerName, :vulnerabilityId)"
    );
    $stmt->execute([
        ':playerName' => $playerName,
        ':vulnerabilityId' => $vulnerability['id']
    ]);
}

function removePlayerVulnerability(string $playerName, string $vulnerabilityName){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT id FROM RPG.vulnerabilities WHERE name = :name");
    $stmt->execute([':name' => $vulnerabilityName]);
    $vulnerability = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare(
        "DELETE FROM RPG.player_vulnerabilities WHERE player_name = :playerName AND vulnerability_id = :vulnerabilityId"
    );
    $stmt->execute([
        ':playerName' => $playerName,
        ':vulnerabilityId' => $vulnerability['id']
    ]);
}

function getAllVulnerabilities(): array{
    $pdo = conecta();
    return $pdo->query("SELECT name FROM RPG.vulnerabilities ORDER BY name")->fetchAll(PDO::FETCH_COLUMN, 0);
}

function addPlayerInvulnerability(string $playerName, string $invulnerabilityName){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT id FROM RPG.invulnerabilities WHERE name = :name");
    $stmt->execute([':name' => $invulnerabilityName]);
    $invulnerability = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO RPG.player_invulnerabilities (player_name, invulnerability_id) VALUES (:playerName, :invulnerabilityId)"
    );
    $stmt->execute([
        ':playerName' => $playerName,
        ':invulnerabilityId' => $invulnerability['id']
    ]);
}

function removePlayerInvulnerability(string $playerName, string $invulnerabilityName){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT id FROM RPG.invulnerabilities WHERE name = :name");
    $stmt->execute([':name' => $invulnerabilityName]);
    $invulnerability = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare(
        "DELETE FROM RPG.player_invulnerabilities WHERE player_name = :playerName AND invulnerability_id = :invulnerabilityId"
    );
    $stmt->execute([
        ':playerName' => $playerName,
        ':invulnerabilityId' => $invulnerability['id']
    ]);
}

function getAllInvulnerabilities(): array{
    $pdo = conecta();
    return $pdo->query("SELECT name FROM RPG.invulnerabilities ORDER BY name")->fetchAll(PDO::FETCH_COLUMN, 0);
}

function addPlayerExtraArmor(string $playerName, string $extraArmorName){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT id FROM RPG.extra_armor_types WHERE name = :name");
    $stmt->execute([':name' => $extraArmorName]);
    $extraArmor = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO RPG.player_extra_armor (player_name, extra_armor_id) VALUES (:playerName, :extraArmorId)"
    );
    $stmt->execute([
        ':playerName' => $playerName,
        ':extraArmorId' => $extraArmor['id']
    ]);
}

function removePlayerExtraArmor(string $playerName, string $extraArmorName){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT id FROM RPG.extra_armor_types WHERE name = :name");
    $stmt->execute([':name' => $extraArmorName]);
    $extraArmor = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare(
        "DELETE FROM RPG.player_extra_armor WHERE player_name = :playerName AND extra_armor_id = :extraArmorId"
    );
    $stmt->execute([
        ':playerName' => $playerName,
        ':extraArmorId' => $extraArmor['id']
    ]);
}

function getAllExtraArmorTypes(): array{
    $pdo = conecta();
    return $pdo->query("SELECT name FROM RPG.extra_armor_types ORDER BY name")->fetchAll(PDO::FETCH_COLUMN, 0);
}

function listPlayerVulnerabilities(string $playerName): array{
    $pdo = conecta();;
    $stmt = $pdo->prepare("
         SELECT v.name
         FROM RPG.vulnerabilities v
         JOIN RPG.player_vulnerabilities pv ON v.id = pv.vulnerability_id
         WHERE pv.player_name = :playerName
     ");
    $stmt->execute([':playerName' => $playerName]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function listPlayerInvulnerabilities(string $playerName): array{
    $pdo = conecta();
    $stmt = $pdo->prepare("
         SELECT i.name
         FROM RPG.invulnerabilities i
         JOIN RPG.player_invulnerabilities pi ON i.id = pi.invulnerability_id
         WHERE pi.player_name = :playerName
     ");
    $stmt->execute([':playerName' => $playerName]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function listPlayerExtraArmor(string $playerName): array{
    $pdo = conecta();
    $stmt = $pdo->prepare("
         SELECT ea.name
         FROM RPG.extra_armor_types ea
         JOIN RPG.player_extra_armor pae ON ea.id = pae.extra_armor_id
         WHERE pae.player_name = :playerName
     ");
    $stmt->execute([':playerName' => $playerName]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function getAllDmgTypes(): array{
    $pdo = conecta();
    $stmt = $pdo->query("SELECT name FROM RPG.damage_types ORDER BY category, name");
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function listPlayerDmgTypes(string $playerName): array{
    $pdo = conecta();
    $stmt = $pdo->prepare("
         SELECT dt.name
         FROM RPG.damage_types dt
         JOIN RPG.player_damage_types pdt ON dt.id = pdt.damage_type_id
         WHERE pdt.player_name = :playerName
     ");
    $stmt->execute([':playerName' => $playerName]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function addPlayerDmgType(string $playerName, string $damageTypeName){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT id FROM RPG.damage_types WHERE name = :name");
    $stmt->execute([':name' => $damageTypeName]);
    $damageType = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO RPG.player_damage_types (player_name, damage_type_id) VALUES (:playerName, :damageTypeId)"
    );
    $stmt->execute([
        ':playerName' => $playerName,
        ':damageTypeId' => $damageType['id']
    ]);
}

function removePlayerDmgType(string $playerName, string $damageTypeName){
    $pdo = conecta();
    $stmt = $pdo->prepare("SELECT id FROM RPG.damage_types WHERE name = :name");
    $stmt->execute([':name' => $damageTypeName]);
    $damageType = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare(
        "DELETE FROM RPG.player_damage_types WHERE player_name = :playerName AND damage_type_id = :damageTypeId"
    );
    $stmt->execute([
        ':playerName' => $playerName,
        ':damageTypeId' => $damageType['id']
    ]);
}


//Tests

function extraArmorTest($player, $dmgType){
    if (in_array($dmgType, listPlayerExtraArmor($player))) {
        return getPlayerStat($player, 'A') * 2;
    } else {
        return getPlayerStat($player, 'A');
    }
}
function invulnerabilitieTest($player, $dmgType, $FA){
    if (in_array($dmgType, listPlayerInvulnerabilities($player))) {
        $FA = $FA / 10;
        return floor($FA);
    } else {
        return $FA;
    }
}
function vulnerabilitieTest($player, $dmgType){
    if (in_array($dmgType, listPlayerVulnerabilities($player))) {
        return 0;
    } else {
        return getPlayerStat($player, 'A');
    }
}
function vulnerabilitieExtraArmorTest($player, $dmgType){
    $hasVulnerabilitie = false;
    $hasExtraArmor = false;
    foreach (listPlayerVulnerabilities($player) as $type) {
        if ($type == $dmgType) {
            $hasVulnerabilitie = true;
        }
    }
    foreach (listPlayerExtraArmor($player) as $type) {
        if ($type == $dmgType) {
            $hasExtraArmor = true;
        }
    }
    if (($hasExtraArmor && $hasVulnerabilitie) or (!$hasExtraArmor && !$hasVulnerabilitie)) {
        return getPlayerStat($player, 'A');
    }
    if ($hasExtraArmor && !$hasVulnerabilitie) {
        return getPlayerStat($player, 'A') * 2;
    }
    if (!$hasExtraArmor && $hasVulnerabilitie) {
        return 0;
    }
}
?>