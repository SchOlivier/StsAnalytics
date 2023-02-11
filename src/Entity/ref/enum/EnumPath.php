<?php

namespace App\Entity\ref\enum;

use App\Entity\room\Event;
use App\Entity\room\Fight;

enum EnumPath : string
{
    case event = '?';
    case monster = 'M';
    case shop = '$';
    case elite = 'E';
    case treasure = 'T';
    case rest = 'R';
    case boss = 'BOSS';
    case undefined = 'undefined';
}