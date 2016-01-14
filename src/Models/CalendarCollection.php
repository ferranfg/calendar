<?php

namespace Ferranfg\Calendar\Models;

use Illuminate\Support\Collection;

class CalendarCollection extends Collection
{
	public function __construct($items)
	{
		parent::__construct($items);
	}

}