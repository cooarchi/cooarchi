<?php

declare(strict_types=1);

namespace CooarchiApp\Authentication;

use CooarchiQueries;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;

class Adapter implements AdapterInterface
{
    private const PASSWORD_COST = 14;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $password;

    /**
     * @var CooarchiQueries\FindUser
     */
    private $userQuery;

    public function __construct(CooarchiQueries\FindUser $userQuery)
    {
        $this->userQuery = $userQuery;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function authenticate() : Result
    {
        $user = $this->userQuery->byName($this->name);

        if (!$user) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $this->name);
        }

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(self::PASSWORD_COST);
        if ($bcrypt->verify($this->password, $user->getPassword())) {
            return new Result(Result::SUCCESS, $user);
        }

        return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->name);
    }
}
