<?php

namespace App\Service;

use App\Entity\ref\enum\EnumRewardType;
use App\Entity\ref\item\Card;
use App\Entity\ref\item\Potion;
use App\Entity\ref\item\Relic;
use App\Entity\ref\SingingBowl;
use App\Entity\Reward;
use stdClass;

class ProcessRewardsParser
{
    public function processRewards(array &$floorRecaps, stdClass $jsonSave): void
    {
        // potions_obtained
        $this->processPotions($jsonSave->potions_obtained, $floorRecaps);

        //card_choices
        $this->processCardRewards($jsonSave->card_choices, $floorRecaps);

        //relics_obtained
        $this->processRelicRewards($jsonSave->relics_obtained, $floorRecaps);

        // boss_relics
        $this->processBossRelicRewards($jsonSave->boss_relics, $floorRecaps);
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