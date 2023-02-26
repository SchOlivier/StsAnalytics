<?php

namespace App\Entity\room;

use App\Entity\ref\Event as RefEvent;

class Event implements IRoom
{

    public function __construct(
        private int $damageHealed,
        private int $goldGain,
        private int $damageTaken,
        private int $maxHPGain,
        private int $maxHPLoss,
        private int $goldLoss,
        private RefEvent $refEvent,
        private array $cardsObtained,
        private array $cardsTransformed,
        private string $playerChoice
    ) {
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getRoomRecap(): string
    {
        $string = $this->refEvent->getLabel() . ":\n";
        $string .= "Choice : " . $this->playerChoice . "\n";
        $string .= "\tGold gained : " . $this->goldGain . "\n";
        $string .= "\tDamage Healed : " . $this->damageHealed . "\n";
        $string .= "\tDamage Taken : " . $this->damageTaken . "\n";
        $string .= "\tMax HP Gained : " . $this->maxHPGain . "\n";
        $string .= "\tMax HP Loss : " . $this->maxHPLoss . "\n";
        $string .= "\tGold Loss : " . $this->goldLoss . "\n";

        if (count($this->cardsObtained) > 0) {
            $string .= "\tCards obtained : \n";
            foreach ($this->cardsObtained as $card) {
                $string .= "\t" . $card->getLabel() . "\n";
            }
        }
        if (count($this->cardsTransformed) > 0) {
            $string .= "\tCards transformed : \n";
            foreach ($this->cardsTransformed as $card) {
                $string .= "\t" . $card->getLabel() . "\n";
            }
        }

        return $string;
    }
}
