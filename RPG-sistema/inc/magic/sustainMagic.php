<?php
$catalogoMagiasSustentadas = [
    'speculusanguis' => [
        'nome' => 'Speculusanguis',
        'parametros' => ['alvo'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainSpeculusanguis',
        'custo_texto' => '2 PVs'
    ],
    'excruentio' => [
        'nome' => 'Excruentio',
        'parametros' => ['alvo'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainExcruentio',
        'custo_texto' => '1 PV'
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
    'inhaerescorpus' => [
        'nome' => 'Inhaerescorpus',
        'parametros' => ['alvo'],
        'precisa_input' => true,
        'funcao_gerar_form' => 'getFormInhaerescorpus',
        'funcao_aplicar' => 'applySustainInhaerescorpus',
        'custo_texto' => '2 PVs'
    ],
    'hemeopsia' => [
        'nome' => 'Hemeopsia',
        'parametros' => [],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainHemeopsia',
        'custo_texto' => 'Custo único'
    ],
    'ataque_vorpal' => [
        'nome' => 'Ataque Vorpal',
        'parametros' => ['alvo'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainAtaqueVorpal',
        'custo_texto' => '1 PM'
    ],
    'fada_servil' => [
        'nome' => 'Fada Servil',
        'parametros' => [],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainFadaServil',
        'custo_texto' => 'Custo único'
    ],
    'furtividade_de_hyninn' => [
        'nome' => 'Furtividade de Hyninn',
        'parametros' => ['alvo'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainFurtividadeDeHyninn',
        'custo_texto' => 'Custo único'
    ],
    'protecao_magica_superior' => [
        'nome' => 'Protecao Magica Superior',
        'parametros' => ['alvo'],
        'precisa_input' => true,
        'funcao_gerar_form' => 'getFormProtecaoMagicaSuperior',
        'funcao_aplicar' => 'applySustainProtecaoMagicaSuperior',
        'custo_texto' => 'Váriavel'
    ],
    'protecao_magica' => [
        'nome' => 'Protecao Magica',
        'parametros' => ['alvo'],
        'precisa_input' => true,
        'funcao_gerar_form' => 'getFormProtecaoMagica',
        'funcao_aplicar' => 'applySustainProtecaoMagica',
        'custo_texto' => 'Váriavel'
    ],
    'recuperacao_natural' => [
        'nome' => 'Recuperacao Natural',
        'parametros' => ['alvo'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainRecuperacaoNatural',
        'custo_texto' => 'Custo único; Um turno'
    ],
    'reflexos' => [
        'nome' => 'Reflexos',
        'parametros' => [],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainReflexos',
        'custo_texto' => 'Custo único'
    ],
    'retribuicao_de_wynna' => [
        'nome' => 'Retribuicao de Wynna',
        'parametros' => [],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainRetribuicaoDeWynna',
        'custo_texto' => '4 PMs'
    ],
    'sentidos_especiais_magia' => [
        'nome' => 'Sentidos Especiais',
        'parametros' => ['sentido_especial'],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainSentidosEspeciais',
        'custo_texto' => 'Custo único'
    ],
    'deteccao_de_magia' => [
        'nome' => 'Deteccao de Magia',
        'parametros' => [],
        'precisa_input' => false,
        'funcao_aplicar' => 'applySustainDeteccaoDeMagia',
        'custo_texto' => 'Custo único'
    ]
    
];


// Sustained magic managers

function parseSustainedSpellsString(string $spellString): array{
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

function generateSustainForm(string $caster, array &$b): string{
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

function processSustainedSpells(string $caster, array $postData, array &$b): array{
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

function removeSustainedSpell(string $caster, string $spellName, array $params, array &$b): void{
    $sustainedString = $b['notes'][$caster]['sustained_spells'] ?? '';
    if (empty($sustainedString)) {
        return;
    }
    $paramsString = implode(', ', $params);
    $spellToRemove = $spellName;
    if (!empty($params)) {
        $paramsString = implode(', ', $params);
        $spellToRemove .= "({$paramsString})";
    }
    $spellEntries = preg_split('/;\s*/', $sustainedString, -1, PREG_SPLIT_NO_EMPTY);
    $spellEntries = array_filter($spellEntries, function ($entry) use ($spellToRemove) {
        return str_replace(' ', '', $entry) !== str_replace(' ', '', $spellToRemove);
    });
    $b['notes'][$caster]['sustained_spells'] = implode(";\n", $spellEntries);
    if (!empty($b['notes'][$caster]['sustained_spells'])) {
        $b['notes'][$caster]['sustained_spells'] .= ';';
    }
}



// Sustained magic inputs and aplication

function applySustainSpeculusanguis($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    if (spendPM($caster, 2, true)) {
        $dmg = 0;
        if (!empty($_SESSION['battle']['sustained_effects'][$caster]['speculusanguis']['dmg'])) {
            $dmg = floor(num: $_SESSION['battle']['sustained_effects'][$caster]['speculusanguis']['dmg'] / 2) - resistenciaMagia($target);
        }
        applyDamage($caster, $target, $dmg, 'Magia');
        $_SESSION['battle']['sustained_effects'][$caster]['speculusanguis']['dmg'] = 0;
        return "<strong>{$caster}</strong> sustenta S" . getMagicSpecialName($caster, 'speculusanguis') . " (Speculusanguis) em <strong>{$target}</strong> (-2 PV). Dano Refletido = {$dmg}.";
    } else {
        removeSustainedSpell($caster, 'Speculusanguis', $params, $b);
        return "<strong>{$caster}</strong> não tem PVs para sustentar " . getMagicSpecialName($caster, 'speculusanguis') . " (Speculusanguis).";
    }
}

function applySustainVisExVulnere($caster, $params, &$b, $inputs = []){
    if (spendPM($caster, 2, true, true)) {
        $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['pms'] = 0;
        if ($_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['dmg'] > 0) {
            $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['pms'] = floor($_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['dmg'] / 2);
            $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['dmg'] = 0;
        }
        $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['dmg'] = 0;
        $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['flag'] = $b['init_index'] % count(value: $b['order']);
        $extra_pms = $_SESSION['battle']['sustained_effects'][$caster]['visExVulnere']['pms'];
        return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'vis_ex_vulnere') . " (Vis Ex Vulnere) (-2 PV). PMs temporários disponíveis neste turno: {$extra_pms}.";
    } else {
        removeSustainedSpell($caster, 'Vis Ex Vulnere', [], $b);
        return "<strong>{$caster}</strong> não tem PVs para sustentar " . getMagicSpecialName($caster, 'vis_ex_vulnere') . " (Vis Ex Vulnere).";
    }
}

function applySustainInhaerescorpus($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    $testDiff = (int)($inputs['testDiff'] ?? 0);
    $testR = (int)($inputs['testR'] ?? 1);
    if (spendPM($caster, $testDiff, true)) {
        if (!statTest($target, 'R', floor($testDiff / 2), $testR)) {
            $dmg = 0;
            if (!empty($_SESSION['battle']['sustained_effects'][$caster]['inhaerescorpus']['dmg'])) {
                $dmg = $_SESSION['battle']['sustained_effects'][$caster]['inhaerescorpus']['dmg'];
            }
            applyDamage($caster, $target, $dmg, 'Magia');
            $_SESSION['battle']['sustained_effects'][$caster]['inhaerescorpus']['dmg'] = 0;
            return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'inhaerescorpus') . " (Inhaerescorpus) em <strong>{$target}</strong>. Dano Refletido = {$dmg}.";
        } else {
            removeSustainedSpell($caster, 'Inhaerescorpus', $params, $b);
            return "<strong>{$target}</strong> passou no teste de resistência e saiu da magia " . getMagicSpecialName($caster, 'inhaerescorpus') . " (Inhaerescorpus) de <strong>{$caster}</strong>.";
        }
    } else {
        removeSustainedSpell($caster, 'Inhaerescorpus', $params, $b);
        return "<strong>{$caster}</strong> não tem PVs para sustentar " . getMagicSpecialName($caster, 'inhaerescorpus') . " (Inhaerescorpus).";
    }
}
function getFormInhaerescorpus($caster, $params, $instanceIndex){
    $slug = 'inhaerescorpus';
    $target = htmlspecialchars($params['alvo'], ENT_QUOTES);
    $html = "<fieldset><legend>Inhaerescorpus em {$target}</legend>";
    $html .= "<label>Custo para dificultar teste de R:
            <input type='number' name='inputs[{$slug}][{$instanceIndex}][testDiff]' min='0' required>
            </label><br>";
    $html .= "<label>Teste R:
            <input type='number' name='inputs[{$slug}][{$instanceIndex}][testR]' min='1' max='6' required>
            </label><br>";
    $html .= "</fieldset>";
    return $html;
}

function applySustainExcruentio($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    if (spendPM($caster, 1, true)) {
        applyDamage($caster, $target, 2, 'Magia');
        return "<strong>{$caster}</strong> sustenta Excruentio em <strong>{$target}</strong> (-1 PV)(2 de dano).";
    } else {
        removeSustainedSpell($caster, 'Excruentio', $params, $b);
        return "<strong>{$caster}</strong> não tem PVs para sustentar " . getMagicSpecialName($caster, 'excruentio') . " (Excruentio).";
    }
}


function applySustainSolcruoris($caster, $params, &$b, $inputs = []){
    $custo = $params['custo'];
    $extraA = floor($custo / 3);
    $_SESSION['battle']['sustained_effects'][$caster]['solcruoris']['extraA'] = $extraA;
    return "<strong>{$caster}</strong> sustenta armadura de sangue " . getMagicSpecialName($caster, 'solcruoris') . " (Solcruoris). A +{$extraA}";
}

function applySustainSpectraematum($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    $debuffCost = (int)($inputs['debuffCost'] ?? 0);
    if (spendPM($caster, 2 + $debuffCost, true)) {
        $debuff = floor($debuffCost / 2);
        setPlayerStat($target, 'H', $_SESSION['battle']['sustained_effects'][$caster]['spectraematum'][$target]['origH'] - $debuff);
        applyDamage($caster, $target, 1, 'Magia');
        return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'spectraematum') . " (Spectraematum) em <strong>{$target}</strong>. Custo: " . (2 + $debuffCost) . "; Dano: 1; Debuff H: " . $debuff;
    } else {
        removeSustainedSpell($caster, 'Spectraematum', $params, $b);
        return "<strong>{$caster}</strong> não tem PVs para sustentar " . getMagicSpecialName($caster, 'spectraematum') . " (Spectraematum).";
    }
}
function getFormSpectraematum($caster, $params, $instanceIndex){
    $slug = 'spectraematum';
    $target = htmlspecialchars($params['alvo'], ENT_QUOTES);
    $html = "<fieldset><legend>Spectraematum em {$target}</legend>";
    $html .= "<label>Custo do Debuff: 
                <input type='number' name='inputs[{$slug}][{$instanceIndex}][debuffCost]' min='0' value='0' required>
              </label><br>";
    $html .= "</fieldset>";
    return $html;
}

function applySustainHemeopsia($caster, $params, &$b, $inputs = []){
    return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'hemeopsia') . " (Hemeópsia).";
}

function applySustainAtaqueVorpal($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    if (spendPM($caster, 1)) {
        return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'ataque_vorpal') . " (Ataque Vorpal) em <strong>{$target}</strong>.";
    } else {
        removeSustainedSpell($caster, 'Ataque Vorpal', $params, $b);
        $_SESSION['battle']['notes'][$target]['efeito'] = removeEffect($_SESSION['battle']['notes'][$target]['efeito'], ['Sob efeito da magia Ataque Vorpal;']);
        return "<strong>{$caster}</strong> não tem PVs para sustentar " . getMagicSpecialName($caster, 'ataque_vorpal') . " (Ataque Vorpal).";
    }
}

function applySustainFadaServil($caster, $params, &$b, $inputs = []){
    return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'fada_servil') . " (Fada Servil).";
}

function applySustainFurtividadeDeHyninn($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'furtividade_de_hyninn') . " (Furtividade de Hyninn) em <strong>{$target}</strong>.";
}

function applySustainProtecaoMagicaSuperior($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    $cost = (int)($inputs['cost'] ?? 0);
    setPlayerStat($target, 'A', getPlayerStat($target, 'A')-$_SESSION['battle']['sustained_effects'][$caster]['protecaoMagicaSuperior'][$target]['buffA']);
    if (spendPM($caster, $cost)) {
        $_SESSION['battle']['sustained_effects'][$caster]['protecaoMagicaSuperior'][$target]['buffA'] = $cost;
        setPlayerStat($target, 'A', getPlayerStat($target, 'A')+$cost);
        return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'protecao_magica_superior') . " (Proteção Mágica Superior) em <strong>{$target}</strong>.";
    } else {
        removeSustainedSpell($caster, 'Proteção Mágica Superior', $params, $b);
        return "<strong>{$caster}</strong> não tem PMs para sustentar " . getMagicSpecialName($caster, 'protecao_magica_superior') . " (Proteção Mágica Superior).";
    }
}
function getFormProtecaoMagicaSuperior($caster, $params, $instanceIndex){
    $slug = 'protecao_magica_superior';
    $target = htmlspecialchars($params['alvo'], ENT_QUOTES);
    $html = "<fieldset><legend>Proteção Mágica Superior em {$target}</legend>";
    $html .= "<label>Custo:
            <input type='number' name='inputs[{$slug}][{$instanceIndex}][cost]' min='1' max='5' required>
            </label></fieldset>";
    return $html;
}

function applySustainProtecaoMagica($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    $cost = (int)($inputs['cost'] ?? 0);
    setPlayerStat($target, 'A', getPlayerStat($target, 'A')-$_SESSION['battle']['sustained_effects'][$caster]['protecaoMagica'][$target]['buffA']);
    if (spendPM($caster, $cost)) {
        $buffA = floor($cost/2);
        $_SESSION['battle']['sustained_effects'][$caster]['protecaoMagica'][$target]['buffA'] = $buffA;
        setPlayerStat($target, 'A', getPlayerStat($target, 'A')+$buffA);
        return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'protecao_magica') . " (Proteção Mágica) em <strong>{$target}</strong>.";
    } else {
        removeSustainedSpell($caster, 'Proteção Mágica', $params, $b);
        return "<strong>{$caster}</strong> não tem PMs para sustentar " . getMagicSpecialName($caster, 'protecao_magica') . " (Proteção Mágica).";
    }
}
function getFormProtecaoMagica($caster, $params, $instanceIndex){
    $slug = 'protecao_magica';
    $target = htmlspecialchars($params['alvo'], ENT_QUOTES);
    $html = "<fieldset><legend>Proteção Mágica em {$target}</legend>";
    $html .= "<label>Custo:
            <input type='number' name='inputs[{$slug}][{$instanceIndex}][cost]' min='2' max='10' required>
            </label></fieldset>";
    return $html;
}

function applySustainRecuperacaoNatural($caster, $params, &$b, $inputs = []){
    $target = $params['alvo'];
    setPlayerStat($target, 'PV', getPlayerStat($target, 'PV')+1);
    unset($_SESSION['battle']['playingAlly']);
    $_SESSION['battle']['init_index']++;
    return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'recuperacao_natural') . " (Recuperação Natural) em <strong>{$target}</strong>. PV+1";
}

function applySustainReflexos($caster, $params, &$b, $inputs = []){
    return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'reflexos') . " (Reflexos).";
}

function applySustainRetribuicaoDeWynna($caster, $params, &$b, $inputs = []){
    if (spendPM($caster, 4)) {
        return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'retribuicao_de_wynna') . " (Retribuição de Wynna).";
    } else {
        removeSustainedSpell($caster, 'Retribuicao de Wynna', $params, $b);
        return "<strong>{$caster}</strong> não tem PMs para sustentar " . getMagicSpecialName($caster, 'retribuicao_de_wynna') . " (Retribuição de Wynna).";
    }
}

function applySustainSentidosEspeciais($caster, $params, &$b, $inputs = []){
    return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'sentidos_especiais_magia') . " (Sentidos Especiais).";
}

function applySustainDeteccaoDeMagia($caster, $params, &$b, $inputs = []){
    return "<strong>{$caster}</strong> sustenta " . getMagicSpecialName($caster, 'deteccao_de_magia') . " (Detecção De Magia).";
}
?>