<?php
    include_once 'generalFuncs.php';
    include_once 'battleFuncs.php';

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


    function FAtiroMultiplo(string $atacante, int $quant, array $dados, string $tgt, string $defesa, int $dadoFD, $dmgType): int {
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
                $FDval = FDindefeso($tgt, $dmgType);
            } else if ($defesa === 'defender_esquiva_success') {
               continue;
            } else {
               $FDval = FD($tgt, $dadoFD, $dmgType);
            }
            $danoTotal += max(invulnerabilitieTest($tgt, $dmgType, $FA) - $FDval, 0);
        }
        return $danoTotal;
    }



    function tiroMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dadoFD, $def, $dmgType){
        if (!empty($b['agarrao'][$tgt]['agarrado'])) {
            $def = 'indefeso';
        }
        if ($def === 'defender_esquiva') {
            $resultadoEsq = esquivaMulti($pl, $tgt, $dadoFD);
            if ($resultadoEsq === 'defender_esquiva_success') {
                return 0;
            } else {
                return FAtiroMultiplo($pl, $q, $dados, $tgt, 'indefeso', $dadoFD, $dmgType);
            }
        } else {
            $tipoDef = $def === 'indefeso' ? 'indefeso' : 'defender';
            return FAtiroMultiplo($pl, $q, $dados, $tgt, $tipoDef, $dadoFD, $dmgType);
        }
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
            addPlayerInvulnerability($player, 'Fogo');
            addPlayerVulnerability($player, 'Sônico');
            addPlayerVulnerability($player, 'Elétrico');
            setPlayerStat($player, 'F', getPlayerStat($player, 'F')+getPlayerStat($player, 'PdF')*2);
            setPlayerStat($player, 'PdF', 0);
        } else {
            removePlayerInvulnerability($player, 'Fogo');
            removePlayerVulnerability($player, 'Sônico');
            removePlayerVulnerability($player, 'Elétrico');
            setPlayerStat($player, 'F', (getPlayerStat($player, 'F')-$PdFOriginal*2));
            setPlayerStat($player, 'PdF', $PdFOriginal);
        }
    }


    function energiaExtra ($player){
        setPlayerStat($player, 'PV', getPlayerStat($player, 'PV_max'));
    }


    function getPlayerAllies(string $player){
        $pdo = conecta();
        $stmt = $pdo->prepare("SELECT aliado FROM RPG.allies WHERE dono = :dono");
        $stmt->execute(['dono' => $player]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    function getAlliePlayer(string $aliado) {
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
    function getPartnerOwner(string $partner) {
        $pdo = conecta();
        $sql = "SELECT a.dono FROM `RPG`.`allies` AS a JOIN `RPG`.`player_advantages` AS pa ON pa.player_name = a.dono JOIN `RPG`.`advantages` AS adv ON adv.id = pa.advantage_id AND adv.name = 'Parceiro' WHERE a.aliado = :aliado";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['aliado' => $partner]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }


    function agarrao(string $player, string $alvo, int $dF){
        $result = ($dF + getPlayerStat($player, 'F')) - getPlayerStat($alvo, 'F');

        if ($result <= 0){
            return false;
        }
        if ($result > 0){
            return true;
        }
    }

    function debilitateStat($alvo, $stat, $dado){
        if ($dado > getPlayerStat($alvo, 'A') || $dado == 6){
            setPlayerStat($alvo, $stat, getPlayerStat($alvo, $stat)-1);
            return $stat.' de '.$alvo.' -1';
        }{
            return $alvo.' não foi debilitado.';
        }
    }

?>