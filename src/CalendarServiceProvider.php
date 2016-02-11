<?php

namespace Ferranfg\Calendar;

use Ferranfg\Calendar\Models\Calendar;
use Illuminate\Support\ServiceProvider;

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
			$clientEmail = env('GOOGLE_CLIENT_EMAIL');
			$privatePath = base_path(env('GOOGLE_PRIVATE_KEY'));
			$storagePath = storage_path() . '/framework/cache';

			return new Calendar($clientEmail, $privatePath, $storagePath);
		});
	}

}