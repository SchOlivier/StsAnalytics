<?php

namespace App\Entity\ref\item;

use App\Entity\ref\enum\EnumColor;
use App\Entity\ref\enum\EnumRarity;

abstract class Item
{

    public function __construct(
        private string $code,
        private string $label,
        private string $description,
        private EnumRarity $rarity,
        private EnumColor $color
    ) {
    }

    /**
     * Get the value of code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the value of label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the value of rarity
     *
     * @return EnumRarity
     */
    public function getRarity(): EnumRarity
    {
        return $this->rarity;
    }

    /**
     * Get the value of color
     *
     * @return EnumColor
     */
    public function getColor(): EnumColor
    {
        return $this->color;
    }

    public function __toString(): string
    {
        return $this->label;
    }
}
