<?php

function Pokemon(...$pokemon){
    return array(...$pokemon);
}

function ExibirPokemonList($pokemonList){
    echo "-{Sua lista de Pokemons}-<br>";
    foreach ($pokemonList as $pokemon){
        echo '<br>';
        foreach ($pokemon as $pokeStat => $statInfo){
            echo "[".$pokeStat."] == ".$statInfo."<br>";
        }
    } 
    echo '---------------------------<br><br><br>';
}

function DefMaiorOitenta($pokemonList){
    echo "-{Pokemons na lista com defesa > 80}-<br>";
    foreach ($pokemonList as $pokemon){
        if ($pokemon['Defesa']>80){
            echo '<br>';
            foreach ($pokemon as $pokeStat => $statInfo){
                echo "[".$pokeStat."] == ".$statInfo."<br>";
            }
        }
    } 
    echo '---------------------------<br><br><br>';
}

$chariz = array(
    'Nome' => 'Charizard',
    'Tipo' => 'Fogo',
    'Ataque' => 84,
    'Defesa' => 78,
    'HP' => 70
);

$blasto = array(
    'Nome' => 'Blastoise',
    'Tipo' => 'Água',
    'Ataque' => 83,
    'Defesa' => 100,
    'HP' => 79
);

$venusa = array(
    'Nome' => 'Venosaur',
    'Tipo' => 'Grama',
    'Ataque' => 82,
    'Defesa' => 83,
    'HP' => 80
);

$pikach = array(
    'Nome' => 'Pikachu',
    'Tipo' => 'Raio',
    'Ataque' => 75,
    'Defesa' => 70,
    'HP' => 90
);

$meusPokemons = Pokemon($chariz, $blasto, $venusa, $pikach);
ExibirPokemonList($meusPokemons);
DefMaiorOitenta($meusPokemons);

?>