<?php

namespace App\Entity\room;

use App\Entity\ref\enum\EnumCampfireChoice;
use App\Entity\ref\item\Card;
use App\Entity\ref\item\Potion;

class Campfire implements IRoom
{

    private EnumCampfireChoice $choice;

    public function getRoomRecap(): string
    {
        return "Campfire : " . $this->choice->value;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getChoice(): EnumCampfireChoice
    {
        return $this->choice;
    }

    public function setChoice(EnumCampfireChoice $choice)
    {
        $this->choice = $choice;
    }
}
