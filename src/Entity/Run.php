<?php

namespace App\Entity;

class Run
{
    public function __construct(
        private RunMetadata $metadata,
        private array $floorRecaps,
        private array $relics,
        private NeowChoice $neowChoice,
        private array $deck
    )
    {
    }

    public function __toString()
    {
        $string = "Métadonnéees : \n";
        $string .= $this->metadata->__toString() . "\n";
        return $string;
    }
}