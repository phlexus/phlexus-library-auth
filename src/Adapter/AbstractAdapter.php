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
     * @var mixed|null
     */
    protected $identity = null;

    /**
     * @param array $credentials
     * @return bool
     */
    abstract public function validate(array $credentials): bool;

    /**
     * Identity of just logged in user
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        return $this->identity;
    }
}
