<?php
namespace App\Entity\ref\enum;

enum EnumCardType: string
{
    case Skill = 'Skill';
    case Attack = 'Attack';
    case Power = 'Power';
    case Status = 'Status';
    case Curse = 'Curse';
}
