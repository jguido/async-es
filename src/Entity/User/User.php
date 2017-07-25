<?php


namespace Unrlab\Entity\User;


use Ramsey\Uuid\UuidInterface;

class User
{
    /**
     * @var UuidInterface
     */
    public $id;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $givenName;
    /**
     * @var string
     */
    public $familyName;

    /**
     * User constructor.
     * @param string $username
     * @param string $email
     * @param string $givenName
     * @param string $familyName
     */
    public function __construct($username = null, $email = null, $givenName = null, $familyName = null)
    {
        $this->username = $username;
        $this->email = $email;
        $this->givenName = $givenName;
        $this->familyName = $familyName;
    }

    public function equals(User $user)
    {
        if ($this->id->toString() !== $user->id->toString()) {
            return false;
        }
        if ($this->username !== $user->username) {
            return false;
        }
        if ($this->email !== $user->email) {
            return false;
        }
        if ($this->givenName !== $user->givenName) {
            return false;
        }
        if ($this->familyName !== $user->familyName) {
            return false;
        }

        return true;
    }

    /**
     * @param array $userData
     * @return User
     */
    public static function fromArray(array $userData): User
    {
        $user = new self();
        if (array_key_exists('username', $userData)) {
            $user->username = $userData['username'];
        }
        if (array_key_exists('familyName', $userData)) {
            $user->familyName = $userData['familyName'];
        }
        if (array_key_exists('givenName', $userData)) {
            $user->givenName = $userData['givenName'];
        }
        if (array_key_exists('email', $userData)) {
            $user->email = $userData['email'];
        }

        return $user;
    }
}