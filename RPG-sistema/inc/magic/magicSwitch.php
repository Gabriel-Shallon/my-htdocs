<?php
include_once 'magicActFuncs.php';

function magicSwitch($postData, &$b, $pl, $magic_slug, $target){
    switch ($magic_slug) {
        case 'bola_de_fogo':
            $tgts = $postData['magic_targets'] ?? ['']; // Info do alvo: name + dFD
            $PMs = $postData['magic_pm_cost'] ?? [''];
            $dadoFA = $postData['dadoFA'] ?? [''];

            $out = bolaDeFogo($pl, $tgts, $PMs, $dadoFA);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'cura_magica':
            $heal_type = $postData['magic_heal_type'] ?? 'heal_pv';
            $cost = ($heal_type === 'heal_pv') ? 2 : 4;
            $out = "<strong>{$pl}</strong> usou Cura Mágica em <strong>{$target}</strong> (custo: {$cost} PMs).";
            break;

        case 'ataque_magico':
            $pmCost = $postData['magic_pm_cost'] ?? 1;
            $atkType = $postData['magic_attack_type'] ?? 'F';
            $tgtsInfo = $postData['magic_targets'] ?? ['']; //alvos [name] + reação [reaction] + dado de defesa [rollFD] + dado de ataque [rollFA]

            $out = ataqueMagico($b, $pl, $tgtsInfo, $pmCost, $atkType);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'lanca_infalivel_de_talude':
            $pmCost = $postData['magic_pm_cost'] ?? 1;
            $tgtsInfo = $postData['magic_targets'] ?? ['']; //alvos [name] + lancas atacando ele [qtdAtk]

            $out = lancaInfalivelDeTalude($pl, $tgtsInfo, $pmCost);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'brilho_explosivo':
            $tgt = $postData['magic_target'] ?? [''];
            $dadoFD = $postData['dadoFD'] ?? [''];
            $def = $postData['magic_def'];

            $somaDosDadosFA = 0;
            for ($i = 1; $i < 11; $i++) {
                $somaDosDadosFA += $postData['dado' . $i] ?? [0];
            }

            $out = brilhoExplosivo($pl, $tgt, $somaDosDadosFA, $dadoFD, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'morte_estelar':
            $tgt = $postData['magic_target'] ?? [''];

            $out = morteEstelar($pl, $tgt);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'enxame_de_trovoes':
            $tgt = $postData['magic_target'] ?? [''];
            $dFA1 = $postData['dadoFA1'] ?? [''];
            $dFA2 = $postData['dadoFA2'] ?? [''];
            $dFD = $postData['dadoFD'] ?? [''];
            $def = $postData['magic_def'];

            $out = enxameDeTrovoes($b, $pl, $tgt, $dFA1, $dFA2, $dFD, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'nulificacao_total_de_talude':
            $tgt = $postData['magic_target'] ?? [''];
            $RTest = $postData['RTest'] ?? [''];

            $out = nulificacaoTotalDeTalude($pl, $tgt, $RTest);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'bola_de_fogo_instavel':
            $tgts = $postData['magic_targets'] ?? ['']; // Info do alvo: name + dFD + def
            $PMs = $postData['magic_pm_cost'] ?? [''];
            $dadosFA = $postData['dados'] ?? [''];

            $out = bolaDeFogoInstavel($pl, $tgts, $PMs, $dadosFA);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'bola_de_lama':
            $tgt = $postData['magic_target'] ?? [''];
            $dadosFA = $postData['dadosFA'] ?? [''];
            $dadoFD = $postData['dadoFD'] ?? [''];
            $def = $postData['magic_def'] ?? [''];

            $out = bolaDeLama($pl, $tgt, $dadosFA, $dadoFD, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'bomba_de_luz':
            $tgts = $postData['magic_targets'] ?? [''];  // Info do alvo: name + dFD + def
            $PMs = $postData['magic_pm_cost'] ?? [''];

            $out = bombaDeLuz($pl, $tgts, $PMs);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'bomba_de_terra':
            $tgt = $postData['magic_target'] ?? [''];
            $dadoFD = $postData['dadoFD'] ?? [''];
            $def = $postData['magic_def'] ?? [''];

            $out = bombaDeTerra($pl, $tgt, $dadoFD, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'solisanguis':
            $tgt = $postData['magic_target'] ?? [''];
            $dadoCusto = $postData['dadoCusto'] ?? [''];
            $dadoFA1 = $postData['dadoFA1'] ?? [''];
            $dadoFA2 = $postData['dadoFA2'] ?? [''];

            $out = solisanguis($pl, $tgt, $dadoCusto, $dadoFA1, $dadoFA2);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'solisanguis_ruptura':
            $tgt = $postData['magic_target'] ?? [''];
            $dadoCusto = $postData['dadoCusto'] ?? [''];
            $dadoFA1 = $postData['dadoFA1'] ?? [''];
            $dadoFA2 = $postData['dadoFA2'] ?? [''];
            $dadoFA3 = $postData['dadoFA3'] ?? [''];
            $dadoFA4 = $postData['dadoFA4'] ?? [''];

            $out = solisanguisRuptura($pl, $tgt, $dadoCusto, $dadoFA1, $dadoFA2, $dadoFA3, $dadoFA4);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;


        case 'solisanguis_evisceratio':
            $tgt = $postData['magic_target'] ?? [''];
            $dado = $postData['dado'] ?? [''];

            $out = solisanguisEvisceratio($pl, $tgt, $dado);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'sortilegium':
            $tgt = $postData['magic_target'] ?? [''];
            $dadoCusto = $postData['dadoCusto'] ?? [''];
            $dadoPV1 = $postData['dadoPV1'] ?? [''];
            $dadoPV2 = $postData['dadoPV2'] ?? [''];

            $out = sortilegium($pl, $tgt, $dadoCusto, $dadoPV1, $dadoPV2);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'sancti_sanguis':
            $tgt = $postData['magic_target'] ?? [''];
            $qtd = $postData['qtd'] ?? [''];

            $out = sanctiSanguis($pl, $tgt, $qtd);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'luxcruentha':
            $out = luxcruentha($pl);
            break;

        case 'artifanguis':
            $obj = $postData['obj'] ?? [''];
            $cost = $postData['cost'] ?? [''];

            $out = artifanguis($pl, $obj, $cost);
            break;

        case 'excruentio':
            $tgt = $postData['magic_target'] ?? [''];
            $dFD = $postData['dadoFD'] ?? [''];
            $dFA1 = $postData['dadoFA1'] ?? [''];
            $dFA2 = $postData['dadoFA2'] ?? [''];
            $def = $postData['magic_def'] ?? [''];

            $out = excruentio($pl, $tgt, $dFD, $dFA1, $dFA2, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;


        case 'speculusanguis':
            $tgt = $postData['magic_target'] ?? [''];

            $out = speculusanguis($pl, $tgt);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;


        case 'vis_ex_vulnere':
            $out = visExVulnere($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'solcruoris':
            $cost = $postData['cost'] ?? [''];

            $out = solcruoris($pl, $cost);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'spectraematum':
            $debuff = $postData['debuff'] ?? [''];
            $tgt = $postData['magic_target'] ?? [''];

            $out = spectraematum($pl, $debuff, $tgt);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'aeternum_tribuo':
            $tgt = $postData['magic_target'] ?? [''];

            $out = aeternumTribuo($pl, $tgt);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'inhaerescorpus':
            $tgt = $postData['magic_target'] ?? [''];
            $testR = $postData['testR'] ?? [''];

            $out = inhaerescorpus($pl, $tgt, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'hemeopsia':
            $out = hemeopsia($pl);
            break;

        case 'cegueira':
            $tgt = $postData['magic_target'] ?? [''];
            $testR = $postData['testR'] ?? [''];

            $out = cegueira($pl, $tgt, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'amor_incontestavel':
            $tgt = $postData['magic_target'] ?? [''];
            $tgtLove = $postData['magic_love'] ?? [''];
            $testR = $postData['testR'] ?? [''];

            $out = amorIncontestavel($pl, $tgt, $tgtLove, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'ataque_vorpal':
            $tgt = $postData['magic_target'] ?? [''];

            $out = ataqueVorpal($pl, $tgt);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;
        
        case 'cura_para_o_mal':
            $tgt = $postData['magic_target'] ?? [''];
            $evilCureMode = $postData['evil_cure_mode'] ?? [''];

            $out = curaParaOMal($pl, $tgt, $evilCureMode);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'desmaio':
            $tgt = $postData['magic_target'] ?? [''];
            $cost = $postData['cost'] ?? [''];
            $testR = $postData['testR'] ?? [''];

            $out = desmaio($pl, $tgt, $cost, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'destrancar':
            $out = destrancar($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'escapatoria_de_valkaria':
            $qtd = $postData['qtdAlly'] ?? [''];

            $out = aEscapatoriaDeValkaria($pl, $qtd);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'fada_servil':
            $out = fadaServil($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'farejar_tesouro':
            $out = farejarTesouro($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'flor_perene_de_milady_a':
            $tgt = $postData['magic_target'] ?? [''];
            $testR = $postData['testR'] ?? [''];

            $out = florPereneDeMiladyA($pl, $tgt, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        default:
            $out = "A magia '{$magic_slug}' foi selecionada, mas sua lógica ainda não foi implementada.";
            unset($b['playingAlly']);
            $b['init_index']++;
            break;
    }
    return $out;
}   