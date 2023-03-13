<?php

namespace App\Entity;

use App\Entity\ref\enum\EnumRewardType;
use App\Entity\ref\item\Item;
use App\Entity\ref\SingingBowl;

class Reward
{
    public function __construct(private EnumRewardType $type, private null|Item|SingingBowl $taken, private array $skipped)
    {
    }

    public function __toString()
    {
        $string = "type : " . $this->type->value . "\n";
        $string .= "\t\ttaken : " . $this->taken ."\n";
        $string .= "\t\tskipped : \n";
        foreach($this->skipped as $skip)
        {
            $string .= "\t\t" . $skip . "\n";
        }
        return $string;
    }
}
