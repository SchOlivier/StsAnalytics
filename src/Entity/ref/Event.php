<?php

namespace App\Entity\ref;

class Event
{
    private static $eventList;

    public function __construct(private string $code, private string $label)
    {
    }

    public static function createByCode(string $code)
    {
        $eventList = self::getEventList();
        $jsonObject = $eventList->{"Living Wall"};

        return new self(
            code: $jsonObject->code,
            label: $jsonObject->label,
        );
    }


    private static function getEventList()
    {
        if (self::$eventList) return self::$eventList;

        $filePath = $_ENV['PROJECT_DIR'] . 'public/assets/Events.json';
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        self::$eventList = json_decode(file_get_contents($filePath));
        return self::$eventList;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
