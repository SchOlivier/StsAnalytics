<?php
namespace App\Entity\ref\enum;

enum EnumCampfireChoice: string
{
    case Rest = 'REST';
    case Smith = 'SMITH';
    case Recall = 'RECALL';
    case Toke = 'PURGE';
    case Dig = 'DIG';
    case Lift = 'LIFT';
}