<?php

namespace App\room;

interface IRoom
{
    public function getRoomRecap(): string;

    public function getName(): string;
}