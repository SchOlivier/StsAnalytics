<?php

namespace App\Service;

use App\Entity\DeckCard;
use App\Entity\FloorRecap;
use App\Entity\NeowChoice;
use App\Entity\ref\enum\EnumCardType;
use App\Entity\ref\enum\EnumColor;
use App\Entity\ref\enum\EnumHeroClass;
use App\Entity\ref\enum\EnumRarity;
use App\Entity\ref\item\Card;
use App\Entity\ref\item\Relic;
use App\Entity\ref\neow\NeowBonus;
use App\Entity\ref\neow\NeowCost;
use App\Entity\Run;
use App\Entity\RunMetadata;

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
        $floorRecap = [];

        $run = new Run($metada, $floorRecap, $relics, $neowChoice, $deck);

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
        $neowCost = new NeowCost(code: "code", label: "label");
        $neowBonus = new NeowBonus(code: "code", label: "label");
        return new NeowChoice($neowCost, $neowBonus);
    }

    private function getRelics(): array
    {
        $relics = $this->jsonSave->relics;
        $arr = [];
        foreach ($relics as $relic) {
            $arr[] = new Relic($relic, $relic, "description", EnumRarity::Common, EnumColor::Colorless);
        }

        return $arr;
    }

    private function getDeck(): array
    {
        $deck = $this->jsonSave->master_deck;
        $arr = [];
        foreach ($deck as $card) {
            $refCard = new Card($card, $card, "description", EnumRarity::Common, EnumColor::Colorless, EnumCardType::Attack);
            $arr[] = new DeckCard($refCard, 0);
        }

        return $arr;
    }

    private function getFloorRecaps(): array
    {
        // Boucler sur le json->path_per_floor, instancier un floorRecap pour chacun d'eux

        // Traiter tous les tableaux du json pertinents (gold per floor, campfires, ...) et pour chacun d'eux agrémenter les floorRecaps associés.
        // probalement oublier l'histoire des potions utilisées (il nous manque l'info claire de QUELLE potion est utilisée)
        // dans les fights, ajouter "damage taken"
        // Créer des fonctions spécifiques pour les entités (json) composites (e.g. les achats)
        return [];
    }
}
