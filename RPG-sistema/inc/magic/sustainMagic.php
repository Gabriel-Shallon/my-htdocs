<?php

$catalogoMagiasSustentadas = [
    'speculusanguis' => [
        'nome' => 'Speculusanguis',
        'parametros' => ['alvo'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainSpeculusanguis',
        'custo_texto' => '2 PVs'
    ],
    'protecao_magica_superior' => [
        'nome' => 'Proteção Mágica Superior',
        'parametros' => ['custo_pms'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainProtecaoMagica',
        'custo_texto' => '1 a 5 PMs'
    ],
    'criar_vento' => [
        'nome' => 'Criar Vento',
        'parametros' => ['custo_pms'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainCriarVento',
        'custo_texto' => '1 a 5 PMs'
    ],
    'excruentio' => [
        'nome' => 'Excruentio',
        'parametros' => ['alvo'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainExcruentio',
        'custo_texto' => '1 PV'
    ],
    'oracao_de_combate' => [
        'nome' => 'Oração de Combate',
        'parametros' => ['bonus_max'],
        'precisa_input' => true,
        'funcao_gerar_form' => 'getFormOracaoCombate',
        'funcao_aplicar' => 'applySustainOracaoCombate'
    ],
    'vis_ex_vulnere' => [
        'nome' => 'Vis Ex Vulnere',
        'parametros' => [],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainVisExVulnere',
        'custo_texto' => '2 PVs'
    ],    
    'solcruoris' => [
        'nome' => 'Solcruoris',
        'parametros' => ['custo'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainSolcruoris',
        'custo_texto' => 'Custo único'
    ],
    'spectraematum' => [
        'nome' => 'Spectraematum',
        'parametros' => ['alvo'],
        'precisa_input' => true,
        'funcao_gerar_form' => 'getFormSpectraematum',
        'funcao_aplicar' => 'applySustainSpectraematum',
        'custo_texto' => '2 PVs'
    ],
];


function parseSustainedSpellsString(string $spellString): array {
    if (empty(trim($spellString))) return [];
    $parsedSpells = [];
    $spellEntries = preg_split('/;\s*/', $spellString, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($spellEntries as $entry) {
        $entry = trim($entry);
        if (empty($entry)) continue;
        preg_match('/^([a-zA-Z\s_]+)(?:\((.*?)\))?$/', $entry, $matches);
        $spellName = trim($matches[1] ?? '');
        $paramsStr = $matches[2] ?? '';
        if (empty($spellName)) continue;
        $params = !empty($paramsStr) ? array_map('trim', explode(',', $paramsStr)) : [];        
        $parsedSpells[] = ['nome' => $spellName, 'params' => $params];
    }
    return $parsedSpells;
}

function generateSustainForm(string $caster, array &$b): string {
    global $catalogoMagiasSustentadas;
    $sustainedString = $b['notes'][$caster]['sustained_spells'] ?? '';
    $parsedSpells = parseSustainedSpellsString($sustainedString);

    if (empty($parsedSpells)) {
        return '';
    }

    $htmlOut = '<h2>Sustentar Magias</h2>';
    $htmlOut .= '<form method="post" action="?step=act">'; 
    $htmlOut .= '<input type="hidden" name="action" value="sustain_act">'; 
    $htmlOut .= '<input type="hidden" name="player" value="' . htmlspecialchars($caster, ENT_QUOTES) . '">';
    
    $spellInstanceCounter = [];

    foreach ($parsedSpells as $spell) {
        $slug = slugify($spell['nome']);
        if (!isset($catalogoMagiasSustentadas[$slug])) continue;
        
        $spellDef = $catalogoMagiasSustentadas[$slug];
        $namedParams = array_combine($spellDef['parametros'], $spell['params']);
        
        if ($spellDef['precisa_input']) {
            $instanceIndex = $spellInstanceCounter[$slug] ?? 0;
            $htmlOut .= call_user_func($spellDef['funcao_gerar_form'], $caster, $namedParams, $instanceIndex);
            $spellInstanceCounter[$slug] = $instanceIndex + 1;
        } else {
            // Para magias automáticas, apenas mostramos o que será feito.
            $costo = $spellDef['custo_texto'] === 'PMs definidos' ? $namedParams['custo_pms'] . ' PMs' : $spellDef['custo_texto'];
            $htmlOut .= "<div style='padding: 8px; border: 1px solid #ccc; margin-top: 5px;'>";
            $htmlOut .= "<strong>{$spellDef['nome']}</strong>(" . implode(', ', $spell['params']) . ")<br>";
            $htmlOut .= "<em>Custo do sustento: {$costo}</em>";
            $htmlOut .= "</div>";
        }
    }

    $htmlOut .= '<br><button type="submit">Confirmar Sustento</button>';
    $htmlOut .= '</form>';
    
    return $htmlOut;
}

function processSustainedSpells(string $caster, array $postData, array &$b): array {
    global $catalogoMagiasSustentadas;
    
    $sustainedString = $b['notes'][$caster]['sustained_spells'] ?? '';
    $parsedSpells = parseSustainedSpellsString($sustainedString);
    $logMsgs = [];
    $spellInstanceCounter = [];

    foreach ($parsedSpells as $spell) {
        $slug = slugify($spell['nome']);
        if (!isset($catalogoMagiasSustentadas[$slug])) continue;
        
        $spellDef = $catalogoMagiasSustentadas[$slug];
        $namedParams = array_combine($spellDef['parametros'], $spell['params']);
        $inputs = [];
        
        if ($spellDef['precisa_input']) {
            $instanceIndex = $spellInstanceCounter[$slug] ?? 0;
            $inputs = $postData['inputs'][$slug][$instanceIndex] ?? [];
            $spellInstanceCounter[$slug] = $instanceIndex + 1;
        }
        
        $logMsg = call_user_func_array($spellDef['funcao_aplicar'], [$caster, $namedParams, &$b, $inputs]);
        if ($logMsg) {
            $logMsgs[] = $logMsg;
        }
    }
    
    return $logMsgs;
}




function applySustainSpeculusanguis($caster, $params) {
    $target = $params['alvo'];
    if (spendPM($caster, 2, true)) {
        $dmg = 0;
        if (!empty($_SESSION['battle']['sustained_effects'][$caster]['speculusanguis']['dmg'])) {
            $dmg = floor(num: $_SESSION['battle']['sustained_effects'][$caster]['speculusanguis']['dmg']/2) - resistenciaMagia($target);
        }
        applyDamage($caster, $target, $dmg, 'Magico');
        $_SESSION['battle']['sustained_effects'][$caster]['speculusanguis']['dmg'] = 0;
        return "<strong>{$caster}</strong> sustenta Speculusanguis em <strong>{$target}</strong> (-2 PV). Dano Refletido = {$dmg}.";
    } else {
        return "<strong>{$caster}</strong> não tem PVs para sustentar Speculusanguis.";
    }
}

function applySustainVisExVulnere($caster) {
    if (spendPM($caster, 2, true, true)) {
        if ($_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['dmg'] > 0){
            $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['pms'] = floor($_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['dmg']/2);
            $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['dmg'] = 0;
        }
        $extra_pms = $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['pms'] ?? 0;
        $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['dmg'] = 0;
        return "<strong>{$caster}</strong> sustenta Vis Ex Vulnere (-2 PV). PMs temporários disponíveis neste turno: {$extra_pms}.";
    } else {
        return "<strong>{$caster}</strong> não tem PVs para sustentar Speculusanguis.";
    }
}

function monitorPVChange($pl, $dano){
    if (isset($_SESSION['battle']['sustained_effects'][$pl]['visExVulnere']['dmg'])){
        $_SESSION['battle']['sustained_effects'][$pl]['visExVulnere']['dmg'] += $dano;
    }
    if (isset($_SESSION['battle']['sustained_effects'][$pl]['speculusanguis']['dmg'])){
        $_SESSION['battle']['sustained_effects'][$pl]['speculusanguis']['dmg'] += $dano;
    }
}



function applySustainExcruentio($caster, $params) {
    $target = $params['alvo'];
    if (spendPM($caster, 1, true)) { 
        applyDamage($caster, $target, 2, 'Magico');
        return "<strong>{$caster}</strong> sustenta Excruentio em <strong>{$target}</strong> (-1 PV)(2 de dano).";
    } else {
        return "<strong>{$caster}</strong> não tem PVs para sustentar Excruentio.";
    }
}


function applySustainSolcruoris($caster, $params) {
    $custo = $params['custo'];
    $extraA = floor($custo/3);
    $_SESSION['battle']['sustained_effects'][$caster]['solcruoris']['extraA'] = $extraA;
    return "<strong>{$caster}</strong> sustenta armadura de sangue Solcruoris. A +{$extraA}";
}

function applySustainSpectraematum($caster, $params, $b, $inputs = []) {
    $target = $params['alvo'];
    $debuffCost = (int)($inputs['debuffCost'] ?? 0); 
    if (spendPM($caster, 2+$debuffCost, true)) { 
        $debuff = floor($debuffCost/2);
        setPlayerStat($target, 'H', $_SESSION['battle']['sustained_effects'][$caster]['spectraematum'][$target]['origH'] - $debuff);
        applyDamage($caster, $target, 1, 'Magico');
        return "<strong>{$caster}</strong> sustenta Spectraematum em <strong>{$target}</strong>. Custo: ".(2+$debuffCost)."; Dano: 1; Debuff H: ".$debuff;
    } else {
        return "<strong>{$caster}</strong> não tem PVs para sustentar Spectraematum.";
    }
}
function getFormSpectraematum($caster, $params, $instanceIndex) {
    $slug = 'spectraematum';
    $target = htmlspecialchars($params['alvo'], ENT_QUOTES);
    $html = "<fieldset><legend>Spectraematum em {$target}</legend>";
    $html .= "<label>Custo do Debuff: 
                <input type='number' name='inputs[{$slug}][{$instanceIndex}][debuffCost]' min='0' value='0' required>
              </label><br>";
    $html .= "</fieldset>";
    return $html;
}

function applySustainProtecaoMagica($caster, $params, &$b, $inputs = []) {
    $cost = (int)$params['custo_pms'];
    if (spendPM($caster, $cost)) {
        $b['notes'][$caster]['efeito'] .= "\nProteção Mágica Superior: A+{$cost} (-{$cost} PM).";
        $_SESSION['battle']['sustained_effects'][$caster]['bonus_A'] = ($_SESSION['battle']['sustained_effects'][$caster]['bonus_A'] ?? 0) + $cost;
        return "<strong>{$caster}</strong> sustenta Proteção Mágica Superior (+{$cost} A) por {$cost} PMs.";
    } else {
        return "<strong>{$caster}</strong> não tem PMs para sustentar Proteção Mágica Superior.";
    }
}

function applySustainCriarVento($caster, $params, &$b, $inputs = []) {
    $cost = (int)$params['custo_pms'];
    if (spendPM($caster, $cost)) {
        $b['notes'][$caster]['efeito'] .= "\nCriar Vento: FD+{$cost} (-{$cost} PM).";
        $_SESSION['battle']['sustained_effects'][$caster]['bonus_FD'] = ($_SESSION['battle']['sustained_effects'][$caster]['bonus_FD'] ?? 0) + $cost;
        return "<strong>{$caster}</strong> sustenta Criar Vento (+{$cost} FD) por {$cost} PMs.";
    } else {
        return "<strong>{$caster}</strong> não tem PMs para sustentar Criar Vento.";
    }
}

function getFormOracaoCombate($caster, $params, $instanceIndex) {
    $slug = 'oracao_de_combate';
    $bonusMax = $params['bonus_max'];
    $html = "<fieldset><legend>Oração de Combate (Bônus Máx: +{$bonusMax})</legend>";
    $html .= "<label>Custo em PMs (1-{$bonusMax}): <input type='number' name='inputs[{$slug}][{$instanceIndex}][pm_cost]' min='1' max='{$bonusMax}' value='1' required></label><br>";
    $html .= "<label>Rolagem de Teste de Fé (1d6): <input type='number' name='inputs[{$slug}][{$instanceIndex}][roll]' min='1' max='6' required></label>";
    $html .= "</fieldset>";
    return $html;
}

function applySustainOracaoCombate($caster, $params, &$b, $inputs = []) {
    $cost = (int)($inputs['pm_cost'] ?? 1);
    $roll = (int)($inputs['roll'] ?? 0);
    
    if (spendPM($caster, $cost)) {
        if (statTest($caster, 'H', 3, $roll)) {
            $bonus = $cost;
            $b['notes'][$caster]['efeito'] .= "\nOração de Combate bem-sucedida: +{$bonus} na FA (-{$cost} PM).";
            return "<strong>{$caster}</strong> passa no teste de fé e sustenta Oração de Combate (+{$bonus} FA) por {$cost} PMs.";
        } else {
            $b['notes'][$caster]['efeito'] .= "\nOração de Combate falhou (-{$cost} PM).";
            return "<strong>{$caster}</strong> falha no teste de fé para Oração de Combate, gastando {$cost} PMs à toa.";
        }
    } else {
        return "<strong>{$caster}</strong> não tem PMs para sustentar Oração de Combate.";
    }
}
?>