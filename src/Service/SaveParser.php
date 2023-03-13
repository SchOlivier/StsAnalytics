<?php

namespace App\Service;

use App\Entity\FloorRecap;
use App\Entity\NeowChoice;
use App\Entity\ref\Encounter;
use App\Entity\ref\enum\EnumCampfireChoice;
use App\Entity\ref\enum\EnumCardType;
use App\Entity\ref\enum\EnumColor;
use App\Entity\ref\enum\EnumHeroClass;
use App\Entity\ref\enum\EnumPath;
use App\Entity\ref\enum\EnumRarity;
use App\Entity\ref\enum\EnumRewardType;
use App\Entity\ref\Event as RefEvent;
use App\Entity\ref\item\Card;
use App\Entity\ref\item\Potion;
use App\Entity\ref\item\Relic;
use App\Entity\ref\neow\NeowBonus;
use App\Entity\ref\neow\NeowCost;
use App\Entity\ref\SingingBowl;
use App\Entity\Reward;
use App\Entity\room\Campfire;
use App\Entity\room\Event;
use App\Entity\room\Fight;
use App\Entity\room\IRoom;
use App\Entity\room\Shop;
use App\Entity\Run;
use App\Entity\RunMetadata;
use Exception;

class SaveParser
{
    private mixed $jsonSave;

    public function loadSave(string $savePath): Run
    {
        $this->loadJson($savePath);

        $metada = $this->getRunMetadata();
        $relics = $this->getRelics();
        $deck = $this->getDeck();
        $neowChoice = $this->getNeowChoice();
        $floorRecaps = $this->getFloorRecaps();

        $run = new Run($metada, $floorRecaps, $relics, $neowChoice, $deck);

        return $run;
    }

    private function loadJson(string $savePath): void
    {
        $this->jsonSave = json_decode(file_get_contents($savePath));
    }

    private function getRunMetadata(): RunMetadata
    {
        //analyser le json
        $ascension = $this->jsonSave->ascension_level;
        $hero = EnumHeroClass::from($this->jsonSave->character_chosen);
        $isVictory = $this->jsonSave->victory;
        $score = $this->jsonSave->score;
        $seed = $this->jsonSave->seed_played;
        $floor_reached = $this->jsonSave->floor_reached;
        $playtime = $this->jsonSave->playtime;

        return new RunMetadata($hero, $ascension, $isVictory, $score, $seed, $floor_reached, $playtime);
    }

    private function getNeowChoice(): NeowChoice
    {
        $neowCostCode = $this->jsonSave->neow_cost;
        $neowCost = NeowCost::createByCode($neowCostCode);
        $neowBonusCode = $this->jsonSave->neow_bonus;
        $neowBonus = NeowBonus::createByCode($neowBonusCode);
        return new NeowChoice($neowCost, $neowBonus);
    }

    private function getRelics(): array
    {
        $relics = $this->jsonSave->relics;

        $arr = [];
        foreach ($relics as $relic) {
            $arr[] = Relic::createByCode($relic);
        }

        return $arr;
    }

    private function getDeck(): array
    {
        $deck = $this->jsonSave->master_deck;
        $arr = [];
        foreach ($deck as $card) {
            $arr[] = Card::createByCode($card);
        }

        return $arr;
    }

    private function getFloorRecaps(): array
    {
        $floorRecaps = [];
        $path_per_floor = $this->jsonSave->path_per_floor;

        $nbUndefined = 0;
        $path_taken = $this->jsonSave->path_taken;

        foreach ($path_per_floor as $level => $path) {
            if (!$path) $nbUndefined++;

            $floorRecap = new FloorRecap;
            $this->createScalars($floorRecap, $level, $path);
            $floorRecap->setPath($this->getPathTaken($level, $nbUndefined, $path));

            // $room = $this->createRoom($level, $path);
            // $floorRecap->addRoom($room);
            $floorRecaps[$floorRecap->getFloor()] = $floorRecap;
        }

        $this->processCampfires($floorRecaps); // campfire_choices
        $this->processFights($floorRecaps); // damage_taken
        $this->processEvents($floorRecaps); // event_choices
        $this->processRewards($floorRecaps); // potions_obtained, card_choices, relics_obtained, boss_relics

        //TODO :
        // upgrades (in events, astrolabe, whetstone, war pain, tiny house, ???)
        // purges (in events, empty cage, transform?)
        // purchases

        // repasser le json en revue et voir si on a traité toutes les clefs

        return $floorRecaps;
    }

    private function createScalars(FloorRecap $floorRecap, int $level)
    {
        $floorRecap->setFloor($level + 1);
        $floorRecap->setCurrentGold($this->jsonSave->gold_per_floor[$level]);
        $floorRecap->setMaxHP($this->jsonSave->max_hp_per_floor[$level]);
        $floorRecap->setCurrentHP($this->jsonSave->current_hp_per_floor[$level]);
    }

    private function getPathTaken(int $level, int $nbUndefined, ?string $realPath): EnumPath
    {
        echo "level : $level, realPath : $realPath, nbUndefined: $nbUndefined\n";
        if (!$realPath) return EnumPath::undefined;

        return EnumPath::from($this->jsonSave->path_taken[$level - $nbUndefined]);
    }

    private function processCampfires(array &$floorRecaps)
    {
        $campfiresJson = $this->jsonSave->campfire_choices;
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
                    $floorRecaps[$floor]->addUpgrade($card);
                    break;
                case EnumCampfireChoice::Toke:
                    $jsonCard = $campfireJson->data;
                    $card = Card::createByCode($jsonCard);
                    $floorRecaps[$floor]->addPurge($card);
                    break;
            }
        }
    }

    private function processFights(array &$floorRecaps): void
    {
        $jsonEncounters = $this->jsonSave->damage_taken;
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

    //Add card upgrade
    private function processEvents($floorRecaps): void
    {
        $jsonEvents = $this->jsonSave->event_choices;
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

    private function processRewards($floorRecaps): void
    {
        // potions_obtained
        $this->processPotions($this->jsonSave->potions_obtained, $floorRecaps);

        //card_choices
        $this->processCardRewards($this->jsonSave->card_choices, $floorRecaps);

        //relics_obtained
        $this->processRelicRewards($this->jsonSave->relics_obtained, $floorRecaps);

        // boss_relics
        $this->processBossRelicRewards($this->jsonSave->boss_relics, $floorRecaps);
    }

    private function processPotions($potionsObtained, $floorRecaps): void
    {
        foreach ($potionsObtained as $jsonPotion) {
            $potion = Potion::createByCode($jsonPotion->key);
            $reward = new Reward(EnumRewardType::Potion, $potion, []);
            $floorRecaps[$jsonPotion->floor]->addReward($reward);
        }
    }

    private function processCardRewards($cardChoices, $floorRecaps): void
    {
        foreach ($cardChoices as $cardChoice) {
            $floor = $cardChoice->floor;

            switch ($cardChoice->picked) {
                case "SKIP":
                    $picked = null;
                    break;
                case "????singingbowl":
                    $picked = new SingingBowl;
                    break;
                default:
                    $picked = Card::createByCode($cardChoice->picked);
            }

            $skipped = [];
            foreach ($cardChoice->not_picked as $skip) {
                $skipped[] = Card::createByCode($skip);
            }
            $reward = new Reward(EnumRewardType::Card, $picked, $skipped);

            $floorRecaps[$floor]->addReward($reward);
        }
    }

    private function processRelicRewards($relicsObtained, $floorRecaps): void
    {
        foreach ($relicsObtained as $jsonRelic) {
            $relic = Relic::createByCode($jsonRelic->key);
            $reward = new Reward(EnumRewardType::Relic, $relic, []);
            $floorRecaps[$jsonRelic->floor]->addReward($reward);
        }
    }

    private function processBossRelicRewards($relicsObtained, $floorRecaps): void
    {
        foreach ($relicsObtained as $i => $jsonRelic) {
            $picked = Relic::createByCode($jsonRelic->picked);
            $skipped = [];
            foreach ($jsonRelic->not_picked as $skip) {
                $skipped[] = Relic::createByCode($skip);
            }
            $reward = new Reward(EnumRewardType::BossRelic, $picked, $skipped);
            $floorRecaps[($i + 1) * 17]->addReward($reward);
        }
    }
}
