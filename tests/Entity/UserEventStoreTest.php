<?php


namespace Tests\Entity;


use PHPUnit\Framework\TestCase;
use Unrlab\Entity\User as SourceUser;

class UserEventStoreTest extends TestCase
{
    /**
     * @test
     */
    public function it_applies_event_when_event_handler_is_triggered()
    {
        $userData1 = new SourceUser\User('username', 'email@mail.com', 'givenName', 'familyName');
        $userData2 = new SourceUser\User('username1', 'email@mail.com', 'givenName', 'familyName');

        $userStore = SourceUser\UserStore::registerUser($userData1);
        $userData2->id = $userStore->getFromHistory(0)->id;

        $registeredUser = $userStore->getFromHistory(0);
        self::assertEquals('username'      , $registeredUser->username);
        self::assertEquals('familyName'    , $registeredUser->familyName);
        self::assertEquals('givenName'     , $registeredUser->givenName);
        self::assertEquals('email@mail.com', $registeredUser->email);

        $userStore->updateUser($userData2);

        self::assertEquals('username', $userStore->getFromHistory(0)->username);
        self::assertEquals('username1', $userStore->getFromHistory(1)->username);

        $pendingEvents = $userStore->getRecordedEvents();
        self::assertCount(2, $pendingEvents);

        $userWasRegisteredEvent = $pendingEvents[0];

        self::assertEquals('username', $userWasRegisteredEvent->getUser()->username);
        self::assertEquals(1, $userWasRegisteredEvent->version());

        $userWasUpdatedEvent = $pendingEvents[1];

        self::assertEquals('username1', $userWasUpdatedEvent->getNewUser()->username);
        self::assertEquals(2, $userWasUpdatedEvent->version());

    }

    /**
     * @test
     */
    public function it_replay_from_history()
    {
        $userData1 = new SourceUser\User('username', 'email@mail.com', 'givenName', 'familyName');
        $userData2 = new SourceUser\User('username1', 'email@mail.com', 'givenName', 'familyName');

        $userStore = SourceUser\UserStore::registerUser($userData1);
        $userData2->id = $userStore->getFromHistory(0)->id;

        $registeredUser = $userStore->getFromHistory(0);
        self::assertEquals('username'      , $registeredUser->username);
        self::assertEquals('familyName'    , $registeredUser->familyName);
        self::assertEquals('givenName'     , $registeredUser->givenName);
        self::assertEquals('email@mail.com', $registeredUser->email);

        $userStore->updateUser($userData2);

        $events = $userStore->getRecordedEvents();

        $restoredUser = SourceUser\UserStore::fromHistory($events);

        self::assertEquals('username1', $restoredUser->username);
    }
}