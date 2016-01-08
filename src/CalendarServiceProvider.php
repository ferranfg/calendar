<?php

namespace Ferranfg\Calendar;

use Ferranfg\Calendar\Models\Calendar;

class CalendarServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('calendar', function ($app)
		{
			return new Calendar(env('GOOGLE_CLIENT_EMAIL'), base_path(env('GOOGLE_PRIVATE_KEY')));
		});
	}

}