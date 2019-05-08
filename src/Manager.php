<?php declare(strict_types=1);

namespace Phlexus\Libraries\Auth;

use Phalcon\Di\Injectable;
use Phalcon\Events\EventsAwareInterface;

class Manager extends Injectable implements EventsAwareInterface
{
    public function __construct(string $model, string $loginField, string $passwordField, string $userIdField)
    {

    }

    public function login(): bool
    {

    }

    public function logout(): bool
    {

    }
}
