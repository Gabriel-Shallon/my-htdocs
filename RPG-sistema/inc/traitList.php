<?php

// traitList.php

require_once 'func.php';

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
        $res[] = strtolower(str_replace(' ','_', $n));
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

// Efeitos:


// ASSOMBRADO: precisa de input 'roll_assombrado' no form
function apply_assombrado(string $player, array $input): ?string {
    if (empty($_SESSION['battle']['orig'][$player])) {
        foreach (['F','H','R','A','PdF'] as $s) {
            $_SESSION['battle']['orig'][$player][$s] = getPlayerStat($player, $s);
        }
    }
    $d = intval($input['roll_assombrado'] ?? 0);
    if ($d >= 4 && $d <= 6) {
        foreach (['F','H','R','A','PdF'] as $s) {
            $current = &$_SESSION['battle']['stats'][$player][$s];
            $current = max(0, $current - 1);
        }
        return 'Assombrado(-1 F,H,R,A,PdF)';
    }
    return null;
}


// REGENERAÇÃO: +1 PV todo turno
function apply_regeneracao(string $player): void {
    $pv  = getPlayerStat($player,'PV') + 1;
    $max = getPlayerStat($player, 'PV_max');
    setPlayerStat($player, 'PV', min($pv, $max));
    echo "<p><em>Regeneração:</em> +1 PV (máx {$max}).</p>";
}








?>