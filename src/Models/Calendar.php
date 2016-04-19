<?php

namespace Ferranfg\Calendar\Models;

use Google_Client;
use Google_Config;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_AclRule;
use Google_Auth_AssertionCredentials;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_AclRuleScope;

class Calendar
{
    private $service;

    public function __construct($clientEmail, $clientKeyPath, $storagePath = '../tmp/cache')
    {
        $client = $this->getClient($clientEmail, $clientKeyPath, $storagePath);

        $this->service = new Google_Service_Calendar($client);
    }

    private function getClient($clientEmail, $clientKeyPath, $storagePath): Google_Client
    {
        $credentials = new Google_Auth_AssertionCredentials(
            $clientEmail,
            [Google_Service_Calendar::CALENDAR],
            file_get_contents($clientKeyPath)
        );

        $config = new Google_Config();
        $config->setClassConfig('Google_Cache_File', array('directory' => $storagePath));

        $client = new Google_Client($config);
        $client->setAssertionCredentials($credentials);

        if ($client->getAuth()->isAccessTokenExpired()) $client->getAuth()->refreshTokenWithAssertion();

        return $client;
    }

    public function getColors()
    {
        return $this->service->colors->get();
    }

    public function all(): CalendarCollection
    {
        $items   = [];
        $results = $this->service->calendarList->listCalendarList();
        foreach ($results as $result) $items[] = new CalendarItem($result);
        return new CalendarCollection($items);
    }

    public function create($attributes = []): CalendarItem
    {
        $calendar = new Google_Service_Calendar_Calendar();
        $calendar->setSummary($attributes['name']);

        $calendar = $this->service->calendars->insert($calendar);

        $rule  = new Google_Service_Calendar_AclRule();
        $scope = new Google_Service_Calendar_AclRuleScope();

        $scope->setType("user");
        $scope->setValue(env('GOOGLE_PUBLIC_EMAIL'));
        $rule->setScope($scope);
        $rule->setRole("owner");

        $this->service->acl->insert($calendar->id, $rule);

        return new CalendarItem($calendar);
    }

    public function find($calendarId): CalendarItem
    {
        return new CalendarItem($this->service->calendarList->get($calendarId));
    }

    public function newInstance(): CalendarItem
    {
        return new CalendarItem(new Google_Service_Calendar_Calendar);
    }

    public function destroy($calendarId)
    {
        return $this->service->calendars->delete($calendarId);
    }

    public function save($calendar)
    {
        $calendar = new Google_Service_Calendar_Calendar($calendar->toArray());

        return $this->service->calendars->update($calendar->id, $calendar);
    }

    public function newInstanceEvent($attributes = []): Google_Service_Calendar_Event
    {
        return new Google_Service_Calendar_Event([
            'summary' => array_key_exists('name', $attributes) ? $attributes['name'] : null,
            'start' => [
                'dateTime' => $attributes['start'],
                'timeZone' => 'Europe/Madrid',
            ],
            'end' => [
                'dateTime' => $attributes['end'],
                'timeZone' => 'Europe/Madrid',
            ],
            'attendees' => [
                ['email' => array_key_exists('email', $attributes) ? $attributes['email'] : null],
            ],
            'location'    => array_key_exists('location',    $attributes) ? $attributes['location']     : null,
            'description' => array_key_exists('description', $attributes) ? $attributes['description']  : null,
            'colorId'     => array_key_exists('colorId',     $attributes) ? $attributes['colorId']      : null,
            'recurrence'  => array_key_exists('recurrence',  $attributes) ? [$attributes['recurrence']] : null
        ]);
    }

    public function allEvents($calendarId)
    {
        return $this->service->events->listEvents($calendarId);
    }

    public function createEvent($calendarId, $attributes = [])
    {
        return $this->service->events->insert($calendarId, $this->newInstanceEvent($attributes));
    }

    public function findEvent($calendarId, $eventId)
    {
        return new EventItem($this->service->events->get($calendarId, $eventId));
    }

    public function saveEvent($calendarId, $event)
    {
        return $this->service->events->update($calendarId, $event->id, $event->getAttributes());
    }

    public function destroyEvent($calendarId, $eventId)
    {
        return $this->service->events->delete($calendarId, $eventId);
    }

}