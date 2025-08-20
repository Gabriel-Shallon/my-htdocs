<?php

include_once './inc/generalFuncs.php';
include_once './inc/battleFuncs.php';

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
            $FA = invulnerabilitieTest($tgtName, 'Magia',$tgt['qtdAtk'] * 2);
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
        $dano = max(invulnerabilitieTest($alvo, 'Magia',$dadosFA) - ($dadoFD+getPlayerStat($alvo, 'H')+resistenciaMagia($alvo)), 0);
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
            monitorPVChange($alvo, getPlayerStat($alvo, 'PV_max'));
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
            $dano = max((invulnerabilitieTest($alvo, 'Magia',$dadoFA1+$dadoFA2+getPlayerStat($mago, 'H'))) - ($dadoFD+getPlayerStat($alvo, 'H')+resistenciaMagia($alvo)), 0);
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
            $dano = max(invulnerabilitieTest($tgtName, 'Magia',$FA) - $FD, 0);
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
            $dano = max(invulnerabilitieTest($tgtName, 'Magia',$FA) - $FD, 0);
            applyDamage($mago, $tgtName, $dano, 'Magico', $out);
            $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'bola_de_fogo')." (Bola de Fogo) em <strong>{$tgtName}</strong>. Dano = {$dano}<br>";   
        }
        $out .= "PMs = -{$PMs}";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar essa Bola de Fogo!";
    }
}


function bolaDeLama($mago, $tgt, $dadosFA, $dadoFD){
    $out = '';
    if (spendPM($mago,1)){
        $FA = 0;
        foreach ($dadosFA as $dadoFA){
            $FA += $dadoFA;
        }
        $FD = getPlayerStat($tgt, 'H') + $dadoFD + resistenciaMagia($tgt); 
        $dano = max(invulnerabilitieTest($tgt, 'Magia',$FA) - $FD, 0);
        applyDamage($mago, $tgt, $dano, 'Magico', $out);
        $_SESSION['battle']['notes'][$tgt]['efeito'] .= "\nMonstruoso: Coberto de lama.";
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'bola_de_lama')." (Bola de Lama) em <strong>{$tgt}</strong>. Dano = {$dano}; PMs = -1<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar essa Bola de Lama!";
    }
}


function bombaDeLuz($mago, $tgts, $PMs){
    $out = '';
    if (spendPM($mago,$PMs)){
        $FA = getPlayerStat($mago, 'H') + $PMs;
        foreach ($tgts as $tgt){
            $tgtName = $tgt['name'];
            $FD = FD($tgtName, $tgt['dFD'], 'Magia') + resistenciaMagia($tgtName); 
            $dano = max(invulnerabilitieTest($tgtName, 'Magia',$FA) - $FD, 0);
            applyDamage($mago, $tgtName, $dano, 'Magico', $out);
            $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'bomba_de_luz')." (Bomba de Luz) em <strong>{$tgtName}</strong>. Dano = {$dano}<br>";   
        }
        $out .= "PMs = -{$PMs}";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar essa Bomba de Luz!";
    }
}


function bombaDeTerra($mago, $tgt, $dFD){
    $out = '';
    if (spendPM($mago,10)){
        $FA = invulnerabilitieTest($tgt, 'Magia', getPlayerStat($mago, 'H') + 15);
        $FD = FD($tgt, $dFD, 'Magia') + resistenciaMagia($tgt); 
        $dano = max($FA - $FD, 0);
        applyDamage($mago, $tgt, $dano, 'Magico', $out);
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'bomba_de_terra')." (Bomba de Terra) em <strong>{$tgt}</strong>. Dano = {$dano}; PMs = -10<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar essa Bomba de Terra!";
    }
}


function solisanguis($mago, $tgt, $dCusto, $dFA1, $dFA2){
    $out = '';
    $custo = floor($dCusto/2)+5;
    if (spendPM($mago, $custo, true)){
        $dano = invulnerabilitieTest($tgt, 'Magia',getPlayerStat($mago, 'H') + $dFA1 + $dFA2) - resistenciaMagia($tgt);
        applyDamage($mago, $tgt, $dano, 'Magico', $out);
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'solisanguis')." (Solisanguis) em <strong>{$tgt}</strong>. Dano = {$dano}; PVs = -{$custo}<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Solisanguis!";
    }
}



function solisanguisRuptura($mago, $tgt, $dCusto, $dFA1, $dFA2, $dFA3, $dFA4){
    $out = '';
    $custo = $dCusto+5;
    if (spendPM($mago, $custo, true)){
        $dano = invulnerabilitieTest($tgt, 'Magia', getPlayerStat($mago, 'H') + $dFA1 + $dFA2 + $dFA3 + $dFA4) - resistenciaMagia($tgt);
        applyDamage($mago, $tgt, $dano, 'Magico', $out);
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'solisanguis_ruptura')." (Solisanguis Ruptura) em <strong>{$tgt}</strong>. Dano = {$dano}; PVs = -{$custo}<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Solisanguis Ruptura!";
    }
}



function solisanguisEvisceratio($mago, $tgt, $d){
    $out = '';
    $custo = $d+getPlayerStat($mago, 'H');
    if (spendPM($mago, $custo, true)){
        $dano = invulnerabilitieTest($tgt, 'Magia',getPlayerStat($mago, 'H') * $d) - resistenciaMagia($tgt);
        applyDamage($mago, $tgt, $dano, 'Magico', $out);
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'solisanguis_ruptura')." (Solisanguis Ruptura) em <strong>{$tgt}</strong>. Dano = {$dano}; PVs = -{$custo}<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Solisanguis Ruptura!";
    }
}



function sortilegium($mago, $tgt, $dC, $dPV1, $dPV2){
    $out = '';
    if (spendPM($mago, $dC, true)){
        setPlayerStat($tgt, 'PV', getPlayerStat($tgt, 'PV') + ($dPV1+$dPV2));
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'sortilegium')." (Sortilegium) em <strong>{$tgt}</strong>. Cura = +".$dPV1+$dPV2."; PVs = -{$dC}<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Sortilegium!";
    }
}



function sanctiSanguis($mago, $tgt, $qtd){
    $out = '';
    if (spendPM($mago, $qtd, true)){
        setPlayerStat($tgt, 'PV', getPlayerStat($tgt, 'PV') + $qtd);
        monitorPVChange($tgt, getPlayerStat($tgt, 'PV') + $qtd);
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'sancti_sanguis')." (Sancti Sanguis) em <strong>{$tgt}</strong>. PVs Tranferidos: {$qtd}<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem essa quantidade de PVs para transferir!";
    }
}



function luxcruentha($mago){
    $out = '';
    if (spendPM($mago, 4, true)){
        $addLuxcru = getPlayerStat($mago, 'equipado')."\nEspada Luxcruentha (FA = H + 2d)(FD/2);";
        setPlayerStat($mago, 'equipado', $addLuxcru);
        $out .= "<strong>{$mago}</strong> usou a magia ".getMagicSpecialName($mago, 'luxcruentha')." (Luxcruentha) para invocar uma Espada de Sangue. PVs: -4<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para invocar a Luxcruentha!";
    }
}
function atkLuxcruentha($b, $mago, $tgt, $def, $dFD, $dFA1, $dFA2){
    $out = '';
    $FA = invulnerabilitieTest($tgt, 'Magia', getPlayerStat($mago, 'H')+$dFA1+$dFA2);
    $FD = 0;
    $H = invisivel_debuff($b, $mago, $tgt, 'F');
    if ($def == 'defender_esquiva' || $def == 'defender_esquiva_deflexao'){
        $bonus = 0;
        if (in_array('aceleracao_i', listPlayerTraits($tgt), true)) {$bonus = 1;};
        if (in_array('aceleracao_ii', listPlayerTraits($tgt), true)) {$bonus = 2;};
        if (in_array('teleporte', listPlayerTraits($tgt), true)) {$bonus = 3;};
        $defH = getPlayerStat($tgt, 'H');
        if ($def == 'defender_esquiva_deflexao') { if (spendPM($tgt, 2)) {$defH *= 2;}}
        $meta = ($defH + $bonus) - $H;
        if ($meta <= 0){
            $FD = FDindefeso($tgt, 'Magia');
            $out .= "<strong>{$tgt}</strong> tentou desviar do ataque de <strong>{$mago}</strong> mas acabou indefeso!";
        }
        if ($meta > 0 && $meta < 6){
            if($dFD <= $meta){
                $FA =  0;
                $out .= "<strong>{$tgt}</strong> conseguiu desviar da Espada de <strong>{$mago}</strong> e saiu ileso!";
            }else{
                $FD = FDindefeso($tgt, 'Magia');
                $out .= "<strong>{$tgt}</strong> tentou desviar da Espada de <strong>{$mago}</strong> mas acabou indefeso!";
            }
        }
        if ($meta>=6){
            $FA = 0;
            $out .= "<strong>{$tgt}</strong> conseguiu desviar da Espada de <strong>{$mago}</strong> e saiu ileso!";
        }
    }
    if ($def == 'indefeso'){
        $FD = FDindefeso($tgt, 'Magia');
        $out .= "<strong>{$mago}</strong> atacou <strong>{$tgt}</strong>(indefeso) usando a Espada Luxcruentha.";
    }
    if ($def == 'defender'){
        $FD = FD($tgt, $dFD, 'Magia');
        $out .= "<strong>{$mago}</strong> atacou <strong>{$tgt}</strong> usando a Espada Luxcruentha.";
    }
    $FD += resistenciaMagia($tgt);
    $dano = max($FA - floor($FD/2), 0);
    $out .= " Dano = ".$dano;
    applyDamage($mago, $tgt, $dano, 'Magico', $out);
    return $out;
}



function artifanguis($mago, $obj, $cost){
    $out = '';
    if (spendPM($mago, $cost, true)){
        $addObj = getPlayerStat($mago, 'equipado')."\n".$obj.";";
        setPlayerStat($mago, 'equipado', $addObj);
        $out .= "<strong>{$mago}</strong> usou a magia ".getMagicSpecialName($mago, 'artifanguis')." (Artifanguis) para invocar um objeto ({$obj}). PVs: -{$cost}<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente ou não há sangue para invocar um objeto!";
    }
}



function excruentio($mago, $tgt, $dFD, $dFA1, $dFA2){
    $out = '';
    if (spendPM($mago,2, true)){
        $FA = invulnerabilitieTest($tgt, 'Magia', getPlayerStat($mago, 'H') + $dFA1 + $dFA2);
        $FD = FD($tgt, $dFD, 'Magia') + resistenciaMagia($tgt);
        $dano = max($FA - $FD, 0);
        if ($dano > 0){
            if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])){
                $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Excruentio(".$tgt.");\n";
            } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
                $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Excruentio(".$tgt.");\n"; 
            }
        }
        applyDamage($mago, $tgt, $dano, 'Magico', $out);
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'excruentio')." (Excruentio) em <strong>{$tgt}</strong>. Dano = {$dano}; PMs = -2<br>";   
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Excruentio!";
    }
}




function speculusanguis($mago, $tgt){
    $out = '';
    if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])){
        $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Speculusanguis(".$tgt.");\n";
    } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
        $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Speculusanguis(".$tgt.");\n"; 
    }
    $_SESSION['battle']['sustained_effects'][$mago]['speculusanguis']['dmg'] = 0;
    $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'speculusanguis')." (Speculusanguis) em <strong>{$tgt}</strong>.<br>";   
    return $out;
}


function visExVulnere($mago){
    $out = '';
    if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])){
        $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Vis Ex Vulnere;\n";
    } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
        $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Vis Ex Vulnere;\n"; 
    }
    $_SESSION['battle']['sustained_effects'][$mago]['visExVulnere']['dmg'] = 0;
    $_SESSION['battle']['sustained_effects'][$mago]['visExVulnere']['pms'] = 0;
    $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'vis_ex_vulnere')." (Vis Ex Vulnere).<br>";   
    return $out;
}


function solcruoris($mago, $cost){
    $out = '';
    if (spendPM($mago, $cost, true)){
        if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])){
            $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Solcruoris;\n";
        } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
            $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Solcruoris;\n"; 
        }
        $out .= "<strong>{$mago}</strong> usou ".getMagicSpecialName($mago, 'vis_ex_vulnere')." (Vis Ex Vulnere).<br>";
        return '';
    } else {
        return '';    
    }   
}


?>