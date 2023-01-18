<?php

class Campfire implements IRoom
{

    private EnumCampfireChoice $choice;

    public function getRoomRecap(): string
    {
        return $this->choice->value;
    }

    public function getName(): string
    {
        return self::class;
    }
}