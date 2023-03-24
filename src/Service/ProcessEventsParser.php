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

            $cardCodes = $jsonEvent->cards_obtained ?? [];
            $cardsObtained = [];
            foreach ($cardCodes as $card) {
                $cardsObtained[] = Card::createByCode($card);
            }

            $cardTransformedCodes = $jsonEvent->cards_transformed ?? [];
            $cardsTransformed = [];
            foreach ($cardTransformedCodes as $card) {
                $cardsTransformed[] = Card::createByCode($card);
            }

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
                playerChoice: $jsonEvent->player_choice
            );

            /** @var FloorRecap */
            $recap = $floorRecaps[$floor];
            $recap->addRoom($event);
        }
    }
}