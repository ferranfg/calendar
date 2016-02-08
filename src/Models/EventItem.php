<?php

namespace Ferranfg\Calendar\Models;

use Carbon\Carbon;
use Google_Service_Calendar_EventDateTime;

class EventItem extends Item
{
    public function setStart(Carbon $startDate)
    {
        $start = new Google_Service_Calendar_EventDateTime;
        $start->setTimeZone("Europe/Madrid");
        $start->setDateTime($startDate->toAtomString());

        $this->attributes->setStart($start);
    }

    public function setEnd(Carbon $endDate)
    {
        $end = new Google_Service_Calendar_EventDateTime;
        $end->setTimeZone("Europe/Madrid");
        $end->setDateTime($endDate->toAtomString());

        $this->attributes->setEnd($end);
    }

    public function setKind($kind)
    {
        $this->attributes->setKind($kind);
    }
}