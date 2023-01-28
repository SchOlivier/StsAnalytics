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
        private int $playTime
    ) {
    }

    public function __toString()
    {
        $string = "Classe : " . $this->class->value . "\n";
        $string .= "Ascension : " . $this->ascensionLevel . "\n";
        $string .= "Victoire ? " . ($this->isVictory ? "oui" : "non") . "\n";
        $string .= "Score : " . $this->score . "\n";
        $string .= "Seed :" . $this->seed . "\n";
        $string .= "Etage atteint : " . $this->floorReached . "\n";
        $string .= "Temps de jeu : " . $this->playTime . "s\n";

        return $string;
    }
}
