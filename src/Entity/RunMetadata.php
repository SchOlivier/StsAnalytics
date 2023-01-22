<?php

namespace App\Entity;

use App\Entity\ref\enum\EnumHeroClass;

class RunMetadata
{
    public function __construct(
        private EnumHeroClass $class,
        private int $ascensionLevel,
        private bool $isVictory,
        private int $score,
        private string $seed,
        private int $floorReached,
        private int $playTime,
        private NeowChoice $neowChoice
    ) {
    }
}
