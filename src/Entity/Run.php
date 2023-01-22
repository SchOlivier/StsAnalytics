<?php

namespace App\Entity;

class Run
{
    public function __construct(
        private RunMetadata $metadata,
        private FloorRecap $floorRecap,
        private array $relics,
        private NeowChoice $neowChoice,
        private array $deck
    )
    {
    }
}