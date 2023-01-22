<?php

namespace App\Entity;

use App\Entity\ref\enum\EnumRewardType;
use App\Entity\ref\item\Item;
use App\Entity\ref\SingingBowl;

class Reward
{
 public function __construct(private EnumRewardType $type, private null|Item|SingingBowl $taken, private array $skipped){}
}
