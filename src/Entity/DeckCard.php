<?php

namespace App\Entity;

use App\Entity\ref\item\Card;

class DeckCard
{
    public function __construct(
        private Card $card,
        private int $upgradeLevel
    ){}

    public function __toString()
    {
        $level = $this->upgradeLevel > 0 ? "+" . $this->upgradeLevel : "";
        return $this->card->__toString() . $level;
    }
}