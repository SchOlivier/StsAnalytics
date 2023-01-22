<?php

namespace App\Entity\room;

interface IRoom
{
    public function getRoomRecap(): string;

    public function getName(): string;
}