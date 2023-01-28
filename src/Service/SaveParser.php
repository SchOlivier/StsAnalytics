<?

namespace App\Service;

use App\Entity\FloorRecap;
use App\Entity\NeowChoice;
use App\Entity\ref\enum\EnumHeroClass;
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
        $relics = [];
        $deck = [];
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

}