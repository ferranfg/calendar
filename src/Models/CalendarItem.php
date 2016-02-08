<?php

namespace Ferranfg\Calendar\Models;

class CalendarItem extends Item
{
    public function setName($name)
    {
        return $this->attributes->setSummary($name);
    }
}