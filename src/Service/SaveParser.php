<?php

namespace App\Service;

use App\Entity\DeckCard;
use App\Entity\FloorRecap;
use App\Entity\NeowChoice;
use App\Entity\ref\Encounter;
use App\Entity\ref\enum\EnumCampfireChoice;
use App\Entity\ref\enum\EnumCardType;
use App\Entity\ref\enum\EnumColor;
use App\Entity\ref\enum\EnumHeroClass;
use App\Entity\ref\enum\EnumPath;
use App\Entity\ref\enum\EnumRarity;
use App\Entity\ref\Event as RefEvent;
use App\Entity\ref\item\Card;
use App\Entity\ref\item\Relic;
use App\Entity\ref\neow\NeowBonus;
use App\Entity\ref\neow\NeowCost;
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
            $split = explode("+", $card);
            $refCard = Card::createByCode($split[0]);
            $arr[] = new DeckCard($refCard, $split[1] ?? 0);
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
        // $this->processRewards(); //

        // Boucler sur le json->path_per_floor, instancier un floorRecap pour chacun d'eux

        // Traiter tous les tableaux du json pertinents (gold per floor, campfires, ...) et pour chacun d'eux agrémenter les floorRecaps associés.
        // probalement oublier l'histoire des potions utilisées (il nous manque l'info claire de QUELLE potion est utilisée)
        // dans les fights, ajouter "damage taken"
        // Créer des fonctions spécifiques pour les entités (json) composites (e.g. les achats)
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
                    $deckCard = $this->getDeckCardByJsonCode($jsonCard);
                    $floorRecaps[$floor]->addUpgrade($deckCard);
                    break;
                case EnumCampfireChoice::Toke:
                    $jsonCard = $campfireJson->data;
                    $deckCard = $this->getDeckCardByJsonCode($jsonCard);
                    $floorRecaps[$floor]->addPurge($deckCard);
                    break;
            }
        }
    }

    private function getDeckCardByJsonCode(string $code): DeckCard
    {
        $split = explode("+", $code);
        $card = Card::createByCode($split[0]);
        $level = $split[1] ?? 0;
        return new DeckCard($card, $level);
    }

    private function processFights(array &$floorRecaps): void
    {
        $jsonEncounters = $this->jsonSave->damage_taken;
        foreach ($jsonEncounters as $jsonEncounter)
        {
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

    private function processEvents($floorRecaps): void
    {
        $jsonEvents = $this->jsonSave->event_choices;
        foreach ($jsonEvents as $jsonEvent)
        {
            $floor = $jsonEvent->floor;
            $refEvent = RefEvent::createByCode($jsonEvent->event_name);
            
            $cardCodes = $jsonEvent->cards_obtained ?? [];
            $cardsObtained = [];
            foreach ($cardCodes as $card)
            {
                $cardsObtained[] = Card::createByCode($card);
            }
            
            $cardTransformedCodes = $jsonEvent->cards_transformed ?? [];
            $cardsTransformed = [];
            foreach ($cardTransformedCodes as $card)
            {
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
