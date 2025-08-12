<?php
// battle.php - Sistema de Combate 3D&T
session_start();
include_once 'inc/generalFuncs.php';
include_once 'inc/traitFuncs.php';
include_once 'inc/battleFuncs.php';
include_once 'inc/magicFuncs.php';

// Inicializa sessão
if (!isset($_SESSION['battle'])) {
    $_SESSION['battle'] = [
        'players'     => [],
        'order'       => [],
        'init_index'  => 0,
        'round'       => 1,
        'notes'       => [],
        'arena'       => '',
    ];
}
$b = &$_SESSION['battle'];


// Passos
$step = $_GET['step'] ?? 'select';
switch ($step) {

    // 1) Seleção de players
    case 'select':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selected = array_map('trim', $_POST['players'] ?? []);
            if (count($selected) < 2) {
                header('Location: battle.php');
                exit;
            }
            $b = [
                'players'     => $selected,
                'order'       => [],
                'init_index'  => 0,
                'round'       => 1,
                'notes'       => [],
                'arena'       => '',
            ];
            $b['hasAlly'] = [];

            header('Location: battle.php?step=initiative');
            exit;
        }
        $all = getAllPlayers();
        $exclude = getAllAllies();
        echo '<h1>Iniciar Batalha</h1><form method="post">';

        foreach ($all as $p) {
            $n = $p['nome'];
            if (in_array($n, $exclude, true)) {
                continue;
            }
            $safe = htmlspecialchars($n, ENT_QUOTES);
            echo '<label><input type="checkbox" name="players[]" value="' . $n . '"> ' . $n . '</label><br>';
        }
        echo '<button type="submit">Confirmar</button></form>';
        break;


    // 2) Iniciativa
    case 'initiative':
        $rolls = $_POST['rolls'] ?? [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($b['players'] as $i => $pl) { 
                syncEquipBuffs($pl);  
                if (!isset($b['orig'][$pl])) {
                    $b['orig'][$pl] = [];
                    foreach (['F', 'H', 'R', 'A', 'PdF'] as $s) {
                        $b['orig'][$pl][$s] = getPlayerStat($pl, $s);
                    }
                }
            }
            if (!empty($_POST['roll_assombrado'])) {
                foreach ($b['players'] as $i => $pl) {
                    if (isset($_POST['roll_assombrado'][$i])) {
                        // Aplica Desvantagem Assombrado
                        $msg = assombrado($pl, [
                            'roll_assombrado' => $_POST['roll_assombrado'][$i]
                        ]);
                        if ($msg !== null) {
                            $b['notes'][$pl]['efeito']     = $msg;
                            $b['notes'][$pl]['assombrado'] = true;
                        }
                    }
                }
            }

            $inicList     = iniciativa($b['players'], $rolls);
            $b['order']   = array_column($inicList, 'nome');

            header('Location: battle.php?step=turn');
            exit;
        }

        echo '<h1>Iniciativa</h1>
          <form method="post" action="battle.php?step=initiative">';
        foreach ($b['players'] as $i => $nome) {
            echo '<label>' . htmlspecialchars($nome, ENT_QUOTES) . ':
                <input type="number" name="rolls[' . $i . ']" required>
              </label><br>';
            if (in_array('assombrado', listPlayerTraits($nome), true)) {
                echo '<label>' . htmlspecialchars($nome, ENT_QUOTES) . ' Assombrado (1–6), dado:
                    <input type="number" name="roll_assombrado[' . $i . ']" min="1" max="6" required>
                  </label><br>';
            }
        }
        echo '<button type="submit">Ok</button>
          </form>';
        break;

    // 2.1) Atualizar stats
    case 'update_stats':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pl = $_GET['player'] ?? '';
            if ($pl && in_array($pl, $b['players'], true)) {
                foreach ($_POST['stats'] as $stat => $val) {
                    setPlayerStat($pl, $stat, (int)$val);
                }
                if (isset($_POST['inventory'])) setPlayerStat($pl, 'inventario', $_POST['inventory']);
                if (isset($_POST['equipped']))  setPlayerStat($pl, 'equipado', $_POST['equipped']);
            }
        }
        header('Location: battle.php?step=turn');
        exit;

    // 2.2) Atualizar arena    
    case 'update_arena':
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $_SESSION['battle']['arena'] = trim($_POST['arena'] ?? '');
        }
        header('Location: battle.php?step=turn');
        exit;

    // 2.3) Atualizar magias sustentadas
    case 'update_sustained_spells':
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $pl = $_GET['player'] ?? '';
            if ($pl && in_array($pl, $b['players'], true)){
                $_SESSION['battle']['notes'][$pl]['sustained_spells'] = trim($_POST['sustained_spells'] ?? '');
            }
        }
        header('Location: battle.php?step=turn');
        exit;


    // 2.4) Gerar inputs de magias    
    case 'get_magic_inputs':
        $magic_slug = $_GET['magic'] ?? '';
        $cur = $b['order'][$b['init_index'] % count($b['order'])];
        if (empty($magic_slug) || empty($cur)) {
            exit;
        }
        include_once 'inc/magicInputs.php';
        renderMagicInputs($magic_slug, $cur, $b);
        exit;



        // 3) initiative atual
    case 'turn':
        
        $order = $b['order'];
        if (count($order) === 0) {
            echo "<p>Nenhum lutador na batalha.</p>";
            exit;
        }

        if (! empty($b['playingAlly'])) {
            $cur = $b['playingAlly'];
        } else {
            $cur = $order[$b['init_index'] % count($order)];
        }
          
        if (!empty($b['needs_reload'])) {
            unset($b['needs_reload']);
            header("Refresh:0");
        }
        syncEquipBuffs($cur);
        $stats = getPlayer($cur);
        $notes = array_merge(
            ['efeito' => '', 'posicao' => '', 'concentrado' => 0, 'draco_active' => false, 'incorp_active' => false],
            $b['notes'][$cur] ?? []
        );
        $b['notes'][$cur] = $notes;
        $maxMulti = 1 + intdiv(max($stats['H'], 0), 2);
        $isIncorp = ! empty($notes['incorp_active']);
        

        if (!empty($b['notes'][$cur]['use_pv'])){
            $efeitoUsePV = "\nUsando PVs invés de PMs.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoUsePV)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoUsePV;
            }
        } else {
            $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], ['Usando PVs invés de PMs.']);
        }

        if ($b['notes'][$cur]['draco_active'] && !empty($b['notes'][$cur]['draco_flag'])) {
            unset($b['notes'][$cur]['draco_flag']);
            if (!spendPM($cur, 1)) {
                draconificacao($cur, false);
                $b['notes'][$cur]['draco_active'] = false;
                $efeitoDraco = "\nDraconificação desativada (PM esgotado) e Instável.";
                if (strpos($b['notes'][$cur]['efeito'], trim($efeitoDraco)) === false) {
                    $b['notes'][$cur]['efeito'] .= $efeitoDraco;
                }
            }
        } else {
            $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], ['Draconificação desativada (PM esgotado) e Instável.']);
        }

        if (!empty($notes['fusao_active'])) {
            $curPV = getPlayerStat($cur, 'PV');
            $curR  = getPlayerStat($cur, 'R');
            if ($curPV <= $curR) {
                $b['notes'][$cur]['furia'] = true;
                $efeitoFuria = "\nEm Fúria até o fim da batalha (Não pode usar magia nem esquiva).";
                if (strpos($b['notes'][$cur]['efeito'], trim($efeitoFuria)) === false) {
                    $b['notes'][$cur]['efeito'] .= $efeitoFuria;
                }
            }
        }

        if (!empty($notes['extra_energy_next'])) {
            energiaExtra($cur);
            $b['notes'][$cur]['efeito'] .= "\nEnergia extra aplicada: PVs restaurados ao máximo.";
            unset($b['notes'][$cur]['extra_energy_next']);
        } else {
            $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], ['Energia extra aplicada: PVs restaurados ao máximo.']);
        }

        if (!empty($notes['magia_extra_next'])) {
            magiaExtra($cur, 'apply');
            $b['notes'][$cur]['efeito'] .= "\nMagia extra aplicada: PMs restaurados ao máximo.";
            unset($b['notes'][$cur]['magia_extra_next']);
        } else {
            $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], ['Magia extra aplicada: PMs restaurados ao máximo.']);
        }

        if (!empty($b['notes'][$cur]['invisivel']) && !empty($b['notes'][$cur]['invisivel_flag'])){
            unset($b['notes'][$cur]['invisivel_flag']);
            if (!spendPM($cur, 1)){
                unset($b['notes'][$cur]['invisivel']);
                $efeitoInvisibilidade = "\nInvisibilidade desativada por falta de PMs.";
                if (strpos($b['notes'][$cur]['efeito'], trim($efeitoInvisibilidade )) === false) {
                    $b['notes'][$cur]['efeito'] .= $efeitoInvisibilidade;
                }
                $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], ['Você está invisível.']);
            }
        } else if(empty($notes['invisivel'])){
            $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], ['Você está invisível.']);
        }


        if (in_array('furia', listPlayerTraits($cur), true)) {
            $curPV = getPlayerStat($cur, 'PV');
            $curR  = getPlayerStat($cur, 'R');
            if ($curPV <= $curR) {
                $b['notes'][$cur]['furia'] = true;
                $efeitoFuria = "\nEm Fúria até o fim da batalha (Não pode usar magia nem esquiva).";
                if (strpos($b['notes'][$cur]['efeito'], trim($efeitoFuria)) === false) {
                    $b['notes'][$cur]['efeito'] .= $efeitoFuria;
                }
            }
        }

        // Efeitos estáticos
        if (in_array('invulnerabilidade_fogo', listPlayerTraits($cur), true)) {
            $efeitoInvuln = "\nInvulnerabilidade a fogo (dano por fogo dividido por 10).";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoInvuln)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoInvuln;
            }
        }

        if (in_array('codigo_da_derrota', listPlayerTraits($cur), true)) {
            $efeitoDerrota = "\nLute até a morte.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoDerrota)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoDerrota;
            }
        }

        if (in_array('manco', listPlayerTraits($cur), true)) {
            $efeitoManco = "\nManco: H -2 para movimentação.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoManco)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoManco;
            }
        }

        if (in_array('notivago', listPlayerTraits($cur), true)) {
            $efeitoNotivago = "\nNotívago: -1 em qualquer teste se sob a luz do sol.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoNotivago)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoNotivago;
            }
        }

        if (in_array('gregario', listPlayerTraits($cur), true)) {
            $efeitoGregario = "\nGregário: -2 em todos os testes se estiver sozinho.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoGregario)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoGregario;
            }
        }

        if (in_array('terreno_desfavoravel_agua', listPlayerTraits($cur), true)) {
            $efeitoTerreno = "\nTerreno Desfavorável Água: H -2 em ambientes aquáticos.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoTerreno)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoTerreno;
            }
        }

        if (in_array('aceleracao_i', listPlayerTraits($cur), true)) {
            $efeitoAceli = "\nAceleração I: H +1 situações de perseguição/fuga & Movimento extra.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoAceli)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoAceli;
            }
        }  

        if (in_array('aceleracao_ii', listPlayerTraits($cur), true)) {
            $efeitoAcelii = "\nAceleração II: H +2 situações de perseguição/fuga & 2 Movimentos extras ou 1 ação extra.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoAcelii)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoAcelii;
            }
        }

        if (in_array('audicao_agucada', listPlayerTraits($cur), true)) {
            $efeitoAudicao = "\nAudição Aguçada: Debuffs visuais são negados.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoAudicao)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoAudicao;
            }
        } 

        if (in_array('visao_360', listPlayerTraits($cur), true)) {
            $efeitoVisao = "\nVisão 360°: H +2 para perceber inimigos escondidos e ataques surpresas.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoVisao)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoVisao;
            }
        } 

        if (in_array('visao_penumbra', listPlayerTraits($cur), true)) {
            $efeitoPenumbra = "\nVisão Penumbra: Não sofre debuffs de H quando no escuro.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoPenumbra)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoPenumbra;
            }
        } 

        if (in_array('faro_agucado', listPlayerTraits($cur), true)) {
            $efeitoFaro = "\nFaro Aguçado: Identifica cheiros com facilidade.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoFaro)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoFaro;
            }
        }

        if (in_array('pericia_manipulacao', listPlayerTraits($cur), true)) {
            $efeitoManipul = "\nManipulação: Habilidades de manipulação, hipnose, interrogatório, intimidação, lábia e sedução";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoManipul)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoManipul;
            }
        }

        if (in_array('sentido_de_perigo', listPlayerTraits($cur), true)) {
            $efeitoSentidoPerig = "\nSentido de Perigo: Capaz de pressentir o perigo, assim nunca pode ser surpreendido";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoSentidoPerig)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoSentidoPerig;
            }
        }

        if (in_array('domar', listPlayerTraits($cur), true)) {
            $efeitoDomar = "\nDomar: Tem facilidade domesticar animais selvagens.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoDomar)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoDomar;
            }
        }

        if (in_array('furtividade', listPlayerTraits($cur), true)) {
            $efeitoFurtividade = "\nFurtividade: Consegue se esconder facilmente, e agir de maneira quase que silenciosa.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoFurtividade)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoFurtividade;
            }
        }

        if (in_array('emocoes', listPlayerTraits($cur), true)) {
            $efeitoEmocoes = "\nEmoções: Consegue se fingir de coitadinho e controlar emocionalmente outros com facilidade.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoEmocoes)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoEmocoes;
            }
        }

        if (in_array('inimigo_humanos', listPlayerTraits($cur), true)) {
            $efeitoInimigoHuman = "\nInimigo (Humanos): H+2 quando lutando contra humanos.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoInimigoHuman)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoInimigoHuman;
            }
        }

        if (in_array('item_de_poder', listPlayerTraits($cur), true)) {
            $efeitoItemPod = "\nItem de Poder: Reduz gasto de PM em 2, custando no minimo 1, enquanto estiver com item de poder.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoItemPod)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoItemPod;
            }
        }

        if (in_array('resistencia_a_magia', listPlayerTraits($cur), true)) {
            $efeitoResistenMagia = "\nResistência a Magia: Recebe +2 em testes de defesa contra magia.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoResistenMagia)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoResistenMagia;
            }
        }

        if (in_array('terreno_desfavoravel', listPlayerTraits($cur), true)) {
            $efeitoTerrDesfavAgua = "\nTerreno Desfavorável (Água): H-2 em ambientes encharcados.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoTerrDesfavAgua)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoTerrDesfavAgua;
            }
        }

        if (in_array('fetiche', listPlayerTraits($cur), true)) {
            $efeitoFetiche = "\nFetiche: Não pode fazer mágica sem um objeto especial.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoFetiche)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoFetiche;
            }
        }

        if (in_array('grito_arcano', listPlayerTraits($cur), true)) {
            $efeitoGritoArcano = "\nGrito Arcano: Sempre que lança uma magia a profere gritando.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoGritoArcano)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoGritoArcano;
            }
        }

        if (in_array('ponto_fraco', listPlayerTraits($cur), true)) {
            $efeitoPontoFraco = "\nPonto Fraco: Se um inimigo sabe seu ponto fraco ele ganha H+1 lutando contra você.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoPontoFraco)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoPontoFraco;
            }
        }

        if (in_array('teleporte', listPlayerTraits($cur), true)) {
            $efeitoTelep = "\nTeleporte: H+3 para esquiva. Alcance do teleporte = ".getPlayerStat($cur, 'H')*10;
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoTelep)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoTelep;
            }
        } else {
            $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], ["Teleporte: H+3 para esquiva. Alcance do teleporte = ".getPlayerStat($cur, 'H')*10]);
        }

        if (in_array('armadura_extra_fogo', listPlayerTraits($cur), true)) {
            $efeitoPontoFraco = "\nArmadura Extra (Fogo): A x2 contra ataques de fogo.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoPontoFraco)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoPontoFraco;
            }
        }

        if (in_array('magia_branca', listPlayerTraits($cur), true)) {
            $efeitoMagiaBranca = "\nMagia Branca.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoMagiaBranca)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoMagiaBranca;
            }
        }

        if (in_array('magia_elemental', listPlayerTraits($cur), true)) {
            $efeitoMagiaElemental = "\nMagia elemental.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoMagiaElemental)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoMagiaElemental;
            }
        }

        if (in_array('magia_negra', listPlayerTraits($cur), true)) {
            $efeitoMagiaNegra = "\nMagia negra.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoMagiaNegra)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoMagiaNegra;
            }
        }

        if (in_array('arcano', listPlayerTraits($cur), true)) {
            $efeitoArcano = "\nArcano: Pode utilizar magias de todas as escolas de magia.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoArcano)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoArcano;
            }
        }

        if (in_array('pericia_medicina', listPlayerTraits($cur), true)) {
            $efeitoMedicina = "\nPericia Medicina: Conhecimentos gerais sobre medicina, primeiros socorros, cirurgia, etc.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoMedicina)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoMedicina;
            }
        }

        if (in_array('escalar', listPlayerTraits($cur), true)) {
            $efeitoEscalar = "\nEscalar: consegue escalar paredes, árvores, tetos e quaisquer superfícies, à velocidade normal e sem precisar fazer testes de perícia.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoEscalar)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoEscalar;
            }
        }

        if (in_array('queda_lenta', listPlayerTraits($cur), true)) {
            $efeitoQuedaLenta = "\nQueda Lenta: pode cair de qualquer distância sem se machucar, usando saliências para desacelerar a queda. Precisa estar consciente para usar este movimento.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoQuedaLenta)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoQuedaLenta;
            }
        }

        if (in_array('constancia', listPlayerTraits($cur), true)) {
            $efeitoConstancia = "\nConstância: não sofre os efeitos de terrenos difíceis ou obstáculos que possam dificultar o movimento. Sempre pode se mover à velocidade máxima total sem nenhuma penalidade.";
            if (strpos($b['notes'][$cur]['efeito'], trim($efeitoConstancia)) === false) {
                $b['notes'][$cur]['efeito'] .= $efeitoConstancia;
            }
        }




        // Limpa os espaços em branco.
        $b['notes'][$cur]['efeito'] = ltrim($b['notes'][$cur]['efeito']);


        
        if (!empty($b['playingPartner'][$cur])) {
            echo '<h1>Iniciativa da dupla <strong>' . $cur . ' & ' . $b['playingPartner'][$cur]['name'] . '</strong> <small>(Rodada ' . $b['round'] . ')</small></h1>';
        } else {
            echo '<h1>Iniciativa de <strong>' . $cur . '</strong> <small>(Rodada ' . $b['round'] . ')</small></h1>';
        }
        // Stats e inventário
        echo '<h2>Stats & Inventário</h2>';
        echo '<form method="post" action="?step=update_stats&player=' . urlencode($cur) . '">';
        foreach (['F', 'H', 'R', 'A', 'PdF', 'PV', 'PM', 'PE'] as $c) {
            $v = htmlspecialchars($stats[$c] ?? '', ENT_QUOTES);
            echo $c . ': <input type="number" name="stats[' . $c . ']" value="' . $v . '" required><br>';
        }
        echo 'Inventário:<br><textarea name="inventory" rows="4" cols="60">' . htmlspecialchars($stats['inventario'] ?? '', ENT_QUOTES) . '</textarea><br>';
        echo 'Equipado:<br><textarea name="equipped" rows="2" cols="60">' . htmlspecialchars($stats['equipado'] ?? '', ENT_QUOTES) . '</textarea><br>';
        echo '<button>Salvar Stats</button></form>';







        // Form de ações
        echo '<h2>Ações</h2>';

        echo '<form method="post" action="battle.php?step=act" id="actionForm">'
            . '<input type="hidden" name="player" value="' . htmlspecialchars($cur, ENT_QUOTES) . '">'
            . 'Efeito:<br><textarea name="efeito" rows="8" cols="60">' . htmlspecialchars($notes['efeito'], ENT_QUOTES) . '</textarea><br>'
            . 'Posição:<br><textarea name="posicao" rows="2" cols="60">' . htmlspecialchars($notes['posicao'], ENT_QUOTES) . '</textarea><br>'
            . 'Concentrado: <input type="number" name="concentrado" min=0 value="' . htmlspecialchars($notes['concentrado'], ENT_QUOTES) . '"><br>'
            . '<select id="actionSelect" name="action">';
        echo '<option value="pass">Passar Iniciativa</option>';
        echo '<option value="fim">Terminar Batalha</option>';

        $isDefeated = ($stats['PV'] <= 0);
        if (!$isDefeated){

            $concentrando = false;
            $agarrando = false;
            $agarrado = false;
            if ($notes['concentrado'] > 0) {
                $concentrando = true;
            }
            if (!empty($b['agarrao'][$cur]['agarrando'])) {
                $agarrando = true;
            }
            if (!empty($b['agarrao'][$cur]['agarrado'])) {
                $agarrado = true;
            }

            //Select de ações
            if (in_array('energia_vital', listPlayerTraits($cur), true)) {
                if (empty($notes['use_pv'])) {
                    echo '<option value="enable_use_pv">Usar PV em vez de PM</option>';
                } else {
                    echo '<option value="disable_use_pv">Voltar a usar PM normalmente</option>';
                }
            }

            if (in_array('incorporeo', listPlayerTraits($cur), true)) {
                if (!$notes['incorp_active']) {
                    echo '<option value="activate_incorp">Tornar‑se Incorpóreo (2 PM)</option>';
                } else {
                    echo '<option value="deactivate_incorp">Reverter Incorporeo</option>';
                }
            }

            if (in_array('draconificacao', listPlayerTraits($cur), true)) {
                if (!$notes['draco_active']) {
                    echo '<option value="activate_draco">Ativar Draconificação (1PM/turno)</option>';
                } else {
                    echo '<option value="deactivate_draco">Desativar Draconificação (ação extra)</option>';
                }
            }

            if (in_array('fusao_eterna', listPlayerTraits($cur), true)) {
                if (empty($notes['fusao_active'])) {
                    echo '<option value="activate_fusao">Ativar Fusão Eterna (3PM)</option>';
                } else {
                    echo '<option value="deactivate_fusao">Desativar Fusão Eterna</option>';
                }
            }

            if (!$concentrando && !$agarrado && !$agarrando) {
                echo '<option value="start_concentrar">Iniciar Concentração</option>';
            } else if ($notes['concentrado'] > 0) {
                echo '<option value="start_concentrar">Continuar Concentração (+1)</option>';
                echo '<option value="release_concentrar">Liberar Concentração (bônus: +' . $notes['concentrado'] . ')</option>';
            }

            if (in_array('energia_extra', listPlayerTraits($cur), true) && !$concentrando && !$agarrado && !$agarrando) {
                echo '<option value="extra_energy">Usar Energia Extra</option>';
            }

            if (in_array('magia_extra', listPlayerTraits($cur), true) && !$concentrando && !$agarrado && !$agarrando) {
                echo '<option value="magia_extra">Usar Magia Extra</option>';
            }

            if (in_array('aliado', listPlayerTraits($cur), true) && empty($b['playingAlly']) && empty($b['playingPartner'][$cur]) && !$cocentrado) {
                echo '<option value="use_ally">Jogar com Aliado</option>';
            }

            if ($cur == $b['playingAlly']) {
                echo '<option value="back_to_owner">Voltar ao Dono</option>';
            }

            if (in_array('parceiro', listPlayerTraits($cur), true) && empty($b['playingPartner'][$cur]) && in_array('aliado', listPlayerTraits($cur), true) && !$concentrado && !$agarrado && !$agarrando) {
                echo '<option value="start_partner">Formar Dupla com Parceiro</option>';
            }

            if (!empty($b['playingPartner'][$cur]) && in_array('parceiro', listPlayerTraits($cur), true)) {
                echo '<option value="end_partner">Separar Dupla</option>';
            }


            if (!empty($b['agarrao'][$cur]['agarrando'])) {
                echo '<option value="soltar_agarrao">Soltar agarrão (' . $b['agarrao'][$cur]['agarrando'] . ')</option>';
            }

            if (!empty($b['agarrao'][$cur]['agarrado'])) {
                echo '<option value="se_soltar_agarrao">Se soltar do agarrão (' . $b['agarrao'][$cur]['agarrado'] . ')</option>';
            }

            if (in_array('invisibilidade', listPlayerTraits($cur), true) && empty($b['notes'][$cur]['invisivel'])) {
                echo '<option value="activate_invisibilidade">Ficar invisível (1PM/turno)</option>';
            }      

            if (in_array('invisibilidade', listPlayerTraits($cur), true) && !empty($b['notes'][$cur]['invisivel'])) {
                echo '<option value="deactivate_invisibilidade">Voltar a ser visível</option>';
            }  

            if (!$concentrando && !$agarrado && !$agarrando) {
                echo '<option value="ataque">Atacar</option>';
                echo '<option value="multiple">Múltiplo</option>';


                if (in_array('tiro_multiplo', listPlayerTraits($cur)) ||
                    (in_array('tiro_multiplo', listPlayerTraits(getAlliePlayer($cur))) &&
                        in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($cur)))) ||
                    (!empty($b['playingPartner'][$cur]) &&
                        in_array('tiro_multiplo', listPlayerTraits($b['playingPartner'][$cur]['name'])))
                ) {
                    echo '<option value="tiro_multiplo">Tiro Múltiplo</option>';
                }

                if (in_array('agarrao', listPlayerTraits($cur)) ||
                    (in_array('agarrao', listPlayerTraits(getAlliePlayer($cur))) &&
                        in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($cur)))) ||
                    (!empty($b['playingPartner'][$cur]) &&
                        in_array('agarrao', listPlayerTraits($b['playingPartner'][$cur]['name'])))
                ) {
                    echo '<option value="agarrao">Agarrão</option>';
                }

                if (in_array('ataque_debilitante', listPlayerTraits($cur)) ||
                    (in_array('ataque_debilitante', listPlayerTraits(getAlliePlayer($cur))) &&
                        in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($cur)))) ||
                    (!empty($b['playingPartner'][$cur]) &&
                        in_array('ataque_debilitante', listPlayerTraits($b['playingPartner'][$cur]['name'])))
                ) {
                    echo '<option value="ataque_debilitante">Ataque Debilitante</option>';
                }
            }
            echo '</select>';


            $validTargets = [];
            foreach ($order as $o) {
                if ($o === $cur) continue;
                $targetNotes   = $b['notes'][$o] ?? [];
                $targetIncorp  = !empty($targetNotes['incorp_active']);
                if ($isIncorp === $targetIncorp) {
                    $validTargets[] = $o;
                }
            }

            $allies = getAllAllies();
            foreach ($allies as $ally) {
                if ($ally === $cur || !in_array(getAlliePlayer($ally), $order)) continue;
                $targetNotes  = $b['notes'][$ally] ?? [];
                $targetIncorp = !empty($targetNotes['incorp_active']);
                if ($isIncorp === $targetIncorp) {
                    $validTargets[] = $ally;
                }
            }

            $hasTargets = count($validTargets) > 0;


            // Ataque simples
            if ($hasTargets) {
                echo '<div id="atkSimple" style="display: none;"><fieldset><legend>Ataque</legend>'
                    . 'Tipo de Ataque: <select name="atkType" id="atkTypeSimple"><option value="F">F</option><option value="PdF">PdF</option></select><br>'
                    . 'Tipo de Dano: <select name="dmgType">'; 
                selectDmgType($cur);
                echo '</select><br>'
                    . 'Roll FA: <input id="dadoFA" type="number" name="dadoFA" required><br>'
                    . 'Alvo: <select name="target">';
                selectTarget($cur, $validTargets);
                echo '</select><br>'
                    . 'Reação: <select id="def" name="defesa">'
                    . '<option value="defender">Defender</option>'
                    . '<option value="defender_esquiva">Esquivar</option>'
                    . '<option value="indefeso">Indefeso</option>'
                    . '<option id="opt-deflexao-simples" value="defender_esquiva_deflexao">Deflexão (2 PM)</option>'
                    . '</select><br>'
                    . '<label>'
                    . 'Roll FD/Esq.: <input id="dadoFD" type="number" name="dadoFD" required>'
                    . '</label>'
                //Ataque debilitante
                    . '<br><div id="stat_debili">'
                    . '<label>Stat a ser debilitado: '
                    . '<select name="stat"> ' 
                    . '<option value="F">F</option> <option value="H">H</option> <option value="R">R</option> <option value="A">A</option> <option value="PdF">PdF</option>'
                    . '</select></label></div>'
                    . '</div></div>';
            }

            // Ataque múltiplo
            if ($hasTargets) {
                    echo '<div id="atkMulti" style="display:none"><fieldset><legend>Múltiplo</legend>';
                    if ($maxMulti >= 2){
                        echo 'Tipo de Ataque: <select name="atkType" id="atkTypeMulti"><option value="F">F</option><option value="PdF">PdF</option></select><br>'
                            . 'Tipo de Dano: <select name="dmgType">'; 
                        selectDmgType($cur); 
                        echo '</select><br>'
                            . 'Quantidade (2-' . $maxMulti . '): <input id="quant" type="number" name="quant" '
                            . 'min="2" max="' . $maxMulti . '" value="2"><br>'
                            . 'Alvo: <select name="targetMulti">';
                        selectTarget($cur, $validTargets);
                        echo '</select><br>'
                            . 'Reação: <select id="defM" name="defesaMulti">'
                            . '<option value="defender">Defender</option>'
                            . '<option id="opt-esquiva-multi" value="defender_esquiva">Esquivar</option>'
                            . '<option value="indefeso">Indefeso</option>'
                            . '<option id="opt-deflexao-multi" value="defender_esquiva_deflexao">Deflexão (2 PM)</option>'
                            . '</select>'
                            . '<div id="dCont"></div>';
                    } else {
                        echo 'Este personagem não tem H o suficiente para usar ataque múltiplo.';
                    }
                    echo '</fieldset></div>';
            }

            //Tiro múltiplo
            if ($hasTargets) {
                echo '<div id="atkTiroMulti" style="display:none"><fieldset><legend>Tiro Múltiplo</legend>'
                    . 'Tipo de Dano: <select name="dmgType">'; 
                selectDmgType($cur); 
                echo '</select><br>'
                    . 'Quantidade (1-' . $stats['H'] . '): <input id="quantTiro" type="number" name="quantTiro" '
                    . 'min="1" max="' . $stats['H'] . '" value="1"><br>'
                    . 'Alvo: <select name="targetTiroMulti">';
                selectTarget($cur, $validTargets);
                echo '</select><br>'
                    . 'Reação: <select id="defTiro" name="defesaTiroMulti">'
                    . '<option value="defender">Defender</option>'
                    . '<option id="opt-esquiva-tiro" value="defender_esquiva">Esquivar</option>'
                    . '<option value="indefeso">Indefeso</option>'
                    . '<option id="opt-deflexao-tiro" value="defender_esquiva_deflexao">Deflexão (2 PM)</option>'
                    . '</select><br>'
                    . '<div id="dContTiro"></div>'
                    . '<label>Roll FD/Esq.: '
                    . '<input id="dadoFDTiro" type="number" name="dadoFDTiro" required>'
                    . '</label>'
                    . '</fieldset></div>';
            }


            //Agarrão
            if ($hasTargets) {
                echo '<div id="atkAgarrao" style="display:none;"><fieldset><legend>Agarrão</legend>'
                    . 'Alvo: <select name="targetAgarrao">';
                foreach ($validTargets as $tgt) if ($tgt !== $cur) {
                    echo '<option value="' . htmlspecialchars($tgt) . '">'
                        . htmlspecialchars($tgt)
                        . '</option>';
                }
                echo '</select>'
                    . 'Roll Teste de Força: <input id="rollAgarrao" type="number" name="rollAgarrao" required><br>'
                    . '</fieldset></div>';
            }

            //Se soltar de um agarrão
            echo '<div id="soltarAgarrao" style="display:none;"><fieldset><legend>Teste para se Soltar do Agarrão</legend>'
                . 'Roll Teste de Força: <input id="rollSoltarAgarrao" type="number" name="rollSoltarAgarrao" required><br>'
                . '</fieldset></div>';



                    //Parceiro
            echo '<div id="partnerSelect" style="display:none;margin-top:0.5em;">'
                . '<fieldset><legend>Selecione o Parceiro</legend>'
                . '<select name="partner">';
            foreach (getPlayerPartner($cur) as $p) {
                echo '<option value="' . htmlspecialchars($p, ENT_QUOTES) . '">'
                    . htmlspecialchars($p, ENT_QUOTES)
                    . '</option>';
            }
            echo '</select>'
                . '</fieldset>'
                . '</div>';

        }

        //Aliado
        echo '<div id="allySelect" style="display:none;margin-top:0.5em;">'
            . '<fieldset><legend>Selecione o Parceiro</legend>'
            . '<select name="ally">';
        foreach (getPlayerAllies($cur) as $p) {
            echo '<option value="' . htmlspecialchars($p, ENT_QUOTES) . '">'
                . htmlspecialchars($p, ENT_QUOTES)
                . '</option>';
        }
        echo '</select>'
            . '</fieldset>'
            . '</div>';




        echo '<br><button type="submit">Executar</button> ';
        echo '<button type="button" onclick="history.back()">Voltar</button>';
        echo '</form>';

        





        // Form de Magias
        if(!$isDefeated){
            $has_magic_school = false;
            foreach (['magia_branca', 'magia_negra', 'magia_elemental', 'magia_de_sangue', 'arcano'] as $school) {
                if (in_array($school, listPlayerTraits($cur))) {
                    $has_magic_school = true;
                    break;
                }
            }
            if ($has_magic_school && empty($notes['furia'])) {
                echo '<h2>Magias</h2>';

                $susteinedSpellsText = htmlspecialchars($_SESSION['battle']['notes'][$cur]['sustained_spells'] ?? '', ENT_QUOTES);
                echo '<form method="post" action="?step=update_sustained_spells&player=' . urlencode($cur) . '">'
                .'<textarea name="sustained_spells" rows="3" cols="60" placeholder="Anote magias sustentadas aqui...">'.$susteinedSpellsText.'</textarea><br>'
                .'<button type="submit">Salvar Anotações de Magia</button>'
                .'</form>';

                $player_magics = getPlayerMagics($cur);
                if (!empty($player_magics)) {
                    echo '<form method="post" action="battle.php?step=act" id="magicForm">';
                    echo '<input type="hidden" name="action" value="cast_magic">';
                    echo '<input type="hidden" name="player" value="' . htmlspecialchars($cur, ENT_QUOTES) . '">';

                    // Select de Magias
                    echo '<select id="magicSelect" name="magic" style="margin-top: 10px;">';
                    echo '<option value="">-- Selecione uma Magia --</option>';
                    foreach ($player_magics as $magic) {
                        $slug = htmlspecialchars($magic['efeito_slug'], ENT_QUOTES);
                        $nome = htmlspecialchars($magic['nome'], ENT_QUOTES);

                        $magic_nome = (string)($magic['nome'] ?? '');
                        $magic_escola = (string)($magic['escola'] ?? '');
                        $magic_custo_desc = (string)($magic['custo_descricao'] ?? '');
                        $magic_duracao = (string)($magic['duracao'] ?? '');
                        $magic_alcance = (string)($magic['alcance'] ?? '');
                        $magic_descricao = str_replace(["\r", "\n"], ' ', (string)($magic['descricao'] ?? ''));

                        // Adicionando todos os dados da magia como data-attributes
                        echo "<option value=\"{$slug}\" " .
                             "data-nome=\"" . htmlspecialchars($magic['nome'], ENT_QUOTES) . "\" " .
                             "data-escola=\"" . htmlspecialchars($magic['escola'], ENT_QUOTES) . "\" " .
                             "data-custo-descricao=\"" . htmlspecialchars($magic['custo_descricao'], ENT_QUOTES) . "\" " .
                             "data-duracao=\"" . htmlspecialchars($magic['duracao'], ENT_QUOTES) . "\" " .
                             "data-alcance=\"" . htmlspecialchars($magic['alcance'], ENT_QUOTES) . "\" " .
                             "data-descricao=\"" . htmlspecialchars(str_replace(["\r", "\n"], ' ', $magic['descricao']), ENT_QUOTES) . "\">" .
                             $nome . "</option>";
                    }
                    echo '</select><br>';
                    echo '<div id="magicInfo" style="display:none; width: 440px; min-height: 120px; border: 1px solid #ccc; padding: 10px; margin-top: 10px;"></div>';
                    echo '<div id="magicInputsContainer" style="display:none;"><fieldset><legend>Opções da Magia</legend>';
                    echo '</fieldset></div>';
                    echo '<button type="submit">Lançar Magia</button>';
                    echo '</form>';
                } else {
                    echo '<p>Este personagem não conhece nenhuma magia.</p>';
                }
            }
        }

        //Arena
        echo '<h2>Arena</h2>';
        echo '<form method="post" action="?step=update_arena">';
        $arenaText = htmlspecialchars($_SESSION['battle']['arena'] ?? '', ENT_QUOTES);
        echo '<textarea name="arena" rows="8" cols="60">' . $arenaText . '</textarea><br>';
        echo '<button type="submit">Salvar Arena</button></form>';



        // Resumo Parcial
        echo '<h2>Resumo Parcial da Batalha</h2>';
        echo '<form method="post" action="?step=save_partial">';
        $lutadores = $b['players'];

        foreach ($b['players'] as $hasAlly) {
            if (getPlayerAllies($hasAlly)) {
                $lutadores = array_merge($lutadores, getPlayerAllies($hasAlly));
            }
        }
        foreach ($lutadores as $pl) {
            if ($pl === $cur) continue;
            $ps   = getPlayer($pl);
            $note = array_merge(['efeito' => '', 'posicao' => ''], $b['notes'][$pl] ?? []);
            $id   = preg_replace('/\W+/', '_', $pl);

            echo '<fieldset style="margin:0.5em 0;padding:0.5em;border:1px solid #ccc">';
            echo '<legend><strong>' . $pl . '</strong></legend>';
            echo '<input type="hidden" name="player_names[]" value="' . $pl . '">';

            foreach (['F', 'H', 'R', 'A', 'PdF', 'PV', 'PM', 'PE'] as $c) {
                $val = (int)($ps[$c] ?? 0);
                echo '<label style="display:block">' . $c . ': '
                    . '<input type="number" name="partial_stats[' . $pl . '][' . $c . ']" '
                    . 'value="' . $val . '" required></label>';
            }

            echo '<button type="button" onclick="toggleSection(\'sec_' . $id . '\')">'
                . 'Mostrar/Ocultar Detalhes</button>';

            echo '<div id="sec_' . $id . '" style="display:none;margin-top:.5em;">';

            echo 'Inventário:<br>'
                . '<textarea name="partial_stats[' . $pl . '][inventario]" rows="4" cols="60">'
                . htmlspecialchars($ps['inventario'] ?? '', ENT_QUOTES)
                . '</textarea><br>';

            echo 'Equipado:<br>'
                . '<textarea name="partial_stats[' . $pl . '][equipado]" rows="2" cols="60">'
                . htmlspecialchars($ps['equipado'] ?? '', ENT_QUOTES)
                . '</textarea><br>';

            echo 'Efeito:<br>'
                . '<textarea name="partial_stats[' . $pl . '][efeito]" rows="4" cols="60">'
                . htmlspecialchars($note['efeito'], ENT_QUOTES)
                . '</textarea><br>';

            echo 'Posição:<br>'
                . '<textarea name="partial_stats[' . $pl . '][posicao]" rows="2" cols="60">'
                . htmlspecialchars($note['posicao'], ENT_QUOTES)
                . '</textarea><br>';

            echo '</div>';
            echo '</fieldset>';
        }
        echo '<button type="submit">Salvar Resumo Parcial</button>';
        echo '</form>';


        $isInculto_php = in_array('inculto', listPlayerTraits($cur), true);
        $isInculto_js = $isInculto_php ? 'true' : 'false';
        
        echo <<<JS
<script>
document.addEventListener('DOMContentLoaded', () => {
  //Seletores de Elementos
  const actionSel   = document.getElementById('actionSelect');
  
  const atkSimple   = document.getElementById('atkSimple');
  const atkMulti    = document.getElementById('atkMulti');
  const atkTiro     = document.getElementById('atkTiroMulti');
  const atkAgarrao  = document.getElementById('atkAgarrao');
  const soltarAgarr = document.getElementById('soltarAgarrao');

  const atkTypeSimp = document.getElementById('atkTypeSimple');
  const atkTypeMult = document.getElementById('atkTypeMulti');

  const defSimple   = document.getElementById('def');
  const defMulti    = document.getElementById('defM');
  const defTiro     = document.getElementById('defTiro');

  const faInput     = document.getElementById('dadoFA');
  const fdInput     = document.getElementById('dadoFD');

  const quant       = document.getElementById('quant');
  const quantTiro   = document.getElementById('quantTiro');
  
  const dCont       = document.getElementById('dCont');
  const dContTiro   = document.getElementById('dContTiro');
  const dadoFDTiro  = document.getElementById('dadoFDTiro');
  const dadoAgarrao = document.getElementById('rollAgarrao');
  const dSoltAgarra = document.getElementById('rollSoltarAgarrao');

  const selAlvoSi   = document.querySelector('select[name="target"]');
  const selAlvoMu   = document.querySelector('select[name="targetMulti"]');
  const selAlvoTi   = document.querySelector('select[name="targetTiroMulti"]');
  const selAlvoAg   = document.querySelector('select[name="targetAgarrao"]');

  const esqSi       = document.getElementById('opt-esquiva-simples');
  const esqMu       = document.getElementById('opt-esquiva-multi');
  const esqTi       = document.getElementById('opt-esquiva-tiro');
  
  const deflexSim   = document.getElementById('opt-deflexao-simples');
  const deflexMulti = document.getElementById('opt-deflexao-multi');
  const deflexTiro  = document.getElementById('opt-deflexao-tiro');

  const selStatDebi = document.getElementById('stat_debili');

  const magicSelect = document.getElementById('magicSelect') ?? '';
  const magicInfo   = document.getElementById('magicInfo') ?? '';
  const magicInputsContainer = document.getElementById('magicInputsContainer') ?? '';
  const isInculto   = {$isInculto_js};


  //Funções Auxiliares

  function atualizaReacao(sel, esq) {
    if (!sel || !esq) return;

    const furia = sel.selectedOptions[0].dataset.furia   === '1';
    const agar  = sel.selectedOptions[0].dataset.agarrao === '1';
    esq.style.display = (furia || agar) ? 'none' : '';

    let reactSelId;
    if (esq.id === 'opt-esquiva-simples')   reactSelId = 'def';
    if (esq.id === 'opt-esquiva-multi')     reactSelId = 'defM';
    if (esq.id === 'opt-esquiva-tiro')      reactSelId = 'defTiro';

    const reactSel = document.getElementById(reactSelId);
    if (!reactSel) return;

    const defOpt = reactSel.querySelector('option[value="defender"]');
    if (defOpt) defOpt.style.display = agar ? 'none' : '';

    if (agar) {
      reactSel.value = 'indefeso';
      if (reactSelId === 'def')    onDef();
      if (reactSelId === 'defM')   onDefM();
      if (reactSelId === 'defTiro') onAct();
    }
  }

  async function onMagicSelect() {
    if (!magicSelect) return;

    magicInfo.style.display = 'none';
    magicInputsContainer.innerHTML = '';
    magicInputsContainer.style.display = 'none';

    const selectedOption = magicSelect.options[magicSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
      return;
    }
    
    const data = selectedOption.dataset;
    if (!isInculto) {
      magicInfo.style.display = 'block';
      magicInfo.innerHTML =
        '<strong>Nome:</strong> ' + data.nome + '<br>' +
        '<strong>Escola:</strong> ' + data.escola + '<br>' +
        '<strong>Custo:</strong> ' + data.custoDescricao + '<br>' +
        '<strong>Duração:</strong> ' + data.duracao + '<br>' +
        '<strong>Alcance:</strong> ' + data.alcance + '<br>' +
        '<strong>Descrição:</strong> ' + data.descricao;
    }

    const magicSlug = selectedOption.value;
    magicInputsContainer.style.display = 'block';
    magicInputsContainer.innerHTML = '<fieldset><legend>Opções da Magia</legend>Carregando opções...</fieldset>';
    
    try {
        const response = await fetch('battle.php?step=get_magic_inputs&magic='+magicSlug);
        if (!response.ok) {
            throw new Error('Erro ao buscar opções da magia.');
        }
        const htmlInputs = await response.text();
        if (htmlInputs.trim() !== ''){
            magicInputsContainer.innerHTML = '<fieldset><legend>Opções da Magia</legend>'+htmlInputs+'</fieldset>';
            magicInputsContainer.querySelectorAll('script').forEach(script => {
                const newScript = document.createElement('script');
                newScript.textContent = script.textContent;
                script.parentNode.replaceChild(newScript, script);
            });
        } else {
             magicInputsContainer.style.display = 'none';
        }
    } catch (error) {
        console.error("Falha na requisição da magia:", error);
        magicInputsContainer.innerHTML = '<fieldset><legend>Erro</legend>Não foi possível carregar as opções da magia.</fieldset>';
    }
  }

  function genMulti() {
    if (!quant || !dCont) return;
    let cnt = parseInt(quant.value, 10) || 0;
    let html = '';
    for (let i = 1; i <= cnt; i++) {
      html += '<label>Roll FA ' + i + ': <input type="number" name="dadosMulti[]" required></label><br>';
    }
    html += '<label id="fdLblMulti">Roll FD/Esq.: <input type="number" name="dadoFDMulti" id="dadoFDMulti" required></label><br>';
    dCont.innerHTML = html;
  }

  function genTiro() {
    if (!quantTiro || !dContTiro) return;
    let cnt = parseInt(quantTiro.value, 10) || 0;
    let html = '';
    for (let i = 1; i <= cnt; i++) {
      html += '<label>Roll PdF ' + i + ': <input type="number" name="dadosTiroMulti[]" required></label><br>';
    }
    dContTiro.innerHTML = html;
  }

  window.toggleSection = function(id) {
    const el = document.getElementById(id);
    if (el) {
      el.style.display = (el.style.display === 'none') ? 'block' : 'none';
    }
  };


  function manageDeflexaoSimple() {
    if (!atkTypeSimp || !selAlvoSi || !deflexSim) return;

    const showDeflexaoSim = atkTypeSimp.value == 'PdF' &&
                         selAlvoSi.selectedOptions[0]?.dataset.temDeflexao === '1';

    deflexSim.style.display = showDeflexaoSim ? 'block' : 'none';
    if (!showDeflexaoSim && defSimple.value === 'defender_esquiva_deflexao') {
        defSimple.value = 'defender_esquiva';
    }
  }

  function manageDeflexaoMulti() {
    if (!atkTypeMult || !selAlvoMu || !deflexMulti) return;

    const showDeflexaoMulti = atkTypeMult.value == 'PdF' &&
                           selAlvoMu.selectedOptions[0]?.dataset.temDeflexao === '1';

    deflexMulti.style.display = showDeflexaoMulti ? 'block' : 'none';
    if (!showDeflexaoMulti && defMulti.value === 'defender_esquiva_deflexao') {
        defMulti.value = 'defender_esquiva';
    }
  }

  function manageDeflexaoTiro() {
    if (!selAlvoTi || !deflexTiro) return;

    const showDeflexaoTiro = selAlvoTi.selectedOptions[0]?.dataset.temDeflexao === '1';

    deflexTiro.style.display = showDeflexaoTiro ? 'block' : 'none';
    if (!showDeflexaoTiro && defTiro.value === 'defender_esquiva_deflexao') {
        defTiro.value = 'defender_esquiva';
    }
  }

  function onAct() {
    const act = actionSel.value;

    //Habilitar/desabilitar inputs e torná-los required/not-required
    if (atkSimple) atkSimple.style.display = (act === 'ataque' || act === 'release_concentrar' || act === 'ataque_debilitante') ? 'block' : 'none';
    if (atkMulti) atkMulti.style.display  = (act === 'multiple') ? 'block' : 'none';
    if (atkTiro) atkTiro.style.display = (act === 'tiro_multiplo') ? 'block' : 'none';
    if (atkAgarrao) atkAgarrao.style.display = (act === 'agarrao') ? 'block' : 'none';
    if (soltarAgarr) soltarAgarr.style.display = (act === 'se_soltar_agarrao') ? 'block' : 'none';
    if (selStatDebi) selStatDebi.style.display = (act === 'ataque_debilitante') ? 'block' : 'none';


    if (faInput) faInput.required = (act === 'ataque' || act === 'release_concentrar' || act === 'ataque_debilitante');
    
    if (dadoAgarrao) {
        dadoAgarrao.required = (act === 'agarrao');
        dadoAgarrao.disabled = (act !== 'agarrao');
    }
    if (dSoltAgarra) {
        dSoltAgarra.required = (act === 'se_soltar_agarrao');
        dSoltAgarra.disabled = (act !== 'se_soltar_agarrao');
    }

    if (dadoFDTiro) {
        const isTiroVisible = atkTiro && atkTiro.style.display === 'block';
        dadoFDTiro.disabled = !isTiroVisible;
        dadoFDTiro.required = isTiroVisible && defTiro && defTiro.value !== 'indefeso';
    }

    const needFDSimple = faInput && faInput.required && defSimple && defSimple.value !== 'indefeso';
    if (fdInput) fdInput.required = needFDSimple;

    if (act === 'multiple') { genMulti(); } else if (dCont) { dCont.innerHTML = ''; }
    if (act === 'tiro_multiplo') { genTiro(); } else if (dContTiro) { dContTiro.innerHTML = ''; }

    onDefM();
  }

  function onDef() {
      if (!defSimple || !fdInput) return;
      const modo = defSimple.value;
      const needFD = (modo !== 'indefeso');
      fdInput.required = needFD;
  }
  
  function onDefM() {
      const fdLblMulti = document.getElementById('fdLblMulti');
      if (!defMulti || !fdLblMulti) return;
      const modo = defMulti.value;
      fdLblMulti.style.display = (modo !== 'indefeso') ? 'block' : 'none';
      const fdInputMulti = document.getElementById('dadoFDMulti');
      if (fdInputMulti) fdInputMulti.required = (modo !== 'indefeso');
  }

  //Event Listeners

  if (actionSel) {
    actionSel.addEventListener('change', e => {
      const partnerDiv = document.getElementById('partnerSelect');
      const allyDiv = document.getElementById('allySelect');
      if(partnerDiv) partnerDiv.style.display = (e.target.value === 'start_partner') ? 'block' : 'none';
      if(allyDiv) allyDiv.style.display = (e.target.value === 'use_ally') ? 'block' : 'none';
      manageDeflexaoSimple();
      manageDeflexaoMulti()
      manageDeflexaoTiro()
      onAct();
    });
  }

  if (selAlvoSi) selAlvoSi.addEventListener('change', () => atualizaReacao(selAlvoSi, esqSi));
  if (selAlvoMu) selAlvoMu.addEventListener('change', () => atualizaReacao(selAlvoMu, esqMu));
  if (selAlvoTi) selAlvoTi.addEventListener('change', () => atualizaReacao(selAlvoTi, esqTi));
  
  if (defSimple) defSimple.addEventListener('change', onDef);
  if (defMulti) defMulti.addEventListener('change', onDefM);
  if (defTiro) defTiro.addEventListener('change', onAct);

  if (quant) quant.addEventListener('input', () => { genMulti(); onDefM(); });
  if (quantTiro) quantTiro.addEventListener('input', genTiro);

  if (atkTypeSimp) atkTypeSimp.addEventListener('change', manageDeflexaoSimple);
  if (selAlvoSi)  selAlvoSi.addEventListener('change', manageDeflexaoSimple);
  if (atkTypeMulti) atkTypeMulti.addEventListener('change', manageDeflexaoMulti);
  if (selAlvoMu)  selAlvoMu.addEventListener('change', manageDeflexaoMulti);
  if (selAlvoTi)  selAlvoTi.addEventListener('change', manageDeflexaoTiro);

  if (magicSelect) { magicSelect.addEventListener('change', onMagicSelect); onMagicSelect(); }

  //Inicialização
  atualizaReacao(selAlvoSi, esqSi);
  atualizaReacao(selAlvoMu, esqMu);
  atualizaReacao(selAlvoTi, esqTi);
  
  manageDeflexaoTiro();
  manageDeflexaoMulti();
  manageDeflexaoSimple();

  onAct();
});
</script>
JS;
        break;

    // 4) Processar ação
    case 'act':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pl = $_POST['player'] ?? '';
            $out = '';
            if ($pl && in_array($pl, $b['players'], true)) {
                if (!isset($b['notes'][$pl])) {
                    $b['notes'][$pl] = [];
                }
                $b['notes'][$pl]['efeito']      = $_POST['efeito']     ?? '';
                $b['notes'][$pl]['posicao']     = $_POST['posicao']    ?? '';
                $b['notes'][$pl]['concentrado'] = (int)($_POST['concentrado'] ?? 0);
            }
            $origInitIndex = $b['init_index'];
            syncEquipBuffs($pl); 
            switch ($_POST['action'] ?? '') {

                case 'pass':
                    $out = "<strong>{$pl}</strong> passou seu turno.";
                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;



                case 'ataque':
                    $dFA  = (int)($_POST['dadoFA'] ?? 0);
                    $tipo = $_POST['atkType'] ?? 'F';
                    $tgt  = $_POST['target']  ?? '';
                    $dFD  = (int)($_POST['dadoFD'] ?? 0);
                    $def  = $_POST['defesa']   ?? 'defender';
                    $dmgType = $_POST['dmgType'] ?? '';

                    $dano = defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $tipo, $dmgType);

                    $out = "<strong>{$pl}</strong> atacou <strong>{$tgt}</strong> ({$tipo}): dano = {$dano}"
                        . applyDamage($pl, $tgt, $dano, $tipo, $out);

                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;



                case 'multiple':
                    $tipo  = $_POST['atkTypeMulti']   ?? 'F';
                    $tgt   = $_POST['targetMulti']    ?? '';
                    $q     = (int)($_POST['quant']    ?? 1);
                    $dados = $_POST['dadosMulti']      ?? [];
                    $dFD   = (int)($_POST['dadoFDMulti'] ?? 0);
                    $def   = $_POST['defesaMulti']     ?? 'defender';
                    $dmgType = $_POST['dmgType'] ?? '';

                    $dano = atkMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dFD, $def, $tipo, $dmgType);

                    $out = "<strong>{$pl}</strong> fez ataque múltiplo em <strong>{$tgt}</strong> ({$q}x{$tipo}): FA total = {$faTot}, dano = {$dano}";
                    $out .= applyDamage($pl, $tgt, $dano, $tipo, $out);

                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;



                case 'tiro_multiplo':
                    $tgt    = $_POST['targetTiroMulti']   ?? '';
                    $q      = (int)($_POST['quantTiro']   ?? 1);
                    $dados  = $_POST['dadosTiroMulti']    ?? [];
                    $dadoFD = (int)($_POST['dadoFDTiro']  ?? 0);
                    $def    = $_POST['defesaTiroMulti']   ?? 'defender';
                    $dmgType = $_POST['dmgType'] ?? '';

                    $dano = tiroMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dadoFD, $def, $dmgType);

                    $out = "<strong>{$pl}</strong> usou <em>Tiro Múltiplo</em> em <strong>{$tgt}</strong>\n({$q}xPdF): dano total = {$dano}";
                    applyDamage($pl, $tgt, $dano, "PdF", $out);

                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;

                    


                case 'start_concentrar':
                    if (empty($b['notes'][$pl]['concentrado'])) {
                        $b['notes'][$pl]['concentrado'] = 1;
                    }
                    $out = "<strong>{$pl}</strong> começou a se concentrar (rodada atual: +{$b['notes'][$pl]['concentrado']})";
                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;
                case 'release_concentrar':
                    $bonus = $b['notes'][$pl]['concentrado'] ?? 0;
                    $dFA  = (int)($_POST['dadoFA'] ?? 0) + $bonus;
                    $tipo = $_POST['atkType'] ?? 'F';
                    $tgt  = $_POST['target']  ?? '';
                    $dFD  = (int)($_POST['dadoFD'] ?? 0);
                    $def  = $_POST['defesa']   ?? 'defender';
                    $dmgType = $_POST['dmgType'] ?? '';

                    $dano = defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $tipo, $dmgType);

                    $out = "<strong>{$pl}</strong> atacou <strong>{$tgt}</strong> ({$tipo}): dano = {$dano} (Bonus de concentração = {$bonus})"
                        . applyDamage($pl, $tgt, $dano, $tipo, $out);

                    $b['notes'][$pl]['concentrado'] = 0;
                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;



                case 'agarrao':
                    $dF   = (int)($_POST['rollAgarrao'] ?? 0);
                    $tgt  = $_POST['targetAgarrao']  ?? '';

                    $r = agarrao($pl, $tgt, $dF);
                    if ($r) {
                        $b['agarrao'][$pl]['agarrando'] = $tgt;
                        $b['agarrao'][$tgt]['agarrado'] = $pl;
                        $b['agarrao'][$tgt]['flag'] = 0;
                        $out = "<strong>{$pl}</strong> agarrou <strong>{$tgt}</strong>!";
                    } else {
                        $out = "<strong>{$pl}</strong> falhou em agarrar <strong>{$tgt}</strong>!";
                    }
                    $b['notes'][$tgt]['efeito'] .= "\nAgarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).";
                    $b['notes'][$pl]['efeito'] .= "\nAgarrando um inimigo (Não pode realizar ações por si mesmo).";
                    unset($b['playingAlly']);
                    if (!empty($b['playingPartner'][$pl])) {
                        if (!empty($b['statsBackup'][$pl])) {
                            foreach ($b['statsBackup'][$pl] as $campo => $valor) {
                                setPlayerStat($pl, $campo, $valor);
                            }
                            unset($b['statsBackup'][$pl]);
                        }
                        unset($b['statsBackup'][$pl]);
                        unset($b['playingPartner'][$pl]);
                    }
                    $b['init_index']++;
                    break;
                case 'soltar_agarrao':
                    $tgt = $b['agarrao'][$pl]['agarrando'];
                    $itensARemover = [
                        'Agarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).',
                        'Agarrando um inimigo (Não pode realizar ações por si mesmo).'
                    ];
                    $b['notes'][$pl]['efeito'] = removeEffect($b['notes'][$pl]['efeito'], $itensARemover);
                    $b['notes'][$tgt]['efeito'] = removeEffect($b['notes'][$tgt]['efeito'], $itensARemover);
                    unset($b['agarrao'][$tgt]);
                    unset($b['agarrao'][$pl]);
                    $out = "<strong>{$pl}</strong> soltou <strong>{$tgt}</strong> de seu agarrão!";
                    break;
                case 'se_soltar_agarrao':
                    $dF   = (int)($_POST['rollSoltarAgarrao'] ?? 0) + $b['agarrao'][$pl]['flag'];
                    $tgt = $b['agarrao'][$pl]['agarrado'];
                    $r = agarrao($pl, $tgt, $dF);

                    if ($r) {
                        $itensARemover = [
                            'Agarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).',
                            'Agarrando um inimigo (Não pode realizar ações por si mesmo).'
                        ];
                        $b['notes'][$pl]['efeito'] = removeEffect($b['notes'][$pl]['efeito'], $itensARemover);
                        $b['notes'][$tgt]['efeito'] = removeEffect($b['notes'][$tgt]['efeito'], $itensARemover);
                        unset($b['agarrao'][$pl]);
                        unset($b['agarrao'][$tgt]);
                        $out = "<strong>{$pl}</strong> se soltou do agarrão de <strong>{$tgt}</strong>!";
                    } else {
                        $out = "<strong>{$pl}</strong> tentou se soltar do agarrão de <strong>{$tgt}</strong> mas falhou!";
                    } 
                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;



                case 'ataque_debilitante':
                    $dFA  = (int)($_POST['dadoFA'] ?? 0);
                    $tgt  = $_POST['target']  ?? '';
                    $dFD  = (int)($_POST['dadoFD'] ?? 0);
                    $def  = $_POST['defesa']   ?? 'defender';
                    $stat = $_POST['stat'] ?? 0;
                    $dmgType = $_POST['dmgType'] ?? '';

                    $dano = defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $tipo, $dmgType);
                    
                    $result = '';
                    if ($dano > 0){
                       $result = debilitateStat($tgt, $stat, $dFD);
                    }

                    $out = "<strong>{$pl}</strong> atacou <strong>{$tgt}</strong> (F): dano = {$dano} <br>"
                        . applyDamage($pl, $tgt, $dano, 'F', $out)."<br>"
                        . $result;

                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;




                case 'activate_draco':
                    $pm = getPlayerStat($pl, 'PM');
                    if ($pm >= 1) {
                        draconificacao($pl, true);
                        $b['notes'][$pl]['draco_active'] = true;
                        $voo = getPlayerStat($pl, 'H') * 20;
                        $out = "<strong>{$pl}</strong> ativou draconificação (+1PdF,+1R,+2H; Voo={$voo}m/s)";
                        $b['notes'][$pl]['efeito'] .= "\nDracônico: (+1PdF,+1R,+2H; Voo={$voo}m/s)";
                    } else {
                        $out = "<strong>{$pl}</strong> não tem PM suficientes para a draconificação.";
                    }
                    break;
                case 'deactivate_draco':
                    draconificacao($pl, false);
                    $b['notes'][$pl]['draco_active'] = false;
                    $out = "<strong>{$pl}</strong> desativou draconificação.";
                    unset($b['playingAlly']);
                    $b['init_index']++;
                    break;



                case 'activate_fusao':
                    $pm = getPlayerStat($pl, 'PM');
                    if ($pm >= 3) {
                        fusaoEterna($pl, $b['orig'][$pl]['PdF'], true);
                        $b['notes'][$pl]['fusao_active'] = true;
                        $out = "<strong>{$pl}</strong> ativou Fusão Eterna (F = ".($b['orig'][$pl]['PdF']*2)."; PdF = 0; -3 PMs.)";
                        $b['notes'][$pl]['efeito'] .= "\nForma Demoníaca:";
                        $b['notes'][$pl]['efeito'] .= "\nInvulnerável a fogo.(dano de fogo dividido por 10)";
                        $b['notes'][$pl]['efeito'] .= "\nVulnerável a Sônico e Elétrico.(Ignora sua armadura na FD)";
                    } else {
                        $out = "<strong>{$pl}</strong> não tem PM suficientes para ser possuído.";
                    }
                    break;
                case 'deactivate_fusao':
                    $origPdF = $b['orig'][$pl]['PdF'] ?? 0;
                    fusaoEterna($pl, $origPdF, false);
                    $b['notes'][$pl]['fusao_active'] = false;
                    $linhas = explode("\n", $b['notes'][$pl]['efeito']);
                    $linhas_filtradas = array_filter($linhas, function ($linha) {
                        return ! in_array(trim($linha), [
                            'Forma Demoníaca:',
                            'Invulnerável a fogo.(dano de fogo dividido por 10)',
                            'Vulnerável a Sônico e Elétrico.(Ignora sua armadura na FD)'
                        ], true);
                    });
                    $b['notes'][$pl]['efeito'] = implode("\n", $linhas_filtradas);
                    $out = "<strong>{$pl}</strong> desativou Fusão Eterna e voltou à forma normal.";
                    break;



                case 'activate_incorp':
                    $pm = getPlayerStat($pl, 'PM');
                    if (spendPM($pl, 2)) {
                        $b['notes'][$pl]['incorp_active'] = true;
                        $b['notes'][$pl]['efeito'] .= "\nIncorpóreo: imune a dano F/PdF; não pode usar F ou PdF.";
                        $out = "<strong>{$pl}</strong> tornou‑se Incorpóreo (−2 PM).";
                    } else {
                        $out = "<strong>{$pl}</strong> tentou ficar incorpóreo, mas não tem PM suficientes.";
                    }
                    break;
                case 'deactivate_incorp':
                    $b['notes'][$pl]['incorp_active'] = false;
                    // remove apenas a linha de efeito relativa
                    $linhas = explode("\n", $b['notes'][$pl]['efeito']);
                    $linhas = array_filter($linhas, function ($l) {
                        return strpos($l, 'Incorpóreo:') === false;
                    });
                    $b['notes'][$pl]['efeito'] = implode("\n", $linhas);
                    $out = "<strong>{$pl}</strong> retornou ao corpo físico.";
                    break;



                case 'enable_use_pv':
                    $b['notes'][$pl]['use_pv'] = true;
                    $out = "<strong>{$pl}</strong> ativou uso de PV no lugar de PM.";
                    break;
                case 'disable_use_pv':
                    unset($b['notes'][$pl]['use_pv']);
                    $out = "<strong>{$pl}</strong> voltou a usar PM normalmente.";
                    break;



                case 'extra_energy':
                    if (spendPM($pl, 2)) {
                        $b['notes'][$pl]['extra_energy_next'] = true;
                        $out = "<strong>{$pl}</strong> irá recuperar todos seus PVs até o próximo turno.";
                        unset($b['playingAlly']);
                        $b['init_index']++;
                    } else {
                        $out = "<strong>{$pl}</strong> não tem PMs o suficiente.";
                    }
                    break;

                case 'magia_extra':
                    if (magiaExtra($pl, 'spend')) {
                        $b['notes'][$pl]['magia_extra_next'] = true;
                        $out = "<strong>{$pl}</strong> irá recuperar todos seus PMs até o próximo turno.";
                        unset($b['playingAlly']);
                        $b['init_index']++;
                    } else {
                        $out = "<strong>{$pl}</strong> não está perto da morte para usar Magia Extra.";
                    }
                    break;                    



                case 'use_ally':
                    $b['playingAlly'] = $_POST['ally'];
                    header('Location: battle.php?step=turn');
                    exit;
                case 'back_to_owner':
                    unset($b['playingAlly']);
                    header('Location: battle.php?step=turn');
                    exit;



                case 'start_partner':
                    $chosen = $_POST['partner'] ?? '';
                    $b['statsBackup'][$pl] = [
                        'F' => getPlayerStat($pl, 'F'),
                        'H' => getPlayerStat($pl, 'H'),
                        'R' => getPlayerStat($pl, 'R'),
                        'A' => getPlayerStat($pl, 'A'),
                        'PdF' => getPlayerStat($pl, 'PdF'),
                    ];
                    $b['playingPartner'][$pl] = [
                        'name'  => $chosen,
                        'owner' => $pl,
                        'stats' => [
                            'F'   => max(getPlayerStat($chosen, 'F'), getPlayerStat($pl, 'F')),
                            'H'   => max(getPlayerStat($chosen, 'H'), getPlayerStat($pl, 'H')),
                            'R'   => max(getPlayerStat($chosen, 'R'), getPlayerStat($pl, 'R')),
                            'A'   => max(getPlayerStat($chosen, 'A'), getPlayerStat($pl, 'A')),
                            'PdF' => max(getPlayerStat($chosen, 'PdF'), getPlayerStat($pl, 'PdF'))
                        ]
                    ];
                    foreach ($b['playingPartner'][$pl]['stats'] as $campo => $valor) {
                        setPlayerStat($pl, $campo, $valor);
                    }
                    header('Location: battle.php?step=turn');
                    exit;
                case 'end_partner':
                    if (!empty($b['statsBackup'][$pl])) {
                        foreach ($b['statsBackup'][$pl] as $campo => $valor) {
                            setPlayerStat($pl, $campo, $valor);
                        }
                        unset($b['statsBackup'][$pl]);
                    }
                    unset($b['statsBackup'][$pl]);
                    unset($b['playingPartner'][$pl]);
                    header('Location: battle.php?step=turn');
                    exit;


                case 'activate_aceleracao_ii':
                    if (spendPM($pl, 2)) {
                        $b['notes'][$pl]['aceleracao_ii_active'] = true;
                        $out = "<strong>{$pl}</strong> gasta 2 PM e usa Aceleração II, ganhando uma ação extra!";
                    } else {
                        $out = "<strong>{$pl}</strong> não tem PMs suficientes para usar Aceleração II.";
                    }
                    break;

                case 'activate_invisibilidade':
                    if (spendPM($pl, 1)) { 
                        setPlayerStat($pl, 'PM', getPlayerStat($pl, 'PM')+1);
                        $b['notes'][$pl]['invisivel'] = true;
                        $efeitoInvisibilidade = "\nVocê está invisível.";
                        if (strpos($b['notes'][$pl]['efeito'], trim($efeitoInvisibilidade )) === false) {
                            $b['notes'][$pl]['efeito'] .= $efeitoInvisibilidade;
                        }
                        $out = "<strong>{$pl}</strong> está invisível (1PM/turno)";
                    } else {
                        $out = "<strong>{$pl}</strong> não tem PMs suficientes para usar Invisibilidade.";
                    }
                    break;
                case 'deactivate_invisibilidade':
                    unset($b['notes'][$pl]['invisivel']);
                    $out = "<strong>{$pl}</strong> está visível novamente.";
                    break;



                case 'fim':
                    header('Location: battle.php?step=final');
                    unset($b['playingAlly']);
                    $b['init_index']++;
                    exit;









                // Processamento de dados de magias
                case 'cast_magic':
                    $magic_slug = $_POST['magic'] ?? '';
                    $target = $_POST['magic_target'] ?? $pl; // Alvo padrão é o próprio conjurador
                    $pm_cost = (int)($_POST['magic_pm_cost'] ?? 1);
                    
                    if (empty($magic_slug)) {
                        $out = 'Nenhuma magia foi selecionada.';
                        break;
                    }

                    // Switch interno para lidar com cada magia
                    switch ($magic_slug) {
                        case 'bola_de_fogo':
                            // Esta magia precisa de 'magic_target' e 'magic_pm_cost'
                            $out = "<strong>{$pl}</strong> lançou Bola de Fogo em <strong>{$target}</strong> gastando {$pm_cost} PMs!";
                            // ... aqui viria a lógica de dano, gasto de PM, etc.
                            unset($b['playingAlly']);
                            $b['init_index']++;
                            break;
                        
                        case 'cura_magica':
                            // Precisa de 'magic_target' e 'magic_heal_type'
                            $heal_type = $_POST['magic_heal_type'] ?? 'heal_pv';
                            $cost = ($heal_type === 'heal_pv') ? 2 : 4;
                            $out = "<strong>{$pl}</strong> usou Cura Mágica em <strong>{$target}</strong> (custo: {$cost} PMs).";
                            // ... lógica de cura ...
                            // Magias de cura geralmente não passam o turno, então não incrementamos init_index.
                            break;
                            
                        case 'ataque_magico':
                            $pmCost = $_POST['magic_pm_cost'] ?? 1;
                            $atkType = $_POST['magic_attack_type'] ?? 'F';
                            $tgtsInfo = $_POST['magic_targets'] ?? ['']; //alvos [name] + reação [reaction] + dado de defesa [rollFD] + dado de ataque [rollFA]

                            $out = ataqueMagico($b, $pl, $tgtsInfo, $pmCost, $atkType);
                        
                            unset($b['playingAlly']);
                            $b['init_index']++;
                            break;

                        case 'lanca_infalivel_de_talude':
                            $pmCost = $_POST['magic_pm_cost'] ?? 1;
                            $tgtsInfo = $_POST['magic_targets'] ?? ['']; //alvos [name] + lancas atacando ele [qtdAtk]

                            $out = lancaInfalivelDeTalude($b, $pl, $tgtsInfo, $pmCost);
                        
                            unset($b['playingAlly']);
                            $b['init_index']++;
                            break;

                        case 'brilho_explosivo':
                            $tgt = $_POST['target'] ?? [''];
                            $dadoFD = $_POST['dadoFD'] ?? [''];
                            $somaDosDadosFA = 0;
                            for ($i = 1; $i < 11; $i++){
                                $somaDosDadosFA += $_POST['dado'.$i] ?? [0];
                            }

                            $out = brilhoExplosivo($b, $pl, $tgt, $somaDosDadosFA, $dadoFD);
                        
                            unset($b['playingAlly']);
                            $b['init_index']++;
                            break;
                            
                        case 'morte_estelar':
                            $tgt = $_POST['target'] ?? [''];

                            $out = morteEstelar( $pl, $tgt);
                        
                            unset($b['playingAlly']);
                            $b['init_index']++;
                            break;


                        default:
                            $out = "A magia '{$magic_slug}' foi selecionada, mas sua lógica ainda não foi implementada.";
                            unset($b['playingAlly']);
                            $b['init_index']++;
                            break;
                    }
                    break;

                    
                default:
                    $out = 'Ação inválida ou não reconhecida.';
                    break;
            }





            if ($b['notes'][$pl]['draco_active'] && empty($b['notes'][$pl]['draco_flag'])){
                $b['notes'][$pl]['draco_flag'] = true;
            }
            if (!empty($b['notes'][$pl]['invisivel']) && empty($b['notes'][$pl]['invisivel_flag'])){
                $b['notes'][$pl]['invisivel_flag'] = true;
            }


            if ($origInitIndex != $b['init_index'] && !empty($b['notes'][$pl]['aceleracao_ii_active'])){
                $b['init_index']--;
                unset($b['notes'][$pl]['aceleracao_ii_active']);
            }

            $b['needs_reload'] = true;

            $total   = count($b['order']);
            if ($b['init_index'] % $total === 0) {
                $b['round']++;
                foreach ($b['notes'] as $player => &$note) {
                    if (!empty($note['concentrado'])) {
                        $note['concentrado']++;
                    }
                }
                unset($note);
            }

            echo '<p>' . $out . '</p><p><a href="battle.php?step=turn">Próximo Turno</a></p>';
        } else {
            echo '<p>Nenhum dado recebido.</p><p><a href="battle.php?step=turn">Voltar</a></p>';
        }
        exit;

        // 4.5) Salvar Resumo Parcial
    case 'save_partial':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $names        = $_POST['player_names']   ?? [];
            $partialStats = $_POST['partial_stats']  ?? [];
            $partialNotes = $_POST['partial_stats']  ?? [];

            foreach ($names as $pl) {
                if (isset($partialStats[$pl])) {
                    foreach ($partialStats[$pl] as $campo => $valor) {
                        if (in_array($campo, ['F', 'H', 'R', 'A', 'PdF', 'PV', 'PM', 'PE'], true)) {
                            setPlayerStat($pl, $campo, (int)$valor);
                        } elseif (in_array($campo, ['inventario', 'equipado'], true)) {
                            setPlayerStat($pl, $campo, $valor);
                        }
                    }
                }
                if (isset($partialStats[$pl]['efeito'])) {
                    $b['notes'][$pl]['efeito'] = $partialStats[$pl]['efeito'];
                }
                if (isset($partialStats[$pl]['posicao'])) {
                    $b['notes'][$pl]['posicao'] = $partialStats[$pl]['posicao'];
                }
            }
        }
        header('Location: battle.php?step=turn');
        exit;

        // 5) Tela Final
    case 'final':

        if (!empty($b['orig'])){
            foreach ($b['orig'] as $pl => $origStats) {
                foreach ($origStats as $stat => $valor) {
                    setPlayerStat($pl, $stat, $valor);
                }
            }
            unset($b['orig']);
        }
        // formulário de resumo final
        echo '<h1>Resumo da Batalha</h1>';
        echo '<form method="post" action="battle.php?step=save_final">';
        foreach ($b['players'] as $pl) {
            $stats = getPlayer($pl);
            echo '<fieldset style="margin-bottom:1em;padding:1em;border:1px solid #ccc">';
            echo '<legend><strong>' . htmlspecialchars($pl, ENT_QUOTES) . '</strong></legend>';
            echo '<input type="hidden" name="player_names[]" value="' . htmlspecialchars($pl, ENT_QUOTES) . '">';
            foreach (['F', 'H', 'R', 'A', 'PdF', 'PV', 'PM', 'PE'] as $c) {
                echo '<label style="display:block;margin:0.5em 0">'
                    . $c . ': <input type="number" '
                    . 'name="stats[' . htmlspecialchars($pl, ENT_QUOTES) . '][' . $c . ']" '
                    . 'value="' . (int)$stats[$c] . '" required>'
                    . '</label>';
            }
            // Inventário e Equipado
            $inv = htmlspecialchars($stats['inventario'] ?? '', ENT_QUOTES);
            $eq  = htmlspecialchars($stats['equipado']   ?? '', ENT_QUOTES);
            echo 'Inventário:<br>'
                . '<textarea name="stats[' . htmlspecialchars($pl, ENT_QUOTES) . '][inventario]" rows="4" cols="60">'
                . $inv . '</textarea><br>';
            echo 'Equipado:<br>'
                . '<textarea name="stats[' . htmlspecialchars($pl, ENT_QUOTES) . '][equipado]" rows="2" cols="60">'
                . $eq . '</textarea><br>';
            echo '</fieldset>';
        }
        // Reseta sessão de batalha
        $b['init_index'] = 0;
        $b['round']      = 1;
        $b['notes']      = [];
        echo '<button type="submit">Salvar Alterações</button> '
            . '<a href="index.php">Nova Batalha</a></form>';
        exit;

        // 6) Salvar Final
    case 'save_final':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $names    = $_POST['player_names'] ?? [];
            $allStats = $_POST['stats']        ?? [];
            foreach ($names as $pl) {
                if (isset($allStats[$pl])) {
                    foreach ($allStats[$pl] as $campo => $valor) {
                        setPlayerStat($pl, $campo, ($campo === 'inventario' || $campo === 'equipado') ? $valor : (int)$valor);
                    }
                }
            }
            session_destroy();
            echo '<p>Alterações salvas com sucesso!</p>';
            echo '<p><a href="index.php">Nova Batalha</a></p>';
        }
        exit;

    default:
        header('Location: battle.php');
        exit;
}

?>