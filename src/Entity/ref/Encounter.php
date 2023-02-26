<?php

namespace App\Entity\ref;

use App\Entity\ref\enum\EnumEncounterType;

class Encounter
{
    public function __construct(private string $code, private string $label, private EnumEncounterType $type)
    {
    }
}
