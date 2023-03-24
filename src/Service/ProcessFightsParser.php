<?php

namespace App\Service;

use App\Entity\ref\Encounter;
use App\Entity\room\Fight;
use stdClass;

class ProcessFightsParser
{
    public static function processFights(array &$floorRecaps, stdClass $jsonSave): void
    {
        $jsonEncounters = $jsonSave->damage_taken;
        foreach ($jsonEncounters as $jsonEncounter) {
            $floor = $jsonEncounter->floor;
            $encounter = Encounter::createByCode($jsonEncounter->enemies);
            $fight = new Fight(
                nbTurn: $jsonEncounter->turns,
                damageTaken: $jsonEncounter->damage,
                encounter: $encounter
            );

            /** @var FloorRecap */
            $recap = $floorRecaps[$floor];
            $recap->addRoom($fight);
        }
    }
}