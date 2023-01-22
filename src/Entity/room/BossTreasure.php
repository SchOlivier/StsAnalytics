<?

namespace App\Entity\room;

class BossTreasure implements IRoom
{
    private $chosenRelic;
    private $offeredRelics;

    public function getName(): string
    {
        return self::class;
    }



    public function getRoomRecap(): string
    {
        return "Offered fuu, picked foo, skipped bar";
    }
}