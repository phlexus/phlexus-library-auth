<?php declare(strict_types=1);

namespace Phlexus\Libraries\Auth\Adapter;

use Phalcon\Di\DiInterface;

/**
 * Auth AdapterInterface Interface
 *
 * @package Phlexus\Libraries\Auth\Adapter
 */
interface AdapterInterface
{
    public function __construct(DiInterface $di, array $configurations);

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
