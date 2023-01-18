<?php

class Event implements IRoom
{
    //ref dans des json
    // private refEvent $eventName;
    private int $damageHealed;
    private int $goldGain;
    // private refEventChoice $playerChoice;
    private int $damageTaken;
    private int $maxHPGain;
    private int $maxHPLoss;
    private int $goldLoss;

    public function getName(): string
    {
        return self::class;    
    }

    public function getRoomRecap(): string
    {
        return "todo";
    }
}