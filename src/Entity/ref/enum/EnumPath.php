<?php

namespace App\Entity\ref\enum;

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