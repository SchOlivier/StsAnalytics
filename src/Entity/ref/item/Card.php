<?php

namespace App\Entity\ref\item;

use App\Entity\ref\enum\EnumCardType;
use App\Entity\ref\enum\EnumColor;
use App\Entity\ref\enum\EnumRarity;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class Card extends Item
{
    private static $cardList = null;

    public function __construct(
        string $code,
        string $label,
        string $description,
        EnumRarity $rarity,
        EnumColor $color,
        private EnumCardType $type,
        private int $upgradeLevel = 0
    ) {
        parent::__construct($code, $label, $description, $rarity, $color);
    }

    /**
     * Get the value of type
     *
     * @return EnumCardType
     */
    public function getType(): EnumCardType
    {
        return $this->type;
    }


    /**
     * Set the value of type
     *
     * @param EnumCardType $type
     *
     * @return self
     */
    public function setType(EnumCardType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public static function createByCode(string $code): self|null
    {
        $cardList = self::getCardList();

        $split = explode("+", $code);
        $code = $split[0];

        $level = $split[1] ?? 0;
        $jsonObject = $cardList->$code ?? null;

        if(!$jsonObject) return null;
        return new self(
            code: $jsonObject->Code,
            label: $jsonObject->Name,
            description: $jsonObject->Description,
            rarity: EnumRarity::from($jsonObject->Rarity),
            color: EnumColor::from($jsonObject->Color),
            type: EnumCardType::from($jsonObject->Type),
            upgradeLevel: $level
        );
    }


    private static function getCardList()
    {
        if (self::$cardList) return self::$cardList;

        $filePath = $_ENV['PROJECT_DIR'] . 'public/assets/Cards.json';
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        self::$cardList = json_decode(file_get_contents($filePath));
        return self::$cardList;
    }
}
