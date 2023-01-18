<?

class Shop implements IRoom
{

    //Est-ce qu'on met les purchase ici ? au lieu du floorRecap
    public function getName(): string
    {
        return self::class;
    }

    public function getRoomRecap(): string
    {
        return "";
    }
}