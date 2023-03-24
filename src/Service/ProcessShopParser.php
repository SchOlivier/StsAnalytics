<?php

namespace App\Service;

use App\Entity\ref\item\Card;
use App\Entity\ref\item\Relic;
use Exception;
use stdClass;

class ProcessShopParser
{

    public static function processPurchases(array &$floorRecaps, stdClass $jsonSave): void
    {
        foreach ($jsonSave->item_purchase_floors as $index => $floor) {
            $itemCode = $jsonSave->items_purchased[$index];
            $item = Card::createByCode($itemCode) ?? Relic::createByCode($itemCode);
            if(!$item) throw new Exception("Objet non trouve : " . $itemCode);
            $floorRecaps[$floor]->addPurchase($item);
        }

    }

    public static function processPurge(array &$floorRecaps, stdClass $jsonSave): void
    {
        foreach ($jsonSave->items_purged_floors as $index => $floor) {
            $itemCode = $jsonSave->items_purged[$index];
            $item = Card::createByCode($itemCode);
            
            $floorRecaps[$floor]->addPurge($item);
        }
    }
}