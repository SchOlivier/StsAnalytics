<?php

namespace App\Entity\ref\item;

use App\Entity\ref\enum\EnumCardType;
use App\Entity\ref\enum\EnumColor;
use App\Entity\ref\enum\EnumRarity;

class Card extends Item
{
    public function __construct(
        string $code,
        string $label,
        string $description,
        EnumRarity $rarity,
        EnumColor $color,
        private EnumCardType $type
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
}
