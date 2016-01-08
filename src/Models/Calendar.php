<?php

namespace Ferranfg\Calendar\Models;

use Google_Auth_AssertionCredentials;
use Google_Service_Calendar;
use Google_Client;

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
            [Google_Service_Calendar::CALENDAR_READONLY],
            file_get_contents($clientKeyPath)
        );

        $client = new Google_Client();
        $client->setAssertionCredentials($credentials);

        if ($client->getAuth()->isAccessTokenExpired()) $client->getAuth()->refreshTokenWithAssertion();

        return $client;
    }

    public function all()
    {
        return $this->service->calendarList->listCalendarList();
    }

    public function create(array $data = array())
    {
        $data = array_merge($data, array(
            'id' => '',
            'defaultReminders' => array(
                'method'  => 'email',
                'minutes' => 10
            ),            
            'notificationSettings' => array(
                'method' => 'email',
                'type' => 'eventCreation'
            )
        ));

        return $this->service->calendarList->insert($data);
    }

}