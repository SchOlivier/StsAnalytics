<?php

namespace App\Entity\ref\item;

use App\Entity\ref\enum\EnumColor;
use App\Entity\ref\enum\EnumRarity;

class Potion extends Item
{
    private static $potionList = null;

    public static function createByCode(string $code)
    {
        $potionList = self::getPotionList();
        $jsonObject = $potionList->$code;

        return new self(
            code: $jsonObject->Code,
            label: $jsonObject->Name,
            description: $jsonObject->Description,
            rarity: EnumRarity::from($jsonObject->Rarity),
            color: EnumColor::from($jsonObject->Color),
        );
    }


    private static function getPotionList()
    {
        if (self::$potionList) return self::$potionList;

        $filePath = __DIR__ . '/../../../../public/assets/';
        $filename = "Potions.json";
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        self::$potionList = json_decode(file_get_contents($filePath . $filename));
        return self::$potionList;
    }
}
