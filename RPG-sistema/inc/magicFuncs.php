<?php

include_once 'inc/generalFuncs.php';
include_once 'inc/battleFuncs.php';

function getMagic($magic) {
    $conn = conecta();
    $sql = "SELECT * FROM RPG.magias WHERE id = :magic OR nome = :magic";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':magic', $magic);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getMagicStat($magic, $stat) {
    $magicData = getMagic($magic);
    if ($magicData && isset($magicData[$stat])) {
        return $magicData[$stat];
    }
    return null;
}


function getPlayerMagics($player) {
        $conn = conecta();
        $sql = "SELECT m.* 
                FROM RPG.magias AS m
                JOIN RPG.player_magias AS pm ON m.id = pm.magia_id
                WHERE pm.player_name = :player";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':player', $player, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getMagicSchool($school) {
        $conn = conecta();
        $sql = "SELECT * FROM RPG.magias WHERE escola = :school ORDER BY nome ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':school', $school, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function setMagic($nome, $escola, $custo_descricao, $tipo_custo, $custo_inicial, $custo_por_turno, $custo_permanente, $custo_variavel_min, $custo_variavel_max, $alcance, $duracao, $exigencias, $descricao) {
    $efeitoSlug = slugify($nome);
    $sql = "INSERT INTO RPG.magias 
                (nome, escola, custo_descricao, tipo_custo, custo_inicial, custo_por_turno, custo_permanente, custo_variavel_min, custo_variavel_max, alcance, duracao, efeito_slug, exigencias, descricao) 
            VALUES 
                (:nome, :escola, :custo_descricao, :tipo_custo, :custo_inicial, :custo_por_turno, :custo_permanente, :custo_variavel_min, :custo_variavel_max, :alcance, :duracao, :efeito_slug, :exigencias, :descricao)";
    $conn = conecta();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':escola', $escola);
    $stmt->bindParam(':custo_descricao', $custo_descricao);
    $stmt->bindParam(':tipo_custo', $tipo_custo);
    $stmt->bindParam(':custo_inicial', $custo_inicial, PDO::PARAM_INT);
    $stmt->bindParam(':custo_por_turno', $custo_por_turno, PDO::PARAM_INT);
    $stmt->bindParam(':custo_permanente', $custo_permanente, PDO::PARAM_INT);
    $stmt->bindParam(':custo_variavel_min', $custo_variavel_min, PDO::PARAM_INT);
    $stmt->bindParam(':custo_variavel_max', $custo_variavel_max, PDO::PARAM_INT);
    $stmt->bindParam(':alcance', $alcance);
    $stmt->bindParam(':duracao', $duracao);
    $stmt->bindParam(':efeito_slug', $efeitoSlug);
    $stmt->bindParam(':exigencias', $exigencias);
    $stmt->bindParam(':descricao', $descricao);
    return $stmt->execute();
}


function setPlayerMagic($player, $magic) {
    $magicData = getMagic($magic);
    if (!$magicData) {
        return false;
    }
    $magicId = $magicData['id'];
    $sql = "INSERT INTO RPG.player_magias (player_name, magia_id) VALUES (:player, :magic_id)";
    $conn = conecta();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':player', $player);
    $stmt->bindParam(':magic_id', $magicId, PDO::PARAM_INT);
    return $stmt->execute();
}


function editMagic($magic, $stat, $newStat) {
    $magicData = getMagic($magic);
    if (!$magicData) {
        return false; 
    }
    $allowed_columns = [
        'nome', 'escola', 'custo_descricao', 'tipo_custo', 'custo_inicial', 
        'custo_por_turno', 'custo_permanente', 'custo_variavel_min', 'custo_variavel_max', 
        'alcance', 'duracao', 'exigencias', 'descricao'
    ];
    if (!in_array($stat, $allowed_columns)) {
        return false;
    }
    $magicId = $magicData['id'];
    if ($stat === 'nome') {
        $newSlug = slugify($newStat);
        editMagic($magicId, 'efeito_slug', $newSlug);
    }
    $sql = "UPDATE RPG.magias SET `$stat` = :new_stat WHERE id = :magic_id";
    $conn = conecta();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':new_stat', $newStat);
    $stmt->bindParam(':magic_id', $magicId, PDO::PARAM_INT); 
    return $stmt->execute();
}


function removePlayerMagic($player, $magic) {
    $magicData = getMagic($magic);
    if (!$magicData) {
        return false;
    }
    $magicId = $magicData['id'];
    $sql = "DELETE FROM RPG.player_magias WHERE player_name = :player AND magia_id = :magic_id";
    $conn = conecta();
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':player', $player);
    $stmt->bindParam(':magic_id', $magicId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}


function removeMagic($magic) {
    $magicData = getMagic($magic);
    if (!$magicData) {
        return false;
    }
    $magicId = $magicData['id'];
    $conn = conecta();
    $conn->beginTransaction();
    $sqlPlayer = "DELETE FROM RPG.player_magias WHERE magia_id = :magic_id";
    $stmtPlayer = $conn->prepare($sqlPlayer);
    $stmtPlayer->bindParam(':magic_id', $magicId, PDO::PARAM_INT);
    $stmtPlayer->execute();
    $sqlMagic = "DELETE FROM RPG.magias WHERE id = :magic_id";
    $stmtMagic = $conn->prepare($sqlMagic);
    $stmtMagic->bindParam(':magic_id', $magicId, PDO::PARAM_INT);
    $stmtMagic->execute();
    $conn->commit();
    return $stmtMagic->rowCount() > 0;
}




// EXECUÇÃO DE MAGIAS //



function ataqueMagico($b, $mago, $alvosInfo, $PMs, $atkType){
    $out = '';
    $costPerTgt = round($PMs/count($alvosInfo));
    if (spendPM($mago,$PMs)){
        foreach ($alvosInfo as $tgt) {
            $tgtName = $tgt['name'];
            $rollFAAndPMs = $tgt['rollFA']+$costPerTgt;
            $dano = defaultReactionTreatment($b, $tgtName, $mago, $tgt['reaction'], $rollFAAndPMs, ($tgt['rollFD']+resistenciaMagia($tgtName)), $atkType, 'Magia');
            $out .= applyDamage($mago, $tgtName, $dano, 'Magico', $out);
            $out .= "<strong>{$mago}</strong> usou Ataque Mágico ({$atkType}) em <strong>{$tgtName}</strong>. PMs = {$PMs}; Dano = {$dano}<br>";
        }
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente par lançar esse ataque mágico.";
    }
}


function lancaInfalivelDeTalude($b, $mago, $alvosInfo, $PMs){
    $out = '';
    if (spendPM($mago,$PMs)){
        foreach ($alvosInfo as $tgt) {
            $tgtName = $tgt['name'];
            $FA = $tgt['qtdAtk'] * 2;
            $dano = $FA - (FDindefeso($tgtName, 'Magia')+resistenciaMagia($tgtName));
            $out .= applyDamage($mago, $tgtName, $dano, 'Magico', $out);
            $out .= "<strong>{$mago}</strong> usou A Lança Infalível de Talude em <strong>{$tgtName}</strong>. PMs = {$PMs}; Dano = {$dano}<br>";
        }
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente par lançar essa quantidade de Lanças Infalíveis de Talude.";
    }
}

function brilhoExplosivo($b, $mago, $alvo, $dadosFA, $dadoFD){
    $out = '';
    if (spendPM($mago,25)){
        $dano = $dadosFA - ($dadoFD+getPlayerStat($alvo, 'H')+resistenciaMagia($alvo));
        $out .= applyDamage($mago, $alvo, $dano, 'Magico', $out);
        $out .= "<strong>{$mago}</strong> usou Brilho Explosivo em <strong>{$alvo}</strong>. PMs = 25; Dano = {$dano}<br>";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente par lançar essa quantidade de Lanças Infalíveis de Talude.";
    }
}


























?>