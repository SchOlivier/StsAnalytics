<?

class Treasure implements IRoom
{

    public function getName(): string
    {
        return self::class;
    }

    public function getRoomRecap(): string
    {
        return "";
    }
}