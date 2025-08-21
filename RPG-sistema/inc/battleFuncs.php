<?php

include_once 'generalFuncs.php';
include_once 'traitFuncs.php';

    function iniciativa(array $lutadores, array $dados): array {
        $inicList = [];
        foreach ($lutadores as $idx => $nome) {
            $H = (int) getPlayerStat($nome, 'H');
            $traits = listPlayerTraits($nome);
            if (in_array('teleporte', $traits, true)) {
                $bonus = 2;
            } elseif (in_array('aceleracao', $traits, true)) {
                $bonus = 1;
            } else {
                $bonus = 0;
            }
            $dado = isset($dados[$idx]) ? (int) $dados[$idx] : 0;
            $total = $H + $dado + $bonus;
            $inicList[] = [
                'nome'       => $nome,
                'total'      => $total,
                'habilidade' => $H,
                'indice'     => $idx,
                'dado'       => $dado,
                'bonus'      => $bonus
            ];
        }
        usort($inicList, function($a, $b) {
            if ($a['total'] !== $b['total']) {
                return $b['total'] <=> $a['total'];
            }
            if ($a['habilidade'] !== $b['habilidade']) {
                return $b['habilidade'] <=> $a['habilidade'];
            }
            return $a['indice'] <=> $b['indice'];
        });
        return $inicList;
    }


    function statTest($player, $stat, $diff, $dado){
        $meta = getPlayerStat($player, $stat) - $diff;
        if ($dado > $meta || $dado == 6){
            return false;
        }
        if ($dado <= $meta){
            return true;
        }
    }

    
    function FA (string $atacante, string $atkType, int $dado, $H){
        return getPlayerStat($atacante, $atkType) + $H + $dado + applyCrit($atacante, $atkType, $dado);
    }


    function FD (string $defensor, int $dado, $dmgType){
        return vulnerabilitieExtraArmorTest($defensor, $dmgType) + getPlayerStat($defensor, 'H') + $dado + applyCrit($defensor, 'A', $dado);
    }


    function FDindefeso (string $defensor, $dmgType){
        return vulnerabilitieExtraArmorTest($defensor, $dmgType);
    }
    
    function FAmulti(string $atacante, int $quant, string $atkType, array $dados, $H) {
        $maxExtras  = intdiv(max($H, 0), 2);
        $quant = min($quant, $maxExtras + 1);
    
        $effH = max($H - ($quant * 2)+2, 0);
    
        $baseFA = getPlayerStat($atacante, $atkType) + $effH;
    
        $totalFA = 0;
        $count = min($quant, count($dados));
        for ($i = 0; $i < $count; $i++) {
            $totalFA += ($baseFA + (int)$dados[$i]) + applyCrit($atacante, $atkType, $dados[$i]);
        }
    
        return $totalFA;
    }
    




    function FAFDresult(string $atacante, string $defensor, int $dadoFA, int $dadoFD, string $atkType, string $dmgType, $H){
        return max(invulnerabilitieTest($defensor, $dmgType,FA($atacante, $atkType, $dadoFA, $H)) - FD($defensor,$dadoFD, $dmgType),0);
    }


    
    function FAFDindefeso(string $atacante, string $defensor, int $dadoFA, string $atkType, string $dmgType, $H){  
        return max(invulnerabilitieTest($defensor, $dmgType,FA($atacante, $atkType, $dadoFA, $H)) - FDindefeso($defensor, $dmgType),0);
    }




    function FAFDesquiva(string $atacante, string $defensor, int $dadoFD, int $dadoFA, string $atkType, $dmgType, $H, $deflex = false){
        $bonus = 0;
        if (in_array('aceleracao_i', listPlayerTraits($defensor), true)) {$bonus = 1;};
        if (in_array('aceleracao_ii', listPlayerTraits($defensor), true)) {$bonus = 2;};
        if (in_array('teleporte', listPlayerTraits($defensor), true)) {$bonus = 3;};
        
        $defH = getPlayerStat($defensor, 'H');
        if ($deflex) { if (spendPM($defensor, 2)) {$defH *= 2;}}
        $meta = ($defH + $bonus) - $H;
        if ($meta <= 0){
            return FAFDindefeso($atacante, $defensor, $dadoFA, $atkType, $dmgType, $H);
        }
        if ($meta > 0 && $meta < 6){
            if($dadoFD <= $meta){
                return 0;
            }else{
                return FAFDindefeso($atacante, $defensor, $dadoFA, $atkType, $dmgType, $H);
            }
        }
        if ($meta>=6){
            return 0;
        }
    }

    function esquivaMulti(string $defensor, int $dado, $H, $deflex = false) {
        $bonus = 0;
        if (in_array('aceleracao_i', listPlayerTraits($defensor), true)) {$bonus = 1;};
        if (in_array('aceleracao_ii', listPlayerTraits($defensor), true)) {$bonus = 2;};
        if (in_array('teleporte', listPlayerTraits($defensor), true)) {$bonus = 3;};
        
        $defH = getPlayerStat($defensor, 'H');
        if ($deflex) { if (spendPM($defensor, 2)) {$defH *= 2;}}
        $meta = ($defH + $bonus) - $H;
        if ($meta <= 0) {
            return 'defender_esquiva_fail';
        }
        if ($dado <= $meta) {
            return 'defender_esquiva_success';
        } else {
        return 'defender_esquiva_fail';
        }
    }








    function defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $tipo, $dmgType){
        if (!empty($b['agarrao'][$tgt]['agarrado'])) {
            $def = 'indefeso';
        }
        $H = invisivel_debuff($b, $pl, $tgt, $tipo);
        if ($def === 'indefeso') {
            return FAFDindefeso($pl, $tgt, $dFA, $tipo, $dmgType, $H);
        } else if ($def === 'defender_esquiva') {
            return FAFDesquiva($pl, $tgt, $dFD, $dFA, $tipo, $dmgType, $H);
        } else if ($def === 'defender_esquiva_deflexao'){
            return FAFDesquiva($pl, $tgt, $dFD, $dFA, $tipo, $dmgType, $H, true);
        } else {
            return FAFDresult($pl, $tgt, $dFA, $dFD, $tipo, $dmgType, $H);
        }
    }

    function atkMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dFD, $def, $tipo, $dmgType){
        $H = invisivel_debuff($b, $pl, $tgt, $tipo);
        $faTot = FAmulti($pl, $q, $tipo, $dados, $H);
        if (!empty($b['agarrao'][$tgt]['agarrado'])) {
            $def = 'indefeso';
        }
        if ($def === 'indefeso') {
            return max(invulnerabilitieTest($tgt, $dmgType, $faTot) - FD($tgt, $dFD, $dmgType), 0);
        } else if ($def === 'defender_esquiva' || $def === 'defender_esquiva_deflexao' ) {
            if ($def == 'defender_esquiva_deflexao') 
                 {$resEsq = esquivaMulti($tgt, $dFD, $H, true);} 
            else {$resEsq = esquivaMulti($tgt, $dFD, $H);}
            if ($resEsq === 'defender_esquiva_success') {
                return 0;
            } else {
                return max(invulnerabilitieTest($tgt, $dmgType, $faTot) - FDindefeso($tgt, $dmgType), 0);
            }
        } else {
            return max(invulnerabilitieTest($tgt, $dmgType, $faTot) - FD($tgt, $dFD, $dmgType), 0);
        }
    }








    function extraArmorTest($player, $dmgType){
        if (in_array($dmgType, listPlayerExtraArmor($player))){
            return getPlayerStat($player, 'A')*2;
        } else {
            return getPlayerStat($player, 'A');
        }
    }

    function invulnerabilitieTest($player, $dmgType, $FA){
        if (in_array($dmgType, listPlayerInvulnerabilities($player))){
            $FA = $FA/10;
            return floor($FA);
        } else {
            return $FA;
        }
    }

    function vulnerabilitieTest($player, $dmgType){
        if (in_array($dmgType, listPlayerVulnerabilities($player))){
            return 0;
        } else {
            return getPlayerStat($player, 'A');
        }
    }


    function vulnerabilitieExtraArmorTest($player, $dmgType){
        $hasVulnerabilitie = false;
        $hasExtraArmor = false;
        foreach (listPlayerVulnerabilities($player) as $type){
            if ($type == $dmgType){
                $hasVulnerabilitie = true;
            }
        }
        foreach (listPlayerExtraArmor($player) as $type){
            if ($type == $dmgType){
                $hasExtraArmor = true;
            }
        }

        if (($hasExtraArmor && $hasVulnerabilitie) or (!$hasExtraArmor && !$hasVulnerabilitie)){
            return getPlayerStat($player, 'A');
        } 
        if ($hasExtraArmor && !$hasVulnerabilitie) {
            return getPlayerStat($player, 'A')*2;
        } 
        if (!$hasExtraArmor && $hasVulnerabilitie){
            return 0; 
        }
    }







    function spendPM(string $player, int $cost, $sangue = false, $ignoreDiscount = false): bool {
        $discount = 0; 
        if (!$ignoreDiscount){
            if (!empty($_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms'])){
                $discount += min($_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms'], $cost);
                if ($cost < $_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms']){
                    $_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms'] -= $cost;
                } else {
                    $_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms'] = 0;
                }
            }
        }
        $cost -= itemDePoder($player) + $discount; if ($cost < 0){$cost = 0;}
        if ($cost <= 0) {
            return true;
        }
        $pm   = getPlayerStat($player, 'PM');
        $pv   = getPlayerStat($player, 'PV');
        $traits = listPlayerTraits($player);
        $hasEV = in_array('energia_vital', $traits, true);
        $usePv = $_SESSION['battle']['notes'][$player]['use_pv'] ?? false;
        
        if ($usePv || $sangue ) {
            $ratio = in_array('magia_de_sangue', $traits, true) ? 1 : 2;
            $pvNeeded = $cost * $ratio;
            if ($pv >= $pvNeeded) {
                setPlayerStat($player, 'PV', $pv - $pvNeeded);
                monitorPVChange($player, $pvNeeded);
                return true;
            }
            return false;
        }
        if ($pm >= $cost) {
            setPlayerStat($player, 'PM', $pm - $cost);
            return true;
        }
        if (! $hasEV) {
            return false;
        }
        $remaining = $cost - $pm;
        setPlayerStat($player, 'PM', 0);
        $ratio = in_array('magia_de_sangue', $traits, true) ? 1 : 2;
        $pvNeeded = $remaining * $ratio;
        if ($pv >= $pvNeeded) {
            setPlayerStat($player, 'PV', $pv - $pvNeeded);
            monitorPVChange($player, $pvNeeded);
            return true;
        }
        return false;
    }



    function applyCrit($pl, $critType, $dado){
        if($dado == 6){
            return getPlayerStat($pl, $critType);
        } else {
            return 0;
        }
    }

    function applyDamage(string $pl, string $tgt, int $dano, string $tipoAtk, string &$out = ''){
        if (!empty($_SESSION['battle']['notes'][$tgt]['incorp_active']) && in_array($tipoAtk, ['F','PdF'], true) && empty($_SESSION['battle']['notes'][$pl]['incorp_active'])) {
            $dano = 0;
            $out .= " (inútil: alvo incorpóreo)";
        } else {
            $ligacaoNatural = false;
            if (!empty(getAlliePlayer($tgt)) && in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($tgt)), true)){       
                setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
                setPlayerStat(getAlliePlayer($tgt), 'PV', max(getPlayerStat(getAlliePlayer($tgt),'PV') - $dano, 0));
                $ligacaoNatural = true;
            } 
            if (!empty($_SESSION['battle']['playingPartner'][$tgt]) && $ligacaoNatural == false){ 
                $distriDano = ceil($dano/2);
                setPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['owner'], 'PV', max(getPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['owner'],'PV') - $distriDano, 0));
                setPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['name'], 'PV', max(getPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['name'],'PV') - $distriDano, 0));
            } else {
                setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
            }
            $ligacaoNatural = false;
            monitorPVChange($tgt, $dano);
        }
       return $out; 
    }




    function parseBuffs(string $equipString): array {
        $pattern = '/\b(F|H|R|A|PdF)([+-])(\d+)\b/';
        $buffs = [];
        if (preg_match_all($pattern, $equipString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                [$full, $stat, $sign, $val] = $m;
                $delta = (int)$val * ($sign === '+' ? +1 : -1);
                $buffs[$stat] = ($buffs[$stat] ?? 0) + $delta;
            }
        }
        return $buffs;
    }


    function syncEquipBuffs($pl){
        // 1) Leitura
        $equipString = getPlayerStat($pl, 'equipado');
        $currentBuffs = parseBuffs($equipString);
        // 2) Guarda referência a notes
        if (! isset($_SESSION['battle']['notes'][$pl]['buffs'])) {
            $_SESSION['battle']['notes'][$pl]['buffs'] = [];
        }
        $oldBuffs = $_SESSION['battle']['notes'][$pl]['buffs'];

        // 3) Aplicar buffs novos ou maiores
        foreach ($currentBuffs as $stat => $newDelta) {
            $oldDelta = $oldBuffs[$stat] ?? 0;
            if ($newDelta !== $oldDelta) {
                $diff = $newDelta - $oldDelta; // se new > old, diff é positivo
                $base = (int) $_SESSION['battle']['orig'][$pl][$stat];
                setPlayerStat($pl, $stat, $base + $diff);
            }
        }

        // 4) Reverter buffs que sumiram (ou reduzir valores)
        foreach ($oldBuffs as $stat => $oldDelta) {
            $newDelta = $currentBuffs[$stat] ?? 0;
            if ($oldDelta !== $newDelta) {
                // diferença negativa: tirar do stat
                $diff = $newDelta - $oldDelta; // se new < old, diff é negativo
                $base  = (int) getPlayerStat($pl, $stat);
                setPlayerStat($pl, $stat, $base + $diff);
            }
        }

        // 5) Atualiza notes com o estado atual
        $_SESSION['battle']['notes'][$pl]['buffs'] = $currentBuffs;
    }

    


    function removeEffect(string $efeito, array $remover): string {
        $linhas = explode("\n", $efeito);

        $linhasFiltradas = array_filter($linhas, function($linha) use ($remover) {
            return ! in_array(trim($linha), $remover, true);
        });

        return implode("\n", $linhasFiltradas);
    }
  



    function selectTarget($cur, $validTargets) {
        foreach ($validTargets as $tgt) if ($tgt !== $cur) {
            $isFuria    = ! empty($_SESSION['battle']['notes'][$tgt]['furia']);
            $isAgarrado = ! empty($_SESSION['battle']['agarrao'][$tgt]['agarrado']);
            $hasDeflexao = in_array('deflexao', listPlayerTraits($tgt), true);
            echo '<option value="'.htmlspecialchars($tgt).'" '
            .'data-furia="'.($isFuria? '1' : '0').'" '
            .'data-agarrao="'.($isAgarrado? '1' : '0').'" '
            .'data-tem-deflexao="'.($hasDeflexao? '1' : '0').'">'
            .htmlspecialchars($tgt).'</option>';                              
        }
    }

    function selectDmgType($cur){
        $knownTypes = listPlayerDmgTypes($cur);
        $allTypes = getAllDmgTypes();
        $unknownTypes = array_diff($allTypes, $knownTypes);
        if (!empty($knownTypes)) {
            echo '<optgroup label="Conhecidos">';
            foreach ($knownTypes as $type) {
                echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($type) . '</option>';
            }
            echo '</optgroup>';
        }
        if (!empty($unknownTypes)) {
            echo '<optgroup label="Desconhecidos">';
            foreach ($unknownTypes as $type) {
                echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($type) . '</option>';
            }
            echo '</optgroup>';
        }
    }

