<?php declare(strict_types=1);

namespace Phlexus\Libraries\Auth\Adapter;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @return bool
     */
    abstract public function login(): bool;

    /**
     * @return bool
     */
    abstract public function logout(): bool;

    /**
     * @return bool
     */
    abstract public function isLoggedIn(): bool;
}
