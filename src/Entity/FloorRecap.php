<?php

namespace App\Entity;

use App\Entity\ref\enum\EnumPath;

class FloorRecap
{
    private int $floor;
    private int $currentGold;
    private int $currentHP;
    private int $maxHP;
    private EnumPath $path;
    private array $purchases;
    private array $upgrades;
    private array $purges;
    private array $rooms;
    private array $rewards;
    private array $potionUse;

    public function __construct(

    ){}

    public function __toString()
    {
        
    }
}