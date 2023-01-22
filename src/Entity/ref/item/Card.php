<?php

namespace App\Entity\ref\item;

use App\Entity\ref\enum\EnumCardType;

class Card extends Item
{
    public function __construct(
        $code,
        $label,
        $description,
        $rarity,
        $color,
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
