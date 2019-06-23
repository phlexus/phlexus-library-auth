<?php declare(strict_types=1);

namespace Phlexus\Libraries\Auth\Adapter;

/**
 * Auth AdapterInterface Interface
 *
 * @package Phlexus\Libraries\Auth\Adapter
 */
interface AdapterInterface
{
    /**
     * @param array $credentials
     * @return bool
     */
    public function login(array $credentials = []): bool;

    /**
     * @return bool
     */
    public function logout(): bool;

    /**
     * @return bool
     */
    public function isLogged(): bool;

    /**
     * @return mixed
     */
    public function getIdentity();
}
