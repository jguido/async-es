<?php


namespace Unrlab\Entity\User;


use Unrlab\Domain\Message;

class UserWasUpdated extends Message
{
    /**
     * @return User
     */
    public function getOldUser()
    {
        return $this->payload['oldUser'];
    }
    /**
     * @return User
     */
    public function getNewUser()
    {
        return $this->payload['newUser'];
    }
}
