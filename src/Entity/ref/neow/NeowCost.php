<?php

namespace App\Entity\ref\neow;

use Exception;

class NeowCost
{

    public static function createByCode($code) : self
    {
        $costs = [
            "NONE" => "None",
            "PERCENT_DAMAGE" => "Take damage",
            "CURSE" => "Get a curse",
            "TEN_PERCENT_HP_LOSS" => "Lose Max HP",
            "NO_GOLD" => "Lose all gold"
        ];
        if (!isset($costs[$code])) throw new Exception("Error, the requested Neow cost ($code) doesn't exist.");
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
