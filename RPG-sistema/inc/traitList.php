<?php

// traitList.php

require_once 'func.php';

function normalizeKey(string $s): string {
    // remove acentos
    $s = iconv('UTF-8','ASCII//TRANSLIT',$s);
    // minusculas e underlines
    return strtolower(str_replace(' ','_',$s));
}

// Retorna lista de chaves de traits (ex: ['assombrado','regeneracao'])
function listPlayerTraits(string $player): array {
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
        $res[] = normalizeKey($n);
    }
    // desvantagens
    $stmt = $pdo->prepare("
      SELECT d.name FROM RPG.disadvantages d
      JOIN RPG.player_disadvantages pd ON pd.disadvantage_id = d.id
      WHERE pd.player_name = ?
    ");
    $stmt->execute([$player]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $n) {
        $res[] = strtolower(str_replace(' ','_', $n));
    }
    return $res;
}

function addPlayerTrait(string $player, int $traitId, string $type): bool {
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

function removePlayerTrait(string $player, int $traitId, string $type): bool {
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





// Efeitos:



//DESVANTAGENS//

function assombrado(string $player, array $input): ?string {
    $d = intval($input['roll_assombrado'] ?? 0);
    if ($d >= 4 && $d <= 6) {
        foreach (['F','H','R','A','PdF'] as $s) {
            setPlayerStat($player, $s, max(0, getPlayerStat($player,$s)-1));
        }
        return 'Assombrado(-1 F,H,R,A,PdF)';
    }
    return null;
}

function muggles(string $player){
    //atualizar quando houver magias
}





//VANTAGENS//

function invulnerabilidadeFogo(){
    //atualizar quando tivermos tipos de dano
}

function PMextra(string $player){
    setPlayerStat($player, 'PM_max', (getPlayerStat($player, 'PM_max')+10));
    setPlayerStat($player, 'PM', (getPlayerStat($player, 'PM')+10));
    return $player.' ganhou +10 PMs';
}; function deapplyPMextra(string $player){
    setPlayerStat($player, 'PM_max', (getPlayerStat($player, 'PM_max')-10));
    return $player.' perdeu 10 PMs';
};


function PVextra(string $player){
    setPlayerStat($player, 'PV_max', (getPlayerStat($player, 'PV_max')+10));
    setPlayerStat($player, 'PV', (getPlayerStat($player, 'PV')+10));
    return $player.' ganhou +10 PMs';
}; function deapplyPVextra(string $player){
    setPlayerStat($player, 'PV', (getPlayerStat($player, 'PV')-10));
    return $player.' perdeu 10 PMs';
};


function FAtiroMultiplo(string $atacante, int $quant, array $dados, string $tgt, string $defesa, int $dadoFD): int {
    $H      = (int) getPlayerStat($atacante, 'H');
    $PdF    = (int) getPlayerStat($atacante, 'PdF');
    $maxT   = max($H, 0);
    $q      = min($quant, $maxT);
    if (!spendPM($atacante, $q)) {
        throw new Exception("PM/PVs insuficientes para {$q} tiros.");
    }
    $danoTotal = 0;
    for ($i = 0; $i < $q; $i++) {
        $rollFA = isset($dados[$i]) ? (int)$dados[$i] : 0;
        $FA     = $PdF + $H + $rollFA;

        if ($defesa === 'indefeso') {
            $FDval = FDindefeso($tgt);
        } else if ($defesa === 'defender_esquiva_success') {
           continue;
        } else {
           $FDval = FD($tgt, $dadoFD);
        }
        $danoTotal += max($FA - $FDval, 0);
    }
    return $danoTotal;
}



function draconificacao(string $player, bool $active){
    if ($active) {
        setPlayerStat($player,'PdF', getPlayerStat($player,'PdF')+1);
        setPlayerStat($player,'R',   getPlayerStat($player,'R')+1);
        setPlayerStat($player,'H',   getPlayerStat($player,'H')+2);
    } else {
        setPlayerStat($player,'PdF', getPlayerStat($player,'PdF')-1);
        setPlayerStat($player,'R',   getPlayerStat($player,'R')-1);
        setPlayerStat($player,'H',   getPlayerStat($player,'H')-2);
    }
}


function fusaoEterna(string $player, int $PdFOriginal, bool $active){
    if($active==true){
        if (! spendPM($player, 3)) {
            throw new Exception("PM/PVs insuficientes para Fusão Eterna.");
        }
        setPlayerStat($player, 'F', getPlayerStat($player, 'F')+getPlayerStat($player, 'PdF')*2);
        setPlayerStat($player, 'PdF', 0);
    } else {
        setPlayerStat($player, 'F', (getPlayerStat($player, 'F')-$PdFOriginal*2));
        setPlayerStat($player, 'PdF', $PdFOriginal);
    }
}


function energiaExtra ($player){
    setPlayerStat($player, 'PV', getPlayerStat($player, 'PV_max'));
}




?>