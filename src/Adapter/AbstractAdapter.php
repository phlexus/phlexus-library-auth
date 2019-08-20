<?php declare(strict_types=1);

namespace Phlexus\Libraries\Auth\Adapter;

use Phalcon\Di\DiInterface;

/**
 * AbstractAdapter
 *
 * Implementation of AdapterInterface interface and common stuff.
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var DiInterface
     */
    protected $di;

    /**
     * @param array $credentials
     * @return bool
     */
    abstract public function login(array $credentials = []): bool;

    /**
     * @return bool
     */
    abstract public function logout(): bool;

    /**
     * @return bool
     */
    abstract public function isLogged(): bool;

    /**
     * @return mixed
     */
    abstract public function getIdentity();
}
