<?php

namespace App\Entity\room;

use App\Entity\ref\Encounter;

class Fight implements IRoom
{
    public function __construct(
        private int $nbTurn,
        private int $damageTaken,
        private Encounter $encounter
    ) {
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getRoomRecap(): string
    {
        return $this->encounter->getType() . " Fight : " . $this->encounter->getLabel() . "\n" .
        $this->damageTaken . " damage taken";
    }
}
