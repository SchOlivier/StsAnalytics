<?php
namespace App\Entity\ref\enum;

enum EnumRarity: string
{
    case Basic = 'Basic';
    case Common = 'Common';
    case Uncommon = 'Uncommon';
    case Rare = 'Rare';
    case Special = 'Special';
    case Boss = 'Boss';
    case Shop = 'Shop';
}