<?php

namespace Ferranfg\Calendar\Models;

use Google_Auth_AssertionCredentials;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_AclRule;
use Google_Service_Calendar_AclRuleScope;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_Event;

class Calendar
{
    private $service;

    public function __construct($clientEmail, $clientKeyPath)
    {
        $client = $this->getClient($clientEmail, $clientKeyPath);

        $this->service = new Google_Service_Calendar($client);
    }

    private function getClient($clientEmail, $clientKeyPath) : Google_Client
    {
        $credentials = new Google_Auth_AssertionCredentials(
            $clientEmail,
            [Google_Service_Calendar::CALENDAR],
            file_get_contents($clientKeyPath)
        );

        $client = new Google_Client();
        $client->setAssertionCredentials($credentials);

        if ($client->getAuth()->isAccessTokenExpired()) $client->getAuth()->refreshTokenWithAssertion();

        return $client;
    }

    public function all() : CalendarCollection
    {
        $items   = [];
        $results = $this->service->calendarList->listCalendarList();
        foreach ($results as $result) $items[] = new CalendarItem($result);
        return new CalendarCollection($items);
    }

    public function create($attributes = []) : CalendarItem
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

    public function find($calendarId) : CalendarItem
    {
        return new CalendarItem($this->service->calendarList->get($calendarId));
    }

    public function newInstance() : CalendarItem
    {
        return new CalendarItem(new Google_Service_Calendar_Calendar);
    }

    public function destroy($calendarId)
    {
        return $this->service->calendars->delete($calendarId);
    }

    public function allEvents($calendarId)
    {
        return $this->service->events->listEvents($calendarId);
    }

    public function createEvent($calendarId, $attributes = [])
    {
        $event = new Google_Service_Calendar_Event([
            'summary' => $attributes['name'],
            'start' => [
                'dateTime' => $attributes['start'],
                'timeZone' => 'Europe/Madrid',
            ],
            'end' => [
                'dateTime' => $attributes['end'],
                'timeZone' => 'Europe/Madrid',
            ],
            'attendees' => [
                ['email' => $attributes['email']],
            ],
            'location' => $attributes['location'],
            'description' => $attributes['description']
        ]);        

        return $this->service->events->insert($calendarId, $event);
    }

}