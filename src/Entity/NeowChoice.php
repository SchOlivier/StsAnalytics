<?php

namespace App\Entity;

use App\Entity\ref\neow\NeowBonus;
use App\Entity\ref\neow\NeowCost;

class NeowChoice
{
    public function __construct(private NeowCost $cost, private NeowBonus $bonus)
    {
    }

    public function getCost(): NeowCost
    {
        return $this->cost;
    }

    public function getBonus(): NeowBonus
    {
        return $this->bonus;
    }
}
