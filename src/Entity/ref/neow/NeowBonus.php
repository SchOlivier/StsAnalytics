<?php

namespace App\Entity\ref\neow;

use Exception;

class NeowBonus
{
    public static function createByCode($code): self
    {
        $costs = [
            "NONE" => "None",
            "TRANSFORM_CARD" => "Transform a card",
            "THREE_RARE_CARDS" => "Choose a rare card to obtain",
            "RANDOM_COLORLESS" => "Get a random colorless card",
            "BOSS_RELIC" => "Trade your starting relic for a random boss relic",
            "THREE_ENEMY_KILL" => "Enemies in the next three combat will have one health.",
            "TEN_PERCENT_HP_BONUS" => "Gain 10% max HP",
            "TRANSFORM_TWO_CARDS" => "Transform 2 cards.",
            "THREE_CARDS" => "Choose a card to obtain.",
            "ONE_RANDOM_RARE_CARD" => "Obtain a random rare card.",
            "HUNDRED_GOLD" => "Get 100 gold.",
            "ONE_RARE_RELIC" => "Get a random rare relic.",
            "TWO_FIFTY_GOLD" => "Gain 250 gold.",
            "REMOVE_CARD" => "Remove a card.",
            "RANDOM_COLORLESS_2" => "Get a random colorless card.",
            "RANDOM_COMMON_RELIC" => "Obtain a random common relic.",
            "TWENTY_PERCENT_HP_BONUS" => "Gain 20% max HP."
        ];
        if (!isset($costs[$code])) throw new Exception("Error, the requested Neow Bonus ($code) doesn't exist.");
        return new self($code, $costs[$code]);
    }

    private function __construct(private string $code, private string $label)
    {
    }
    
    public function __toString()
    {
        return $this->label;
    }
}
