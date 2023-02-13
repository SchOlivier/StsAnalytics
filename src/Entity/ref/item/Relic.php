<?php
namespace App\Entity\ref\item;

use App\Entity\ref\enum\EnumColor;
use App\Entity\ref\enum\EnumRarity;

class Relic extends Item 
{
    private static $relicList = null;

    public function __construct(
        string $code,
        string $label,
        string $description,
        EnumRarity $rarity,
        EnumColor $color,
    ) {
        parent::__construct($code, $label, $description, $rarity, $color);
    }

    public static function createByCode(string $code)
    {
        $relicList = self::getRelicList();
        $jsonObject = $relicList->$code;

        return new self(
            code: $jsonObject->code,
            label: $jsonObject->label,
            description: $jsonObject->description,
            rarity: EnumRarity::from($jsonObject->rarity),
            color: EnumColor::from($jsonObject->color),
        );
    }

    private static function getRelicList()
    {
        if (self::$relicList) return self::$relicList;

        $filePath = __DIR__ . '/../../../../public/assets/';
        $filename = "Relics.json";
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        self::$relicList = json_decode(file_get_contents($filePath . $filename));
        return self::$relicList;
    }
}