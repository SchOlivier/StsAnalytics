<?php

namespace App\Service;

use App\Entity\FloorRecap;
use App\Entity\NeowChoice;
use App\Entity\ref\enum\EnumHeroClass;
use App\Entity\ref\enum\EnumPath;
use App\Entity\ref\item\Card;
use App\Entity\ref\item\Relic;
use App\Entity\ref\neow\NeowBonus;
use App\Entity\ref\neow\NeowCost;
use App\Entity\Run;
use App\Entity\RunMetadata;
use Exception;
use stdClass;

class SaveParser
{
    private stdClass $jsonSave;

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
        foreach ($relics as $relicCode) {
            $relic = Relic::createByCode($relicCode);
            if(!$relic) throw new Exception("Relique non trouvée : " . $relicCode);
            $arr[] = $relic;
        }

        return $arr;
    }

    private function getDeck(): array
    {
        $deck = $this->jsonSave->master_deck;
        $arr = [];
        foreach ($deck as $cardCode) {
            $card = Card::createByCode($cardCode);
            if(!$card) throw new Exception("Carte non trouvée : " . $cardCode);
            $arr[] = $card;
        }

        return $arr;
    }

    private function getFloorRecaps(): array
    {
        $floorRecaps = [];
        $path_per_floor = $this->jsonSave->path_per_floor;

        $nbUndefined = 0;

        foreach ($path_per_floor as $level => $path) {
            if (!$path) $nbUndefined++;

            $floorRecap = new FloorRecap;
            $this->createScalars($floorRecap, $level, $path);
            $floorRecap->setPath($this->getPathTaken($level, $nbUndefined, $path));

            // $room = $this->createRoom($level, $path);
            // $floorRecap->addRoom($room);
            $floorRecaps[$floorRecap->getFloor()] = $floorRecap;
        }

        ProcessCampfiresParser::processCampfires($floorRecaps, $this->jsonSave);
        ProcessFightsParser::processFights($floorRecaps, $this->jsonSave); // damage_taken
        ProcessEventsParser::processEvents($floorRecaps, $this->jsonSave); // event_choices
        $processParser = new ProcessRewardsParser;
        $processParser->processRewards($floorRecaps, $this->jsonSave); // potions_obtained, card_choices, relics_obtained, boss_relics
        ProcessShopParser::processPurchases($floorRecaps, $this->jsonSave);
        ProcessShopParser::processPurge($floorRecaps, $this->jsonSave);

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
        if (!$realPath) return EnumPath::undefined;

        return EnumPath::from($this->jsonSave->path_taken[$level - $nbUndefined]);
    }
}
