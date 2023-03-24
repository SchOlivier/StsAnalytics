<?php

namespace App\Service;

use App\Entity\ref\Event as RefEvent;
use App\Entity\ref\item\Card;
use App\Entity\room\Event;
use stdClass;

class ProcessEventsParser
{
    //Add card upgrade
    public static function processEvents(array $floorRecaps, stdClass $jsonSave): void
    {
        $jsonEvents = $jsonSave->event_choices;
        foreach ($jsonEvents as $jsonEvent) {
            $floor = $jsonEvent->floor;
            $refEvent = RefEvent::createByCode($jsonEvent->event_name);

            $cardsObtained = self::createCardFromList($jsonEvent, "cards_obtained");
            $cardsTransformed = self::createCardFromList($jsonEvent, "cards_transformed");
            $cardUpgraded = self::createCardFromList($jsonEvent, "cards_upgraded");
            $cardRemoved = self::createCardFromList($jsonEvent, "cards_removed");

            $event = new Event(
                damageHealed: $jsonEvent->damage_healed,
                goldGain: $jsonEvent->gold_gain,
                damageTaken: $jsonEvent->damage_taken,
                maxHPGain: $jsonEvent->max_hp_gain,
                maxHPLoss: $jsonEvent->max_hp_loss,
                goldLoss: $jsonEvent->gold_loss,
                refEvent: $refEvent,
                cardsObtained: $cardsObtained,
                cardsTransformed: $cardsTransformed,
                playerChoice: $jsonEvent->player_choice,
                cardsUpgraded : $cardUpgraded,
                cardsRemoved : $cardRemoved
            );

            /** @var FloorRecap */
            $recap = $floorRecaps[$floor];
            $recap->addRoom($event);
        }
    }

    private static function createCardFromList(stdClass $jsonEvent, string $key): array
    {
        $jsonCardList = $jsonEvent->$key ?? [];
        $cardsList = [];
        foreach ($jsonCardList as $card) {
            $cardsList[] = Card::createByCode($card);
        }
        return $cardsList;
    }
}
