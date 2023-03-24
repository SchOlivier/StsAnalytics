<?php

namespace App\Service;

use App\Entity\ref\enum\EnumCampfireChoice;
use App\Entity\ref\item\Card;
use App\Entity\room\Campfire;
use Exception;
use stdClass;

class ProcessCampfiresParser
{
    public static function processCampfires(array &$floorRecaps, stdClass $jsonSave): void
    {
        $campfiresJson = $jsonSave->campfire_choices;
        foreach ($campfiresJson as $campfireJson) {
            $floor = $campfireJson->floor;
            $campfire = new Campfire();

            $choice = EnumCampfireChoice::from($campfireJson->key);
            $campfire->setChoice($choice);

            $floorRecaps[$floor]->addRoom($campfire);
            switch ($choice) {
                case EnumCampfireChoice::Smith:
                    $jsonCard = $campfireJson->data;
                    $card = Card::createByCode($jsonCard);
                    if(!$card) throw new Exception("Card not found :" . $jsonCard);
                    $floorRecaps[$floor]->addUpgrade($card);
                    break;
                case EnumCampfireChoice::Toke:
                    $jsonCard = $campfireJson->data;
                    if(!$card) throw new Exception("Card not found :" . $jsonCard);
                    $card = Card::createByCode($jsonCard);
                    $floorRecaps[$floor]->addPurge($card);
                    break;
            }
        }
    }
}