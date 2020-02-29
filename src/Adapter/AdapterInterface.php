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
 * Auth AdapterInterface Interface
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
