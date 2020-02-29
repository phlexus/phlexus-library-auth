<?php

/**
 * This file is part of the Phlexus CMS.
 *
 * (c) Phlexus CMS <cms@phlexus.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phlexus\Libraries\Auth\Adapter;

use Phalcon\Di\DiInterface;

/**
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
