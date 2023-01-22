<?php

namespace App\Entity;

use App\Entity\ref\item\Card;

class DeckCard
{
    public function __construct(
        private Card $card,
        private int $upgradeLevel
    ){}
}