<?php

namespace App\ValueObjects\User;

use App\ValueObjects\BaseValueObject;

class Password implements BaseValueObject
{
    const MIN_LENGTH = 4;
    const MAX_LENGTH = 10;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $name
     * @throws \Exception
     */
    public function __construct(string $password)
    {
        $this->password = $this->hash($password);
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function isRawPassword(string $password): bool
    {
        return true;
    }

    /**
     * @param string $password
     * @return string
     */
    public function hash(string $password): string
    {
        return $this->isRawPassword($password) ? \Hash::make($password) : $password;
    }
}