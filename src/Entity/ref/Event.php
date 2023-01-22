<?php

namespace App\Entity\ref;

class Event
{
    public function __construct(private string $code, private string $label)
    {
    }
}
