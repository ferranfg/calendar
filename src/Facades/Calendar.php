<?php

namespace Ferranfg\Calendar\Facades;

use Illuminate\Support\Facades\Facade;

class Calendar extends Facade
{
	protected static function getFacadeAccessor()
    {
        return 'calendar';
    }
}