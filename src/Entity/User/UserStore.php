<?php


namespace Unrlab\Entity\User;


use Assert\Assert;
use Ramsey\Uuid\Uuid;
use Unrlab\Domain\EventStore;
use Unrlab\Domain\Message;

class UserStore extends EventStore
{
    /**
     * @var User
     */
    private $user;


    /**
     * @return Message[]
     */
    public function getRecordedEvents()
    {
        return $this->popRecordedEvents();
    }

    /**
     * @param array $events
     * @return User
     */
    public static function fromHistory(array $events)
    {
        $store = self::reconstituteFromHistory($events);

        return $store->getFromHistory(count($events) -1);
    }

    /**
     * @param int $index
     * @return User
     */
    public function getFromHistory($index = 0)
    {
        return $this->extractUserFromMessage(parent::getFromHistory($index));

    }

    /**
     * @param User $user
     * @return UserStore
     */
    public static function registerUser(User $user)
    {
        Assert::that($user->username)->notEmpty()->string();
        Assert::that($user->familyName)->notEmpty()->string();
        Assert::that($user->givenName)->notEmpty()->string();
        Assert::that($user->email)->notEmpty()->string();

        $id = Uuid::uuid4();

        $instance = new self();

        $instance->recordThat(UserWasRegistered::occur($id->toString(), ['user' => $user]));

        return $instance;
    }

    /**
     * @param User $newUser
     */
    public function updateUser(User $newUser)
    {
        Assert::that($newUser->username)->notEmpty()->string();
        Assert::that($newUser->familyName)->notEmpty()->string();
        Assert::that($newUser->givenName)->notEmpty()->string();
        Assert::that($newUser->email)->notEmpty()->string();

        if (!$newUser->equals($this->user)) {
            $this->recordThat(UserWasUpdated::occur($newUser->id->toString(), ['oldUser' => $this->user, 'newUser' => $newUser]));
        }
    }

    /**
     * @param UserWasRegistered $event
     */
    protected function whenUserWasRegistered(UserWasRegistered $event)
    {

        $id = Uuid::fromString($event->getId());
        $user = $event->getUser();
        $user->id = $id;
        $this->user = $user;
    }

    /**
     * @param UserWasUpdated $event
     */
    protected function whenUserWasUpdated(UserWasUpdated $event)
    {
        $this->user = $event->getNewUser();
    }

    /**
     * @return string representation of the unique identifier of the event store
     */
    protected function getIdentifier()
    {
        return $this->user->id->toString();
    }

    /**
     * @param $message
     * @return mixed
     */
    private function extractUserFromMessage($message)
    {
        if (method_exists($message, 'getUser')) {

            return $message->getUser();
        } else {

            return $message->getNewUser();
        }
    }
}
