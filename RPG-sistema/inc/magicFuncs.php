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


function getMagicSpecialName($mago, $magica){
    $conn = conecta();
    $sql = "SELECT pm.nome_personagem 
            FROM RPG.player_magias AS pm
            JOIN RPG.magias AS m ON pm.magia_id = m.id
            WHERE pm.player_name = :mago AND m.efeito_slug = :magica";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':mago', $mago, PDO::PARAM_STR);
    $stmt->bindParam(':magica', $magica, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && !empty($result['nome_personagem'])) {
        return $result['nome_personagem'];
    }
    return null;
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
            $dano = max(defaultReactionTreatment($b, $tgtName, $mago, $tgt['reaction'], $rollFAAndPMs, ($tgt['rollFD']+resistenciaMagia($tgtName)), $atkType, 'Magia'), 0);
            $out .= applyDamage($mago, $tgtName, $dano, 'Magico', $out);
            $out .= "<strong>{$mago}</strong> usou Ataque Mágico ({$atkType}) em <strong>{$tgtName}</strong>. PMs = {$PMs}; Dano = {$dano}<br>";
        }
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente par lançar esse ataque mágico.";
    }
}


function lancaInfalivelDeTalude($mago, $alvosInfo, $PMs){
    $out = '';
    if (spendPM($mago,$PMs)){
        foreach ($alvosInfo as $tgt) {
            $tgtName = $tgt['name'];
            $FA = $tgt['qtdAtk'] * 2;
            $dano = max($FA - (FDindefeso($tgtName, 'Magia')+resistenciaMagia($tgtName)), 0);
            $out .= applyDamage($mago, $tgtName, $dano, 'Magico', $out);
            $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'lanca_infalivel_de_talude')." (A Lança Infalível de Talude) em <strong>{$tgtName}</strong>. PMs = {$PMs}; Dano = {$dano}<br>";
        }
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente par lançar essa quantidade de Lanças Infalíveis de Talude.";
    }
}

function brilhoExplosivo($mago, $alvo, $dadosFA, $dadoFD){
    $out = '';
    if (spendPM($mago,25)){
        $dano = max($dadosFA - ($dadoFD+getPlayerStat($alvo, 'H')+resistenciaMagia($alvo)), 0);
        $out .= applyDamage($mago, $alvo, $dano, 'Magico', $out);
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'brilho_explosivo')." (Brilho Explosivo) em <strong>{$alvo}</strong>. PMs = 25; Dano = {$dano}<br>";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Brilho Explisivo.";
    }
}

function morteEstelar($mago, $alvo){
    $out = '';
    if (spendPM($mago,5)){
        $FA = '992625164071551871061817274843250294784';
        $FD = (string)(FDindefeso($alvo, 'Magia')+resistenciaMagia($alvo));
        $dano = bcsub($FA, $FD, 0);
        $PV = (string)getPlayerStat($alvo, 'PV');
        $resultado = bcsub($PV, $dano, 0);
        if (bccomp($resultado, '0', 0) <= 0){
            setPlayerStat($mago, 'PM_max', getPlayerStat($mago, 'PM_max')-5);
            setPlayerStat($alvo, 'PV', 0);
            return "<strong>{$mago}</strong> aniquilou <strong>{$alvo}</strong> com <strong>".$dano."</strong> de dano!";
        } else {
            setPlayerStat($mago, 'PM_max', getPlayerStat($mago, 'PM_max')-5);
            setPlayerStat($alvo, 'PV', (int)$resultado);
            return "<strong>{$alvo}</strong> sobreviveu a <strong>".$dano."</strong> de dano da ".getMagicSpecialName($mago, 'brilho_explosivo')." (Morte Estelar) de <strong>{$mago}</strong>, com <strong>{$resultado}</strong> de PVs. WOW!!!";
        }
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Morte Estelar.";
    }
}


function enxameDeTrovoes($b, $mago, $alvo, $dadoFA1, $dadoFA2, $dadoFD){
    $out = '';
    if (spendPM($mago,4)){
        if (in_array('Magia',listPlayerExtraArmor($alvo))){
            $dano = defaultReactionTreatment($b, $alvo, $mago, 'defender', ($dadoFA1+$dadoFA2), ($dadoFD+resistenciaMagia($alvo)-getPlayerStat($alvo, 'A')), 'PdF', 'Magia');
        } else {
            $dano = max(($dadoFA1+$dadoFA2+getPlayerStat($mago, 'H')) - ($dadoFD+getPlayerStat($alvo, 'H')+resistenciaMagia($alvo)), 0);
        }
        $out .= applyDamage($mago, $alvo, $dano, 'Magico', $out);
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'enxame_de_trovoes')." (Enxame de Trovoes) em <strong>{$alvo}</strong>. -4 PMs; Dano = {$dano}<br>";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Enxame de Trovões.";
    }
}




function nulificacaoTotalDeTalude($mago, $alvo, $RTest){
    $out = '';
    if (spendPM($mago,50)){
        if (statTest($alvo, 'R', getPlayerStat($mago, 'H')-resistenciaMagia($alvo), $RTest)){
            return "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'nulificacao_total_de_talude')." (A Nulificação Total de Talude) para apagar <strong>{$alvo}</strong> da existência, mas não teve Habilidade o suficiente para afetar-lo!!! -50 PMs.<br>";
        }
        $pdo = conecta();
        $tabelasFilhas = [
            'player_advantages',
            'player_disadvantages',
            'player_damage_types',
            'player_vulnerabilities',
            'player_invulnerabilities',
            'player_extra_armor',
            'player_magias'
        ];
        $pdo->beginTransaction();
        $sqlAllies = "DELETE FROM RPG.allies WHERE dono = :nome OR aliado = :nome";
        $stmtAllies = $pdo->prepare($sqlAllies);
        $stmtAllies->execute([':nome' => $alvo]);
        foreach ($tabelasFilhas as $tabela) {
            $sql = "DELETE FROM RPG.{$tabela} WHERE player_name = :nome";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nome' => $alvo]);
        }
        if (statTest($alvo, 'R', 4-resistenciaMagia($alvo), $RTest)){
            $pdo->commit();
            return  "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'nulificacao_total_de_talude')." (A Nulificação Total de Talude) para apagar <strong>{$alvo}</strong> da existência. <strong>{$alvo}</strong> ressistiu, mas perdeu seus poderes... -50 PMs.<br>";
        }
        $constraintOff = "SET foreign_key_checks = 0";
        $sqlPlayer = "DELETE FROM RPG.player WHERE nome = :nome";
        $constraintOn = "SET foreign_key_checks = 1";
        $stmtConstraintOff = $pdo->prepare($constraintOff);
        $stmtPlayer = $pdo->prepare($sqlPlayer);
        $stmtConstraintOn = $pdo->prepare($constraintOn);
        $stmtConstraintOff->execute();
        $stmtPlayer->execute([':nome' => $alvo]);
        $stmtConstraintOn->execute();
        $pdo->commit();
        $key = array_search($alvo, $_SESSION['battle']['order']);
        if ($key !== false) {
            unset($_SESSION['battle']['order'][$key]);
            $_SESSION['battle']['order'] = array_values($_SESSION['battle']['order']);
        }
        $key_players = array_search($alvo, $_SESSION['battle']['players']);
        if ($key_players !== false) {
            unset($_SESSION['battle']['players'][$key_players]);
            $_SESSION['battle']['players'] = array_values($_SESSION['battle']['players']);
        }
        if (isset($_SESSION['battle']['notes'][$alvo])) {
            unset($_SESSION['battle']['notes'][$alvo]);
        }
        if (isset($_SESSION['battle']['orig'][$alvo])) unset($_SESSION['battle']['orig'][$alvo]);
        if (isset($_SESSION['battle']['agarrao'][$alvo])) unset($_SESSION['battle']['agarrao'][$alvo]);

        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'nulificacao_total_de_talude')." (A Nulificação Total de Talude) e apagou <strong>{$alvo}</strong> da existência. -50 PMs.<br>";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para Nulificar alguém!";
    }
}


function bolaDeFogoInstavel($mago, $tgts, $PMs, $dadosFA){
    $out = '';
    if (spendPM($mago,$PMs)){
        $FA = getPlayerStat($mago, 'H');
        foreach ($dadosFA as $dFA){
            $FA += $dFA;
        }
        foreach ($tgts as $tgt){
            $tgtName = $tgt['name'];
            $FD = FD($tgtName, $tgt['dFD'], 'Magia') + resistenciaMagia($tgtName); 
            $dano = max($FA - $FD, 0);
            applyDamage($mago, $tgtName, $dano, 'Magico', $out);
            $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'bola_de_fogo_instavel')." (Bola de Fogo Instável) em <strong>{$tgtName}</strong>. Dano = {$dano}<br>";   
        }
        $out .= "PMs = -{$PMs}";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar essa Bola de Fogo Instável!";
    }
}



function bolaDeFogo($mago, $tgts, $PMs, $dadoFA){
    $out = '';
    if (spendPM($mago,$PMs)){
        $FA = getPlayerStat($mago, 'H') + $PMs + $dadoFA;
        foreach ($tgts as $tgt){
            $tgtName = $tgt['name'];
            $FD = FD($tgtName, $tgt['dFD'], 'Magia') + resistenciaMagia($tgtName); 
            $dano = max($FA - $FD, 0);
            applyDamage($mago, $tgtName, $dano, 'Magico', $out);
            $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'bola_de_fogo')." (Bola de Fogo) em <strong>{$tgtName}</strong>. Dano = {$dano}<br>";   
        }
        $out .= "PMs = -{$PMs}";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar essa Bola de Fogo Instável!";
    }
}
















?>