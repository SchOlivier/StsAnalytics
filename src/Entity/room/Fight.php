<?

namespace App\room;

class Fight implements IRoom
{
    // private Encounter $encounter;
    private int $nbTurn;

    public function getName(): string
    {
        return self::class;
    }

    public function getRoomRecap(): string
    {
        return "todo";
    }
}