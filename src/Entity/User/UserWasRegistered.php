<?php


namespace Unrlab\Entity\User;


use Unrlab\Domain\Message;

class UserWasRegistered extends Message
{
    /**
     * @return User
     */
    public function getUser()
    {
        return $this->payload['user'];
    }

}