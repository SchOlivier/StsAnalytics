<?php

namespace App\Entity\ref;

use App\Entity\ref\enum\EnumEncounterType;

class Encounter
{
    private static $encounterList = null;

    public function __construct(private string $code, private string $label, private EnumEncounterType $type)
    {
    }

    public static function createByCode(string $code) : self
    {
        $encounterList = self::getEncounterList();
        $jsonObject = $encounterList->$code;

        return new self(
            code: $jsonObject->code,
            label: $jsonObject->label,
            type: EnumEncounterType::from($jsonObject->type)
        );
    }

    private static function getEncounterList()
    {
        if (self::$encounterList) return self::$encounterList;

        $filePath = __DIR__ . '/../../../../public/assets/';
        $filename = "Encounters.json";
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        self::$encounterList = json_decode(file_get_contents($filePath . $filename));
        return self::$encounterList;
    }
}
