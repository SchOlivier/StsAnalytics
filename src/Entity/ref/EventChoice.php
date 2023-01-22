<?php

namespace App\Entity\ref;

class EventChoice
{
    public function __construct(private Event $event, private string $code, private string $label)
    {
    }
}
