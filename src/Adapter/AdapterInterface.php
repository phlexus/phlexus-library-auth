<?php declare(strict_types=1);

namespace Phlexus\Libraries\Auth\Adapter;

interface AdapterInterface
{
    /**
     * @return bool
     */
    public function login(): bool;

    /**
     * @return bool
     */
    public function logout(): bool;

    /**
     * @return bool
     */
    public function isLoggedIn(): bool;
}
